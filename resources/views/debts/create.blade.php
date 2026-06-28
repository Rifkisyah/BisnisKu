@extends('layouts.app')
@section('page-title', __('messages.add') . ' ' . __('messages.debt'))
@section('content')
<div class="mx-auto max-w-xl">
    
    <div class="bg-[var(--color-canvas)] rounded-[var(--radius-xxxl)] p-8">
        <form method="POST" action="{{ route('debts.store') }}" class="space-y-6">
            @csrf
            <div>
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1.5">{{ __('messages.debtor_name') }} *</label>
                <input type="text" name="debtor_name" value="{{ old('debtor_name') }}" required minlength="3" maxlength="100" class="input-field"><p class="mt-1 text-xs text-gray-400">3–100 karakter</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block type-caption-bold text-[var(--color-slate)] mb-1.5">{{ __('messages.contact') }}</label>
                    <input type="text" name="debtor_contact" value="{{ old('debtor_contact') }}" minlength="7" maxlength="20" class="input-field" placeholder="08xxxxxxxxxx"><p class="mt-1 text-xs text-gray-400">7–20 karakter</p>
                </div>
                <div>
                    <label class="block type-caption-bold text-[var(--color-slate)] mb-1.5">{{ __('messages.total_amount') }} *</label>
                    <input type="number" name="total_amount" value="{{ old('total_amount') }}" required min="1" class="input-field">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block type-caption-bold text-[var(--color-slate)] mb-1.5">{{ __('messages.date') }} *</label>
                    <input type="date" name="debt_date" value="{{ old('debt_date', date('Y-m-d')) }}" required class="input-field">
                </div>
                <div>
                    <label class="block type-caption-bold text-[var(--color-slate)] mb-1.5">{{ __('messages.due_date') }}</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}" class="input-field">
                </div>
            </div>
            
            <div>
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1.5">{{ __('messages.address') }}</label>
                <textarea name="debtor_address" rows="2" maxlength="300" class="input-field resize-none">{{ old('debtor_address') }}</textarea><p class="mt-1 text-xs text-gray-400">Maks. 300 karakter</p>
            </div>
            
            <div>
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1.5">{{ __('messages.notes') }}</label>
                <textarea name="notes" rows="2" maxlength="500" class="input-field resize-none">{{ old('notes') }}</textarea><p class="mt-1 text-xs text-gray-400">Maks. 500 karakter</p>
            </div>
            
            <div class="flex gap-3 pt-4 border-t border-[var(--color-hairline-soft)] mt-6 justify-center">
                <a href="javascript:void(0)" onclick="if(window.history.length > 2) { window.history.back(); } else { window.location.href = '{{ route('dashboard') }}'; }" class="btn-ghost !px-8"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
                <button type="submit" class="btn-primary !px-8">{{ __('messages.save') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
