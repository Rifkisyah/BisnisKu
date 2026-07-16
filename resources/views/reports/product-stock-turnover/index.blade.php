@extends('layouts.app')
@section('page-title', 'Laporan Perputaran Stok Produk')
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
            <h1 class="type-heading-sm text-[var(--color-ink-deep)] flex items-center gap-2">
                <svg class="w-6 h-6 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                Laporan Perputaran Stok Produk
            </h1>
            <p class="type-caption text-[var(--color-slate)] mt-2">Analisis produk populer, kategori, dan pengadaan stok · Periode {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} <b>-</b> {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
        
        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
            <form method="GET" class="flex flex-col md:flex-row md:items-end gap-3 w-full" id="bi-filter-form">
                <div class="flex flex-col w-full md:w-auto">
                    <label for="bi-start-date" class="type-caption-bold text-[var(--color-slate)] mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="bi-start-date" value="{{ $startDate->format('Y-m-d') }}" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                </div>
                <div class="flex flex-col w-full md:w-auto">
                    <label for="bi-end-date" class="type-caption-bold text-[var(--color-slate)] mb-1">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="bi-end-date" value="{{ $endDate->format('Y-m-d') }}" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                </div>
                <div class="flex flex-col w-full md:w-auto">
                    <label for="category-select" class="type-caption-bold text-[var(--color-slate)] mb-1">Kategori</label>
                    <select name="category" id="category-select" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                        <option value="">{{ __('messages.all_categories') }}</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->category_code }}" {{ request('category') == $cat->category_code ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="limit" id="limit-input" value="{{ $limit }}">
                
                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto mt-2 md:mt-0 md:border-r border-[var(--color-hairline-soft)] md:pr-3">
                @php
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
                <a href="{{ route('reports.business_performance.export_pdf', request()->all()) }}" class="flex-1 justify-center btn-ghost !py-2 !px-3 text-xs flex items-center gap-1 text-[var(--color-critical)] border-[var(--color-critical)]/20 hover:bg-[var(--color-critical)]/10 rounded-[var(--radius-md)]" title="PDF">
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
{{-- SECTION 1: PRODUK POPULER                              --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="mb-6 card-feature p-5">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="type-heading-sm text-[var(--color-ink-deep)] flex items-center gap-2">
                <svg class="w-6 h-6 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                Produk Populer
            </h2>
            <p class="type-caption text-[var(--color-slate)] mt-1">Produk yang paling sering dan paling banyak terjual dalam periode ini</p>
        </div>
    </div>
</div>

{{-- Popular Products KPI --}}
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-5">
    <div class="card-feature p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 rounded-t-[var(--radius-xxxl)]" style="background:#6366F1"></div>
        <div class="mb-2"><svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Total Produk</p>
        <p class="mt-1 type-heading-sm text-[var(--color-ink-deep)]">{{ count($clusterResults) }} <span class="type-body-sm font-normal text-[var(--color-slate)]">produk</span></p>
        <p class="mt-1 type-caption text-[var(--color-stone)]">Tercatat dalam sistem</p>
    </div>
    <div class="card-feature p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 rounded-t-[var(--radius-xxxl)]" style="background:#10B981"></div>
        <div class="mb-2"><svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Produk Terjual</p>
        <p class="mt-1 type-heading-sm text-[var(--color-ink-deep)]">{{ $totalProductsSold }} <span class="type-body-sm font-normal text-[var(--color-slate)]">produk</span></p>
        <p class="mt-1 type-caption text-[var(--color-stone)]">Ada transaksi dalam periode ini</p>
    </div>
    <div class="card-feature p-4 relative overflow-hidden col-span-2 lg:col-span-1">
        <div class="absolute top-0 left-0 right-0 h-0.5 rounded-t-[var(--radius-xxxl)]" style="background:#F59E0B"></div>
        <div class="mb-2"><svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg></div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Produk Tidak Terjual</p>
        <p class="mt-1 type-heading-sm text-[var(--color-ink-deep)]">{{ count($clusterResults) - $totalProductsSold }} <span class="type-body-sm font-normal text-[var(--color-slate)]">produk</span></p>
        <p class="mt-1 type-caption text-[var(--color-stone)]">Belum ada transaksi di periode ini</p>
    </div>
</div>

<div class="card-feature mb-6 flex flex-col min-h-[500px]" x-data="{ view: 'chart', chartType: 'bar', metric: 'qty' }" x-init="$watch('chartType', val => updatePopularChart(val, metric)); $watch('metric', val => updatePopularChart(chartType, val))">
    <div class="flex flex-wrap items-center justify-between gap-3 p-5 border-b border-[var(--color-hairline-soft)]">
        <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]">Detail Produk Populer</h3>
        <div class="flex gap-2 mt-2 md:mt-0 w-full sm:w-auto">
            <button @click="view = 'chart'" :class="view === 'chart' ? 'pill-tab-active' : ''" class="pill-tab flex-1 sm:flex-none !text-xs">{{ __('Grafik') }}</button>
            <button @click="view = 'table'" :class="view === 'table' ? 'pill-tab-active' : ''" class="pill-tab flex-1 sm:flex-none !text-xs">{{ __('Tabel') }}</button>
        </div>
    </div>
    <div x-show="view === 'chart'" class="p-5 flex-1 grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
        <div>
            <div class="flex gap-4 mb-4 justify-center">
                <select x-model="chartType" class="input-field w-full md:w-auto text-xs !py-1.5">
                    <option value="bar">Bar Chart</option>
                    <option value="doughnut">Doughnut Chart</option>
                    <option value="pie">Pie Chart</option>
                </select>
                <select x-model="metric" class="input-field w-full md:w-auto text-xs !py-1.5">
                    <option value="qty">Unit Terjual (Qty)</option>
                    <option value="revenue">Total Pendapatan</option>
                </select>
            </div>
            <div class="flex-1 min-h-[300px] relative">
                <canvas id="biPopularChart"></canvas>
            </div>
        </div>
        <div class="space-y-3">
            <div class="rounded-[var(--radius-xl)] p-4 bg-[var(--color-surface-soft)] type-caption text-[var(--color-slate)]">
                <p class="type-body-sm-bold text-[var(--color-ink)] mb-2">Cara Kami Menentukan Produk Populer</p>
                <p>Produk diurutkan berdasarkan kombinasi dua faktor utama:</p>
                <ul class="list-disc pl-4 mt-1 space-y-1">
                    <li><b>Total pendapatan</b> — kontribusi pendapatan produk terhadap total penjualan</li>
                    <li><b>Total unit terjual</b> — total kuantitas yang berhasil dijual</li>
                </ul>
                <p class="mt-2">Produk yang sering dibeli dan dalam jumlah banyak akan muncul di urutan teratas. Data ini berguna untuk memastikan stok produk-produk tersebut selalu tersedia.</p>
            </div>
        </div>
    </div>
    <div x-show="view === 'table'" x-cloak class="flex-1 flex flex-col">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 border-b border-[var(--color-hairline-soft)]">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
                <span class="type-caption text-[var(--color-slate)]">{{ __('messages.show') }}:</span>
                <select class="input-field !py-1 !px-2 w-full sm:!w-auto text-sm" onchange="updateLimit(this.value)">
                    <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10 baris</option>
                    <option value="30" {{ $limit == 30 ? 'selected' : '' }}>30 baris</option>
                    <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50 baris</option>
                    <option value="all" {{ $limit === 'all' ? 'selected' : '' }}>Semua</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto flex-1">
            <table class="w-full">
                <thead><tr class="bg-[var(--color-surface-soft)]">
                    <th class="px-4 py-3 text-center table-header w-10">#</th>
                    <th class="px-4 py-3 text-left table-header">Nama Produk</th>
                    <th class="px-4 py-3 text-right table-header">Jumlah Terjual</th>
                    <th class="px-4 py-3 text-right table-header">Pendapatan</th>
                </tr></thead>
                <tbody>
                @php
                    $displayedProducts = $limit === 'all' ? $clusterResults : array_slice($clusterResults, 0, (int)$limit);
                @endphp
                @foreach($displayedProducts as $i => $cr)
                @php
                    $rank = $i + 1;
                    $rankColor = $rank === 1 ? '#F59E0B' : ($rank === 2 ? '#94A3B8' : ($rank === 3 ? '#CD7F32' : 'var(--color-stone)'));
                @endphp
                <tr onclick="window.location='{{ route('products.show', $cr['product_code']) }}'" class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] cursor-pointer">
                    <td class="px-4 py-2.5 text-center">
                        <span class="type-body-sm-bold" style="color: {{ $rankColor }}">{{ $rank }}</span>
                    </td>
                    <td class="px-4 py-2.5 type-body-sm font-medium text-[var(--color-ink)]">{{ $cr['product_name'] }}</td>
                    <td class="px-4 py-2.5 type-body-sm text-right text-[var(--color-slate)]">{{ number_format($cr['total_qty_sold'], 0, ',', '.') }} <span class="text-[10px]">unit</span></td>
                    <td class="px-4 py-2.5 type-body-sm text-right text-[var(--color-ink-deep)]">Rp {{ number_format($cr['total_revenue'] ?? 0, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if($limit !== 'all')
        <div class="p-4 border-t border-[var(--color-hairline-soft)] text-center mt-auto">
            <a href="{{ route('reports.business_performance.clusters', request()->all()) }}" class="bg-[var(--color-ink)] text-white hover:bg-[var(--color-ink-deep)] transition-colors rounded-[var(--radius-full)] font-medium type-body-sm !py-2 !px-6 inline-flex items-center gap-2">
                Lihat Keseluruhan Data
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- 1.5 TOP KATEGORI PENJUALAN                              --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="card-feature mb-6 flex flex-col min-h-[500px]" x-data="{ view: 'chart', chartType: 'doughnut', metric: 'revenue' }" x-init="if (typeof updateCategoryChart === 'function') { $watch('chartType', val => updateCategoryChart(val, metric)); $watch('metric', val => updateCategoryChart(chartType, val)) }">
    <div class="flex flex-wrap items-center justify-between gap-4 p-5 border-b border-[var(--color-hairline-soft)]">
        <div>
            <h2 class="type-subtitle-lg text-[var(--color-ink-deep)]">{{ __('messages.top_sales_categories') }}</h2>
            <p class="type-caption text-[var(--color-slate)]">Kontribusi pendapatan per kategori produk</p>
        </div>
        <div class="flex gap-2 mt-2 sm:mt-0 w-full sm:w-auto">
            <button @click="view = 'chart'" :class="view === 'chart' ? 'pill-tab-active' : ''" class="pill-tab flex-1 sm:flex-none">Grafik</button>
            <button @click="view = 'table'" :class="view === 'table' ? 'pill-tab-active' : ''" class="pill-tab flex-1 sm:flex-none">Tabel</button>
        </div>
    </div>
    
    <div x-show="view === 'table'" x-cloak class="flex-1 flex flex-col"><div class="overflow-x-auto flex-1">
        <table class="w-full">
            <thead><tr class="bg-[var(--color-surface-soft)]">
                <th class="px-5 py-3 text-left table-header">Kategori</th>
                <th class="px-5 py-3 text-right table-header">Qty Terjual</th>
                <th class="px-5 py-3 text-right table-header">{{ __('messages.revenue') }}</th>
                <th class="px-5 py-3 text-right table-header">%</th>
            </tr></thead>
            <tbody>
            @forelse($categoryBreakdown ?? [] as $cat)
            <tr onclick="window.location='{{ route('categories.show', $cat->id) }}'" class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] transition-colors cursor-pointer">
                <td class="px-5 py-3 type-body-sm font-medium text-[var(--color-ink)]">{{ $cat->name }}</td>
                <td class="px-5 py-3 text-right type-body-sm text-[var(--color-slate)]">{{ number_format($cat->total_qty, 0, ',', '.') }}</td>
                <td class="px-5 py-3 type-body-sm text-right text-[var(--color-ink)]">Rp {{ number_format($cat->total_revenue, 0, ',', '.') }}</td>
                <td class="px-5 py-3 text-right"><span class="type-caption-bold text-[var(--color-primary)]">{{ $cat->revenue_pct }}%</span></td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-5 py-10 text-center type-body-sm text-[var(--color-slate)]">Belum ada data kategori.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div x-show="view === 'chart'" class="p-6 flex-1 flex flex-col">
        <div class="flex justify-center gap-4 mb-4">
            <select x-model="chartType" class="input-field w-full md:w-auto text-xs !py-1.5">
                <option value="doughnut">Doughnut Chart</option>
                <option value="pie">Pie Chart</option>
                <option value="bar">Bar Chart</option>
            </select>
            <select x-model="metric" class="input-field w-full md:w-auto text-xs !py-1.5">
                <option value="revenue">Pendapatan (Rp)</option>
                <option value="qty">Unit Terjual (Qty)</option>
            </select>
        </div>
        <div style="height:280px; position:relative" class="flex justify-center">
            @if(isset($categoryBreakdown) && count($categoryBreakdown) > 0)
            <canvas id="categoryDonutChart"></canvas>
            @else
            <div class="absolute inset-0 flex items-center justify-center bg-[var(--color-surface-soft)] rounded-lg border border-dashed border-[var(--color-hairline-soft)]">
                <p class="type-body-sm text-[var(--color-slate)]">{{ __('messages.no_data_to_display') }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- SECTION 2: DATA PENGADAAN STOK                         --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="mb-6 card-feature p-5">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="type-heading-sm text-[var(--color-ink-deep)] flex items-center gap-2">
                <svg class="w-6 h-6 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Data Pengadaan Stok
            </h2>
            <p class="type-caption text-[var(--color-slate)] mt-1">Riwayat pembelian dan penambahan stok (restok & produk baru) dalam periode ini</p>
        </div>
    </div>
</div>

{{-- Procurement KPI --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="card-feature p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 rounded-t-[var(--radius-xxxl)]" style="background:#6366F1"></div>
        <div class="mb-2"><svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Total Pengadaan</p>
        <p class="mt-1 type-heading-sm text-[var(--color-ink-deep)]">{{ $procurements->count() }} <span class="type-body-sm font-normal text-[var(--color-slate)]">PO</span></p>
        <p class="mt-1 type-caption text-[var(--color-stone)]">Purchase order dalam periode</p>
    </div>
    <div class="card-feature p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 rounded-t-[var(--radius-xxxl)]" style="background:#10B981"></div>
        <div class="mb-2"><svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Diterima</p>
        <p class="mt-1 type-heading-sm {{ $receivedProcurements > 0 ? 'text-[var(--color-success)]' : 'text-[var(--color-ink-deep)]' }}">{{ $receivedProcurements }} <span class="type-body-sm font-normal text-[var(--color-slate)]">PO</span></p>
        <p class="mt-1 type-caption text-[var(--color-stone)]">Stok sudah masuk gudang</p>
    </div>
    <div class="card-feature p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 rounded-t-[var(--radius-xxxl)]" style="background:#F59E0B"></div>
        <div class="mb-2"><svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Dalam Proses</p>
        <p class="mt-1 type-heading-sm {{ $pendingProcurements > 0 ? 'text-[var(--color-attention)]' : 'text-[var(--color-ink-deep)]' }}">{{ $pendingProcurements }} <span class="type-body-sm font-normal text-[var(--color-slate)]">PO</span></p>
        <p class="mt-1 type-caption text-[var(--color-stone)]">Draft / Dipesan / Sebagian</p>
    </div>
    <div class="card-feature p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-500 to-indigo-400 rounded-t-[var(--radius-xxxl)]"></div>
        <div class="mb-2"><svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Total Nilai</p>
        <p class="mt-1 type-heading-sm text-[var(--color-ink-deep)]">Rp {{ number_format($totalProcurementValue, 0, ',', '.') }}</p>
        <p class="mt-1 type-caption text-[var(--color-stone)]">Nilai seluruh pengadaan</p>
    </div>
</div>

<div class="card-feature mb-6 flex flex-col min-h-[500px]" x-data="{ view: 'chart', chartType: 'bar', metric: 'count' }" x-init="$watch('chartType', val => updateProcurementChart(val, metric)); $watch('metric', val => updateProcurementChart(chartType, val))">
    <div class="flex flex-wrap items-center justify-between gap-3 p-5 border-b border-[var(--color-hairline-soft)]">
        <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]">Detail Data Pengadaan Stok</h3>
        <div class="flex gap-2 mt-2 md:mt-0 w-full sm:w-auto">
            <button @click="view = 'chart'" :class="view === 'chart' ? 'pill-tab-active' : ''" class="pill-tab flex-1 sm:flex-none !text-xs">{{ __('Grafik') }}</button>
            <button @click="view = 'table'" :class="view === 'table' ? 'pill-tab-active' : ''" class="pill-tab flex-1 sm:flex-none !text-xs">{{ __('Tabel') }}</button>
        </div>
    </div>
    <div x-show="view === 'chart'" class="p-5 flex-1 flex flex-col">
        <div class="flex flex-col sm:flex-row gap-4 mb-4">
            <select x-model="chartType" class="input-field w-full md:w-auto text-xs !py-1.5">
                <option value="bar">Bar Chart</option>
                <option value="line">Line Chart</option>
                <option value="doughnut">Doughnut Chart</option>
                <option value="pie">Pie Chart</option>
            </select>
            <select x-model="metric" class="input-field w-full md:w-auto text-xs !py-1.5">
                <option value="count">Jumlah PO</option>
                <option value="value">Nilai Pengadaan (Rp)</option>
            </select>
        </div>
        <div class="flex-1 min-h-[300px] relative">
            @if($procurements->count() > 0)
            <canvas id="biProcurementChart"></canvas>
            @else
            <div class="absolute inset-0 flex items-center justify-center bg-[var(--color-surface-soft)] rounded-lg border border-dashed border-[var(--color-hairline-soft)]">
                <p class="type-body-sm text-[var(--color-slate)]">Belum ada data pengadaan dalam periode ini.</p>
            </div>
            @endif
        </div>
    </div>
    <div x-show="view === 'table'" x-cloak class="flex-1 flex flex-col">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 border-b border-[var(--color-hairline-soft)]">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
                <span class="type-caption text-[var(--color-slate)]">{{ __('messages.show') }}:</span>
                <select class="input-field !py-1 !px-2 w-full sm:!w-auto text-sm" onchange="updateLimit(this.value)">
                    <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10 baris</option>
                    <option value="30" {{ $limit == 30 ? 'selected' : '' }}>30 baris</option>
                    <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50 baris</option>
                    <option value="all" {{ $limit === 'all' ? 'selected' : '' }}>Semua</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto flex-1">
            <table class="w-full">
                <thead><tr class="bg-[var(--color-surface-soft)]">
                    <th class="px-4 py-3 text-left table-header">Kode PO</th>
                    <th class="px-4 py-3 text-left table-header">Tanggal</th>
                    <th class="px-4 py-3 text-center table-header">Jml. Item</th>
                    <th class="px-4 py-3 text-left table-header">Dibuat Oleh</th>
                    <th class="px-4 py-3 text-right table-header">Total Nilai</th>
                    <th class="px-4 py-3 text-center table-header">Status</th>
                </tr></thead>
                <tbody>
                @php
                    $displayedProcurements = $limit === 'all' ? $procurements : $procurements->take((int)$limit);
                    $statusLabels = [
                        'draft'             => ['text' => 'Draft',        'style' => 'background:#F3F4F6;color:#6B7280'],
                        'ordered'           => ['text' => 'Dipesan',      'style' => 'background:#EFF6FF;color:#2563EB'],
                        'partial_received'  => ['text' => 'Sebagian',     'style' => 'background:#FFFBEB;color:#D97706'],
                        'received'          => ['text' => 'Diterima',     'style' => 'background:#ECFDF5;color:#059669'],
                        'cancelled'         => ['text' => 'Dibatalkan',   'style' => 'background:#FEF2F2;color:#DC2626'],
                    ];
                @endphp
                @forelse($displayedProcurements as $po)
                @php
                    $sl = $statusLabels[$po->status] ?? ['text' => $po->status, 'style' => ''];
                @endphp
                <tr class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] cursor-pointer" onclick="window.location='{{ route('product-purchases.show', $po->product_purchase_code) }}'">
                    <td class="px-4 py-2.5 type-caption-bold text-[var(--color-primary)]">{{ $po->product_purchase_code }}</td>
                    <td class="px-4 py-2.5 type-body-sm text-[var(--color-slate)]">{{ $po->purchase_date->format('d M Y') }}</td>
                    <td class="px-4 py-2.5 text-center"><span class="badge badge-neutral">{{ $po->items->count() }} item</span></td>
                    <td class="px-4 py-2.5 type-body-sm text-[var(--color-ink)]">{{ $po->creator->username ?? '-' }}</td>
                    <td class="px-4 py-2.5 type-body-sm-bold text-right text-[var(--color-ink-deep)]">Rp {{ number_format($po->total, 0, ',', '.') }}</td>
                    <td class="px-4 py-2.5 text-center">
                        <span class="badge inline-flex items-center" style="{{ $sl['style'] }}; font-size:11px;">{{ $sl['text'] }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-10 text-center type-body-sm text-[var(--color-slate)]">Belum ada data pengadaan dalam periode ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
function updateLimit(val) {
    document.getElementById('limit-input').value = val;
    document.getElementById('bi-filter-form').submit();
}

window.appCharts = {};

const PALETTE = ['#6366F1','#10B981','#F59E0B','#0064E0','#F43F5E','#06B6D4','#8B5CF6','#14B8A6','#F97316','#EC4899'];

// ── Popular Products Chart ────────────────────────────────────────────
window.rawBiData = {
    clusterResults: @json(array_slice($clusterResults, 0, 20)),
    categories: @json($categoryBreakdown ?? [])
};

window.updatePopularChart = function(type, metric) {
    const ctx = document.getElementById('biPopularChart');
    if (!ctx) return;
    if (window.appCharts.popular) window.appCharts.popular.destroy();

    const data = window.rawBiData.clusterResults.filter(p => (p.total_qty_sold || 0) > 0).slice(0, 15);
    const isRadial = type === 'doughnut' || type === 'pie';
    const valueKey = metric === 'qty' ? 'total_qty_sold' : 'total_revenue';
    const labelStr = metric === 'qty' ? 'Unit Terjual' : 'Total Pendapatan';

    const ds = {
        labels: data.map(p => p.product_name),
        datasets: [{
            label: labelStr,
            data: data.map(p => p[valueKey] || 0),
            backgroundColor: isRadial ? PALETTE : PALETTE[0],
            borderColor: isRadial ? '#fff' : 'transparent',
            borderWidth: isRadial ? 2 : 0,
            borderRadius: isRadial ? 0 : 5,
        }]
    };

    const options = {
        responsive: true, maintainAspectRatio: false,
        indexAxis: (!isRadial && type === 'bar') ? 'y' : 'x',
        plugins: {
            legend: { display: isRadial, position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12 } },
            tooltip: { callbacks: { label: ctx => `${ctx.label}: ${metric === 'qty' ? '' : 'Rp '}${ctx.raw.toLocaleString('id-ID')} ${metric === 'qty' ? 'unit' : ''}` } }
        },
        scales: isRadial ? { x: { display: false }, y: { display: false } } : {
            y: { beginAtZero: true, grid: { color: 'rgba(148,163,184,0.12)' }, ticks: { color: '#94A3B8', font: { size: 10 } } },
            x: { grid: { display: false }, ticks: { color: '#94A3B8', font: { size: 10 } } }
        }
    };

    window.appCharts.popular = new Chart(ctx, { type: type === 'bar' ? 'bar' : type, data: ds, options: options });
};

// ── Procurement Chart ─────────────────────────────────────────────────
window.rawProcurementData = @json($procurementTrend);

window.updateProcurementChart = function(type, metric) {
    const ctx = document.getElementById('biProcurementChart');
    if (!ctx || !window.rawProcurementData.length) return;
    if (window.appCharts.procurement) window.appCharts.procurement.destroy();

    const isRadial = type === 'doughnut' || type === 'pie';
    const valueKey = metric === 'count' ? 'count' : 'total';
    const labelStr = metric === 'count' ? 'Jumlah PO' : 'Nilai Pengadaan (Rp)';

    // Aggregate by date (multiple status rows per date)
    const byDate = {};
    window.rawProcurementData.forEach(row => {
        if (!byDate[row.date]) byDate[row.date] = { count: 0, total: 0 };
        byDate[row.date].count += parseInt(row.count || 0);
        byDate[row.date].total += parseFloat(row.total || 0);
    });
    const dates = Object.keys(byDate).sort();

    const ds = {
        labels: dates,
        datasets: [{
            label: labelStr,
            data: dates.map(d => byDate[d][valueKey]),
            backgroundColor: isRadial ? PALETTE : 'rgba(99,102,241,0.8)',
            borderColor: isRadial ? '#fff' : '#6366F1',
            borderWidth: type === 'line' ? 2 : (isRadial ? 2 : 0),
            borderRadius: isRadial ? 0 : 5,
            tension: 0.3,
            fill: type === 'line',
        }]
    };

    const options = {
        responsive: true, maintainAspectRatio: false, cutout: type === 'doughnut' ? '60%' : 0,
        plugins: {
            legend: { display: isRadial, position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12 } },
            tooltip: { callbacks: { label: ctx => `${ctx.label}: ${metric === 'value' ? 'Rp ' + ctx.raw.toLocaleString('id-ID') : ctx.raw + ' PO'}` } }
        },
        scales: isRadial ? { x: { display: false }, y: { display: false } } : {
            y: { beginAtZero: true, grid: { color: 'rgba(148,163,184,0.12)' }, ticks: { color: '#94A3B8' } },
            x: { grid: { display: false }, ticks: { color: '#94A3B8', font: { size: 10 } } }
        }
    };

    window.appCharts.procurement = new Chart(ctx, { type: type, data: ds, options: options });
};

window.updateCategoryChart = function(type, metric) {
    const ctx = document.getElementById('categoryDonutChart');
    if (!ctx || !window.rawBiData.categories.length) return;
    if (window.appCharts.categories) window.appCharts.categories.destroy();
    
    const isRevenue = metric === 'revenue';
    const isRadial = type === 'doughnut' || type === 'pie';
    
    const ds = {
        labels: window.rawBiData.categories.map(c => c.name),
        datasets: [{
            label: isRevenue ? 'Pendapatan' : 'Qty Terjual',
            data: window.rawBiData.categories.map(c => isRevenue ? parseFloat(c.total_revenue) : parseInt(c.total_qty)),
            backgroundColor: PALETTE,
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

document.addEventListener('DOMContentLoaded', function () {
    window.updatePopularChart('bar', 'qty');
    window.updateProcurementChart('bar', 'count');
    if (typeof updateCategoryChart === 'function') {
        window.updateCategoryChart('doughnut', 'revenue');
    }
});

function setDateRange(days) {
    const startInput = document.getElementById('bi-start-date').value;
    const start = startInput ? new Date(startInput) : new Date();
    
    const end = new Date(start);
    end.setDate(end.getDate() + days);
    
    const fmt = d => {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };
    
    document.getElementById('bi-start-date').value = fmt(start);
    document.getElementById('bi-end-date').value   = fmt(end);
    document.getElementById('bi-filter-form').submit();
}
</script>
@endpush
