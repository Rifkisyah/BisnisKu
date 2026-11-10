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
                <div>
                    <p class="type-caption-bold text-[var(--color-slate)] mb-1">{{ __('messages.contact') }}</p>
                    <div class="flex items-center gap-2">
                        <p class="type-body-sm text-[var(--color-ink)]">{{ $debt->debtor_contact ?? '-' }}</p>
                        @if($debt->debtor_contact)
                        @php
                            $storeName = \App\Models\Setting::get('store_name', 'Toko');
                            $phone = preg_replace('/[^0-9]/', '', $debt->debtor_contact);
                            if (str_starts_with($phone, '0')) $phone = '62' . substr($phone, 1);
                            if ($debt->status === 'paid') {
                                $msg = "Halo {$debt->debtor_name}, kami dari {$storeName} mengonfirmasi bahwa hutang Anda telah LUNAS. Terima kasih.";
                            } else {
                                $due = $debt->due_date ? $debt->due_date->format('d/m/Y') : '-';
                                $rem = number_format($debt->remaining_amount, 0, ',', '.');
                                $msg = "Halo {$debt->debtor_name}, kami dari {$storeName} ingin mengingatkan mengenai tagihan hutang sebesar Rp {$rem} yang jatuh tempo pada {$due}. Mohon segera melakukan pelunasan. Terima kasih.";
                            }
                            $waLink = "https://wa.me/{$phone}?text=" . urlencode($msg);
                        @endphp
                        <a href="{{ $waLink }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-[#25D366] bg-[#25D366]/10 px-2 py-0.5 rounded hover:bg-[#25D366]/20 transition-colors">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            Chat
                        </a>
                        @endif
                    </div>
                </div>
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
                    <select name="payment_method" class="input-field"><option value="cash">{{ __('messages.cash') }}</option><option value="qris">QRIS</option></select>
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
