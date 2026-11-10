@extends('layouts.app')
@section('page-title', __('messages.add') . ' ' . __('messages.employee'))
@section('content')
<div class="mx-auto max-w-lg"><div class="card-feature p-8">
    <form method="POST" action="{{ route('employees.store') }}" class="space-y-5">@csrf
        <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.name') }} *</label><input type="text" name="username" value="{{ old('username') }}" required minlength="3" maxlength="60" class="input-field"><p class="mt-1 text-xs text-gray-400">3–60 karakter</p></div>
        <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.email') }} *</label><input type="email" name="email" value="{{ old('email') }}" required maxlength="150" class="input-field"></div>
        <div x-data="{ show: false }">
            <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.password') }} *</label>
            <div class="relative">
                <input :type="show ? 'text' : 'password'" name="password" required minlength="8" class="input-field pr-10">
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-[var(--color-slate)] hover:text-[var(--color-ink)] focus:outline-none">
                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.978 9.978 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                </button>
            </div>
        </div>
        <div x-data="{ show: false }">
            <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">Konfirmasi Password *</label>
            <div class="relative">
                <input :type="show ? 'text' : 'password'" name="password_confirmation" required minlength="8" class="input-field pr-10">
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-[var(--color-slate)] hover:text-[var(--color-ink)] focus:outline-none">
                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.978 9.978 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                </button>
            </div>
        </div>
        <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.role') }} *</label>
            <select name="role_id" required class="input-field">@foreach($roles as $role)<option value="{{ $role->id }}">{{ $role->display_name }}</option>@endforeach</select></div>
        <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.contact') }}</label><input type="text" name="contact" value="{{ old('contact') }}" minlength="7" maxlength="20" class="input-field" placeholder="08xxxxxxxxxx"><p class="mt-1 text-xs text-gray-400">7–20 karakter</p></div>
        <div class="flex flex-col sm:flex-row gap-3 pt-4 mt-6 border-t border-[var(--color-hairline-soft)] w-full">
            <a href="{{ route('employees.index') }}" class="btn-ghost w-full sm:flex-1 text-center order-2 sm:order-1 flex justify-center items-center"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
            <button type="submit" class="btn-primary w-full sm:flex-1 text-center order-1 sm:order-2">{{ __('messages.save') ?? 'Simpan' }}</button>
        </div>
    </form>
</div></div>
@endsection
