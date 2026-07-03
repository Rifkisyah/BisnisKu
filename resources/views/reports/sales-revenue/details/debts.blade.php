@extends('layouts.app')
@section('page-title', 'Seluruh Data Hutang')
@section('content')

<div class="mx-auto max-w-6xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('reports.sales_revenue', request()->except('page')) }}" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('Kembali') }}</a>
    </div>

    <div class="card-feature mb-6">
        <div class="p-5 border-b border-[var(--color-hairline-soft)] flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="type-subtitle-lg text-[var(--color-ink-deep)]">Seluruh Data Hutang</h2>
                <p class="type-caption text-[var(--color-slate)] mt-1">Periode {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
            </div>
            <form method="GET" action="{{ route('reports.sales_revenue.debts') }}" class="flex flex-col md:flex-row md:items-center gap-3 w-full">
                <input type="date" name="start_date" value="{{ $startDate }}" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                <span class="text-[var(--color-slate)] text-xs">sampai</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                
                <select name="status" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Dibayar Sebagian</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
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
                @forelse($debts as $d)
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
                <tr><td colspan="7" class="px-5 py-10 text-center type-body-sm text-[var(--color-slate)]">Tidak ada data hutang ditemukan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($debts->hasPages())
        <div class="p-5 border-t border-[var(--color-hairline-soft)]">
            {{ $debts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
