@extends('layouts.app')
@section('page-title', 'Seluruh Kategori Terjual')
@section('content')

<div class="mx-auto max-w-6xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('reports.sales_revenue', request()->except('page')) }}" class="btn-ghost">{{ __('Kembali') }}</a>
    </div>

    <div class="card-feature mb-6">
        <div class="p-5 border-b border-[var(--color-hairline-soft)] flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="type-subtitle-lg text-[var(--color-ink-deep)]">Seluruh Kategori Terjual</h2>
                <p class="type-caption text-[var(--color-slate)] mt-1">Periode {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
            </div>
            <form method="GET" action="{{ route('reports.sales_revenue.categories') }}" class="flex flex-col md:flex-row md:items-center gap-3 w-full">
                <input type="date" name="start_date" value="{{ $startDate }}" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                <span class="text-[var(--color-slate)] text-xs">sampai</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                
                <select name="sort" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                    <option value="desc" {{ request('sort') != 'asc' ? 'selected' : '' }}>{{ __('Peringkat Tertinggi') }}</option>
                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>{{ __('Peringkat Terendah') }}</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="bg-[var(--color-surface-soft)]">
                    <th class="px-5 py-3 text-left table-header">#</th>
                    <th class="px-5 py-3 text-left table-header">Kategori</th>
                    <th class="px-5 py-3 text-right table-header">Total Qty Terjual</th>
                    <th class="px-5 py-3 text-right table-header">Total Revenue</th>
                </tr></thead>
                <tbody>
                @forelse($categories as $idx => $c)
                <tr class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] transition-colors">
                    <td class="px-5 py-3 type-body-sm text-[var(--color-slate)]">{{ $categories->firstItem() + $idx }}</td>
                    <td class="px-5 py-3 type-body-sm font-medium text-[var(--color-ink)]">{{ $c->name }}</td>
                    <td class="px-5 py-3 type-body-sm-bold text-right text-[var(--color-ink-deep)]">{{ number_format($c->total_qty, 0, ',', '.') }}</td>
                    <td class="px-5 py-3 type-body-sm-bold text-right text-[var(--color-ink-deep)]">Rp {{ number_format($c->total_revenue, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-5 py-10 text-center type-body-sm text-[var(--color-slate)]">Tidak ada kategori terjual pada periode ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
        <div class="p-5 border-t border-[var(--color-hairline-soft)]">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
