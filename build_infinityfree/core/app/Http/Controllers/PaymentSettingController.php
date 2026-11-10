<?php

namespace App\Http\Controllers;

use App\Models\PaymentSetting;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    public function index()
    {
        // Only owner should access this
        if (auth()->user()->role->name !== 'owner') {
            abort(403);
        }

        $paymentSetting = PaymentSetting::getSettings();
        return view('settings.payment', compact('paymentSetting'));
    }

    public function update(Request $request)
    {
        if (auth()->user()->role->name !== 'owner') {
            abort(403);
        }

        $validated = $request->validate([
            'qris_mode'            => 'required|in:manual,dynamic',
            'manual_qris_image'    => 'nullable|image|max:2048',
            'qris_provider'        => 'nullable|string|max:255',
            'merchant_id'          => 'nullable|string|max:255',
            'server_key'           => 'nullable|string|max:500',
            'client_key'           => 'nullable|string|max:500',
            'qris_acquirer'        => 'nullable|in:gopay,airpay shopee',
            'bank_name'            => 'nullable|string|max:255',
            'bank_account_number'  => 'nullable|string|max:255',
            'bank_account_name'    => 'nullable|string|max:255',
        ]);

        $setting = PaymentSetting::getSettings();

        // Validasi spesifik per mode
        if ($validated['qris_mode'] === 'manual') {
            if (!$setting->manual_qris_image && !$request->hasFile('manual_qris_image')) {
                return back()->withErrors(['manual_qris_image' => 'Gambar QRIS statis wajib diunggah untuk mode manual.']);
            }
        } else {
            // Mode dynamic: wajib ada server_key (tidak menggunakan .env lagi)
            if (empty($validated['qris_provider'])) {
                return back()->withErrors(['qris_provider' => 'Provider wajib dipilih untuk mode dinamis.']);
            }
            if (empty($validated['server_key']) && empty($setting->server_key)) {
                return back()->withErrors(['server_key' => 'Server Key wajib diisi untuk mode dinamis.']);
            }
        }

        // Upload gambar QRIS manual
        if ($request->hasFile('manual_qris_image')) {
            $path = $request->file('manual_qris_image')->store('qris', 'public');
            $validated['manual_qris_image'] = '/storage/' . $path;
        }

        // Jangan overwrite server_key/client_key jika field dikosongkan
        // (user mungkin tidak mau mengubah key yang sudah ada)
        if (empty($validated['server_key'])) {
            unset($validated['server_key']);
        }
        if (empty($validated['client_key'])) {
            unset($validated['client_key']);
        }

        $setting->update($validated);

        return back()->with('success', 'Pengaturan pembayaran berhasil diperbarui.');
    }
}
