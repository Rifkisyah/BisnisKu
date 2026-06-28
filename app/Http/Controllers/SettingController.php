<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $settings = [];
        if ($user->role->name === 'owner') {
            $settings = [
                'store_name' => \App\Models\Setting::get('store_name', ''),
                'store_description' => \App\Models\Setting::get('store_description', ''),
                'store_address' => \App\Models\Setting::get('store_address', ''),
                'store_phone' => \App\Models\Setting::get('store_phone', ''),
                'qris_image_url' => \App\Models\Setting::get('qris_image_url', ''),
                'store_logo' => \App\Models\Setting::get('store_logo', ''),
            ];
        }
        return view('settings.index', compact('user', 'settings'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'contact' => 'nullable|string|max:50',
            'photo_profile' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo_profile')) {
            $path = $request->file('photo_profile')->store('profiles', 'public');
            $validated['photo_profile'] = '/storage/' . $path;
        }

        $user->update($validated);
        return back()->with('success', __('messages.updated', ['item' => 'Profil']));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => __('messages.wrong_password')]);
        }
        Auth::user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', __('messages.password_updated'));
    }

    public function switchLocale(Request $request)
    {
        $locale = $request->get('locale', 'id');
        if (in_array($locale, ['id', 'en'])) {
            session(['locale' => $locale]);
        }
        return back();
    }

    public function updateStoreProfile(Request $request)
    {
        // Only owner should be here (middleware protected)
        $validated = $request->validate([
            'store_name' => 'nullable|string|max:255',
            'store_description' => 'nullable|string|max:1000',
            'store_address' => 'nullable|string|max:500',
            'store_phone' => 'nullable|string|max:50',
            'qris_image_url' => 'nullable|url|max:2000',
        ]);

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                \App\Models\Setting::set($key, $value);
            }
        }
        
        // Handle file upload for store_logo if any
        if ($request->hasFile('store_logo')) {
            $request->validate(['store_logo' => 'image|max:2048']);
            $path = $request->file('store_logo')->store('settings', 'public');
            \App\Models\Setting::set('store_logo', '/storage/' . $path);
        }

        return back()->with('success', 'Profil toko berhasil diperbarui.');
    }
}
