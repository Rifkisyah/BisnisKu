@extends('layouts.app')
@section('page-title', 'Detail Estimasi Pengisian Ulang Stok')
@section('content')

<div class="mx-auto max-w-6xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="javascript:void(0)" onclick="if(window.history.length > 2) { window.history.back(); } else { window.location.href = '{{ route('reports.business_performance') }}'; }" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('Kembali') }}</a>
    </div>

    <div class="card-feature mb-6">
        <div class="p-5 border-b border-[var(--color-hairline-soft)] flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="type-subtitle-lg text-[var(--color-ink-deep)]">Seluruh Data Rekomendasi Restock (SMA)</h2>
                <p class="type-caption text-[var(--color-slate)] mt-1">Estimasi kebutuhan stok berdasarkan rata-rata penjualan 3 bulan terakhir</p>
            </div>
            <form method="GET" action="{{ route('reports.business_performance.sma') }}" class="flex flex-col md:flex-row md:items-center gap-3 w-full">
                <select name="status_filter" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                    <option value="all" {{ request('status_filter') == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="restock" {{ request('status_filter') == 'restock' ? 'selected' : '' }}>Perlu Isi Ulang Stok</option>
                    <option value="ok" {{ request('status_filter') == 'ok' ? 'selected' : '' }}>Tidak Perlu Isi Ulang Stok</option>
                </select>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                <span class="text-[var(--color-slate)] text-xs">sampai</span>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="bg-[var(--color-surface-soft)]">
                    <th class="px-4 py-3 text-left table-header">Produk</th>
                    <th class="px-4 py-3 text-right table-header">{{ __('Rata-rata Terjual / Hari') }}</th>
                    <th class="px-4 py-3 text-right table-header">{{ __('Stok Tersedia') }}</th>
                    <th class="px-4 py-3 text-right table-header">{{ __('Estimasi Kebutuhan (:days Hari)', ['days' => $diffDays]) }}</th>
                    <th class="px-4 py-3 text-right table-header">{{ __('Perkiraan Stok Habis') }}</th>
                    <th class="px-4 py-3 text-right table-header">{{ __('Saran Tambah Stok') }}</th>
                    <th class="px-4 py-3 table-header">Status</th>
                </tr></thead>
                <tbody>
                @forelse($paginatedSma as $sr)
                <tr class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)]">
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
                @empty
                <tr><td colspan="7" class="px-5 py-10 text-center type-body-sm text-[var(--color-slate)]">Tidak ada data estimasi stok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($paginatedSma->hasPages())
        <div class="p-5 border-t border-[var(--color-hairline-soft)]">
            {{ $paginatedSma->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
