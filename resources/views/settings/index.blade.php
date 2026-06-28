@extends('layouts.app')
@section('page-title', __('messages.settings'))
@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <div class="mb-2 -ml-2">
        <a href="javascript:void(0)" onclick="if(window.history.length > 2) { window.history.back(); } else { window.location.href = '{{ route('dashboard') }}'; }" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
    </div>
    
    @if(auth()->user()->role->name === 'owner')
    <div class="card-feature p-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] flex items-center">
                <svg class="w-6 h-6 mr-2 text-[var(--color-primary)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
                Pengaturan QRIS & Pembayaran
            </h3>
            <a href="{{ route('settings.payment') }}" class="btn-primary !py-1.5 !px-4 type-caption-bold">Atur QRIS</a>
        </div>
        <p class="type-body-sm text-[var(--color-slate)]">Konfigurasi mode pembayaran QRIS (Manual/Dinamis), upload gambar QRIS statis, atau atur integrasi Payment Gateway.</p>
    </div>

    {{-- Store Profile --}}
    <div class="card-feature p-8">
        <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-[var(--color-primary)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" /></svg>
            Profil Toko
        </h3>
        <form method="POST" action="{{ route('settings.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">Logo/Gambar Toko</label>
                @if(!empty($settings['store_logo']))
                    <img src="{{ asset($settings['store_logo']) }}" alt="Store Logo" class="h-16 mb-2 rounded border border-gray-200">
                @endif
                <input type="file" name="store_logo" accept="image/*" class="input-field !p-1.5">
            </div>
            <div>
                <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">Nama Toko</label>
                <input type="text" name="store_name" value="{{ old('store_name', $settings['store_name']) }}" class="input-field">
            </div>
            <div>
                <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">Keterangan Toko</label>
                <textarea name="store_description" rows="2" class="input-field !h-auto !py-3">{{ old('store_description', $settings['store_description'] ?? '') }}</textarea>
            </div>
            <div>
                <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.address') }}</label>
                <textarea name="store_address" rows="2" class="input-field !h-auto !py-3">{{ old('store_address', $settings['store_address']) }}</textarea>
            </div>
            <div>
                <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">Telepon Toko</label>
                <input type="text" name="store_phone" value="{{ old('store_phone', $settings['store_phone']) }}" class="input-field">
            </div>
            <button type="submit" class="btn-primary !py-2.5 !px-5 w-full">{{ __('messages.save') }}</button>
        </form>
    </div>
    @endif

    {{-- User Profile --}}
    <div class="card-feature p-8">
        <div class="flex items-center gap-3 mb-4">
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] flex items-center">
                <svg class="w-6 h-6 mr-2 text-[var(--color-primary)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                Profil Anda
            </h3>
            <span class="badge badge-success px-2 py-0.5 text-xs font-medium">Aktif</span>
        </div>
        <form method="POST" action="{{ route('settings.profile') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">Foto Profil</label>
                @if(auth()->user()->photo_profile)
                    <img src="{{ asset(auth()->user()->photo_profile) }}" alt="Profile Photo" class="h-16 w-16 mb-2 rounded-full border border-gray-200 object-cover">
                @endif
                <input type="file" name="photo_profile" accept="image/*" class="input-field !p-1.5">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">Username</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" class="input-field" required>
                </div>
                <div>
                    <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.email') }}</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-field" required>
                </div>
                <div class="sm:col-span-2">
                    <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">Nomor HP</label>
                    <input type="text" name="contact" value="{{ old('contact', $user->contact) }}" class="input-field">
                </div>
            </div>
            <button type="submit" class="btn-primary !py-2.5 !px-5 w-full">{{ __('messages.save') }}</button>
        </form>
    </div>

    {{-- Change Password --}}
    <div class="card-feature p-8">
        <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-[var(--color-primary)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
            {{ __('messages.change_password') }}
        </h3>
        <form method="POST" action="{{ route('settings.password') }}" class="space-y-4">
            @csrf
            <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.current_password') }}</label><input type="password" name="current_password" required class="input-field"></div>
            <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.new_password') }}</label><input type="password" name="password" required class="input-field"></div>
            <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.confirm_password') }}</label><input type="password" name="password_confirmation" required class="input-field"></div>
            <button type="submit" class="btn-primary !py-2.5 !px-5 w-full">{{ __('messages.save') }}</button>
        </form>
    </div>
</div>
@endsection
