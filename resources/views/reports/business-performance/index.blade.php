@extends('layouts.app')
@section('page-title', 'Ringkasan Analisis Bisnis')
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
                Ringkasan Analisis Bisnis
            </h1>
            <p class="type-caption text-[var(--color-slate)] mt-2">Analisis pergerakan produk dan Rekomendasi pengisian ulang stok · Periode {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} <b>-</b> {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
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
{{-- SECTION 2: K-MEANS CLUSTERING                           --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="mb-6 card-feature p-5">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="type-heading-sm text-[var(--color-ink-deep)] flex items-center gap-2">
                <svg class="w-6 h-6 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                Analisis Pergerakan Produk
            </h2>
            <p class="type-caption text-[var(--color-slate)] mt-1">Klasifikasi otomatis menggunakan K-Means Clustering (k=3)</p>
        </div>
    </div>
</div>

{{-- Cluster KPI --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    @php
        $clusterCards = [
            ['label' => __('Sangat Laris (Fast)'), 'key' => 'fast_moving', 'color' => '#10B981', 'icon' => '<svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>', 'desc' => 'Prioritaskan reorder'],
            ['label' => __('Cukup Laris (Medium)'), 'key' => 'medium_moving', 'color' => '#F59E0B', 'icon' => '<svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>', 'desc' => 'Monitor tren'],
            ['label' => __('Kurang Laris (Slow)'), 'key' => 'slow_moving', 'color' => '#F97316', 'icon' => '<svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>', 'desc' => 'Pertimbangkan promosi'],
            ['label' => __('Stok Mati (Dead)'), 'key' => 'dead_stock', 'color' => '#EF4444', 'icon' => '<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>', 'desc' => 'Pertimbangkan clearance'],
        ];
    @endphp
    @foreach($clusterCards as $cc)
    <div class="card-feature p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 rounded-t-[var(--radius-xxxl)]" style="background: {{ $cc['color'] }}"></div>
        <div class="mb-2">{!! $cc['icon'] !!}</div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">{{ $cc['label'] }}</p>
        <p class="mt-1 type-heading-sm text-[var(--color-ink-deep)]">{{ $clusterSummary[$cc['key']] }} <span class="type-body-sm font-normal text-[var(--color-slate)]">produk</span></p>
        <p class="mt-1 type-caption text-[var(--color-stone)]">{{ $cc['desc'] }}</p>
    </div>
    @endforeach
</div>

<div class="card-feature mb-6 flex flex-col min-h-[500px]" x-data="{ view: 'chart', clusterFilter: 'all', chartType: 'doughnut', metric: 'count' }" x-init="$watch('chartType', val => updateClusterChart(val, metric)); $watch('metric', val => updateClusterChart(chartType, val))">
    <div class="flex flex-wrap items-center justify-between gap-3 p-5 border-b border-[var(--color-hairline-soft)]">
        <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]">Detail Klasifikasi Pergerakan Produk</h3>
        <div class="flex gap-2 mt-2 md:mt-0 w-full sm:w-auto">
            <button @click="view = 'chart'" :class="view === 'chart' ? 'pill-tab-active' : ''" class="pill-tab flex-1 sm:flex-none !text-xs">{{ __('Grafik') }}</button>
            <button @click="view = 'table'" :class="view === 'table' ? 'pill-tab-active' : ''" class="pill-tab flex-1 sm:flex-none !text-xs">{{ __('Tabel') }}</button>
        </div>
    </div>
    <div x-show="view === 'chart'" class="p-5 flex-1 grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
        <div>
            <div class="flex gap-4 mb-4 justify-center">
                <select x-model="chartType" class="input-field w-full md:w-auto text-xs !py-1.5">
                    <option value="doughnut">Doughnut Chart</option>
                    <option value="pie">Pie Chart</option>
                    <option value="bar">Bar Chart</option>
                </select>
                <select x-model="metric" class="input-field w-full md:w-auto text-xs !py-1.5">
                    <option value="count">Jumlah Produk</option>
                    <option value="qty">Unit Terjual (Qty)</option>
                    <option value="frequency">Frekuensi Penjualan</option>
                </select>
            </div>
            <div class="flex-1 min-h-[300px] relative">
                <canvas id="biClusterDonut"></canvas>
            </div>
        </div>
        <div class="space-y-3">
            <div class="rounded-[var(--radius-xl)] p-4 bg-[var(--color-surface-soft)] type-caption text-[var(--color-slate)]">
                <p class="type-body-sm-bold text-[var(--color-ink)] mb-2">Cara Kami Mengelompokkan Produk</p>
                <p>Sistem menganalisis produk Anda berdasarkan:</p>
                <ul class="list-disc pl-4 mt-1 space-y-1">
                    <li>Total unit yang terjual</li>
                    <li>Seberapa sering transaksi terjadi</li>
                    <li>Sudah berapa lama sejak penjualan terakhir</li>
                </ul>
                <p class="mt-2">Produk yang tidak terjual lebih dari 3 bulan otomatis masuk kategori "Stok Mati". Produk lainnya dikelompokkan secara cerdas agar Anda fokus pada barang yang paling menguntungkan.</p>
            </div>
        </div>
    </div>
    <div x-show="view === 'table'" x-cloak class="flex-1 flex flex-col">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 border-b border-[var(--color-hairline-soft)]">
            <div class="flex gap-2 overflow-x-auto pb-2 w-full" style="scrollbar-width: none; -ms-overflow-style: none;">
                <button @click="clusterFilter = 'all'" :class="clusterFilter === 'all' ? 'pill-tab-active' : ''" class="pill-tab whitespace-nowrap shrink-0 !text-xs">{{ __('Semua') }}</button>
                <button @click="clusterFilter = 'fast_moving'" :class="clusterFilter === 'fast_moving' ? 'pill-tab-active' : ''" class="pill-tab whitespace-nowrap shrink-0 !text-xs"><svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>{{ __('Sangat Laris') }}</button>
                <button @click="clusterFilter = 'medium_moving'" :class="clusterFilter === 'medium_moving' ? 'pill-tab-active' : ''" class="pill-tab whitespace-nowrap shrink-0 !text-xs"><svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>{{ __('Cukup Laris') }}</button>
                <button @click="clusterFilter = 'slow_moving'" :class="clusterFilter === 'slow_moving' ? 'pill-tab-active' : ''" class="pill-tab whitespace-nowrap shrink-0 !text-xs"><svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>{{ __('Kurang Laris') }}</button>
                <button @click="clusterFilter = 'dead_stock'" :class="clusterFilter === 'dead_stock' ? 'pill-tab-active' : ''" class="pill-tab whitespace-nowrap shrink-0 !text-xs"><svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ __('Stok Mati') }}</button>
            </div>
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
                    <th class="px-4 py-3 text-left table-header">Nama Produk</th>
                    <th class="px-4 py-3 text-right table-header">Jumlah Terjual</th>
                    <th class="px-4 py-3 text-right table-header">Frekuensi Terjual</th>
                    <th class="px-4 py-3 text-right table-header">Hari Tanpa Penjualan</th>
                    <th class="px-4 py-3 text-center table-header">Klasifikasi Pergerakan Produk</th>
                    <th class="px-4 py-3 table-header">Rekomendasi</th>
                </tr></thead>
                <tbody>
                @php
                    $displayedClusters = $limit === 'all' ? $clusterResults : array_slice($clusterResults, 0, (int)$limit);
                @endphp
                @foreach($displayedClusters as $cr)
                @php
                    $clusterBadgeColor = match($cr['cluster_label']) {
                        'fast_moving'   => 'background:#ECFDF5;color:#059669',
                        'medium_moving' => 'background:#FFFBEB;color:#D97706',
                        'slow_moving'   => 'background:#FFF7ED;color:#EA580C',
                        'dead_stock'    => 'background:#FEF2F2;color:#DC2626',
                        default         => '',
                    };
                    $clusterText = match($cr['cluster_label']) {
                        'fast_moving'   => '<svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>' . __('Sangat Laris'),
                        'medium_moving' => '<svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>' . __('Cukup Laris'),
                        'slow_moving'   => '<svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>' . __('Kurang Laris'),
                        'dead_stock'    => '<svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' . __('Stok Mati'),
                        default         => $cr['cluster_label'],
                    };
                @endphp
                <tr onclick="window.location='{{ route('products.show', $cr['product_code']) }}'" class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] cursor-pointer"
                    x-show="clusterFilter === 'all' || clusterFilter === '{{ $cr['cluster_label'] }}'">
                    <td class="px-4 py-2.5 type-body-sm font-medium text-[var(--color-ink)]">{{ $cr['product_name'] }}</td>
                    <td class="px-4 py-2.5 type-body-sm text-right text-[var(--color-slate)]">{{ $cr['total_qty_sold'] }}</td>
                    <td class="px-4 py-2.5 type-body-sm text-right text-[var(--color-slate)]">{{ $cr['transaction_frequency'] }}</td>
                    <td class="px-4 py-2.5 type-body-sm text-right text-[var(--color-slate)]">{{ $cr['days_without_sale'] ?? '-' }}</td>
                    <td class="px-4 py-2.5 text-center">
                        <span class="badge inline-flex items-center" style="{{ $clusterBadgeColor }}; font-size:11px;">{!! $clusterText !!}</span>
                    </td>
                    <td class="px-4 py-2.5 type-caption text-[var(--color-slate)]" style="max-width:200px">{{ $cr['recommendation'] ?? '-' }}</td>
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
{{-- SECTION 3: REKOMENDASI RESTOCK (SMA)                   --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="mb-6 card-feature p-5">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="type-heading-sm text-[var(--color-ink-deep)] flex items-center gap-2">
                <svg class="w-6 h-6 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                {{ __('Rekomendasi Pengisian Ulang Stok') }}
            </h2>
            <p class="type-caption text-[var(--color-slate)] mt-1">{{ __('Perhitungan otomatis memperkirakan jumlah stok yang perlu ditambah agar cukup untuk memenuhi permintaan selama :days hari ke depan.', ['days' => $diffDays]) }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-2 gap-4 mb-5">
    <div class="card-feature p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-red-500 to-red-300 rounded-t-[var(--radius-xxxl)]"></div>
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">{{ __('Perlu Isi Ulang Stok') }}</p>
        </div>
        <p class="mt-1 type-heading-sm {{ $restockNeeded > 0 ? 'text-[var(--color-critical)]' : 'text-[var(--color-success)]' }}">{{ $restockNeeded }} <span class="type-body-sm font-normal text-[var(--color-slate)]">{{ __('produk') }}</span></p>
    </div>
    <div class="card-feature p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-emerald-500 to-emerald-300 rounded-t-[var(--radius-xxxl)]"></div>
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">{{ __('Tidak Perlu Isi Ulang Stok') }}</p>
        </div>
        <p class="mt-1 type-heading-sm {{ $safeStock > 0 ? 'text-[var(--color-success)]' : 'text-[var(--color-slate)]' }}">{{ $safeStock }} <span class="type-body-sm font-normal text-[var(--color-slate)]">{{ __('produk') }}</span></p>
    </div>
</div>

<div class="card-feature mb-6 flex flex-col min-h-[500px]" x-data="{ view: 'chart', smaFilter: 'all', chartType: 'doughnut', metric: 'status' }" x-init="$watch('view', val => { if(val === 'chart') setTimeout(() => window.dispatchEvent(new Event('resize')), 50) }); $watch('chartType', val => updateRestockChart(val, metric)); $watch('metric', val => updateRestockChart(chartType, val))">
        <div class="flex flex-wrap items-center justify-between gap-3 p-5 border-b border-[var(--color-hairline-soft)]">
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]">{{ __('Detail Estimasi Pengisian Ulang Stok') }}</h3>
            <div class="flex gap-2 mt-2 md:mt-0 w-full sm:w-auto">
                <button @click="view = 'chart'" :class="view === 'chart' ? 'pill-tab-active' : ''" class="pill-tab flex-1 sm:flex-none !text-xs">{{ __('Grafik') }}</button>
                <button @click="view = 'table'" :class="view === 'table' ? 'pill-tab-active' : ''" class="pill-tab flex-1 sm:flex-none !text-xs">{{ __('Tabel') }}</button>
            </div>
        </div>
    <div x-show="view === 'chart'" class="p-5 flex-1 flex flex-col">
        <div class="flex flex-col sm:flex-row gap-4 mb-4">
            <select x-model="chartType" class="input-field w-full md:w-auto text-xs !py-1.5">
                <option value="doughnut">Doughnut Chart</option>
                <option value="pie">Pie Chart</option>
                <option value="bar">Bar Chart</option>
            </select>
            <select x-model="metric" class="input-field w-full md:w-auto text-xs !py-1.5">
                <option value="status">Status (Jumlah Produk)</option>
                <option value="units">Saran Tambah (Jumlah Unit)</option>
            </select>
        </div>
        <div class="flex-1 min-h-[300px] relative">
            <canvas id="biRestockChart"></canvas>
        </div>
    </div>
    <div x-show="view === 'table'" x-cloak class="flex-1 flex flex-col">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 border-b border-[var(--color-hairline-soft)]">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
                <span class="type-caption text-[var(--color-slate)]">Filter:</span>
                <select x-model="smaFilter" class="input-field !py-1 !px-2 w-full sm:!w-auto text-sm">
                    <option value="all">{{ __('Semua Produk') }}</option>
                    <option value="restock">{{ __('Perlu Isi Ulang Stok') }}</option>
                    <option value="ok">{{ __('Tidak Perlu Isi Ulang Stok') }}</option>
                </select>
            </div>
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
                <th class="px-4 py-3 text-left table-header">Produk</th>
                <th class="px-4 py-3 text-right table-header">{{ __('Rata-rata Terjual / Hari') }}</th>
                <th class="px-4 py-3 text-right table-header">{{ __('Stok Tersedia') }}</th>
                <th class="px-4 py-3 text-right table-header">{{ __('Estimasi Kebutuhan (:days Hari)', ['days' => $diffDays]) }}</th>
                <th class="px-4 py-3 text-right table-header">{{ __('Perkiraan Stok Habis') }}</th>
                <th class="px-4 py-3 text-right table-header">{{ __('Saran Tambah Stok') }}</th>
                <th class="px-4 py-3 text-center table-header">Status</th>
            </tr></thead>
            <tbody>
            @php
                $displayedSma = $limit === 'all' ? $smaResults : array_slice($smaResults, 0, (int)$limit);
            @endphp
            @foreach($displayedSma as $sr)
            <tr onclick="window.location='{{ route('products.show', $sr['product_code']) }}'" class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] cursor-pointer"
                x-show="smaFilter === 'all' || (smaFilter === 'restock' && {{ $sr['needs_restock'] ? 'true' : 'false' }}) || (smaFilter === 'ok' && {{ !$sr['needs_restock'] ? 'true' : 'false' }})">
                <td class="px-4 py-2.5 type-body-sm font-medium text-[var(--color-ink)]">{{ $sr['product_name'] }}</td>
                <td class="px-4 py-2.5 type-body-sm text-right text-[var(--color-slate)]">{{ number_format($sr['sma_daily'], 2, ',', '.') }} <span class="text-[10px]">unit</span></td>
                <td class="px-4 py-2.5 type-body-sm text-right {{ $sr['current_stock'] <= $sr['minimum_stock'] ? 'text-[var(--color-critical)] font-bold' : 'text-[var(--color-ink)]' }}">{{ number_format($sr['current_stock'], 0, ',', '.') }} <span class="text-[10px]">unit</span></td>
                <td class="px-4 py-2.5 type-body-sm text-right text-[var(--color-slate)]">{{ number_format($sr['predicted_demand_14d'], 0, ',', '.') }} <span class="text-[10px]">unit</span></td>
                <td class="px-4 py-2.5 text-right">
                    @php
                        $dsr = $sr['days_of_stock_remaining'];
                        $dsrStyle = $dsr <= 7 ? 'color:var(--color-critical); font-weight:700' : ($dsr <= 14 ? 'color:#D97706' : 'color:var(--color-success)');
                    @endphp
                    <span class="type-body-sm" style="{{ $dsrStyle }}">{{ $dsr >= 999 ? '∞' : $dsr . ' hari' }}</span>
                </td>
                <td class="px-4 py-2.5 text-right">
                    @if($sr['restock_recommendation'] > 0)
                        <span class="badge badge-neutral">+{{ $sr['restock_recommendation'] }} unit</span>
                    @else
                        <span class="type-caption text-[var(--color-stone)]">-</span>
                    @endif
                </td>
                <td class="px-4 py-2.5 text-center">
                    @if($sr['needs_restock'])
                        <span class="badge badge-critical">{{ __('Perlu Isi Ulang Stok') }}</span>
                    @else
                        <span class="badge badge-success">{{ __('Tidak Perlu Isi Ulang Stok') }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>

        @if($limit !== 'all')
        <div class="p-4 border-t border-[var(--color-hairline-soft)] text-center mt-auto">
            <a href="{{ route('reports.business_performance.sma', request()->all()) }}" class="bg-[var(--color-ink)] text-white hover:bg-[var(--color-ink-deep)] transition-colors rounded-[var(--radius-full)] font-medium type-body-sm !py-2 !px-6 inline-flex items-center gap-2">
                Lihat Keseluruhan Data
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
        @endif
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
window.rawBiData = {
    clusterSummary: @json($clusterSummary),
    clusterResults: @json($clusterResults),
    smaData: @json($smaResults)
};

const CLUSTER_COLORS = { fast_moving: '#10B981', medium_moving: '#F59E0B', slow_moving: '#F97316', dead_stock: '#EF4444', new_product: '#3B82F6' };
const CLUSTER_LABELS = { fast_moving: 'Sangat Laris', medium_moving: 'Cukup Laris', slow_moving: 'Kurang Laris', dead_stock: 'Stok Mati', new_product: 'Produk Baru' };
const KEYS = Object.keys(window.rawBiData.clusterSummary);

window.updateClusterChart = function(type, metric) {
    const ctx = document.getElementById('biClusterDonut');
    if (!ctx) return;
    if (window.appCharts.cluster) window.appCharts.cluster.destroy();
    
    const isRadial = type === 'doughnut' || type === 'pie';
    
    let chartData = [];
    let datasetLabel = 'Jumlah Produk';
    
    if (metric === 'count') {
        chartData = KEYS.map(k => window.rawBiData.clusterSummary[k] || 0);
        datasetLabel = 'Jumlah Produk';
    } else if (metric === 'qty') {
        chartData = KEYS.map(k => {
            return window.rawBiData.clusterResults
                .filter(p => p.cluster_label === k)
                .reduce((sum, p) => sum + (p.total_qty_sold || 0), 0);
        });
        datasetLabel = 'Unit Terjual (Qty)';
    } else if (metric === 'frequency') {
        chartData = KEYS.map(k => {
            return window.rawBiData.clusterResults
                .filter(p => p.cluster_label === k)
                .reduce((sum, p) => sum + (p.transaction_frequency || 0), 0);
        });
        datasetLabel = 'Frekuensi Penjualan';
    }
    
    const ds = {
        labels: KEYS.map(k => CLUSTER_LABELS[k] || k),
        datasets: [{
            label: datasetLabel,
            data: chartData,
            backgroundColor: KEYS.map(k => CLUSTER_COLORS[k] || '#94A3B8'),
            borderWidth: isRadial ? 2 : 0,
            borderRadius: isRadial ? 0 : 6,
            borderColor: isRadial ? '#fff' : 'transparent'
        }]
    };
    
    const options = {
        responsive: true, maintainAspectRatio: false, cutout: type === 'doughnut' ? '60%' : 0,
        plugins: {
            legend: { display: isRadial, position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12 } },
            tooltip: { callbacks: { label: ctx => `${ctx.label}: ${ctx.raw} ${metric === 'count' ? 'produk' : (metric === 'qty' ? 'unit' : 'kali')}` } }
        },
        scales: isRadial ? { x: { display: false }, y: { display: false } } : {
            y: { beginAtZero: true, grid: { color: 'rgba(148,163,184,0.12)' }, ticks: { color: '#94A3B8' } },
            x: { grid: { display: false }, ticks: { color: '#94A3B8', font: { size: 10 } } }
        }
    };
    
    window.appCharts.cluster = new Chart(ctx, { type: type, data: ds, options: options });
};

window.updateRestockChart = function(type, metric) {
    const ctx = document.getElementById('biRestockChart');
    if (!ctx || !window.rawBiData.smaData.length) return;
    if (window.appCharts.restock) window.appCharts.restock.destroy();
    
    const isStatus = metric === 'status';
    const isRadial = type === 'doughnut' || type === 'pie';
    
    let labels = [];
    let datasets = [];
    
    if (isStatus) {
        const needsRestockCount = window.rawBiData.smaData.filter(d => d.needs_restock).length;
        const safeCount = window.rawBiData.smaData.length - needsRestockCount;
        
        labels = ['Perlu Isi Ulang Stok', 'Tidak Perlu Isi Ulang Stok'];
        datasets = [{
            label: 'Jumlah Produk',
            data: [needsRestockCount, safeCount],
            backgroundColor: ['#EF4444', '#10B981'],
            borderWidth: isRadial ? 2 : 0,
            borderRadius: isRadial ? 0 : 6,
            borderColor: isRadial ? '#fff' : 'transparent'
        }];
    } else {
        // units (Saran Tambah)
        const productsNeedRestock = window.rawBiData.smaData
            .filter(d => d.needs_restock && d.restock_recommendation > 0)
            .sort((a, b) => b.restock_recommendation - a.restock_recommendation)
            .slice(0, 20); // Top 20
            
        labels = productsNeedRestock.map(p => p.product_name);
        datasets = [{
            label: 'Saran Tambah (Unit)',
            data: productsNeedRestock.map(p => p.restock_recommendation),
            backgroundColor: '#F59E0B',
            borderWidth: isRadial ? 2 : 0,
            borderRadius: isRadial ? 0 : 6,
            borderColor: isRadial ? '#fff' : 'transparent'
        }];
    }
    
    const options = {
        responsive: true, maintainAspectRatio: false, cutout: type === 'doughnut' ? '60%' : 0,
        plugins: {
            legend: { display: isRadial, position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12 } },
            tooltip: { callbacks: { label: ctx => `${ctx.label}: ${ctx.raw} ${isStatus ? 'produk' : 'unit'}` } }
        },
        scales: isRadial ? { x: { display: false }, y: { display: false } } : {
            y: { beginAtZero: true, grid: { color: 'rgba(148,163,184,0.12)' }, ticks: { color: '#94A3B8' } },
            x: { grid: { display: false }, ticks: { color: '#94A3B8', font: { size: 10 } } }
        }
    };
    
    window.appCharts.restock = new Chart(ctx, { 
        type: type, 
        data: {
            labels: labels,
            datasets: datasets
        }, 
        options: options 
    });
};

document.addEventListener('DOMContentLoaded', function () {
    window.updateClusterChart('doughnut', 'count');
    window.updateRestockChart('doughnut', 'status');
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
