@extends('layouts.app')
@section('page-title', 'Detail Penggunaan Sparepart')
@section('content')

<div class="mx-auto max-w-6xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('reports.service_analysis', request()->except('page')) }}" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('Kembali') }}</a>
    </div>

    <div class="card-feature mb-6">
        <div class="p-5 border-b border-[var(--color-hairline-soft)] flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="type-subtitle-lg text-[var(--color-ink-deep)]">Seluruh Data Penggunaan Sparepart</h2>
                <p class="type-caption text-[var(--color-slate)] mt-1">Periode {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
            </div>
            <form method="GET" action="{{ route('reports.service_analysis.spareparts') }}" class="flex flex-col md:flex-row md:items-center gap-3 w-full">
                <input type="date" name="start_date" value="{{ \Carbon\Carbon::parse($startDate)->format('Y-m-d') }}" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                <span class="text-[var(--color-slate)] text-xs">sampai</span>
                <input type="date" name="end_date" value="{{ \Carbon\Carbon::parse($endDate)->format('Y-m-d') }}" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                
                <select name="sort" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                    <option value="desc" {{ request('sort') != 'asc' ? 'selected' : '' }}>{{ __('Peringkat Tertinggi (Terbanyak)') }}</option>
                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>{{ __('Peringkat Terendah') }}</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="bg-[var(--color-surface-soft)]">
                    <th class="px-5 py-3 text-left table-header">#</th>
                    <th class="px-5 py-3 text-left table-header">Kode Komponen</th>
                    <th class="px-5 py-3 text-left table-header">Nama Sparepart</th>
                    <th class="px-5 py-3 text-right table-header">Jumlah Terpakai</th>
                    <th class="px-5 py-3 text-right table-header">Total Harga</th>
                </tr></thead>
                <tbody>
                @forelse($spareparts as $idx => $ts)
                <tr class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] transition-colors">
                    <td class="px-5 py-3 type-body-sm text-[var(--color-slate)]">{{ $spareparts->firstItem() + $idx }}</td>
                    <td class="px-5 py-3 type-caption-bold text-[var(--color-primary)]">{{ $ts->component_code }}</td>
                    <td class="px-5 py-3 type-body-sm font-medium text-[var(--color-ink)]">{{ $ts->name }}</td>
                    <td class="px-5 py-3 type-body-sm-bold text-right text-[var(--color-ink-deep)]">{{ number_format($ts->total_qty, 0, ',', '.') }}</td>
                    <td class="px-5 py-3 type-body-sm-bold text-right text-[var(--color-ink-deep)]">Rp {{ number_format($ts->total_cost, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-10 text-center type-body-sm text-[var(--color-slate)]">Tidak ada penggunaan sparepart pada periode ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($spareparts->hasPages())
        <div class="p-5 border-t border-[var(--color-hairline-soft)]">
            {{ $spareparts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
