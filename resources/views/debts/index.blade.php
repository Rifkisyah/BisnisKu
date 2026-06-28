@extends('layouts.app')
@section('page-title', __('messages.debts'))
@section('content')
<div x-data="{ showFilter: false }" class="mb-6 bg-[var(--color-canvas)] rounded-[var(--radius-xl)] p-5 border border-[var(--color-hairline-soft)]">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 pb-4 border-b border-[var(--color-hairline-soft)]">
        <h2 class="type-subtitle-lg text-[var(--color-ink-deep)] hidden sm:block">Aksi & Cari</h2>
        
        <!-- Mobile Toggle & Icons -->
        <div class="flex items-center justify-between w-full sm:hidden">
            <button @click="showFilter = !showFilter" class="btn-ghost !py-2 !px-3 flex items-center" title="Cari">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg> Cari
            </button>
            <div class="flex gap-2">
                <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn-ghost !p-2 rounded-full" target="_blank" title="Download PDF"><svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></a>
                <button type="button" onclick="window.print()" class="btn-ghost !p-2 rounded-full" title="Print"><svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg></button>
            </div>
        </div>

        <!-- Add Button: Large on Mobile -->
        <a href="{{ route('debts.create') }}" class="btn-primary w-full sm:w-auto !py-3 sm:!py-2 !px-4 whitespace-nowrap text-center text-lg sm:text-sm order-first sm:order-last mb-2 sm:mb-0">
            <svg class="h-5 w-5 sm:h-4 sm:w-4 mr-2 sm:mr-1.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="inline-block">{{ __('messages.add') }}</span>
        </a>

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
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode/Debitur..." class="input-field w-full">
        </div>
        <div class="w-full sm:w-48">
            <label class="block type-caption-bold text-[var(--color-slate)] mb-1">Status</label>
            <select name="status" class="input-field w-full">
                <option value="">Semua Status</option>
                <option value="unpaid" @selected(request('status') === 'unpaid')>Belum Lunas</option>
                <option value="partial" @selected(request('status') === 'partial')>Bayar Sebagian</option>
                <option value="paid" @selected(request('status') === 'paid')>Lunas</option>
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
        <th class="px-5 py-3 text-left table-header">{{ __('messages.name') }}</th>
        <th class="px-5 py-3 text-left table-header">{{ __('messages.total') }}</th><th class="px-5 py-3 text-left table-header">{{ __('messages.remaining') }}</th>
        <th class="px-5 py-3 text-left table-header">{{ __('messages.status') }}</th><th class="px-5 py-3 text-left table-header">{{ __('messages.actions') }}</th>
    </tr></thead><tbody>
    @forelse($debts as $d)
    <tr class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] transition-colors duration-150">
        <td class="px-5 py-3 type-body-sm font-medium text-[var(--color-ink)]">{{ $d->debtor_name }}</td>
        <td class="px-5 py-3 type-body-sm text-[var(--color-charcoal)]">Rp {{ number_format($d->total_amount, 0, ',', '.') }}</td>
        <td class="px-5 py-3 type-body-sm font-bold text-[var(--color-critical)]">Rp {{ number_format($d->remaining_amount, 0, ',', '.') }}</td>
        <td class="px-5 py-3"><span class="badge {{ $d->status === 'paid' ? 'badge-success' : ($d->status === 'partial' ? 'badge-attention' : 'badge-critical') }}">{{ __('messages.'.$d->status) }}</span></td>
        <td class="px-5 py-3"><div class="flex items-center gap-1">
                <a href="{{ route('debts.show', $d) }}" class="btn-ghost !py-1.5 !px-3 !text-xs">{{ __('messages.detail') }}</a>
                <button @click="$dispatch('open-delete-modal', { url: '{{ route('debts.destroy', $d) }}' })" class="btn-ghost !py-1.5 !px-3 !text-xs !text-[var(--color-critical)] !border-[var(--color-critical)]/20">{{ __('messages.delete') }}</button>
            </div></td>
    </tr>
    @empty<tr><td colspan="6" class="px-5 py-12 text-center type-body-md text-[var(--color-stone)]">{{ __('messages.no_data') }}</td></tr>@endforelse
    </tbody></table></div>
    <div class="border-t border-[var(--color-hairline-soft)] px-5 py-3">{{ $debts->links() }}</div>
</div>
@endsection


