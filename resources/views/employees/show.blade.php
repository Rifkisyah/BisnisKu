@extends('layouts.app')
@section('page-title', __('messages.detail') . ' ' . __('messages.employee'))
@section('content')
<div class="mx-auto max-w-2xl">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('employees.index') }}" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
    </div>
    <div class="card-feature p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="type-heading-sm text-[var(--color-ink-deep)]">{{ $employee->username }}</h3>
            <div class="flex gap-2">
                <span class="badge badge-info">{{ strtoupper($employee->role->display_name) }}</span>
                <span class="badge {{ $employee->status === 'active' ? 'badge-success' : 'badge-critical' }}">{{ $employee->status }}</span>
            </div>
        </div>
        <div class="w-32 h-32 rounded-full bg-[var(--color-fb-blue)]/10 text-[var(--color-fb-blue)] flex items-center justify-center text-4xl font-bold mx-auto mb-6 shadow-sm border-4 border-[var(--color-surface)]">
            {{ strtoupper(substr($employee->username, 0, 2)) }}
        </div>
        
        <div class="text-center mb-6">
            <h2 class="type-heading-md text-[var(--color-ink-deep)]">{{ $employee->username }}</h2>
            <p class="text-[var(--color-slate)] mt-1">{{ $employee->email }}</p>
        </div>
        
        <div class="mt-6 text-left border-t border-[var(--color-hairline-soft)] pt-6 space-y-4">
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-1 type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">{{ __('messages.phone') }}</div>
                <div class="col-span-2 type-body-sm text-[var(--color-ink)]">{{ $employee->phone ?? '-' }}</div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-1 type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">{{ __('messages.address') }}</div>
                <div class="col-span-2 type-body-sm text-[var(--color-ink)]">{{ $employee->address ?? '-' }}</div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-1 type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Terdaftar Sejak</div>
                <div class="col-span-2 type-body-sm text-[var(--color-ink)]">{{ $employee->created_at->format('d M Y') }}</div>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <a href="{{ route('employees.edit', $employee) }}" class="btn-primary !px-5 text-sm">{{ __('messages.edit') }}</a>
        </div>
    </div>
</div>
@endsection
