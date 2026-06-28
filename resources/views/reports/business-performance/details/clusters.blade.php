@extends('layouts.app')
@section('page-title', 'Detail Klasifikasi Produk')
@section('content')

<div class="mx-auto max-w-6xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="javascript:void(0)" onclick="if(window.history.length > 2) { window.history.back(); } else { window.location.href = '{{ route('reports.business_performance') }}'; }" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('Kembali') }}</a>
    </div>

    <div class="card-feature mb-6">
        <div class="p-5 border-b border-[var(--color-hairline-soft)] flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="type-subtitle-lg text-[var(--color-ink-deep)]">Seluruh Data Klasterisasi Produk (K-Means)</h2>
                <p class="type-caption text-[var(--color-slate)] mt-1">Pembagian kategori pergerakan stok berdasarkan Qty terjual</p>
            </div>
            <form method="GET" action="{{ route('reports.business_performance.clusters') }}" class="flex flex-col md:flex-row md:items-center gap-3 w-full">
                <select name="cluster_filter" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                    <option value="all" {{ request('cluster_filter') == 'all' ? 'selected' : '' }}>Semua Klasifikasi</option>
                    <option value="fast_moving" {{ request('cluster_filter') == 'fast_moving' ? 'selected' : '' }}>Sangat Laris</option>
                    <option value="medium_moving" {{ request('cluster_filter') == 'medium_moving' ? 'selected' : '' }}>Cukup Laris</option>
                    <option value="slow_moving" {{ request('cluster_filter') == 'slow_moving' ? 'selected' : '' }}>Kurang Laris</option>
                    <option value="dead_stock" {{ request('cluster_filter') == 'dead_stock' ? 'selected' : '' }}>Stok Mati</option>
                </select>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                <span class="text-[var(--color-slate)] text-xs">sampai</span>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="bg-[var(--color-surface-soft)]">
                    <th class="px-4 py-3 text-left table-header">Nama Produk</th>
                    <th class="px-4 py-3 text-right table-header">Jumlah Terjual</th>
                    <th class="px-4 py-3 text-right table-header">Frekuensi Terjual</th>
                    <th class="px-4 py-3 text-right table-header">Hari Tanpa Penjualan</th>
                    <th class="px-4 py-3 table-header">Klasifikasi Pergerakan Produk</th>
                    <th class="px-4 py-3 table-header">Rekomendasi</th>
                </tr></thead>
                <tbody>
                @forelse($paginatedClusters as $cr)
                @php
                    $clusterBadgeColor = match($cr['cluster_label']) {
                        'fast_moving'   => 'background:#ECFDF5;color:#059669',
                        'medium_moving' => 'background:#FFFBEB;color:#D97706',
                        'slow_moving'   => 'background:#FFF7ED;color:#EA580C',
                        'dead_stock'    => 'background:#FEF2F2;color:#DC2626',
                        default         => '',
                    };
                    
                    $clusterLabel = match($cr['cluster_label']) {
                        'fast_moving'   => '<svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>' . __('Sangat Laris'),
                        'medium_moving' => '<svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>' . __('Cukup Laris'),
                        'slow_moving'   => '<svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>' . __('Kurang Laris'),
                        'dead_stock'    => '<svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' . __('Stok Mati'),
                        default         => $cr['cluster_label']
                    };
                @endphp
                <tr class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)]">
                    <td class="px-4 py-2.5 type-body-sm font-medium text-[var(--color-ink)]">{{ $cr['product_name'] }}</td>
                    <td class="px-4 py-2.5 type-body-sm text-right text-[var(--color-slate)]">{{ number_format($cr['total_qty_sold'], 0, ',', '.') }} <span class="text-[10px]">unit</span></td>
                    <td class="px-4 py-2.5 type-body-sm text-right text-[var(--color-slate)]">{{ number_format($cr['transaction_frequency'], 0, ',', '.') }} <span class="text-[10px]">kali</span></td>
                    <td class="px-4 py-2.5 type-body-sm text-right text-[var(--color-slate)]">{{ $cr['days_without_sale'] >= 999 ? 'Belum Ada' : $cr['days_without_sale'] . ' Hari' }}</td>
                    <td class="px-4 py-2.5 text-center">
                        <span class="px-2 py-0.5 rounded-md text-xs font-medium inline-flex items-center" style="{{ $clusterBadgeColor }}">{!! $clusterLabel !!}</span>
                    </td>
                    <td class="px-4 py-2.5 type-caption text-[var(--color-slate)] text-left" style="max-width:250px;">
                        {{ $cr['recommendation'] }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-10 text-center type-body-sm text-[var(--color-slate)]">Tidak ada klasifikasi produk.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($paginatedClusters->hasPages())
        <div class="p-5 border-t border-[var(--color-hairline-soft)]">
            {{ $paginatedClusters->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
