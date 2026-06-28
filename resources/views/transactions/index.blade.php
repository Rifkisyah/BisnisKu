@extends('layouts.app')
@section('page-title', __('messages.transactions'))
@section('content')
<div x-data="{ showFilter: false }" class="mb-6 bg-[var(--color-canvas)] rounded-[var(--radius-xl)] p-5 border border-[var(--color-hairline-soft)]">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 pb-4 border-b border-[var(--color-hairline-soft)]">
        <h2 class="type-subtitle-lg text-[var(--color-ink-deep)] hidden sm:block">Aksi & Cari</h2>
        
        <!-- Mobile Toggle & Icons -->
        <div class="flex items-center justify-between w-full sm:hidden">
            <button @click="showFilter = !showFilter" class="btn-ghost !py-2 !px-3 flex items-center" title="{{ __('messages.search') }}">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg> Cari
            </button>
            <div class="flex gap-2">
                <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn-ghost !p-2 rounded-full" target="_blank" title="Download PDF"><svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></a>
                <button type="button" onclick="window.print()" class="btn-ghost !p-2 rounded-full" title="Print"><svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg></button>
            </div>
        </div>

        <!-- Desktop Actions -->
        <div class="hidden sm:flex flex-wrap items-center gap-2">
            <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn-ghost !py-2 !px-3" target="_blank" title="Download PDF">
                <svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> <span>PDF</span>
            </a>
            <button type="button" onclick="window.print()" class="btn-ghost !py-2 !px-3" title="Print">
                <svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg> <span>Print</span>
            </button>
        </div>
    </div>
    
    <form method="GET" :class="{ 'hidden sm:flex': !showFilter, 'flex': showFilter }" class="flex-col sm:flex-row flex-wrap gap-3 items-end mt-4 sm:mt-0 w-full">
        <div class="w-full sm:flex-1">
            <label class="block type-caption-bold text-[var(--color-slate)] mb-1">Pencarian</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode Transaksi..." class="input-field w-full">
        </div>
        <div class="w-full sm:w-48">
            <label class="block type-caption-bold text-[var(--color-slate)] mb-1">Metode Bayar</label>
            <select name="payment_method" class="input-field w-full">
                <option value="">Semua Metode</option>
                @foreach(['cash','qris'] as $pm)
                <option value="{{ $pm }}" {{ request('payment_method') === $pm ? 'selected' : '' }}>{{ strtoupper($pm) }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full sm:w-48">
            <label class="block type-caption-bold text-[var(--color-slate)] mb-1">{{ __('messages.start_date') }}</label>
            <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="input-field w-full">
        </div>
        <div class="w-full sm:w-48">
            <label class="block type-caption-bold text-[var(--color-slate)] mb-1">{{ __('messages.end_date') }}</label>
            <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="input-field w-full">
        </div>
        <div class="w-full sm:w-auto">
            <button type="submit" class="btn-primary w-full sm:w-auto px-6 sm:w-auto px-6 !py-2.5">{{ __('messages.search') }}</button>
        </div>
    </form>
</div>
<div class="card overflow-hidden"><div class="overflow-x-auto"><table class="w-full">
    <thead><tr class="border-b border-[var(--color-hairline-soft)] bg-[var(--color-surface-soft)]">
        <th class="px-5 py-3 text-left table-header">{{ __('messages.code') }}</th><th class="px-5 py-3 text-left table-header">{{ __('messages.date') }}</th>
        <th class="px-5 py-3 text-left table-header">{{ __('messages.customer_name') }}</th><th class="px-5 py-3 text-left table-header">{{ __('messages.total') }}</th>
        <th class="px-5 py-3 text-left table-header">{{ __('messages.payment_method') }}</th><th class="px-5 py-3 text-left table-header">{{ __('messages.actions') }}</th>
    </tr></thead><tbody>
    @forelse($transactions as $t)
    <tr class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] transition-colors duration-150">
        <td class="px-5 py-3 type-caption-bold text-[var(--color-primary)]">{{ $t->transaction_code }}</td>
        <td class="px-5 py-3 type-body-sm text-[var(--color-slate)]">{{ $t->transaction_date->format('d/m/Y H:i') }}</td>
        <td class="px-5 py-3 type-body-sm text-[var(--color-ink)]">{{ $t->customer_name ?: '-' }}</td>
        <td class="px-5 py-3 type-body-sm font-bold text-[var(--color-ink)]">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
        <td class="px-5 py-3"><span class="badge badge-neutral">{{ __('messages.'.$t->payment_method) }}</span></td>
        <td class="px-5 py-3 text-right"><div class="flex items-center justify-end gap-1">
            <a href="{{ route('transactions.show', $t) }}" class="btn-ghost !py-1.5 !px-3 !text-xs">{{ __('messages.detail') }}</a>
            @if(auth()->user()->isOwner())
            <button @click="$dispatch('open-delete-modal', { url: '{{ route('transactions.destroy', $t) }}' })" class="btn-ghost !py-1.5 !px-3 !text-xs !text-[var(--color-critical)] !border-[var(--color-critical)]/20">{{ __('messages.delete') }}</button>
            @endif
        </div></td>
    </tr>
    @empty<tr><td colspan="6" class="px-5 py-12 text-center type-body-md text-[var(--color-stone)]">{{ __('messages.no_data') }}</td></tr>@endforelse
    </tbody></table></div>
    <div class="border-t border-[var(--color-hairline-soft)] px-5 py-3">{{ $transactions->links() }}</div>
</div>
@endsection


