@extends('layouts.app')
@section('page-title', __('messages.report_stock_turnover') ?? 'Laporan Perputaran Stok Produk')
@section('content')

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- HEADER & FILTER (hidden on print)                       --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="mb-6 flex items-center justify-between print:hidden">
    <a href="{{ route('reports.index') }}" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
</div>

<div class="mb-6 card-feature p-5 print:hidden">
    <div class="flex flex-col xl:flex-row xl:items-start justify-between gap-6">
        <div class="xl:w-1/3">
            <h1 class="type-heading-sm text-[var(--color-ink-deep)] flex items-center gap-2">
                <svg class="w-6 h-6 text-[var(--color-primary)] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                {{ __('messages.report_stock_turnover') ?? 'Laporan Perputaran Stok Produk' }}
            </h1>
            <p class="type-caption text-[var(--color-slate)] mt-2">Analisis produk populer, kategori, dan pengadaan stok · Periode {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} <b>-</b> {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
        
        <div class="xl:w-2/3 w-full">
            <form method="GET" class="flex flex-row flex-wrap xl:justify-end items-end gap-3 w-full" id="bi-filter-form">
                <div class="flex flex-col">
                    <label for="bi-start-date" class="type-caption-bold text-[var(--color-slate)] mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="bi-start-date" value="{{ $startDate->format('Y-m-d') }}" class="input-field text-xs !py-1.5 min-w-[120px]" onchange="this.form.submit()">
                </div>
                <div class="flex flex-col">
                    <label for="bi-end-date" class="type-caption-bold text-[var(--color-slate)] mb-1">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="bi-end-date" value="{{ $endDate->format('Y-m-d') }}" class="input-field text-xs !py-1.5 min-w-[120px]" onchange="this.form.submit()">
                </div>
                <div class="flex flex-col">
                    <label for="category-select" class="type-caption-bold text-[var(--color-slate)] mb-1">Kategori</label>
                    <select name="category" id="category-select" class="input-field text-xs !py-1.5 min-w-[130px]" onchange="this.form.submit()">
                        <option value="">{{ __('messages.all_categories') ?? 'Semua Kategori' }}</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->category_code }}" {{ request('category') == $cat->category_code ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="limit" id="limit-input" value="{{ $limit }}">
                
                <div class="flex flex-row items-center gap-2 mt-2 md:mt-0 xl:border-r border-[var(--color-hairline-soft)] xl:pr-3">
                @php
                    $activeFilter = null;
                    if ($diffDays == 7) $activeFilter = 7;
                    elseif ($diffDays == 365) $activeFilter = 365;
                    elseif ($diffDays == 30 || !request()->has('start_date')) $activeFilter = 30;
                @endphp
                <button type="button" onclick="setDateRange(7)" class="w-[84px] text-center justify-center {{ $activeFilter === 7 ? 'pill-tab-active !border-transparent' : 'btn-ghost' }} !py-2 !px-3 text-xs">{{ __('messages.week') ?? 'Minggu' }}</button>
                <button type="button" onclick="setDateRange(30)" class="w-[84px] text-center justify-center {{ $activeFilter === 30 ? 'pill-tab-active !border-transparent' : 'btn-ghost' }} !py-2 !px-3 text-xs">{{ __('messages.month') ?? 'Bulan' }}</button>
                <button type="button" onclick="setDateRange(365)" class="w-[84px] text-center justify-center {{ $activeFilter === 365 ? 'pill-tab-active !border-transparent' : 'btn-ghost' }} !py-2 !px-3 text-xs">{{ __('messages.year') ?? 'Tahun' }}</button>
                </div>
                
                <div class="flex flex-row items-center gap-2 mt-2 md:mt-0">
                <button type="button" onclick="downloadPdf()" class="w-[130px] justify-center btn-ghost !py-2 !px-3 text-xs flex items-center gap-1 text-[var(--color-critical)] border-[var(--color-critical)]/20 hover:bg-[var(--color-critical)]/10 rounded-[var(--radius-md)]" title="PDF">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    PDF
                </button>
                <button type="button" onclick="window.print()" class="w-[130px] justify-center btn-ghost !py-2 !px-3 text-xs flex items-center gap-1 rounded-[var(--radius-md)]" title="Cetak">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak
                </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- SCREEN: POPULAR PRODUCTS SECTION                        --}}
{{-- ═══════════════════════════════════════════════════════ --}}
@php
    $highCount = count(array_filter($clusterResults, fn($r) => (($r['total_qty_sold'] / (max(array_column($clusterResults, 'total_qty_sold')) ?: 1)) >= 0.5)));
    $medCount  = count(array_filter($clusterResults, fn($r) => (($r['total_qty_sold'] / (max(array_column($clusterResults, 'total_qty_sold')) ?: 1)) >= 0.15 && ($r['total_qty_sold'] / (max(array_column($clusterResults, 'total_qty_sold')) ?: 1)) < 0.5)));
    $lowCount  = count($clusterResults) - $highCount - $medCount;
    $maxQty    = count($clusterResults) ? max(array_column($clusterResults, 'total_qty_sold')) : 1;
    $clusterResultsTagged = array_map(function($r) use ($maxQty) {
        $pct = $maxQty > 0 ? ($r['total_qty_sold'] / $maxQty) : 0;
        $r['cluster'] = $pct >= 0.5 ? 'high' : ($pct >= 0.15 ? 'medium' : 'low');
        return $r;
    }, $clusterResults);
@endphp

<div class="mb-6 card-feature p-5 print:hidden">
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
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-5 print:hidden">
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

<div class="card-feature mb-6 flex flex-col min-h-[500px] print:hidden" x-data="{ view: 'chart', chartType: 'bar', metric: 'qty' }" x-init="$watch('chartType', val => updatePopularChart(val, metric)); $watch('metric', val => updatePopularChart(chartType, val))">
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
                <p class="mt-2">Produk yang sering dibeli dan dalam jumlah banyak akan muncul di urutan teratas.</p>
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
                    <th class="px-4 py-3 text-center table-header">Cluster</th>
                </tr></thead>
                <tbody>
                @php
                    $displayedProducts = $limit === 'all' ? $clusterResultsTagged : array_slice($clusterResultsTagged, 0, (int)$limit);
                @endphp
                @foreach($displayedProducts as $i => $cr)
                @php
                    $rank = $i + 1;
                    $rankColor = $rank === 1 ? '#F59E0B' : ($rank === 2 ? '#94A3B8' : ($rank === 3 ? '#CD7F32' : 'var(--color-stone)'));
                    $clusterClass = match($cr['cluster'] ?? 'low') {
                        'high'   => 'badge-cluster-fast',
                        'medium' => 'badge-cluster-medium',
                        default  => 'badge-cluster-slow',
                    };
                    $clusterLabel = match($cr['cluster'] ?? 'low') {
                        'high'   => __('messages.high') ?? 'Tinggi',
                        'medium' => __('messages.medium') ?? 'Sedang',
                        default  => __('messages.low') ?? 'Rendah',
                    };
                @endphp
                <tr onclick="window.location='{{ route('products.show', $cr['product_code']) }}'" class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] cursor-pointer">
                    <td class="px-4 py-2.5 text-center"><span class="type-body-sm-bold" style="color: {{ $rankColor }}">{{ $rank }}</span></td>
                    <td class="px-4 py-2.5 type-body-sm font-medium text-[var(--color-ink)]">{{ $cr['product_name'] }}</td>
                    <td class="px-4 py-2.5 type-body-sm text-right text-[var(--color-slate)]">{{ number_format($cr['total_qty_sold'], 0, ',', '.') }} <span class="text-[10px]">unit</span></td>
                    <td class="px-4 py-2.5 type-body-sm text-right text-[var(--color-ink-deep)]">Rp {{ number_format($cr['total_revenue'] ?? 0, 0, ',', '.') }}</td>
                    <td class="px-4 py-2.5 text-center"><span class="badge {{ $clusterClass }}" style="font-size:11px;">{{ $clusterLabel }}</span></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- SCREEN: CATEGORY SECTION                                --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="card-feature mb-6 flex flex-col min-h-[500px] print:hidden" x-data="{ view: 'chart', chartType: 'doughnut', metric: 'revenue' }" x-init="if (typeof updateCategoryChart === 'function') { $watch('chartType', val => updateCategoryChart(val, metric)); $watch('metric', val => updateCategoryChart(chartType, val)) }">
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
    </div></div>
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
{{-- SCREEN: PROCUREMENT SECTION                             --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="mb-6 card-feature p-5 print:hidden">
    <h2 class="type-heading-sm text-[var(--color-ink-deep)] flex items-center gap-2">
        <svg class="w-6 h-6 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        Data Pengadaan Stok
    </h2>
    <p class="type-caption text-[var(--color-slate)] mt-1">Riwayat pembelian dan penambahan stok dalam periode ini</p>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5 print:hidden">
    <div class="card-feature p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 rounded-t-[var(--radius-xxxl)]" style="background:#6366F1"></div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Total Pengadaan</p>
        <p class="mt-1 type-heading-sm text-[var(--color-ink-deep)]">{{ $procurements->count() }} <span class="type-body-sm font-normal text-[var(--color-slate)]">PO</span></p>
    </div>
    <div class="card-feature p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 rounded-t-[var(--radius-xxxl)]" style="background:#10B981"></div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Diterima</p>
        <p class="mt-1 type-heading-sm {{ $receivedProcurements > 0 ? 'text-[var(--color-success)]' : 'text-[var(--color-ink-deep)]' }}">{{ $receivedProcurements }} <span class="type-body-sm font-normal text-[var(--color-slate)]">PO</span></p>
    </div>
    <div class="card-feature p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 rounded-t-[var(--radius-xxxl)]" style="background:#F59E0B"></div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Dalam Proses</p>
        <p class="mt-1 type-heading-sm {{ $pendingProcurements > 0 ? 'text-[var(--color-attention)]' : 'text-[var(--color-ink-deep)]' }}">{{ $pendingProcurements }} <span class="type-body-sm font-normal text-[var(--color-slate)]">PO</span></p>
    </div>
    <div class="card-feature p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 rounded-t-[var(--radius-xxxl)]" style="background:#0064E0"></div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Total Nilai</p>
        <p class="mt-1 type-heading-sm text-[var(--color-ink-deep)]">Rp {{ number_format($totalProcurementValue, 0, ',', '.') }}</p>
    </div>
</div>

<div class="card-feature mb-6 flex flex-col min-h-[500px] print:hidden" x-data="{ view: 'chart', chartType: 'bar', metric: 'count' }" x-init="$watch('chartType', val => updateProcurementChart(val, metric)); $watch('metric', val => updateProcurementChart(chartType, val))">
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
                        'draft'            => ['text' => __('messages.draft'),            'style' => 'background:#F3F4F6;color:#6B7280'],
                        'ordered'          => ['text' => __('messages.ordered'),          'style' => 'background:#EFF6FF;color:#2563EB'],
                        'partial_received' => ['text' => __('messages.partial_received'), 'style' => 'background:#FFFBEB;color:#D97706'],
                        'received'         => ['text' => __('messages.received'),         'style' => 'background:#ECFDF5;color:#059669'],
                        'cancelled'        => ['text' => __('messages.cancelled'),        'style' => 'background:#FEF2F2;color:#DC2626'],
                    ];
                @endphp
                @forelse($displayedProcurements as $po)
                @php $sl = $statusLabels[$po->status] ?? ['text' => $po->status, 'style' => '']; @endphp
                <tr class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] cursor-pointer" onclick="window.location='{{ route('product-purchases.show', $po->product_purchase_code) }}'">
                    <td class="px-4 py-2.5 type-caption-bold text-[var(--color-primary)]">{{ $po->product_purchase_code }}</td>
                    <td class="px-4 py-2.5 type-body-sm text-[var(--color-slate)]">{{ $po->purchase_date->format('d M Y') }}</td>
                    <td class="px-4 py-2.5 text-center"><span class="badge badge-neutral">{{ $po->items->count() }} item</span></td>
                    <td class="px-4 py-2.5 type-body-sm text-[var(--color-ink)]">{{ $po->creator->username ?? '-' }}</td>
                    <td class="px-4 py-2.5 type-body-sm-bold text-right text-[var(--color-ink-deep)]">Rp {{ number_format($po->total, 0, ',', '.') }}</td>
                    <td class="px-4 py-2.5 text-center"><span class="badge inline-flex items-center" style="{{ $sl['style'] }}; font-size:11px;">{{ $sl['text'] }}</span></td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-10 text-center type-body-sm text-[var(--color-slate)]">Belum ada data pengadaan dalam periode ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- PRINT-ONLY: Full Report (matching PDF exactly)                      --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
@php
    $storeName    = \App\Models\Setting::get('store_name', 'Nama Toko');
    $storeAddress = \App\Models\Setting::get('store_address', '');
    $storePhone   = \App\Models\Setting::get('store_phone', '');
    $categoryName = request('category') ? ($categories->firstWhere('category_code', request('category'))?->name ?? '-') : 'Semua Kategori';

    // Procurement status labels for print
    $printStatusLabels = [
        'draft'            => __('messages.draft'),
        'ordered'          => __('messages.ordered'),
        'partial_received' => __('messages.partial_received'),
        'received'         => __('messages.received'),
        'cancelled'        => __('messages.cancelled'),
    ];
@endphp
<div class="hidden print:block">
<style>
    @media print {
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; }
        body { font-family: Arial, sans-serif; font-size: 9px; color: #1e293b; }
        .pr-header { background: #0f172a !important; color: #fff !important; padding: 18px 24px 14px; margin-bottom: 0; }
        .pr-header .store-name { font-size: 11px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; color: #93c5fd !important; }
        .pr-header .store-contact { font-size: 7.5px; color: #cbd5e1 !important; }
        .pr-header h1 { font-size: 16px; font-weight: bold; color: #fff !important; margin-top: 6px; }
        .pr-header .sub { font-size: 8px; color: #bfdbfe !important; margin-top: 3px; }
        .pr-header .meta { display: flex; justify-content: space-between; margin-top: 10px; padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.2); font-size: 8px; }
        .pr-header .meta .period { color: #bfdbfe !important; }
        .pr-header .meta .period span { font-size: 10px; font-weight: bold; color: #fff !important; display: block; }
        .pr-header .meta .docinfo { text-align: right; color: #93c5fd !important; }
        .pr-section { padding: 10px 18px 4px; }
        .pr-sh { display: flex; align-items: center; margin-bottom: 7px; padding-bottom: 5px; border-bottom: 2px solid #2563EB !important; }
        .pr-snum { background: #2563EB !important; color: #fff !important; width: 17px; height: 17px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 7.5px; font-weight: bold; margin-right: 6px; flex-shrink: 0; }
        .pr-stitle { font-size: 10.5px; font-weight: bold; color: #0f172a; }
        .pr-ssub { font-size: 7.5px; color: #64748b; }
        /* KPI Grid */
        .pr-kpi { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .pr-kpi td { padding: 6px; vertical-align: top; width: 25%; }
        .pr-card { border: 1px solid #e2e8f0; border-radius: 4px; padding: 6px 8px; background: #f8fafc !important; border-left: 3px solid #2563EB !important; }
        .pr-card.g { border-left-color: #10b981 !important; }
        .pr-card.a { border-left-color: #f59e0b !important; }
        .pr-card.r { border-left-color: #ef4444 !important; }
        .pr-card.p { border-left-color: #8b5cf6 !important; }
        .pr-cl { font-size: 7px; color: #64748b; text-transform: uppercase; font-weight: bold; }
        .pr-cv { font-size: 13px; font-weight: bold; color: #0f172a; margin-top: 2px; }
        .pr-cd { font-size: 6.5px; color: #94a3b8; margin-top: 1px; }
        /* Data Table */
        table.prt { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 8px; }
        table.prt thead tr { background: #1e3a5f !important; color: #fff !important; }
        table.prt thead th { padding: 5px 7px; text-align: left; font-size: 7px; font-weight: bold; text-transform: uppercase; }
        table.prt thead th.r { text-align: right; }
        table.prt thead th.c { text-align: center; }
        table.prt tbody tr { border-bottom: 1px solid #f1f5f9; }
        table.prt tbody tr:nth-child(even) { background: #f8fafc !important; }
        table.prt tbody td { padding: 4px 7px; color: #334155; }
        table.prt tbody td.r { text-align: right; }
        table.prt tbody td.c { text-align: center; }
        table.prt tfoot tr { background: #f1f5f9 !important; border-top: 2px solid #cbd5e1; }
        table.prt tfoot td { padding: 5px 7px; font-weight: bold; }
        /* Badges */
        .pb { display: inline-block; padding: 1px 5px; border-radius: 8px; font-size: 7px; font-weight: bold; }
        .pb-h { background: #dcfce7 !important; color: #166534 !important; }
        .pb-m { background: #fef9c3 !important; color: #854d0e !important; }
        .pb-l { background: #fee2e2 !important; color: #991b1b !important; }
        .pb-rec { background: #dcfce7 !important; color: #166534 !important; }
        .pb-pen { background: #fef9c3 !important; color: #854d0e !important; }
        .pb-can { background: #fee2e2 !important; color: #991b1b !important; }
        .pb-ord { background: #eff6ff !important; color: #1d4ed8 !important; }
        .pb-dft { background: #f3f4f6 !important; color: #4b5563 !important; }
        .pb-rst { background: #fef3c7 !important; color: #92400e !important; }
        .pb-dead{ background: #fee2e2 !important; color: #7f1d1d !important; }
        /* Progress bar */
        .pr-bw { background: #e2e8f0 !important; border-radius: 3px; height: 5px; width: 70px; display: inline-block; vertical-align: middle; }
        .pr-bf { height: 5px; border-radius: 3px; background: #2563EB !important; }
        .pr-bf.g { background: #10b981 !important; }
        .pr-bf.a { background: #f59e0b !important; }
        /* Alert */
        .pr-alert { background: #fffbeb !important; border: 1px solid #fcd34d; border-left: 3px solid #f59e0b !important; border-radius: 3px; padding: 5px 8px; margin-bottom: 6px; font-size: 7.5px; color: #78350f; }
        .pr-alert.info { background: #eff6ff !important; border-color: #93c5fd; border-left-color: #2563EB !important; color: #1e3a5f; }
        .pr-alert.danger { background: #fef2f2 !important; border-color: #fca5a5; border-left-color: #ef4444 !important; color: #7f1d1d; }
        /* Sep */
        .pr-sep { border: none; border-top: 1px dashed #e2e8f0; margin: 6px 18px; }
        /* Charts (canvas) */
        .pr-chart-wrap { margin: 0 auto; width: 100%; max-width: 480px; height: 160px; position: relative; }
        .pr-chart-title { font-size: 8px; font-weight: bold; color: #0f172a; margin-bottom: 4px; text-align: center; }
        /* Page break */
        .pr-pb { page-break-before: always; }
        /* Footer */
        .pr-footer { border-top: 1px solid #cbd5e1; padding: 5px 18px; display: flex; justify-content: space-between; font-size: 7px; color: #94a3b8; margin-top: 8px; }
    }
</style>

{{-- ── COVER HEADER ─────────────────────────────────────── --}}
<div class="pr-header">
    <div class="store-name">{{ $storeName }}</div>
    <div class="store-contact">{{ $storeAddress }}{{ $storePhone ? ' · Tel: ' . $storePhone : '' }}</div>
    <h1>{{ __('messages.report_stock_turnover') ?? 'Laporan Perputaran Stok Produk' }}</h1>
    <div class="sub">Business Intelligence — Analisis Populer · Klasifikasi K-Means · Rekomendasi Restock SMA</div>
    <div class="meta">
        <div class="period">Periode<span>{{ $startDate->format('d M Y') }} — {{ $endDate->format('d M Y') }}</span>Kategori: {{ $categoryName }}</div>
        <div class="docinfo">Dicetak: {{ now()->format('d M Y, H:i') }}<br><strong>CONFIDENTIAL</strong></div>
    </div>
</div>

{{-- ── KPI SUMMARY ──────────────────────────────────────── --}}
@php
    $totalRevPrint = ($topProducts->sum('total_revenue') ?? 0) + ($serviceRevenue ?? 0);
@endphp
<div class="pr-section">
    <div class="pr-sh"><div class="pr-snum">★</div><div><div class="pr-stitle">Ringkasan Eksekutif (KPI)</div><div class="pr-ssub">Indikator kinerja kunci periode {{ $startDate->format('d M Y') }} – {{ $endDate->format('d M Y') }}</div></div></div>
    <table class="pr-kpi">
        <tr>
            <td><div class="pr-card g"><div class="pr-cl">Total Pendapatan Produk</div><div class="pr-cv">Rp {{ number_format($topProducts->sum('total_revenue') ?? 0, 0, ',', '.') }}</div><div class="pr-cd">Dari transaksi lunas</div></div></td>
            <td><div class="pr-card p"><div class="pr-cl">Pendapatan Servis</div><div class="pr-cv">Rp {{ number_format($serviceRevenue ?? 0, 0, ',', '.') }}</div><div class="pr-cd">Perbaikan selesai</div></div></td>
            <td><div class="pr-card a"><div class="pr-cl">Perputaran Stok</div><div class="pr-cv">{{ $inventoryTurnover ?? 0 }}×</div><div class="pr-cd">Stok Keluar ÷ Rata-rata Stok</div></div></td>
            <td><div class="pr-card r"><div class="pr-cl">Stok Tidak Aktif</div><div class="pr-cv">{{ count($deadStockProducts ?? []) }} produk</div><div class="pr-cd">&gt;90 hari tanpa penjualan</div></div></td>
        </tr>
        <tr>
            <td><div class="pr-card"><div class="pr-cl">Stok Masuk</div><div class="pr-cv">{{ number_format($stockIn ?? 0, 0, ',', '.') }} unit</div><div class="pr-cd">Total diterima</div></div></td>
            <td><div class="pr-card r"><div class="pr-cl">Stok Keluar</div><div class="pr-cv">{{ number_format($stockOut ?? 0, 0, ',', '.') }} unit</div><div class="pr-cd">Terjual & terpakai</div></div></td>
            <td><div class="pr-card g"><div class="pr-cl">Nilai Pengadaan</div><div class="pr-cv" style="font-size:10px">Rp {{ number_format($totalProcurementValue ?? 0, 0, ',', '.') }}</div><div class="pr-cd">{{ $procurements->count() }} PO dalam periode</div></div></td>
            <td><div class="pr-card"><div class="pr-cl">Total Produk Aktif</div><div class="pr-cv">{{ count($clusterResultsTagged) }}</div><div class="pr-cd">{{ $totalProductsSold }} terjual, {{ count($clusterResultsTagged) - $totalProductsSold }} tidak</div></div></td>
        </tr>
    </table>
</div>

<hr class="pr-sep">

{{-- ── SECTION 1: TOP PRODUK ────────────────────────────── --}}
<div class="pr-section">
    <div class="pr-sh"><div class="pr-snum">1</div><div><div class="pr-stitle">Top 10 Produk Terlaris</div><div class="pr-ssub">Berdasarkan jumlah unit terjual & kontribusi pendapatan</div></div></div>
    <div id="print-popular-chart-container" style="margin-bottom: 12px; text-align: center;"></div>
    @if(count($topProducts) > 0)
    @php
        $topMaxQty = $topProducts->max('total_qty') ?: 1;
        $totalTopRevPrint = $topProducts->sum('total_revenue') ?: 1;
    @endphp
    <table class="prt">
        <thead><tr>
            <th style="width:22px">#</th>
            <th>Nama Produk</th>
            <th class="r">Qty Terjual</th>
            <th class="r">Pendapatan</th>
            <th class="r">% Kontribusi</th>
            <th class="c" style="width:80px">Performa</th>
        </tr></thead>
        <tbody>
        @foreach($topProducts as $i => $p)
        <tr>
            <td class="c" style="font-weight:bold; color:{{ $i===0?'#f59e0b':($i===1?'#94a3b8':($i===2?'#cd7f32':'#64748b')) }}">{{ $i+1 }}</td>
            <td style="font-weight:{{ $i<3?'bold':'normal' }}">{{ $p->name }}</td>
            <td class="r" style="font-weight:bold">{{ number_format($p->total_qty,0,',','.') }}</td>
            <td class="r">Rp {{ number_format($p->total_revenue,0,',','.') }}</td>
            <td class="r" style="color:#2563EB;font-weight:bold">{{ round(($p->total_revenue/$totalTopRevPrint)*100,1) }}%</td>
            <td class="c">
                @php $pct = min(100, round(($p->total_qty/$topMaxQty)*100)); @endphp
                <div class="pr-bw"><div class="pr-bf {{ $i<3?'g':'a' }}" style="width:{{ $pct }}%"></div></div>
            </td>
        </tr>
        @endforeach
        </tbody>
        <tfoot><tr>
            <td colspan="2" style="text-align:right">TOTAL</td>
            <td class="r">{{ number_format($topProducts->sum('total_qty'),0,',','.') }}</td>
            <td class="r">Rp {{ number_format($topProducts->sum('total_revenue'),0,',','.') }}</td>
            <td colspan="2"></td>
        </tr></tfoot>
    </table>
    @else
    <p style="text-align:center;color:#94a3b8;font-style:italic;padding:10px">Tidak ada data produk terjual dalam periode ini.</p>
    @endif
</div>

<hr class="pr-sep">

{{-- ── SECTION 2: KATEGORI ─────────────────────────────── --}}
<div class="pr-section">
    <div class="pr-sh"><div class="pr-snum">2</div><div><div class="pr-stitle">Kontribusi Penjualan per Kategori</div><div class="pr-ssub">Distribusi pendapatan per kategori produk</div></div></div>
    <div id="print-category-chart-container" style="margin-bottom: 12px; text-align: center;"></div>
    @if(count($categoryBreakdown ?? []) > 0)
    <table class="prt">
        <thead><tr>
            <th>#</th>
            <th>Kategori</th>
            <th class="r">Qty Terjual</th>
            <th class="r">Pendapatan</th>
            <th class="r">% Total</th>
            <th class="c" style="width:80px">Distribusi</th>
        </tr></thead>
        <tbody>
        @foreach($categoryBreakdown as $i => $cat)
        <tr>
            <td class="c">{{ $i+1 }}</td>
            <td style="font-weight:bold">{{ $cat->name }}</td>
            <td class="r">{{ number_format($cat->total_qty,0,',','.') }}</td>
            <td class="r">Rp {{ number_format($cat->total_revenue,0,',','.') }}</td>
            <td class="r" style="color:#2563EB;font-weight:bold">{{ $cat->revenue_pct }}%</td>
            <td class="c"><div class="pr-bw"><div class="pr-bf" style="width:{{ $cat->revenue_pct }}%"></div></div></td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @else
    <p style="text-align:center;color:#94a3b8;font-style:italic;padding:10px">Tidak ada data kategori dalam periode ini.</p>
    @endif
</div>

{{-- ── PAGE BREAK ───────────────────────────────────────── --}}
<div class="pr-pb"></div>

{{-- ── SECTION 3: K-MEANS CLUSTER ─────────────────────── --}}
<div class="pr-section">
    <div class="pr-sh"><div class="pr-snum">3</div><div><div class="pr-stitle">Klasifikasi Pergerakan Stok (K-Means)</div><div class="pr-ssub">Pengelompokan produk berdasarkan frekuensi & volume penjualan</div></div></div>
    <table class="pr-kpi" style="margin-bottom:6px">
        <tr>
            <td><div class="pr-card g"><div class="pr-cl">🔥 Pergerakan Tinggi</div><div class="pr-cv">{{ $highCount }}</div><div class="pr-cd">{{ count($clusterResultsTagged)>0?round(($highCount/count($clusterResultsTagged))*100,1):0 }}% dari total</div></div></td>
            <td><div class="pr-card a"><div class="pr-cl">📊 Pergerakan Sedang</div><div class="pr-cv">{{ $medCount }}</div><div class="pr-cd">{{ count($clusterResultsTagged)>0?round(($medCount/count($clusterResultsTagged))*100,1):0 }}% dari total</div></div></td>
            <td><div class="pr-card r"><div class="pr-cl">❄️ Pergerakan Rendah</div><div class="pr-cv">{{ $lowCount }}</div><div class="pr-cd">{{ count($clusterResultsTagged)>0?round(($lowCount/count($clusterResultsTagged))*100,1):0 }}% dari total</div></div></td>
            <td><div class="pr-card"><div class="pr-cl">📦 Total Produk</div><div class="pr-cv">{{ count($clusterResultsTagged) }}</div><div class="pr-cd">Semua produk aktif</div></div></td>
        </tr>
    </table>
    <div class="pr-alert info" style="margin-bottom:6px"><strong>Metode:</strong> Produk diklasifikasikan berdasarkan % penjualan relatif terhadap produk terlaris: Tinggi (≥50%), Sedang (15–49%), Rendah (&lt;15%).</div>

    @if(count($clusterResultsTagged) > 0)
    <table class="prt">
        <thead><tr>
            <th style="width:20px">#</th>
            <th>Nama Produk</th>
            <th class="r">Stok Saat Ini</th>
            <th class="r">Qty Terjual</th>
            <th class="r">Pendapatan</th>
            <th class="r">Frekuensi</th>
            <th class="c">Cluster</th>
        </tr></thead>
        <tbody>
        @foreach($clusterResultsTagged as $i => $cr)
        @php
            $clBadge = match($cr['cluster']) { 'high'=>'pb-h','medium'=>'pb-m',default=>'pb-l' };
            $clLabel = match($cr['cluster']) { 'high'=>(__('messages.high')??'Tinggi'),'medium'=>(__('messages.medium')??'Sedang'),default=>(__('messages.low')??'Rendah') };
        @endphp
        <tr>
            <td class="c" style="color:#94a3b8;font-size:7px">{{ $i+1 }}</td>
            <td>{{ $cr['product_name'] }}</td>
            <td class="r">{{ number_format($cr['current_stock']??0,0,',','.') }}</td>
            <td class="r" style="font-weight:bold">{{ number_format($cr['total_qty_sold'],0,',','.') }}</td>
            <td class="r">Rp {{ number_format($cr['total_revenue'],0,',','.') }}</td>
            <td class="r">{{ $cr['transaction_frequency'] }}×</td>
            <td class="c"><span class="pb {{ $clBadge }}">{{ $clLabel }}</span></td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @else
    <p style="text-align:center;color:#94a3b8;font-style:italic;padding:10px">Tidak ada data produk.</p>
    @endif
</div>

{{-- ── PAGE BREAK ───────────────────────────────────────── --}}
<div class="pr-pb"></div>

{{-- ── SECTION 4: SMA RESTOCK ──────────────────────────── --}}
<div class="pr-section">
    <div class="pr-sh"><div class="pr-snum">4</div><div><div class="pr-stitle">Rekomendasi Restock (Simple Moving Average)</div><div class="pr-ssub">Produk yang diprediksi membutuhkan restok berdasarkan tren penjualan</div></div></div>
    @if(count($smaResults ?? []) > 0)
    <div class="pr-alert" style="margin-bottom:6px">⚠️ <strong>Perhatian:</strong> {{ count($smaResults) }} produk membutuhkan restok segera berdasarkan analisis SMA.</div>
    <table class="prt">
        <thead><tr>
            <th>#</th>
            <th>Nama Produk</th>
            <th class="r">Stok Saat Ini</th>
            <th class="r">Rata-rata Jual/Hari</th>
            <th class="r">Rekomendasi Restock</th>
            <th class="c">Status</th>
        </tr></thead>
        <tbody>
        @foreach($smaResults as $i => $sma)
        <tr>
            <td class="c" style="color:#94a3b8;font-size:7px">{{ $i+1 }}</td>
            <td style="font-weight:bold">{{ $sma['product_name']??'-' }}</td>
            <td class="r">{{ number_format($sma['current_stock']??0,0,',','.') }}</td>
            <td class="r">{{ number_format($sma['sma_value']??0,1,',','.') }}</td>
            <td class="r" style="color:#d97706;font-weight:bold">{{ number_format($sma['restock_qty']??0,0,',','.') }}</td>
            <td class="c"><span class="pb pb-rst">Perlu Restock</span></td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @else
    <div class="pr-alert info">✅ Semua produk berada dalam kondisi stok yang cukup. Tidak ada rekomendasi restock.</div>
    @endif
</div>

<hr class="pr-sep">

{{-- ── SECTION 5: PENGADAAN ────────────────────────────── --}}
<div class="pr-section">
    <div class="pr-sh"><div class="pr-snum">5</div><div><div class="pr-stitle">Ringkasan Pengadaan (Purchase Order)</div><div class="pr-ssub">Data seluruh PO dalam periode laporan</div></div></div>
    <div id="print-procurement-chart-container" style="margin-bottom: 12px; text-align: center;"></div>
    <table class="pr-kpi" style="margin-bottom:6px">
        <tr>
            <td><div class="pr-card"><div class="pr-cl">Total PO</div><div class="pr-cv">{{ $procurements->count() }}</div><div class="pr-cd">Purchase order</div></div></td>
            <td><div class="pr-card g"><div class="pr-cl">Diterima</div><div class="pr-cv">{{ $receivedProcurements }}</div><div class="pr-cd">PO selesai diterima</div></div></td>
            <td><div class="pr-card a"><div class="pr-cl">Dalam Proses</div><div class="pr-cv">{{ $pendingProcurements }}</div><div class="pr-cd">Draft/Ordered/Sebagian</div></div></td>
            <td><div class="pr-card"><div class="pr-cl">Total Nilai</div><div class="pr-cv" style="font-size:10px">Rp {{ number_format($totalProcurementValue,0,',','.') }}</div><div class="pr-cd">Nilai seluruh PO</div></div></td>
        </tr>
    </table>
    @if($procurements->count() > 0)
    <table class="prt">
        <thead><tr>
            <th>#</th>
            <th>Kode PO</th>
            <th>Tanggal</th>
            <th>Dibuat Oleh</th>
            <th class="c">Status</th>
            <th class="r">Total Nilai</th>
        </tr></thead>
        <tbody>
        @foreach($procurements as $i => $po)
        @php
            $poBadge = match($po->status) {
                'received'        => 'pb-rec',
                'ordered'         => 'pb-ord',
                'partial_received'=> 'pb-pen',
                'cancelled'       => 'pb-can',
                default           => 'pb-dft',
            };
            $poLabel = strtoupper($printStatusLabels[$po->status] ?? $po->status);
        @endphp
        <tr>
            <td class="c" style="color:#94a3b8;font-size:7px">{{ $i+1 }}</td>
            <td style="font-family:monospace;font-size:8px;color:#2563EB;font-weight:bold">{{ $po->product_purchase_code }}</td>
            <td>{{ $po->purchase_date?->format('d/m/Y') ?? '-' }}</td>
            <td>{{ $po->creator->username ?? '-' }}</td>
            <td class="c"><span class="pb {{ $poBadge }}" style="font-size:6.5px">{{ $poLabel }}</span></td>
            <td class="r" style="font-weight:bold">Rp {{ number_format($po->total,0,',','.') }}</td>
        </tr>
        @endforeach
        </tbody>
        <tfoot><tr>
            <td colspan="5" style="text-align:right;font-weight:bold">TOTAL NILAI PENGADAAN</td>
            <td class="r" style="font-weight:bold;color:#2563EB">Rp {{ number_format($totalProcurementValue,0,',','.') }}</td>
        </tr></tfoot>
    </table>
    @else
    <p style="text-align:center;color:#94a3b8;font-style:italic;padding:10px">Tidak ada data pengadaan dalam periode ini.</p>
    @endif
</div>

{{-- ── PAGE BREAK ───────────────────────────────────────── --}}
<div class="pr-pb"></div>

{{-- ── SECTION 6: DEAD STOCK ───────────────────────────── --}}
<div class="pr-section">
    <div class="pr-sh"><div class="pr-snum">6</div><div><div class="pr-stitle">Peringatan Stok Tidak Aktif (Dead Stock)</div><div class="pr-ssub">Produk aktif yang tidak ada penjualan dalam 90 hari terakhir</div></div></div>
    @if(count($deadStockProducts ?? []) > 0)
    <div class="pr-alert danger" style="margin-bottom:6px">⚠️ <strong>Perlu Tindakan:</strong> {{ count($deadStockProducts) }} produk tidak terjual dalam 90 hari. Pertimbangkan promosi, bundling, atau penyesuaian harga.</div>
    <table class="prt">
        <thead><tr>
            <th>#</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th class="r">Stok Saat Ini</th>
            <th class="c">Rekomendasi</th>
        </tr></thead>
        <tbody>
        @foreach($deadStockProducts as $i => $dp)
        <tr>
            <td class="c" style="color:#94a3b8;font-size:7px">{{ $i+1 }}</td>
            <td style="font-weight:bold">{{ $dp->name }}</td>
            <td style="color:#64748b">{{ $dp->category->name ?? '-' }}</td>
            <td class="r" style="color:{{ $dp->stock===0?'#94a3b8':'#ef4444' }};font-weight:bold">{{ number_format($dp->stock,0,',','.') }}</td>
            <td class="c">
                @if($dp->stock === 0)
                <span class="pb pb-rec">Habis</span>
                @else
                <span class="pb pb-dead">Promosi/Diskon</span>
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @else
    <div class="pr-alert info">✅ Tidak ada produk dead stock dalam periode ini. Semua produk aktif terjual.</div>
    @endif
</div>

{{-- ── FOOTER ───────────────────────────────────────────── --}}
<div class="pr-footer">
    <span>{{ $storeName }} · Laporan Perputaran Stok Produk</span>
    <span>Periode: {{ $startDate->format('d M Y') }} – {{ $endDate->format('d M Y') }} · Kategori: {{ $categoryName }}</span>
    <span>Dicetak: {{ now()->format('d M Y H:i') }}</span>
</div>
</div>{{-- end print-only --}}

<form id="pdf-export-form" method="POST" action="{{ route('reports.business_performance.export_pdf') }}" style="display:none;">
    @csrf
    <input type="hidden" name="start_date" id="pdf_start_date">
    <input type="hidden" name="end_date" id="pdf_end_date">
    <input type="hidden" name="category" id="pdf_category">
    <input type="hidden" name="limit" id="pdf_limit">
    <input type="hidden" name="chart_popular" id="pdf_chart_popular">
    <input type="hidden" name="chart_category" id="pdf_chart_category">
    <input type="hidden" name="chart_procurement" id="pdf_chart_procurement">
</form>

@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function updateLimit(val) {
    document.getElementById('limit-input').value = val;
    document.getElementById('bi-filter-form').submit();
}

window.appCharts = {};

const PALETTE = ['#2563EB','#10B981','#F59E0B','#6366F1','#F43F5E','#06B6D4','#8B5CF6','#14B8A6','#F97316','#EC4899'];

// ── Popular Products Chart ────────────────────────────────────────────
window.rawBiData = {
    clusterResults: @json(array_slice($clusterResultsTagged, 0, 20)),
    categories: @json($categoryBreakdown ?? [])
};

window.updatePopularChart = function(type, metric) {
    const ctx = document.getElementById('biPopularChart');
    if (!ctx) return;
    if (window.appCharts.popular) window.appCharts.popular.destroy();

    const data = window.rawBiData.clusterResults.filter(p => (p.total_qty_sold || 0) > 0).slice(0, 10);
    const isRadial = type === 'doughnut' || type === 'pie';
    const valueKey = metric === 'qty' ? 'total_qty_sold' : 'total_revenue';
    const labelStr = metric === 'qty' ? 'Unit Terjual' : 'Total Pendapatan';

    const ds = {
        labels: data.map(p => p.product_name),
        datasets: [{
            label: labelStr,
            data: data.map(p => p[valueKey] || 0),
            backgroundColor: isRadial ? PALETTE : PALETTE.slice(0, data.length),
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
            backgroundColor: isRadial ? PALETTE : dates.map((_, i) => PALETTE[i % PALETTE.length]),
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
            backgroundColor: window.rawBiData.categories.map((_, idx) => PALETTE[idx % PALETTE.length]),
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

function downloadPdf() {
    document.getElementById('pdf_start_date').value = document.getElementById('bi-start-date').value;
    document.getElementById('pdf_end_date').value = document.getElementById('bi-end-date').value;
    document.getElementById('pdf_category').value = document.getElementById('category-select').value;
    document.getElementById('pdf_limit').value = document.getElementById('limit-input').value;
    
    const popularChart = document.getElementById('biPopularChart');
    if (popularChart) document.getElementById('pdf_chart_popular').value = popularChart.toDataURL('image/png');
    
    const categoryChart = document.getElementById('categoryDonutChart');
    if (categoryChart) document.getElementById('pdf_chart_category').value = categoryChart.toDataURL('image/png');
    
    const procurementChart = document.getElementById('biProcurementChart');
    if (procurementChart) document.getElementById('pdf_chart_procurement').value = procurementChart.toDataURL('image/png');
    
    document.getElementById('pdf-export-form').submit();
}

window.addEventListener('beforeprint', () => {
    // Inject canvas images into print containers before browser triggers print dialog
    const popularChart = document.getElementById('biPopularChart');
    if (popularChart) {
        document.getElementById('print-popular-chart-container').innerHTML = '<img src="' + popularChart.toDataURL('image/png') + '" style="max-height: 180px; width: auto; margin: 0 auto; display: block;">';
    }
    
    const categoryChart = document.getElementById('categoryDonutChart');
    if (categoryChart) {
        document.getElementById('print-category-chart-container').innerHTML = '<img src="' + categoryChart.toDataURL('image/png') + '" style="max-height: 180px; width: auto; margin: 0 auto; display: block;">';
    }
    
    const procurementChart = document.getElementById('biProcurementChart');
    if (procurementChart) {
        document.getElementById('print-procurement-chart-container').innerHTML = '<img src="' + procurementChart.toDataURL('image/png') + '" style="max-height: 180px; width: auto; margin: 0 auto; display: block;">';
    }
});
</script>
@endpush
