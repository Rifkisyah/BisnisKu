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
            'qris_mode' => 'required|in:manual,dynamic',
            'manual_qris_image' => 'nullable|image|max:2048',
            'qris_provider' => 'nullable|string|max:255',
            'merchant_id' => 'nullable|string|max:255',
            'client_key' => 'nullable|string|max:255',
            'server_key' => 'nullable|string|max:255',
            'callback_url' => 'nullable|url|max:255',
        ]);

        $setting = PaymentSetting::getSettings();

        // Specific validation logic as requested
        if ($validated['qris_mode'] === 'manual') {
            if (!$setting->manual_qris_image && !$request->hasFile('manual_qris_image')) {
                return back()->withErrors(['manual_qris_image' => 'Gambar QRIS statis wajib diunggah untuk mode manual.']);
            }
        } else {
            if (empty($validated['qris_provider']) || empty($validated['merchant_id']) || empty($validated['server_key'])) {
                return back()->withErrors(['qris_provider' => 'Kredensial provider wajib diisi untuk mode dinamis.']);
            }
        }

        if ($request->hasFile('manual_qris_image')) {
            $path = $request->file('manual_qris_image')->store('qris', 'public');
            $validated['manual_qris_image'] = '/storage/' . $path;
        }

        $setting->update($validated);

        return back()->with('success', 'Pengaturan pembayaran berhasil diperbarui.');
    }
}
