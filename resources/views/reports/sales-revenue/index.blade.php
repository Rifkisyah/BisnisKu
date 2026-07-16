@extends('layouts.app')
@section('page-title', __('messages.sales_and_revenue_report'))
@section('content')

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- HEADER & FILTER                                         --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('reports.index') }}" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
</div>

<div class="mb-6 card-feature p-5">
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4">
        <div>
            <h1 class="type-heading-sm !text-xl sm:!text-2xl text-[var(--color-ink-deep)] flex items-center gap-2">
                <svg class="w-6 h-6 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Laporan Penjualan & Pendapatan
            </h1>
            <p class="type-caption text-[var(--color-slate)] mt-2">Mencakup Transaksi Penjualan · Periode {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} <b>-</b> {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
        
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <form method="GET" class="flex flex-col md:flex-row md:items-end gap-3 w-full" id="sales-filter-form">
                <div class="flex flex-col w-full md:w-auto">
                    <label for="sales-start-date" class="type-caption-bold text-[var(--color-slate)] mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="sales-start-date" value="{{ $startDate }}" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                </div>
                <div class="flex flex-col w-full md:w-auto">
                    <label for="sales-end-date" class="type-caption-bold text-[var(--color-slate)] mb-1">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="sales-end-date" value="{{ $endDate }}" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                </div>

                <input type="hidden" name="limit" id="limit-input" value="{{ $limit }}">
                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto mt-2 md:mt-0 md:border-r border-[var(--color-hairline-soft)] md:pr-3">
                @php
                    $diffDays = \Carbon\Carbon::parse($startDate)->startOfDay()->diffInDays(\Carbon\Carbon::parse($endDate)->startOfDay());
                    $activeFilter = null;
                    if ($diffDays == 7) $activeFilter = 7;
                    elseif ($diffDays == 365) $activeFilter = 365;
                    elseif ($diffDays == 30 || !request()->has('start_date')) $activeFilter = 30;
                @endphp
                <button type="button" onclick="setDateRange(7)" class="flex-1 text-center justify-center {{ $activeFilter === 7 ? 'bg-black text-white hover:bg-black/80 rounded-full font-semibold shadow-sm border-2 border-transparent' : 'btn-ghost' }} !py-2 !px-3 text-xs">{{ __('messages.week') }}</button>
                <button type="button" onclick="setDateRange(30)" class="flex-1 text-center justify-center {{ $activeFilter === 30 ? 'bg-black text-white hover:bg-black/80 rounded-full font-semibold shadow-sm border-2 border-transparent' : 'btn-ghost' }} !py-2 !px-3 text-xs">{{ __('messages.month') }}</button>
                <button type="button" onclick="setDateRange(365)" class="flex-1 text-center justify-center {{ $activeFilter === 365 ? 'bg-black text-white hover:bg-black/80 rounded-full font-semibold shadow-sm border-2 border-transparent' : 'btn-ghost' }} !py-2 !px-3 text-xs">{{ __('messages.year') }}</button>
                </div>

                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto mt-2 md:mt-0 md:ml-auto">
                <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="flex-1 justify-center btn-ghost !py-2 !px-3 text-xs flex items-center gap-1 text-[var(--color-critical)] border-[var(--color-critical)]/20 hover:bg-[var(--color-critical)]/10 rounded-[var(--radius-md)]" title="PDF">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    PDF
                </a>
                <button type="button" onclick="window.print()" class="flex-1 justify-center btn-ghost !py-2 !px-3 text-xs flex items-center gap-1 rounded-[var(--radius-md)]" title="Cetak">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak
                </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- KPI CARDS                                               --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card-feature p-5 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-500 to-blue-300 rounded-t-[var(--radius-xxxl)]"></div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Total Penjualan</p>
        <p class="mt-2 type-heading-sm text-[var(--color-ink-deep)]">Rp {{ number_format($salesRevenue, 0, ',', '.') }}</p>
        <p class="mt-1 type-caption text-[var(--color-stone)]">Total Penjualan</p>
    </div>
    <div class="card-feature p-5 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-violet-500 to-violet-300 rounded-t-[var(--radius-xxxl)]"></div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Total Transaksi</p>
        <p class="mt-2 type-heading-sm text-[var(--color-ink-deep)]">{{ number_format($totalTransactions, 0, ',', '.') }}</p>
        <p class="mt-1 type-caption text-[var(--color-stone)]">Transaksi selesai</p>
    </div>
    <div class="card-feature p-5 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-teal-500 to-teal-300 rounded-t-[var(--radius-xxxl)]"></div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Laba Kotor</p>
        <p class="mt-2 type-heading-sm text-[var(--color-ink-deep)]">Rp {{ number_format($grossProfit, 0, ',', '.') }}</p>
        <p class="mt-1 type-caption text-[var(--color-stone)]">Total Penjualan - Modal</p>
    </div>
    <div class="card-feature p-5 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-emerald-500 to-emerald-300 rounded-t-[var(--radius-xxxl)]"></div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Laba Bersih</p>
        <p class="mt-2 type-heading-sm text-[var(--color-success)]">Rp {{ number_format($netProfit, 0, ',', '.') }}</p>
        <p class="mt-1 type-caption text-[var(--color-stone)]">Laba Kotor - Diskon - biaya operasional</p>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════ --}}
{{-- 1. DAFTAR TRANSAKSI                                     --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="card-feature mb-6 flex flex-col min-h-[500px]" x-data="{ view: 'chart', chartType: 'line', metric: 'revenue' }" x-init="$watch('chartType', val => updateSalesTrendChart(val, metric)); $watch('metric', val => updateSalesTrendChart(chartType, val))">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-5 border-b border-[var(--color-hairline-soft)]">
        <div>
            <h2 class="type-subtitle-lg text-[var(--color-ink-deep)]">Daftar Transaksi</h2>
            <p class="type-caption text-[var(--color-slate)] mt-1">Data transaksi dan tren penjualan harian</p>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
            <span class="type-caption text-[var(--color-slate)]">{{ __('messages.show') }}:</span>
            <select class="input-field !py-1 !px-2 w-full sm:!w-auto text-sm" onchange="updateLimit(this.value)">
                <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10 baris</option>
                <option value="30" {{ $limit == 30 ? 'selected' : '' }}>30 baris</option>
                <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50 baris</option>
                <option value="all" {{ $limit === 'all' ? 'selected' : '' }}>Semua</option>
            </select>
            <div class="w-px h-5 bg-[var(--color-hairline-soft)] mx-1 hidden md:block"></div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button @click="view = 'chart'" :class="view === 'chart' ? 'pill-tab-active' : ''" class="pill-tab flex-1 sm:flex-none">Grafik</button>
                <button @click="view = 'table'" :class="view === 'table' ? 'pill-tab-active' : ''" class="pill-tab flex-1 sm:flex-none">Tabel</button>
            </div>
        </div>
    </div>
    
    <div x-show="view === 'table'" x-cloak class="flex-1 flex flex-col">
        <div class="overflow-x-auto flex-1">
            <table class="w-full">
                <thead><tr class="bg-[var(--color-surface-soft)]">
                    <th class="px-5 py-3 text-left table-header">Kode</th>
                    <th class="px-5 py-3 text-left table-header">Tanggal</th>
                    <th class="px-5 py-3 text-left table-header">Kasir</th>
                    <th class="px-5 py-3 text-left table-header">Metode</th>
                    <th class="px-5 py-3 text-right table-header">Total</th>
                </tr></thead>
                <tbody>
                @forelse($transactions as $t)
                <tr class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] transition-colors cursor-pointer" onclick="window.location='{{ route('transactions.show', $t) }}'">
                    <td class="px-5 py-3 type-caption-bold text-[var(--color-primary)]">
                        <a href="{{ route('transactions.show', $t) }}" class="hover:underline">{{ $t->transaction_code }}</a>
                    </td>
                    <td class="px-5 py-3 type-body-sm text-[var(--color-slate)]">{{ $t->transaction_date->format('d M Y H:i') }}</td>
                    <td class="px-5 py-3 type-body-sm text-[var(--color-ink)]">{{ $t->cashier->username ?? '-' }}</td>
                    <td class="px-5 py-3"><span class="badge badge-neutral uppercase">{{ $t->payment_method }}</span></td>
                    <td class="px-5 py-3 type-body-sm-bold text-right text-[var(--color-ink-deep)]">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-10 text-center type-body-sm text-[var(--color-slate)]">Tidak ada transaksi di rentang tanggal ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[var(--color-hairline-soft)] text-center mt-auto">
            <a href="{{ route('reports.sales_revenue.transactions', request()->all()) }}" class="bg-[var(--color-ink)] text-white hover:bg-[var(--color-ink-deep)] transition-colors rounded-[var(--radius-full)] font-medium type-body-sm !py-2 !px-6 inline-flex items-center gap-2">
                Lihat Keseluruhan Data
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </div>

    <div x-show="view === 'chart'" class="p-6 flex-1 flex flex-col">
        <div class="flex gap-4 mb-4">
            <select x-model="chartType" class="input-field w-full md:w-auto text-xs !py-1.5">
                <option value="line">Line Chart</option>
                <option value="bar">Bar Chart</option>
                <option value="doughnut">Doughnut Chart</option>
                <option value="pie">Pie Chart</option>
            </select>
            <select x-model="metric" class="input-field w-full md:w-auto text-xs !py-1.5">
                <option value="revenue">Pendapatan Harian</option>
                <option value="payment">Metode Pembayaran</option>
                <option value="cashier">Transaksi per Kasir</option>
            </select>
        </div>
        <div class="flex-1 min-h-[300px] relative">
            @if(count($salesTrendDaily) > 0)
            <canvas id="salesTrendChart"></canvas>
            @else
            <div class="absolute inset-0 flex items-center justify-center bg-[var(--color-surface-soft)] rounded-lg border border-dashed border-[var(--color-hairline-soft)]">
                <p class="type-body-sm text-[var(--color-slate)]">{{ __('messages.no_data_to_display') }}</p>
            </div>
            @endif
        </div>
    </div>
</div>



{{-- ═══════════════════════════════════════════════════════ --}}
{{-- DEBT ANALYSIS SECTION                                   --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="mb-6 card-feature p-5">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="type-heading-sm text-[var(--color-ink-deep)] flex items-center gap-2">
                <svg class="w-6 h-6 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Analisis Hutang
            </h2>
            <p class="type-caption text-[var(--color-slate)] mt-1">Ringkasan seluruh piutang dan kewajiban yang tercatat</p>
        </div>
    </div>
</div>

{{-- Debt KPI --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card-feature p-5 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-amber-500 to-amber-300 rounded-t-[var(--radius-xxxl)]"></div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Hutang Aktif</p>
        <p class="mt-2 type-heading-sm text-[var(--color-ink-deep)]">{{ number_format($totalDebts, 0, ',', '.') }}</p>
        <p class="mt-1 type-caption text-[var(--color-stone)]">Belum lunas</p>
    </div>
    <div class="card-feature p-5 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-red-500 to-red-300 rounded-t-[var(--radius-xxxl)]"></div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Total Piutang Aktif</p>
        <p class="mt-2 type-heading-sm text-[var(--color-critical)]">Rp {{ number_format($totalDebtAmt, 0, ',', '.') }}</p>
        <p class="mt-1 type-caption text-[var(--color-stone)]">Sisa belum dibayar</p>
    </div>
    <div class="card-feature p-5 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-green-500 to-green-300 rounded-t-[var(--radius-xxxl)]"></div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Total Terlunasi</p>
        <p class="mt-2 type-heading-sm text-[var(--color-success)]">Rp {{ number_format($paidDebtAmt, 0, ',', '.') }}</p>
        <p class="mt-1 type-caption text-[var(--color-stone)]">Akumulasi pembayaran</p>
    </div>
    <div class="card-feature p-5 relative overflow-hidden {{ $overdueDebts > 0 ? 'border-2 border-[var(--color-critical)]/30 bg-[var(--color-critical)]/5' : '' }}">
        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-rose-500 to-rose-300 rounded-t-[var(--radius-xxxl)]"></div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Jatuh Tempo</p>
        <p class="mt-2 type-heading-sm {{ $overdueDebts > 0 ? 'text-[var(--color-critical)]' : 'text-[var(--color-ink-deep)]' }}">{{ number_format($overdueDebts, 0, ',', '.') }}</p>
        <p class="mt-1 type-caption text-[var(--color-stone)]">Hutang melewati jatuh tempo</p>
    </div>
</div>

{{-- Debt Table + Chart Toggle --}}
<div class="card-feature" x-data="{ debtTab: 'chart', chartType: 'bar', metric: 'amount' }" x-init="
    $watch('chartType', val => updateDebtChart(val, metric));
    $watch('metric', val => updateDebtChart(chartType, val));
">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-5 border-b border-[var(--color-hairline-soft)]">
        <div>
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]">Detail Hutang</h3>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
            <span class="type-caption text-[var(--color-slate)]">{{ __('messages.show') }}:</span>
            <select class="input-field !py-1 !px-2 w-full sm:!w-auto text-sm" onchange="updateLimit(this.value)">
                <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10 baris</option>
                <option value="30" {{ $limit == 30 ? 'selected' : '' }}>30 baris</option>
                <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50 baris</option>
                <option value="all" {{ $limit === 'all' ? 'selected' : '' }}>Semua</option>
            </select>
            <div class="w-px h-5 bg-[var(--color-hairline-soft)] mx-1 hidden md:block"></div>
            <div class="flex gap-2">
                <button @click="debtTab = 'chart'" :class="debtTab === 'chart' ? 'pill-tab-active' : ''" class="pill-tab flex-1 sm:flex-none">Grafik</button>
                <button @click="debtTab = 'table'" :class="debtTab === 'table' ? 'pill-tab-active' : ''" class="pill-tab flex-1 sm:flex-none">Tabel</button>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div x-show="debtTab === 'table'" x-cloak class="overflow-x-auto">
        <table class="w-full">
            <thead><tr class="bg-[var(--color-surface-soft)]">
                <th class="px-5 py-3 text-left table-header">Kode</th>
                <th class="px-5 py-3 text-left table-header">Nama Debitur</th>
                <th class="px-5 py-3 text-left table-header">Tgl Hutang</th>
                <th class="px-5 py-3 text-left table-header">Jatuh Tempo</th>
                <th class="px-5 py-3 text-right table-header">Total</th>
                <th class="px-5 py-3 text-right table-header">Sisa</th>
                <th class="px-5 py-3 text-center table-header">Status</th>
            </tr></thead>
            <tbody>
            @php
                $displayedDebts = $limit === 'all' ? $activeDebts : $activeDebts->take((int)$limit);
            @endphp
            @forelse($displayedDebts as $d)
            <tr class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] transition-colors cursor-pointer {{ $d->isOverdue() ? 'bg-[var(--color-critical)]/3' : '' }}" onclick="window.location='{{ route('debts.show', $d) }}'">
                <td class="px-5 py-3 type-caption-bold text-[var(--color-primary)]">
                    <a href="{{ route('debts.show', $d) }}" class="hover:underline">{{ $d->debt_code }}</a>
                </td>
                <td class="px-5 py-3 type-body-sm font-medium text-[var(--color-ink)]">{{ $d->debtor_name }}</td>
                <td class="px-5 py-3 type-body-sm text-[var(--color-slate)]">{{ $d->debt_date->format('d M Y') }}</td>
                <td class="px-5 py-3 type-body-sm {{ $d->isOverdue() ? 'text-[var(--color-critical)] font-bold' : 'text-[var(--color-slate)]' }}">
                    {{ $d->due_date ? $d->due_date->format('d M Y') : '-' }}
                    @if($d->isOverdue()) <span class="badge badge-critical ml-1">Overdue</span> @endif
                </td>
                <td class="px-5 py-3 type-body-sm text-right text-[var(--color-ink)]">Rp {{ number_format($d->total_amount, 0, ',', '.') }}</td>
                <td class="px-5 py-3 type-body-sm-bold text-right text-[var(--color-critical)]">Rp {{ number_format($d->remaining_amount, 0, ',', '.') }}</td>
                <td class="px-5 py-3 text-center">
                    @php
                        $badge = match($d->status) {
                            'paid'    => 'badge-success',
                            'partial' => 'badge-attention',
                            default   => 'badge-critical',
                        };
                        $label = match($d->status) {
                            'paid'    => 'Lunas',
                            'partial' => 'Sebagian',
                            default   => 'Belum Bayar',
                        };
                    @endphp
                    <span class="badge {{ $badge }}">{{ $label }}</span>
                </td>
            </tr>
            @empty
            @endforelse
            </tbody>
        </table>

        @if($limit !== 'all')
        <div class="p-4 border-t border-[var(--color-hairline-soft)] text-center mt-auto">
            <a href="{{ route('reports.sales_revenue.debts', request()->all()) }}" class="bg-[var(--color-ink)] text-white hover:bg-[var(--color-ink-deep)] transition-colors rounded-[var(--radius-full)] font-medium type-body-sm !py-2 !px-6 inline-flex items-center gap-2">
                Lihat Keseluruhan Data
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
        @endif
    </div>

    {{-- Debt Chart --}}
    <div x-show="debtTab === 'chart'" class="p-6">
        <div class="flex gap-4 mb-4">
            <select x-model="chartType" class="input-field w-full md:w-auto text-xs !py-1.5">
                <option value="bar">Bar Chart</option>
                <option value="doughnut">Doughnut Chart</option>
                <option value="pie">Pie Chart</option>
            </select>
            <select x-model="metric" class="input-field w-full md:w-auto text-xs !py-1.5">
                <option value="amount">Trend Nominal Hutang (Rp)</option>
                <option value="status">Status Hutang (Jumlah Data)</option>
            </select>
        </div>
        <div class="flex-1 min-h-[300px] relative">
            @if(count($debtTrend) > 0)
            <canvas id="debtTrendChart"></canvas>
            @else
            <div class="absolute inset-0 flex items-center justify-center bg-[var(--color-surface-soft)] rounded-lg border border-dashed border-[var(--color-hairline-soft)]">
                <p class="type-body-sm text-[var(--color-slate)]">{{ __('messages.no_data_to_display') }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
window.appCharts = {};
window.rawChartData = {
    daily: @json($salesTrendDaily),
    payments: @json($paymentBreakdown),
    cashiers: @json($cashierBreakdown),
    debts: @json($debtTrend),
    debtStatus: {
        unpaid: {{ $debts->where('status', 'unpaid')->count() }},
        partial: {{ $debts->where('status', 'partial')->count() }},
        paid: {{ $debts->where('status', 'paid')->count() }}
    }
};

const COLORS = ['#10B981','#3B82F6','#8B5CF6','#F59E0B','#EF4444','#0EA5E9','#F97316','#EC4899'];

window.updateSalesTrendChart = function(type, metric) {
    const ctx = document.getElementById('salesTrendChart');
    if (!ctx) return;
    if (window.appCharts.salesTrend) window.appCharts.salesTrend.destroy();
    
    const isRadial = type === 'doughnut' || type === 'pie';
    
    let ds = {};
    if (metric === 'revenue') {
        ds = {
            labels: window.rawChartData.daily.map(r => r.date || r.month || r.yw),
            datasets: [{
                label: 'Pendapatan Harian',
                data: window.rawChartData.daily.map(r => parseFloat(r.total)),
                borderColor: isRadial ? '#fff' : '#0064E0',
                backgroundColor: isRadial ? COLORS : 'rgba(0,100,224,0.1)',
                borderWidth: 2,
                pointBackgroundColor: '#0064E0',
                pointRadius: window.rawChartData.daily.length > 60 ? 0 : 3,
                fill: true,
                tension: 0.4
            }]
        };
    } else if (metric === 'payment') {
        ds = {
            labels: window.rawChartData.payments.map(p => p.payment_method.toUpperCase()),
            datasets: [{
                label: 'Metode Pembayaran (Transaksi)',
                data: window.rawChartData.payments.map(p => parseInt(p.count)),
                backgroundColor: isRadial || type === 'bar' ? COLORS : 'rgba(16,185,129,0.1)',
                borderColor: isRadial ? '#fff' : '#10B981',
                borderWidth: 2,
                borderRadius: isRadial ? 0 : 6,
                fill: true,
                tension: 0.4
            }]
        };
    } else if (metric === 'cashier') {
        ds = {
            labels: window.rawChartData.cashiers.map(c => c.cashier_name || 'Tidak Diketahui'),
            datasets: [{
                label: 'Transaksi per Kasir',
                data: window.rawChartData.cashiers.map(c => parseInt(c.count)),
                backgroundColor: isRadial || type === 'bar' ? COLORS : 'rgba(139,92,246,0.1)',
                borderColor: isRadial ? '#fff' : '#8B5CF6',
                borderWidth: 2,
                borderRadius: isRadial ? 0 : 6,
                fill: true,
                tension: 0.4
            }]
        };
    }
    
    const options = {
        responsive: true, maintainAspectRatio: false, cutout: type === 'doughnut' ? '60%' : 0,
        plugins: {
            legend: { display: isRadial, position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12 } },
            tooltip: { callbacks: { label: ctx => `${ctx.label || ctx.dataset.label}: ${metric === 'revenue' ? 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw) : ctx.raw + ' transaksi'}` } }
        },
        scales: isRadial ? { x: { display: false }, y: { display: false } } : {
            y: { beginAtZero: true, grid: { color: 'rgba(148,163,184,0.12)' }, ticks: { color: '#94A3B8', font: { size: 11 } } },
            x: { grid: { display: false }, ticks: { color: '#94A3B8', font: { size: 10 }, maxTicksLimit: metric === 'revenue' ? 14 : undefined } }
        }
    };
    
    window.appCharts.salesTrend = new Chart(ctx, { type: type, data: ds, options: options });
};

window.updateTopProductsChart = function(type, metric) {
    const ctx = document.getElementById('topProductsChart');
    if (!ctx || !window.rawChartData.topProducts.length) return;
    if (window.appCharts.topProducts) window.appCharts.topProducts.destroy();
    
    const isRevenue = metric === 'revenue';
    const isRadial = type === 'doughnut' || type === 'pie';
    
    const ds = {
        labels: window.rawChartData.topProducts.map(p => p.name),
        datasets: [{
            label: isRevenue ? 'Pendapatan' : 'Qty Terjual',
            data: window.rawChartData.topProducts.map(p => isRevenue ? parseFloat(p.total_revenue) : parseInt(p.total_qty)),
            backgroundColor: COLORS,
            borderRadius: isRadial ? 0 : 6,
            borderWidth: isRadial ? 2 : 0,
            borderColor: isRadial ? '#fff' : 'transparent',
        }]
    };
    
    const options = { 
        indexAxis: isRadial ? 'x' : 'y', 
        responsive: true, 
        maintainAspectRatio: false,
        plugins: { 
            legend: { display: isRadial, position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12 } },
            tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': ' + (isRevenue ? 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw) : ctx.raw) } }
        },
        scales: isRadial ? { x: { display: false }, y: { display: false } } : {
            x: { beginAtZero: true, grid: { color: 'rgba(148,163,184,0.12)' }, ticks: { color: '#94A3B8', font: { size: 11 } } },
            y: { grid: { display: false }, ticks: { color: '#475569', font: { size: 11 } } }
        }
    };
    
    window.appCharts.topProducts = new Chart(ctx, { type: type, data: ds, options: options });
};

window.updateCategoryChart = function(type, metric) {
    const ctx = document.getElementById('categoryDonutChart');
    if (!ctx || !window.rawChartData.categories.length) return;
    if (window.appCharts.categories) window.appCharts.categories.destroy();
    
    const isRevenue = metric === 'revenue';
    const isRadial = type === 'doughnut' || type === 'pie';
    
    const ds = {
        labels: window.rawChartData.categories.map(c => c.name),
        datasets: [{
            label: isRevenue ? 'Pendapatan' : 'Qty Terjual',
            data: window.rawChartData.categories.map(c => isRevenue ? parseFloat(c.total_revenue) : parseInt(c.total_qty)),
            backgroundColor: COLORS,
            borderWidth: isRadial ? 2 : 0,
            borderRadius: isRadial ? 0 : 6,
            borderColor: isRadial ? '#fff' : 'transparent',
        }]
    };
    
    const options = {
        responsive: true, maintainAspectRatio: false, cutout: type === 'doughnut' ? '62%' : 0,
        plugins: {
            legend: { display: isRadial, position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12 } },
            tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': ' + (isRevenue ? 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw) : ctx.raw) } }
        },
        scales: isRadial ? { x: { display: false }, y: { display: false } } : {
            y: { beginAtZero: true, grid: { color: 'rgba(148,163,184,0.12)' }, ticks: { color: '#94A3B8', font: { size: 11 } } },
            x: { grid: { display: false }, ticks: { color: '#94A3B8', font: { size: 10 } } }
        }
    };
    window.appCharts.categories = new Chart(ctx, { type: type, data: ds, options: options });
};

window.updateDebtChart = function(type, metric) {
    const ctx = document.getElementById('debtTrendChart');
    if (!ctx) return;
    if (window.appCharts.debts) window.appCharts.debts.destroy();
    
    const isRadial = type === 'doughnut' || type === 'pie';
    let ds = {};
    
    if (metric === 'amount') {
        ds = {
            labels: window.rawChartData.debts.map(d => d.month),
            datasets: [
                { label: 'Total Hutang Baru', data: window.rawChartData.debts.map(d => parseFloat(d.total)), backgroundColor: isRadial ? COLORS[0] : 'rgba(239,68,68,0.7)', borderColor: isRadial ? '#fff' : 'rgba(239,68,68,1)', borderWidth: isRadial ? 2 : 1, borderRadius: isRadial ? 0 : 6, tension: 0.4 },
                { label: 'Terlunasi', data: window.rawChartData.debts.map(d => parseFloat(d.paid)), backgroundColor: isRadial ? COLORS[1] : 'rgba(16,185,129,0.7)', borderColor: isRadial ? '#fff' : 'rgba(16,185,129,1)', borderWidth: isRadial ? 2 : 1, borderRadius: isRadial ? 0 : 6, tension: 0.4 }
            ]
        };
    } else {
        ds = {
            labels: ['Belum Lunas', 'Dibayar Sebagian', 'Lunas'],
            datasets: [{
                label: 'Status Hutang',
                data: [window.rawChartData.debtStatus.unpaid, window.rawChartData.debtStatus.partial, window.rawChartData.debtStatus.paid],
                backgroundColor: ['#EF4444', '#F59E0B', '#10B981'],
                borderColor: isRadial ? '#fff' : 'transparent',
                borderWidth: isRadial ? 2 : 0,
                borderRadius: isRadial ? 0 : 6
            }]
        };
    }
    
    const options = {
        responsive: true, maintainAspectRatio: false, cutout: type === 'doughnut' ? '60%' : 0,
        plugins: {
            legend: { display: true, position: isRadial ? 'bottom' : 'top', labels: { font: { size: 12 }, usePointStyle: !isRadial } },
            tooltip: { callbacks: { label: ctx => `${ctx.dataset.label || ctx.label}: ${metric === 'amount' ? 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw) : ctx.raw + ' transaksi'}` } }
        },
        scales: isRadial ? { x: { display: false }, y: { display: false } } : {
            y: { beginAtZero: true, grid: { color: 'rgba(148,163,184,0.12)' }, ticks: { color: '#94A3B8', font: { size: 11 }, callback: v => metric === 'amount' ? 'Rp ' + new Intl.NumberFormat('id-ID').format(v) : v } },
            x: { grid: { display: false }, ticks: { color: '#94A3B8', font: { size: 10 } } }
        }
    };
    
    window.appCharts.debts = new Chart(ctx, { type: type, data: ds, options: options });
};

document.addEventListener('DOMContentLoaded', function () {
    window.updateSalesTrendChart('line', 'revenue');
    window.updateTopProductsChart('bar', 'qty');
    window.updateCategoryChart('doughnut', 'revenue');
    window.updateDebtChart('bar', 'amount');

    // Payment Chart (Static)
    const payCtx = document.getElementById('paymentChart');
    if (payCtx && window.rawChartData.payments.length) {
        new Chart(payCtx, {
            type: 'doughnut',
            data: {
                labels: window.rawChartData.payments.map(p => p.payment_method.toUpperCase()),
                datasets: [{ data: window.rawChartData.payments.map(p => parseFloat(p.total)), backgroundColor: ['#10B981','#3B82F6','#8B5CF6','#F59E0B'], borderWidth: 0, hoverOffset: 8 }]
            },
            options: {
                responsive: true, cutout: '65%',
                plugins: {
                    legend: { display: true, position: 'bottom', labels: { font: { size: 12 }, color: '#65676B', padding: 14, usePointStyle: true } },
                    tooltip: { callbacks: { label: ctx => ctx.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw) } }
                }
            }
        });
    }
});

function setDateRange(days) {
    const startInput = document.getElementById('sales-start-date').value;
    const start = startInput ? new Date(startInput) : new Date();
    
    const end = new Date(start);
    end.setDate(end.getDate() + days);
    
    const fmt = d => {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };
    
    document.getElementById('sales-start-date').value = fmt(start);
    document.getElementById('sales-end-date').value   = fmt(end);
    document.getElementById('sales-filter-form').submit();
}

function updateLimit(val) {
    document.getElementById('limit-input').value = val;
    document.getElementById('sales-filter-form').submit();
}
</script>
@endpush
