@extends('layouts.app')
@section('page-title', $debt->debt_code)
@section('content')
<div class="mb-6">
    <a href="{{ route('debts.index') }}" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-[var(--color-canvas)] rounded-[var(--radius-xxxl)] p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="type-display-md text-[var(--color-ink-deep)]">{{ $debt->debt_code }}</h2>
                <span class="badge {{ $debt->status==='paid'?'badge-success':($debt->status==='partial'?'badge-attention':'badge-critical') }} px-3 py-1.5 text-sm">{{ __('messages.'.$debt->status) }}</span>
            </div>
            
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-6">Detail Pelanggan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><p class="type-caption-bold text-[var(--color-slate)] mb-1">{{ __('messages.debtor_name') }}</p><p class="type-body-sm font-medium text-[var(--color-ink)]">{{ $debt->debtor_name }}</p></div>
                <div><p class="type-caption-bold text-[var(--color-slate)] mb-1">{{ __('messages.contact') }}</p><p class="type-body-sm text-[var(--color-ink)]">{{ $debt->debtor_contact ?? '-' }}</p></div>
                <div><p class="type-caption-bold text-[var(--color-slate)] mb-1">{{ __('messages.date') }}</p><p class="type-body-sm text-[var(--color-ink)]">{{ $debt->debt_date->format('d/m/Y') }}</p></div>
                <div><p class="type-caption-bold text-[var(--color-slate)] mb-1">{{ __('messages.due_date') }}</p><p class="type-body-sm text-[var(--color-ink)] flex items-center gap-2">{{ $debt->due_date?->format('d/m/Y') ?? '-' }} @if($debt->isOverdue())<span class="badge badge-critical py-0.5">{{ __('messages.overdue') }}</span>@endif</p></div>
                @if($debt->debtor_address)
                <div class="md:col-span-2"><p class="type-caption-bold text-[var(--color-slate)] mb-1">{{ __('messages.address') }}</p><p class="type-body-sm text-[var(--color-ink)]">{{ $debt->debtor_address }}</p></div>
                @endif
                @if($debt->notes)
                <div class="md:col-span-2"><p class="type-caption-bold text-[var(--color-slate)] mb-1">{{ __('messages.notes') }}</p><p class="type-body-sm text-[var(--color-ink)]">{{ $debt->notes }}</p></div>
                @endif
            </div>
        </div>
        <div class="bg-[var(--color-canvas)] rounded-[var(--radius-xxxl)] p-8">
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-4">Riwayat Pembayaran</h3>
            <table class="w-full mb-6"><thead><tr class="border-b border-[var(--color-hairline-soft)]">
                <th class="pb-3 text-left type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">{{ __('messages.date') }}</th><th class="pb-3 text-left type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">{{ __('messages.total') }}</th><th class="pb-3 text-left type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">{{ __('messages.payment_method') }}</th><th class="pb-3 text-left type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">{{ __('messages.notes') }}</th>
            </tr></thead><tbody>
            @forelse($debt->payments as $p)
            <tr class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] transition-colors"><td class="py-3 type-body-sm text-[var(--color-ink)]">{{ $p->payment_date->format('d/m/Y') }}</td><td class="py-3 type-body-sm font-bold text-[var(--color-success)]">Rp {{ number_format($p->amount,0,',','.') }}</td>
                <td class="py-3 type-body-sm capitalize text-[var(--color-ink)]">{{ $p->payment_method }}</td><td class="py-3 type-caption text-[var(--color-stone)]">{{ $p->notes }}</td></tr>
            @empty<tr><td colspan="4" class="py-8 text-center type-body-sm text-[var(--color-stone)]">Belum ada pembayaran</td></tr>@endforelse</tbody></table>
            @if($debt->status !== 'paid')
            <form method="POST" action="{{ route('debts.add-payment', $debt) }}" class="border-t border-[var(--color-hairline-soft)] pt-6 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">@csrf
                <div class="md:col-span-1">
                    <label class="block type-caption-bold text-[var(--color-slate)] mb-1.5">Jumlah</label>
                    <input type="text" name="amount" required min="1" max="{{ $debt->remaining_amount }}" placeholder="Rp" class="input-field input-rupiah" inputmode="numeric">
                </div>
                <div class="md:col-span-1">
                    <label class="block type-caption-bold text-[var(--color-slate)] mb-1.5">Tanggal</label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="input-field">
                </div>
                <div class="md:col-span-1">
                    <label class="block type-caption-bold text-[var(--color-slate)] mb-1.5">Metode</label>
                    <select name="payment_method" class="input-field"><option value="cash">{{ __('messages.cash') }}</option><option value="qris">QRIS</option><option value="transfer">Transfer Bank</option></select>
                </div>
                <div class="md:col-span-1"><button type="submit" class="btn-primary w-full !py-2.5">+ Bayar</button></div>
            </form>@endif
        </div>
    </div>
    <div class="lg:col-span-1"><div class="bg-[var(--color-canvas)] rounded-[var(--radius-xxxl)] p-8 sticky top-6">
        <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-6">Ringkasan</h3>
        <div class="space-y-4">
            <div class="flex justify-between type-body-sm"><span class="text-[var(--color-slate)]">{{ __('messages.total_amount') }}</span><span class="font-medium text-[var(--color-ink)]">Rp {{ number_format($debt->total_amount,0,',','.') }}</span></div>
            <div class="flex justify-between type-body-sm"><span class="text-[var(--color-slate)]">{{ __('messages.paid_amount') }}</span><span class="font-medium text-[var(--color-success)]">Rp {{ number_format($debt->paid_amount,0,',','.') }}</span></div>
            <div class="border-t border-[var(--color-hairline-soft)] pt-4 mt-2 flex flex-col gap-1">
                <span class="text-[var(--color-slate)] type-caption-bold">{{ __('messages.remaining_amount') }}</span>
                <span class="text-[var(--color-critical)] text-3xl font-bold tracking-tight">Rp {{ number_format($debt->remaining_amount,0,',','.') }}</span>
            </div>
        </div>
    </div></div>
</div>
@endsection
