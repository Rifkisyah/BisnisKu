@extends('layouts.app')
@section('page-title', 'Seluruh Transaksi Penjualan')
@section('content')

<div class="mx-auto max-w-6xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="javascript:void(0)" onclick="if(window.history.length > 2) { window.history.back(); } else { window.location.href = '{{ route('dashboard') }}'; }" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
    </div>

    <div class="card-feature mb-6">
        <div class="p-5 border-b border-[var(--color-hairline-soft)] flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="type-subtitle-lg text-[var(--color-ink-deep)]">Seluruh Transaksi Penjualan</h2>
                <p class="type-caption text-[var(--color-slate)] mt-1">Periode {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
            </div>
            <form method="GET" action="{{ route('reports.sales_revenue.transactions') }}" class="flex flex-col md:flex-row md:items-center gap-3 w-full">
                <input type="date" name="start_date" value="{{ $startDate }}" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                <span class="text-[var(--color-slate)] text-xs">sampai</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                
                <select name="payment_method" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                    <option value="">Semua Metode</option>
                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                    <option value="qris" {{ request('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="ewallet" {{ request('payment_method') == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                </select>

                <select name="cashier_id" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                    <option value="">Semua Kasir</option>
                    @foreach($cashiers as $c)
                    <option value="{{ $c->id }}" {{ request('cashier_id') == $c->id ? 'selected' : '' }}>{{ $c->username }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="bg-[var(--color-surface-soft)]">
                    <th class="px-5 py-3 text-left table-header">Kode Transaksi</th>
                    <th class="px-5 py-3 text-left table-header">Tanggal</th>
                    <th class="px-5 py-3 text-left table-header">Kasir</th>
                    <th class="px-5 py-3 text-left table-header">Metode Pembayaran</th>
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
                <tr><td colspan="5" class="px-5 py-10 text-center type-body-sm text-[var(--color-slate)]">Tidak ada transaksi ditemukan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="p-5 border-t border-[var(--color-hairline-soft)]">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
