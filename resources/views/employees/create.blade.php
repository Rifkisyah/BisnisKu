@extends('layouts.app')
@section('page-title', __('messages.add') . ' ' . __('messages.employee'))
@section('content')
<div class="mx-auto max-w-lg"><div class="card-feature p-8">
    <form method="POST" action="{{ route('employees.store') }}" class="space-y-5">@csrf
        <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.name') }} *</label><input type="text" name="username" value="{{ old('username') }}" required minlength="3" maxlength="60" class="input-field"><p class="mt-1 text-xs text-gray-400">3–60 karakter</p></div>
        <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.email') }} *</label><input type="email" name="email" value="{{ old('email') }}" required maxlength="150" class="input-field"></div>
        <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.password') }} *</label><input type="password" name="password" required minlength="8" class="input-field"></div>
        <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">Konfirmasi Password *</label><input type="password" name="password_confirmation" required minlength="8" class="input-field"></div>
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
