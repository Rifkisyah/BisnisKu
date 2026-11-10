@extends('layouts.app')
@section('page-title', __('messages.dashboard') . ' (Kasir)')
@section('content')

@php
    $diffDays = (int) $startDate->diffInDays($endDate) ?: 1;
@endphp

<!-- Date Filter Form -->
<form method="GET" class="mb-6 flex flex-col md:flex-row md:flex-wrap items-start md:items-end gap-4 card-feature p-4 md:p-6" id="dashboard-filter-form">
    <div class="flex flex-col sm:flex-row w-full md:w-auto gap-4">
        <div class="flex-1 w-full sm:w-auto">
            <label class="block type-caption-bold text-[var(--color-slate)] mb-1">{{ __('messages.start_date') }}</label>
            <input type="date" name="start_date" id="dash-start-date" value="{{ $startDate->format('Y-m-d') }}" class="input-field w-full" onchange="this.form.submit()">
        </div>
        <div class="flex-1 w-full sm:w-auto">
            <label class="block type-caption-bold text-[var(--color-slate)] mb-1">{{ __('messages.end_date') }}</label>
            <input type="date" name="end_date" id="dash-end-date" value="{{ $endDate->format('Y-m-d') }}" class="input-field w-full" onchange="this.form.submit()">
        </div>
    </div>
    
    <div class="flex gap-2 w-full md:w-auto md:ml-2 md:mr-2 md:border-r border-b md:border-b-0 border-[var(--color-hairline-soft)] pb-4 md:pb-0 md:pr-4">
        @php
            $activeFilter = null;
            if ($diffDays == 7) $activeFilter = 7;
            elseif ($diffDays == 365) $activeFilter = 365;
            elseif ($diffDays == 30 || !request()->has('start_date')) $activeFilter = 30;
        @endphp
        <button type="button" onclick="setDateRange(7)" class="flex-1 text-center justify-center {{ $activeFilter === 7 ? 'bg-black text-white hover:bg-black/80 rounded-full font-semibold shadow-sm border-2 border-transparent' : 'btn-ghost' }} !py-2 !px-3 text-xs sm:text-sm">{{ __('messages.week') }}</button>
        <button type="button" onclick="setDateRange(30)" class="flex-1 text-center justify-center {{ $activeFilter === 30 ? 'bg-black text-white hover:bg-black/80 rounded-full font-semibold shadow-sm border-2 border-transparent' : 'btn-ghost' }} !py-2 !px-3 text-xs sm:text-sm">{{ __('messages.month') }}</button>
        <button type="button" onclick="setDateRange(365)" class="flex-1 text-center justify-center {{ $activeFilter === 365 ? 'bg-black text-white hover:bg-black/80 rounded-full font-semibold shadow-sm border-2 border-transparent' : 'btn-ghost' }} !py-2 !px-3 text-xs sm:text-sm">{{ __('messages.year') }}</button>
    </div>
</form>

<!-- Metrics Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <!-- Transactions -->
    <div class="card-feature p-5">
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Total Transaksi</p>
        <p class="mt-3 type-heading-sm text-[var(--color-ink-deep)]">{{ $filteredTransactions }}</p>
        <div class="mt-2 h-1 w-12 rounded-full bg-[var(--color-primary)]"></div>
    </div>
    
    <!-- Revenue -->
    <div class="card-feature p-5">
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Total Pendapatan</p>
        <p class="mt-3 type-heading-sm text-[var(--color-ink-deep)]">Rp {{ number_format($filteredRevenue, 0, ',', '.') }}</p>
        <div class="mt-2 h-1 w-12 rounded-full bg-[var(--color-success)]"></div>
    </div>

    <!-- Debts -->
    <div class="card-feature p-5">
        <div class="flex justify-between items-start">
            <div>
                <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Hutang Belum Lunas</p>
                <p class="mt-3 type-heading-sm text-[var(--color-ink-deep)]">{{ $unpaidDebts }} <span class="text-sm text-gray-500 font-normal">Bon</span></p>
            </div>
        </div>
        <p class="mt-2 text-xs font-semibold text-[var(--color-critical)]">Rp {{ number_format($totalUnpaidDebtAmount, 0, ',', '.') }}</p>
        <div class="mt-2 h-1 w-12 rounded-full bg-[var(--color-critical)]"></div>
    </div>

    <!-- Active Repairs -->
    <div class="card-feature p-5">
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Perbaikan Aktif</p>
        <p class="mt-3 type-heading-sm text-[var(--color-ink-deep)]">{{ $activeRepairs }}</p>
        <div class="mt-2 h-1 w-12 rounded-full bg-[var(--color-attention)]"></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Recent Transactions -->
    <div class="lg:col-span-3 card-feature p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]">{{ __('messages.recent_transactions') }}</h3>
            <a href="{{ route('transactions.index') }}" class="text-sm font-semibold text-[var(--color-primary)] hover:underline">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="border-b border-[var(--color-hairline-soft)]">
                    <th class="pb-3 pr-4 text-left table-header">{{ __('messages.code') }}</th>
                    <th class="pb-3 pr-4 text-left table-header">{{ __('messages.date') }}</th>
                    <th class="pb-3 pr-4 text-left table-header">{{ __('messages.total') }}</th>
                    <th class="pb-3 text-left table-header">{{ __('messages.payment_method') }}</th>
                </tr></thead>
                <tbody>
                @forelse($recentTransactions as $t)
                <tr class="border-b border-[var(--color-hairline-soft)]/50">
                    <td class="py-3 pr-4 type-caption-bold text-[var(--color-primary)]">{{ $t->transaction_code }}</td>
                    <td class="py-3 pr-4 type-body-sm text-[var(--color-slate)]">{{ $t->transaction_date->format('d/m/Y H:i') }}</td>
                    <td class="py-3 pr-4 type-body-sm font-bold text-[var(--color-ink)]">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                    <td class="py-3"><span class="badge badge-neutral">{{ __('messages.'.$t->payment_method) }}</span></td>
                </tr>
                @empty<tr><td colspan="4" class="py-8 text-center type-body-sm text-[var(--color-stone)]">{{ __('messages.no_data') }}</td></tr>@endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function setDateRange(days) {
        const end = new Date();
        const start = new Date();
        if (days !== 1) {
            start.setDate(end.getDate() - days + 1);
        }
        
        // Pad single digits with zero
        const startMonth = String(start.getMonth() + 1).padStart(2, '0');
        const startDay = String(start.getDate()).padStart(2, '0');
        const endMonth = String(end.getMonth() + 1).padStart(2, '0');
        const endDay = String(end.getDate()).padStart(2, '0');

        document.getElementById('dash-start-date').value = `${start.getFullYear()}-${startMonth}-${startDay}`;
        document.getElementById('dash-end-date').value = `${end.getFullYear()}-${endMonth}-${endDay}`;
        document.getElementById('dashboard-filter-form').submit();
    }
</script>
@endsection
