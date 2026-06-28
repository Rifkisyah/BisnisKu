@extends('layouts.app')
@section('page-title', __('messages.edit') . ' ' . __('messages.employee'))
@section('content')
<div class="mx-auto max-w-lg"><div class="card-feature p-8">
    <form method="POST" action="{{ route('employees.update', $employee) }}" class="space-y-5">@csrf @method('PUT')
        <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.name') }} *</label><input type="text" name="username" value="{{ old('username', $employee->username) }}" required minlength="3" maxlength="60" class="input-field"><p class="mt-1 text-xs text-gray-400">3–60 karakter</p></div>
        <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.email') }} *</label><input type="email" name="email" value="{{ old('email', $employee->email) }}" required maxlength="150" class="input-field"></div>
        <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.password') }}</label><input type="password" name="password" minlength="8" class="input-field" placeholder="Kosongkan jika tidak diubah"></div>
        <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.role') }} *</label>
            <select name="role_id" required class="input-field">@foreach($roles as $role)<option value="{{ $role->id }}" {{ $employee->role_id == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>@endforeach</select></div>
        <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.contact') }}</label><input type="text" name="contact" value="{{ old('contact', $employee->contact) }}" minlength="7" maxlength="20" class="input-field" placeholder="08xxxxxxxxxx"><p class="mt-1 text-xs text-gray-400">7–20 karakter</p></div>
        <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.status') }}</label>
            <select name="status" class="input-field"><option value="active" {{ $employee->status === 'active' ? 'selected' : '' }}>Active</option><option value="inactive" {{ $employee->status === 'inactive' ? 'selected' : '' }}>Inactive</option></select></div>
        <div class="flex flex-col sm:flex-row justify-between gap-3 pt-4 mt-6 border-t border-[var(--color-hairline-soft)] w-full">
    <a href="javascript:void(0)" onclick="if(window.history.length > 2) { window.history.back(); } else { window.location.href = '{{ route('dashboard') }}'; }" class="btn-ghost w-full sm:w-auto text-center order-2 sm:order-1">{{ __('messages.back') ?? 'Kembali' }}</a>
    <button type="submit" class="btn-primary w-full sm:w-auto text-center order-1 sm:order-2">{{ __('messages.save') ?? 'Simpan' }}</button>
</div>
    </form>
</div></div>
@endsection
