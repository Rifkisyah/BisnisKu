<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'store_name'       => 'required|string|min:3|max:150',
            'store_slug'       => 'nullable|string|max:100|unique:stores,slug|regex:/^[a-z0-9\-]+$/',
            'owner_name'       => 'required|string|min:3|max:100',
            'email'            => 'required|string|email|max:255|unique:users',
            'password'         => ['required', 'confirmed', Password::defaults()],
            'contact'          => 'nullable|string|max:30',
        ], [
            'store_slug.unique' => 'URL Katalog Toko telah digunakan.',
        ]);

        $slug = $request->filled('store_slug')
            ? $request->store_slug
            : Store::generateSlug($request->store_name);

        DB::transaction(function () use ($request, $slug) {
            // 1. Create store (owner_id will be set after user is created)
            $store = Store::create([
                'name'             => $request->store_name,
                'slug'             => $slug,
                'phone'            => $request->contact,
                'is_active'        => true,
                'catalog_enabled'  => true,
            ]);

            // 2. Create owner user
            $ownerRole = Role::where('name', 'owner')->firstOrFail();

            $user = User::create([
                'username'   => $request->owner_name,
                'email'      => $request->email,
                'password'   => Hash::make($request->password),
                'role_id'    => $ownerRole->id,
                'store_id'   => $store->id,
                'contact'    => $request->contact,
                'status'     => 'active',
            ]);

            // 3. Link store owner
            $store->update(['owner_id' => $user->id]);

            // 4. Seed default settings for this store
            app()->instance('current_store', $store);
            \App\Models\Setting::set('store_name', $request->store_name);
            \App\Models\Setting::set('receipt_footer', 'Terima kasih telah berbelanja di ' . $request->store_name . '.');
            \App\Models\Setting::set('tax_percentage', '0');
            \App\Models\Setting::set('default_currency', 'IDR');
            \App\Models\PaymentSetting::create([
                'store_id'       => $store->id,
                'qris_mode'      => 'manual',
                'is_qris_active' => true,
            ]);

            Auth::login($user);
        });

        return redirect()->route('dashboard')
            ->with('success', 'Toko berhasil didaftarkan! Selamat datang di BisnisKu.');
    }
}

