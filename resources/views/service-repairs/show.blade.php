@extends('layouts.app')
@section('page-title', $serviceRepair->repair_code)
@section('content')
@php
$statusColors = [
    'draft'         => 'bg-gray-100 text-gray-600',
    'waiting_dp'    => 'bg-amber-100 text-amber-700',
    'diagnosing'    => 'bg-blue-100 text-blue-700',
    'waiting_parts' => 'bg-amber-100 text-amber-700',
    'repairing'     => 'bg-indigo-100 text-indigo-700',
    'ready'         => 'bg-green-100 text-green-700',
    'done'          => 'badge-success',
    'cancelled'     => 'badge-critical',
];
$statusLabels = [
    'draft'         => '<svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg> ' . __('messages.draft'),
    'waiting_dp'    => '<svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> ' . __('messages.waiting_dp'),
    'diagnosing'    => '<svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg> ' . __('messages.diagnosing'),
    'waiting_parts' => '<svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> ' . __('messages.waiting_parts'),
    'repairing'     => '<svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> ' . __('messages.repairing'),
    'ready'         => '<svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> ' . __('messages.ready'),
    'done'          => '<svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> ' . __('messages.done'),
    'cancelled'     => '<svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> ' . __('messages.canceled'),
];
$st = $serviceRepair->status;
$isFinal = $serviceRepair->isFinal();
$dpOk = $serviceRepair->isDpSufficient();
$remaining = max(0, $serviceRepair->total_cost - $serviceRepair->down_payment);
@endphp

<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('service-repairs.index') }}" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>

    <div class="flex items-center gap-2">
        @if($st === 'draft')
        <a href="{{ route('service-repairs.edit', $serviceRepair) }}" class="btn-primary !px-4 !py-2 text-sm flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg> Edit Info Utama
        </a>
        @endif

        @if(in_array($st, ['ready', 'done']))
        <a href="{{ route('service-repairs.receipt', $serviceRepair) }}" target="_blank" class="rounded-lg bg-white border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 flex items-center gap-1.5 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg> Cetak Nota
        </a>
        @endif

        @if(auth()->user()->isOwner())
        <form action="{{ route('service-repairs.destroy', $serviceRepair) }}" method="POST" @submit.prevent="$dispatch('open-confirm-modal', { form: $event.target, title: 'Hapus Data Perbaikan?', text: 'Data yang dihapus tidak dapat dikembalikan. Apakah Anda yakin?', confirmText: 'Ya, Hapus', color: 'red' })" class="inline">
            @csrf @method('DELETE')
            <button type="submit" class="btn-ghost !px-4 !py-2 text-sm !text-[var(--color-critical)] !border-[var(--color-critical)]/20 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Hapus
            </button>
        </form>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-6">

        {{-- ── Header / Customer Info ────────────────────────────────── --}}
        <div class="card-feature p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="type-heading-sm text-[var(--color-ink-deep)]">{{ $serviceRepair->repair_code }}</h3>
                    <p class="text-xs text-[var(--color-slate)] mt-0.5">Dibuat: {{ $serviceRepair->start_date->format('d/m/Y H:i') }}</p>
                </div>
                <span class="badge {{ $statusColors[$st] ?? '' }} px-3 py-1.5 text-sm">
                    {!! $statusLabels[$st] ?? $st !!}
                </span>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><p class="type-caption-bold text-[var(--color-slate)]">Pelanggan</p><p class="font-medium">{{ $serviceRepair->customer_name }}</p></div>
                <div>
                    <p class="type-caption-bold text-[var(--color-slate)]">No. WhatsApp</p>
                    <p class="flex items-center gap-1.5">
                        {{ $serviceRepair->customer_phone ?? '-' }}
                        @if($serviceRepair->customer_phone)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $serviceRepair->customer_phone) }}" target="_blank" class="text-green-600" title="Chat WA">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 0C5.385 0 0 5.384 0 12.031c0 2.128.552 4.195 1.603 6.02L.034 23.992l6.096-1.598A11.97 11.97 0 0012.031 24c6.646 0 12.03-5.385 12.03-12.031C24.062 5.384 18.678 0 12.031 0zm5.503 16.774c-.302-.152-1.785-.881-2.062-.982-.277-.101-.48-.152-.682.152-.202.303-.781.981-.958 1.183-.177.202-.354.227-.656.076-1.391-.655-2.529-1.533-3.486-2.923-.197-.286.195-.27.489-.858.101-.202.051-.38-.025-.532-.076-.152-.682-1.644-.934-2.251-.247-.591-.497-.512-.682-.521-.177-.008-.38-.01-.582-.01-.202 0-.53.076-.808.38-.277.303-1.06 1.036-1.06 2.527 0 1.491 1.085 2.932 1.237 3.134.152.202 2.136 3.262 5.176 4.571 1.637.705 2.502.82 3.42.684 1.026-.151 2.222-.907 2.535-1.783.313-.877.313-1.628.22-1.783-.093-.155-.345-.246-.647-.398z"/></svg>
                        </a>
                        @endif
                    </p>
                </div>
                <div><p class="type-caption-bold text-[var(--color-slate)]">Teknisi</p><p>{{ $serviceRepair->technician?->username ?? '—' }}</p></div>
                <div><p class="type-caption-bold text-[var(--color-slate)]">Selesai</p><p>{{ $serviceRepair->completion_date?->format('d/m/Y H:i') ?? '—' }}</p></div>
                @if($serviceRepair->notes)<div class="col-span-2"><p class="type-caption-bold text-[var(--color-slate)]">Catatan</p><p>{{ $serviceRepair->notes }}</p></div>@endif
            </div>

            {{-- ── Status Actions ─────────────────────────────────────── --}}
            @if(!$isFinal && auth()->user()->role->name !== 'gudang')
            <div class="mt-5 pt-4 border-t border-[var(--color-hairline-soft)] space-y-3">

                {{-- DRAFT → Kasir kirim ke Teknisi untuk Diagnosa --}}
                @if($st === 'draft' && (auth()->user()->isKasir() || auth()->user()->isOwner()))
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-3">
                    <p class="text-sm font-bold text-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Data Perbaikan Draft — Teruskan ke teknisi untuk pengecekan
                    </p>
                    <form method="POST" action="{{ route('service-repairs.update-status', $serviceRepair) }}" @submit.prevent="$dispatch('open-confirm-modal', { form: $event.target, title: 'Teruskan ke Teknisi?', text: 'Apakah Anda yakin ingin meneruskan data ini ke teknisi untuk diagnosa?', confirmText: 'Ya, Teruskan', color: 'gray' })">@csrf @method('PATCH')
                        <input type="hidden" name="status" value="diagnosing">
                        <button type="submit" class="w-full rounded-full bg-black px-6 py-3 text-sm font-bold text-white hover:bg-gray-800 transition-colors flex justify-center items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Diagnosa (→ Menunggu Diagnosa)
                        </button>
                    </form>
                </div>
                @elseif($st === 'draft')
                <div class="rounded-xl bg-gray-50 border border-gray-200 px-4 py-3 text-sm text-gray-600">
                    ℹ️ Data masih draft. Menunggu kasir meneruskan tiket untuk diagnosa.
                </div>
                @endif

                {{-- DIAGNOSING & WAITING DP --}}
                @if(in_array($st, ['diagnosing', 'waiting_dp']))
                <div class="space-y-3">
                    
                    {{-- Kasir View --}}
                    @if(auth()->user()->isKasir() || auth()->user()->isOwner())
                        @if($st === 'waiting_dp')
                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 space-y-3">
                                <p class="text-sm font-bold text-amber-800 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Pembayaran DP
                                </p>
                                <p class="text-xs text-amber-700">DP minimum 50% (berdasarkan komponen/sparepart jika ada): <strong>Rp {{ number_format($serviceRepair->total_cost * 0.5, 0, ',', '.') }}</strong> dari total estimasi sementara <strong>Rp {{ number_format($serviceRepair->total_cost, 0, ',', '.') }}</strong></p>
                                <form method="POST" action="{{ route('service-repairs.update-status', $serviceRepair) }}" class="space-y-3" @submit.prevent="$dispatch('open-confirm-modal', { form: $event.target, title: 'Simpan Pembayaran DP?', text: 'Apakah Anda yakin ingin menyimpan data pembayaran DP ini?', confirmText: 'Ya, Simpan DP', color: 'amber' })">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="waiting_dp">
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="text-xs font-semibold text-amber-800 mb-1 block">Nominal DP Diterima</label>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs font-bold text-gray-500">Rp</span>
                                                <input type="text" name="down_payment" value="{{ $serviceRepair->down_payment > 0 ? (int)$serviceRepair->down_payment : '' }}" placeholder="Nominal DP" class="input-field !h-9 !pl-9 !py-1 !text-xs input-rupiah" required>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="text-xs font-semibold text-amber-800 mb-1 block">Metode Bayar DP</label>
                                            <select name="payment_method" class="input-field !h-9 !py-1 !text-xs w-full">
                                                <option value="cash" {{ $serviceRepair->payment_method === 'cash' ? 'selected' : '' }}>Cash</option>
                                                <option value="qris" {{ $serviceRepair->payment_method === 'qris' ? 'selected' : '' }}>QRIS</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" class="w-full rounded-full bg-amber-600 px-6 py-2.5 text-sm font-bold text-white hover:bg-amber-700 transition-colors flex justify-center items-center gap-2">
                                        Simpan Pembayaran DP
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="rounded-lg bg-amber-100 px-3 py-2 text-xs text-amber-700 border border-amber-200">
                                ⏳ Menunggu teknisi menyelesaikan Hasil Diagnosa dan Estimasi Sparepart.
                            </div>
                        @endif
                    @endif

                    {{-- Teknisi View --}}
                    @if(auth()->user()->isTeknisi() || auth()->user()->isOwner())
                        @if($st === 'diagnosing')
                            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 space-y-2">
                                <p class="text-xs font-bold text-blue-800">Silakan isi Hasil Diagnosa dan Estimasi Sparepart di form bagian bawah.</p>
                                <p class="text-[11px] text-blue-600 bg-blue-100 p-2 rounded">Tombol 'Simpan Hasil Diagnosa' berada di bagian paling bawah setelah seluruh perangkat diperiksa.</p>
                            </div>
                        @elseif($st === 'waiting_dp')
                            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 space-y-2">
                                <p class="text-xs font-bold text-blue-800">Jika pembayaran DP sudah selesai, mulai perbaikan.</p>
                                @if($dpOk || $serviceRepair->total_cost == 0)
                                <form method="POST" action="{{ route('service-repairs.update-status', $serviceRepair) }}" @submit.prevent="$dispatch('open-confirm-modal', { form: $event.target, title: 'Mulai Perbaikan?', text: 'Apakah Anda yakin ingin memulai proses perbaikan?', confirmText: 'Ya, Mulai Perbaikan', color: 'gray' })">@csrf @method('PATCH')
                                    <input type="hidden" name="status" value="repairing">
                                    <button type="submit" class="w-full rounded-full bg-black px-6 py-3 text-sm font-bold text-white hover:bg-gray-800 transition-colors flex justify-center items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Mulai Perbaikan (→ Diproses)
                                    </button>
                                </form>
                                @else
                                <p class="text-xs text-blue-600 bg-blue-100 p-2 rounded">DP belum mencukupi (min 50%: Rp {{ number_format($serviceRepair->total_cost * 0.5, 0, ',', '.') }}). Tunggu Kasir memproses DP terlebih dahulu.</p>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
                @endif

                {{-- WAITING_PARTS --}}
                @if($st === 'waiting_parts')
                <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-700">
                    ⏳ Menunggu sparepart yang diajukan datang. Status akan otomatis berubah ke "Perbaikan" saat semua sparepart tersedia.
                </div>
                @endif

                {{-- REPAIRING → Teknisi selesaikan perbaikan --}}
                @if($st === 'repairing')
                <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 space-y-3">
                    <p class="text-sm font-bold text-indigo-800 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Perbaikan Sedang Berlangsung
                    </p>
                    @if($serviceRepair->allPartsAvailable())
                        @if(auth()->user()->isTeknisi() || auth()->user()->isOwner())
                        <form method="POST" action="{{ route('service-repairs.update-status', $serviceRepair) }}" @submit.prevent="$dispatch('open-confirm-modal', { form: $event.target, title: 'Selesaikan Perbaikan?', text: 'Apakah Anda yakin perbaikan telah selesai dan perangkat siap diambil pelanggan?', confirmText: 'Ya, Selesai', color: 'gray' })">@csrf @method('PATCH')
                            <input type="hidden" name="status" value="ready">
                            <button type="submit" class="w-full rounded-full bg-black px-6 py-3 text-sm font-bold text-white hover:bg-gray-800 transition-colors flex justify-center items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Selesaikan Perbaikan (→ Siap Diambil)
                            </button>
                        </form>
                        @else
                        <div class="rounded-lg bg-indigo-100 px-3 py-2 text-xs text-indigo-700">
                            ℹ️ Menunggu teknisi menyelesaikan perbaikan.
                        </div>
                        @endif
                    @else
                    <div class="rounded-lg bg-amber-100 border border-amber-200 px-3 py-2 text-xs text-amber-700">
                        ⏳ Menunggu sparepart yang diajukan datang. Perbaikan belum bisa diselesaikan.
                    </div>
                    @endif
                </div>
                @endif

                {{-- READY → Kasir hubungi pelanggan & tutup transaksi --}}
                @if($st === 'ready')
                <div class="space-y-3">
                    <div class="rounded-xl border border-green-200 bg-green-50 p-4">
                        <p class="text-sm font-bold text-green-800 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Perbaikan Selesai — Siap Diambil Pelanggan
                        </p>
                        <div class="flex gap-2">
                            @if($serviceRepair->customer_phone)
                            <a href="{{ $serviceRepair->buildWhatsAppUrl($shopName) }}" target="_blank"
                               class="flex-1 flex justify-center items-center gap-2 rounded-xl bg-green-500 hover:bg-green-600 px-5 py-3 text-sm font-semibold text-white transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 0C5.385 0 0 5.384 0 12.031c0 2.128.552 4.195 1.603 6.02L.034 23.992l6.096-1.598A11.97 11.97 0 0012.031 24c6.646 0 12.03-5.385 12.03-12.031C24.062 5.384 18.678 0 12.031 0zm5.503 16.774c-.302-.152-1.785-.881-2.062-.982-.277-.101-.48-.152-.682.152-.202.303-.781.981-.958 1.183-.177.202-.354.227-.656.076-1.391-.655-2.529-1.533-3.486-2.923-.197-.286.195-.27.489-.858.101-.202.051-.38-.025-.532-.076-.152-.682-1.644-.934-2.251-.247-.591-.497-.512-.682-.521-.177-.008-.38-.01-.582-.01-.202 0-.53.076-.808.38-.277.303-1.06 1.036-1.06 2.527 0 1.491 1.085 2.932 1.237 3.134.152.202 2.136 3.262 5.176 4.571 1.637.705 2.502.82 3.42.684 1.026-.151 2.222-.907 2.535-1.783.313-.877.313-1.628.22-1.783-.093-.155-.345-.246-.647-.398z"/></svg>
                                Hubungi via WA
                            </a>
                            @endif
                            <a href="{{ route('service-repairs.receipt', $serviceRepair) }}" target="_blank"
                               class="flex-1 flex justify-center items-center gap-2 rounded-xl bg-gray-800 hover:bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Cetak Nota
                            </a>
                        </div>
                    </div>

                    @if(auth()->user()->isKasir() || auth()->user()->isOwner())
                    <form method="POST" action="{{ route('service-repairs.update-status', $serviceRepair) }}" 
                          class="bg-black/5 border border-gray-200 rounded-xl p-4 space-y-3"
                          @submit.prevent="$dispatch('open-confirm-modal', { form: $event.target, title: 'Konfirmasi Pelunasan?', text: 'Apakah Anda yakin ingin memproses pelunasan dan menyelesaikan transaksi perbaikan ini?', confirmText: 'Ya, Selesaikan', color: 'green' })"
                          x-data="{
                              dp: {{ $serviceRepair->down_payment }},
                              componentCost: {{ $serviceRepair->component_cost }},
                              serviceFees: {
                                  @foreach($serviceRepair->deviceItems as $device)
                                      '{{ $device->id }}': {{ $device->service_fee > 0 ? (int)$device->service_fee : 0 }},
                                  @endforeach
                              },
                              get totalServiceFee() {
                                  return Object.values(this.serviceFees).reduce((a, b) => a + (Number(String(b).replace(/[^0-9]/g, '')) || 0), 0);
                              },
                              get totalTagihan() {
                                  return this.componentCost + this.totalServiceFee;
                              },
                              get remaining() {
                                  return Math.max(0, this.totalTagihan - this.dp);
                              },
                              formatRupiah(num) {
                                  return new Intl.NumberFormat('id-ID').format(num);
                              }
                          }">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="done">
                        <p class="text-sm font-bold text-gray-800">Konfirmasi Pelunasan</p>
                        
                        <div class="space-y-2 mb-3 bg-white p-3 rounded-lg border border-gray-200">
                            <p class="text-xs font-bold text-gray-800 mb-2">Input Biaya Jasa Perangkat</p>
                            @foreach($serviceRepair->deviceItems as $device)
                                <div>
                                    <label class="text-xs text-gray-600 mb-1 block">{{ $device->name }}</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs font-bold text-gray-500">Rp</span>
                                        <input type="text" name="items[{{ $device->id }}][service_fee]" x-model="serviceFees['{{ $device->id }}']" class="input-field !h-8 !pl-9 !py-1 !text-xs input-rupiah w-full" placeholder="0">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="grid grid-cols-2 gap-2" x-show="remaining > 0">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Sisa Tagihan</p>
                                <p class="text-lg font-bold text-red-600">Rp <span x-text="formatRupiah(remaining)"></span></p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-700 mb-1 block">Metode Pelunasan</label>
                                <select name="final_payment_method" class="input-field !h-9 !py-1 !text-xs w-full" :required="remaining > 0">
                                    <option value="">-- Pilih --</option>
                                    <option value="cash">Cash</option>
                                    <option value="qris">QRIS</option>
                                </select>
                            </div>
                        </div>
                        <p class="text-sm text-green-700 font-medium" x-show="remaining <= 0" x-cloak>✅ Sudah lunas (tidak ada sisa pembayaran)</p>
                        <button type="button" @click="$dispatch('open-confirm', {title: 'Selesaikan Data Perbaikan', message: 'Konfirmasi barang sudah diambil dan pelanggan sudah melunasi pembayaran?', type: 'primary', buttonText: 'Ya, Selesaikan', action: () => $el.closest('form').submit()})" class="rounded-full bg-black px-6 py-3 text-sm font-bold text-white hover:bg-gray-800 w-full flex items-center justify-center gap-2 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            Tutup Data Perbaikan & Konfirmasi Pelunasan
                        </button>
                    </form>
                    @else
                    <div class="rounded-xl bg-gray-50 border border-gray-200 px-4 py-3 text-sm text-gray-600">
                        ℹ️ Menunggu kasir mengkonfirmasi pengambilan dan pelunasan dari pelanggan.
                    </div>
                    @endif
                </div>
                @endif

                {{-- Tombol Batalkan (draft, waiting_dp, diagnosing, repairing) --}}
                @if(in_array($st, ['draft','waiting_dp','diagnosing','repairing']) && auth()->user()->isOwner())
                <form method="POST" action="{{ route('service-repairs.update-status', $serviceRepair) }}">@csrf @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="button" @click="$dispatch('open-confirm', {title: 'Batalkan Perbaikan?', message: 'Data perbaikan yang dibatalkan tidak bisa dilanjutkan lagi. Yakin ingin membatalkan?', type: 'danger', buttonText: 'Ya, Batalkan', action: () => $el.closest('form').submit()})"
                        class="w-full rounded-full border-2 border-gray-200 px-6 py-2.5 text-sm font-bold text-gray-600 hover:border-red-300 hover:text-red-600 transition-colors flex justify-center items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Batalkan Perbaikan
                    </button>
                </form>
                @endif

            </div>
            @endif

            @if($isFinal)
            <div class="mt-4 rounded-xl bg-gray-50 border border-gray-200 px-4 py-3 text-sm text-center text-gray-500">
                @if($st === 'done')
                    <span class="flex items-center justify-center gap-1"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> {{ __('messages.ticket_completed') }}</span>
                @else
                    <span class="flex items-center justify-center gap-1"><svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> {{ __('messages.ticket_canceled') }}</span>
                @endif
            </div>
            @endif

        </div>

        {{-- ── Device Items ────────────────────────────────────────────── --}}
        <div class="card-feature p-6">
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-4 flex items-center gap-2"><svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Detail Perbaikan & Diagnosa</h3>
            <div class="space-y-6">
                @foreach($serviceRepair->items->whereNull('parent_id') as $idx => $item)
                <div class="rounded-xl border border-[var(--color-hairline-soft)] p-4 space-y-4">

                    {{-- Device header --}}
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-semibold text-[var(--color-ink)]">{{ $item->name }}</p>
                            @if($item->brand || $item->series)<p class="text-xs text-[var(--color-slate)]">{{ $item->brand }} {{ $item->series }}</p>@endif
                            @if($item->complaint)<p class="text-sm text-[var(--color-charcoal)] mt-1"><strong>Keluhan:</strong> {{ $item->complaint }}</p>@endif
                        </div>
                        @if($item->service_fee > 0)<span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-1 rounded-full font-medium">Jasa: Rp {{ number_format($item->service_fee, 0, ',', '.') }}</span>@endif
                    </div>

                    {{-- Photos --}}
                    @if($item->images && count($item->images) > 0)
                    <div x-data="{ activeSlide: 0, slides: {{ count($item->images) }} }" class="relative w-full max-w-sm mx-auto aspect-square bg-[var(--color-surface-soft)] rounded-lg overflow-hidden group">
                        <div class="flex h-full transition-transform duration-300" :style="'transform: translateX(-' + (activeSlide * 100) + '%)'" >
                            @foreach($item->images as $img)
                            <div class="w-full h-full flex-shrink-0 cursor-pointer" @click="$dispatch('open-lightbox', '{{ asset('storage/' . $img) }}')">
                                <img src="{{ asset('storage/' . $img) }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                            </div>
                            @endforeach
                        </div>
                        <template x-if="slides > 1">
                            <div>
                                <button type="button" @click="activeSlide = activeSlide === 0 ? slides - 1 : activeSlide - 1" class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/50 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <button type="button" @click="activeSlide = activeSlide === slides - 1 ? 0 : activeSlide + 1" class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/50 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                    @endif

                    {{-- Diagnosis form --}}
                    @if(($serviceRepair->isTechEditable() || $serviceRepair->isDiagnosing()) && (auth()->user()->isTeknisi() || auth()->user()->isOwner()))
                    <div class="space-y-3">
                        <input type="hidden" name="items[{{ $idx }}][id]" value="{{ $item->id }}" form="diagnosis-form">
                        <div>
                            <label class="block text-xs font-semibold text-[var(--color-slate)] mb-1">Hasil Diagnosa *</label>
                            <textarea name="items[{{ $idx }}][diagnosis_result]" form="diagnosis-form" required rows="2" class="input-field !h-auto !py-2" placeholder="Tuliskan hasil diagnosa...">{{ $item->diagnosis_result }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-[var(--color-slate)] mb-1">Tambah Foto Bukti (Opsional)</label>
                                <input type="file" name="items[{{ $idx }}][images][]" form="diagnosis-form" multiple accept="image/*" class="input-field !py-1.5 text-xs">
                            </div>
                        </div>
                    </div>
                    @elseif($item->diagnosis_result)
                    <div class="rounded-lg bg-blue-50 border border-blue-100 px-3 py-2 text-sm text-blue-800">
                        <p class="font-semibold text-xs text-blue-500 mb-1">DIAGNOSA:</p>
                        {{ $item->diagnosis_result }}
                    </div>
                    @endif

                    {{-- ── Sparepart Section ───────────────────────────── --}}
                    <div x-data="{ open: {{ $item->children->count() > 0 ? 'true' : 'false' }} }" class="border border-[var(--color-hairline)] rounded-lg overflow-hidden">
                        <button type="button" @click="open = !open" class="w-full flex items-center justify-between p-3 bg-[var(--color-surface-soft)] hover:bg-gray-100 transition-colors">
                            <span class="text-sm font-semibold text-[var(--color-ink-deep)] flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg> Sparepart ({{ $item->children->count() }})
                                @php $pendingCount = $item->children->where('sparepart_status','pending')->count(); @endphp
                                @if($pendingCount > 0)<span class="ml-2 text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">{{ $pendingCount }} menunggu</span>@endif
                            </span>
                            <svg class="h-4 w-4 transform transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="open" x-cloak class="p-4 border-t border-[var(--color-hairline)] bg-[var(--color-canvas)] space-y-4">
                            {{-- Sparepart list --}}
                            @php $parts = $item->children; @endphp
                            @if($parts->count() > 0)
                            <table class="w-full text-xs">
                                <thead><tr class="border-b border-[var(--color-hairline-soft)] text-left text-gray-500">
                                    <th class="pb-1">Item</th><th class="pb-1 text-center">Tipe</th><th class="pb-1 text-center">Status</th><th class="pb-1 text-center">Qty</th><th class="pb-1 text-right">Subtotal</th>
                                </tr></thead>
                                <tbody>
                                @foreach($parts as $part)
                                <tr class="border-b border-gray-50">
                                    <td class="py-2">
                                        <p class="font-medium">{{ $part->name }}</p>
                                        @if($part->component)<p class="text-gray-400">{{ $part->component->product_code }} | Stok: {{ $part->component->stock }}</p>@endif
                                        @if($part->isRequested() && $part->productPurchases->count() > 0)
                                            @php $latestPO = $part->productPurchases->last(); @endphp
                                            <a href="{{ route('product-purchases.show', $latestPO) }}" class="text-blue-600 hover:underline text-[10px] flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg> Lihat Pengadaan: {{ $latestPO->product_purchase_code }}</a>
                                        @endif
                                    </td>
                                    <td class="py-2 text-center">
                                        @if($part->isFromStock()) <span class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">Stok</span>
                                        @elseif($part->isRequested()) <span class="bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded text-[10px]">Pengajuan</span>
                                        @else <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="py-2 text-center">
                                        @if($part->sparepart_status === 'available') <span class="bg-green-100 text-green-700 px-1.5 py-0.5 rounded text-[10px] inline-flex items-center gap-0.5"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Tersedia</span>
                                        @elseif($part->sparepart_status === 'pending') <span class="bg-amber-100 text-amber-600 px-1.5 py-0.5 rounded text-[10px] inline-flex items-center gap-0.5"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Menunggu</span>
                                        @elseif($part->sparepart_status === 'used') <span class="bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded text-[10px] inline-flex items-center gap-0.5"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Dipakai</span>
                                        @else <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="py-2 text-center">{{ $part->quantity }}</td>
                                    <td class="py-2 text-right font-medium flex items-center justify-end gap-2">
                                        <span>Rp {{ number_format($part->subtotal, 0, ',', '.') }}</span>
                                        @if($serviceRepair->isTechEditable() && (auth()->user()->isTeknisi() || auth()->user()->isOwner()) && $part->sparepart_status !== 'used')
                                        <form method="POST" action="{{ route('service-repairs.delete-part', [$serviceRepair, $part]) }}" @submit.prevent="$dispatch('open-confirm-modal', { form: $event.target, title: 'Hapus Sparepart?', text: 'Apakah Anda yakin ingin menghapus sparepart ini?', confirmText: 'Ya, Hapus', color: 'red' })">@csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-1 rounded transition-colors" title="Hapus"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                            @else
                            <p class="text-xs text-gray-400 text-center py-2">Belum ada sparepart.</p>
                            @endif

                            {{-- Add sparepart form — only when tech editable --}}
                            @if($serviceRepair->isTechEditable() && (auth()->user()->isTeknisi() || auth()->user()->isOwner()))
                            <div class="border-t border-[var(--color-hairline-soft)] pt-3 space-y-3" x-data="{
                                mode: 'from_stock',
                                selectedCode: '',
                                itemName: '',
                                unitPrice: 0,
                                stock: 0,
                                qty: 1,
                                products: [
                                    @foreach($products as $p){ code: '{{ $p->product_code }}', name: '{{ addslashes($p->name) }}', price: {{ $p->selling_price }}, stock: {{ $p->stock }} },@endforeach
                                ],
                                updateFromSelect() {
                                    let p = this.products.find(x => x.code === this.selectedCode);
                                    if(p) { this.itemName = p.name; this.unitPrice = p.price; this.stock = p.stock; }
                                    else { this.itemName = ''; this.unitPrice = 0; this.stock = 0; }
                                }
                            }">
                                <p class="text-xs font-bold text-[var(--color-ink)]">Tambah Sparepart</p>

                                {{-- Mode switch --}}
                                <div class="flex gap-2">
                                    <button type="button" @click="mode='from_stock'; selectedCode=''" class="text-xs px-3 py-1.5 rounded-lg font-medium transition-colors flex items-center gap-1.5" :class="mode==='from_stock' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg> Dari Stok</button>
                                    <button type="button" @click="mode='requested'; selectedCode=''" class="text-xs px-3 py-1.5 rounded-lg font-medium transition-colors flex items-center gap-1.5" :class="mode==='requested' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-600'"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> Pengajuan</button>
                                </div>

                                {{-- From stock mode --}}
                                <div x-show="mode==='from_stock'">
                                    <form method="POST" action="{{ route('service-repairs.add-part', $serviceRepair) }}" class="space-y-2">@csrf
                                        <input type="hidden" name="parent_id" value="{{ $item->id }}">
                                        <input type="hidden" name="sparepart_type" value="from_stock">
                                        <select name="product_code" x-model="selectedCode" @change="updateFromSelect" required class="input-field !py-1.5 !text-xs w-full">
                                            <option value="">-- Pilih Produk --</option>
                                            <template x-for="p in products" :key="p.code">
                                                <option :value="p.code" x-text="`${p.name} (Stok: ${p.stock})`"></option>
                                            </template>
                                        </select>
                                        <div class="grid grid-cols-3 gap-2">
                                            <input type="hidden" name="item_name" x-model="itemName">
                                            <div class="col-span-1"><label class="text-[10px] text-gray-500">Qty</label><input type="number" name="quantity" x-model.number="qty" min="1" :max="stock" class="input-field !h-8 !py-1 !text-xs w-full"></div>
                                            <div class="col-span-2"><label class="text-[10px] text-gray-500">Harga</label><input type="text" name="unit_price" x-model="unitPrice" class="input-field input-rupiah !h-8 !py-1 !text-xs w-full bg-gray-100 pointer-events-none text-gray-600 select-none" readonly tabindex="-1"></div>
                                        </div>
                                        <template x-if="selectedCode && stock <= 0">
                                            <p class="text-[10px] text-amber-600 bg-amber-50 rounded px-2 py-1">⚠️ Stok habis. Gunakan mode Pengajuan.</p>
                                        </template>
                                        <button type="submit" class="btn-primary w-full !py-2 !text-xs" :disabled="!selectedCode || stock <= 0" :class="(!selectedCode || stock <= 0) ? 'opacity-50 cursor-not-allowed' : ''">+ Tambah dari Stok</button>
                                    </form>
                                </div>

                                {{-- Requested mode --}}
                                <div x-show="mode==='requested'">
                                    <form method="POST" action="{{ route('service-repairs.add-part', $serviceRepair) }}" class="space-y-2">@csrf
                                        <input type="hidden" name="parent_id" value="{{ $item->id }}">
                                        <input type="hidden" name="sparepart_type" value="requested">
                                        <input type="text" name="item_name" x-model="itemName" placeholder="Nama sparepart..." required class="input-field !py-1.5 !text-xs w-full">
                                        <div class="grid grid-cols-2 gap-2">
                                            <div><label class="text-[10px] text-gray-500">Qty</label><input type="number" name="quantity" x-model.number="qty" min="1" required class="input-field !h-8 !py-1 !text-xs w-full"></div>
                                            <div><label class="text-[10px] text-gray-500">Est. Harga</label><input type="text" name="unit_price" placeholder="0" required class="input-field input-rupiah !h-8 !py-1 !text-xs w-full"></div>
                                        </div>
                                        <button type="submit" class="w-full rounded-lg bg-purple-600 hover:bg-purple-700 text-white !py-2 !text-xs font-medium">+ Tambah Pengajuan</button>
                                    </form>

                                    {{-- After adding requested part, show "Ajukan ke Pengadaan" --}}
                                    @php $requestedParts = $item->children->where('sparepart_type','requested')->where('sparepart_status', null); @endphp
                                    @if($requestedParts->count() > 0 && auth()->user()->role->name !== 'gudang')
                                    <div class="mt-2 border border-purple-200 rounded-lg p-3 bg-purple-50 space-y-2">
                                        <p class="text-xs font-bold text-purple-800">Sparepart belum diajukan ke pengadaan:</p>
                                        @foreach($requestedParts as $rPart)
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-medium">{{ $rPart->name }} ({{ $rPart->quantity }}x)</span>
                                            <form method="POST" action="{{ route('service-repairs.request-part', $serviceRepair) }}">@csrf
                                                <input type="hidden" name="sparepart_item_id" value="{{ $rPart->id }}">
                                                <button type="submit" class="text-xs bg-purple-600 text-white rounded px-2 py-1 hover:bg-purple-700 flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg> Ajukan Pengadaan</button>
                                            </form>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- NEW BUTTON PLACEMENT FOR DIAGNOSIS SUBMISSION --}}
        @if($st === 'diagnosing' && (auth()->user()->isTeknisi() || auth()->user()->isOwner()))
            <div class="card-feature p-6 text-center space-y-3">
                <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]">Konfirmasi Diagnosa & Sparepart</h3>
                <p class="text-sm text-gray-500">Pastikan semua hasil diagnosa, foto bukti, dan pengajuan/penambahan sparepart dari stok sudah dimasukkan dengan benar. Setelah disimpan, form sparepart tidak dapat diubah (menunggu pembayaran DP dari Kasir).</p>
                
                <form id="diagnosis-form" method="POST" action="{{ route('service-repairs.update-status', $serviceRepair) }}" enctype="multipart/form-data" 
                      x-data="{ confirmed: false }"
                      @submit="if(!confirmed) { $event.preventDefault(); $dispatch('open-confirm-modal', { onConfirm: () => { confirmed = true; setTimeout(() => $el.requestSubmit(), 50); }, title: 'Simpan Diagnosa?', text: 'Apakah Anda yakin semua data diagnosa dan estimasi sparepart sudah final?', confirmText: 'Ya, Simpan & Lanjut', color: 'gray' }); }">@csrf @method('PATCH')
                    <input type="hidden" name="status" value="waiting_dp">
                    <button type="submit" class="btn-buy w-full justify-center text-base py-3 mt-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Hasil Diagnosa & Lanjut
                    </button>
                </form>
            </div>
        @endif

        {{-- Global notes form --}}
        <div class="card-feature p-6">
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-4">Catatan Global</h3>
            @if(auth()->user()->isOwner() || auth()->user()->isTeknisi())
            <form method="POST" action="{{ route('service-repairs.update', $serviceRepair) }}" class="space-y-3">@csrf @method('PUT')
                <textarea name="notes" rows="3" class="input-field !h-auto !py-2" placeholder="Catatan umum perbaikan...">{{ $serviceRepair->notes }}</textarea>
                <div class="text-right"><button type="submit" class="btn-primary !py-2 !px-6 text-sm">Simpan Catatan</button></div>
            </form>
            @else
            <p class="text-sm text-[var(--color-charcoal)] bg-gray-50 rounded-lg p-2.5 border border-gray-100">{{ $serviceRepair->notes ?: 'Tidak ada catatan.' }}</p>
            @endif
        </div>
    </div>

    {{-- ── Right Sidebar: Cost Summary ─────────────────────────────── --}}
    <div class="lg:col-span-1">
        <div class="card-sticky p-6 sticky top-4 space-y-4">
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]">Ringkasan Biaya</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-[var(--color-slate)]">Biaya Jasa</span><span class="font-medium">Rp {{ number_format($serviceRepair->service_fee, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-[var(--color-slate)]">Biaya Sparepart</span><span class="font-medium">Rp {{ number_format($serviceRepair->component_cost, 0, ',', '.') }}</span></div>
                <div class="border-t pt-2 flex justify-between font-bold"><span>Total</span><span class="text-[var(--color-primary)]">Rp {{ number_format($serviceRepair->total_cost, 0, ',', '.') }}</span></div>
                <div class="flex justify-between text-green-600 font-semibold"><span>DP Dibayar</span><span>Rp {{ number_format($serviceRepair->down_payment, 0, ',', '.') }}</span></div>
                <div class="border-t pt-2 flex justify-between font-bold"><span>Sisa Tagihan</span><span class="{{ $remaining > 0 ? 'text-red-600' : 'text-green-600' }}">Rp {{ number_format($remaining, 0, ',', '.') }}</span></div>
            </div>

            {{-- DP Progress Bar --}}
            @if($serviceRepair->total_cost > 0)
            @php $dpPct = min(100, round(($serviceRepair->down_payment / $serviceRepair->total_cost) * 100)); @endphp
            <div>
                <div class="flex justify-between text-xs text-gray-500 mb-1"><span>DP Progress</span><span>{{ $dpPct }}%</span></div>
                <div class="w-full bg-gray-200 rounded-full h-2"><div class="h-2 rounded-full transition-all {{ $dpPct >= 50 ? 'bg-green-500' : 'bg-amber-500' }}" style="width:{{ $dpPct }}%"></div></div>
                <p class="text-[10px] mt-1 flex items-center gap-1 {{ $dpPct >= 50 ? 'text-green-600' : 'text-amber-600' }}">
                    @if($dpPct >= 50)
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> DP sudah ≥ 50%
                    @else
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> Minimal 50% untuk lanjut perbaikan
                    @endif
                </p>
            </div>
            @endif

            <div class="text-xs text-[var(--color-stone)] space-y-1">
                <div>Mulai: {{ $serviceRepair->start_date->format('d/m/Y H:i') }}</div>
                @if($serviceRepair->completion_date)<div>Selesai: {{ $serviceRepair->completion_date->format('d/m/Y H:i') }}</div>@endif
            </div>
        </div>
    </div>
</div>

{{-- Lightbox --}}
<div x-data="{ open: false, url: '' }"
     @open-lightbox.window="url = $event.detail; open = true"
     x-show="open" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4">
    <button @click="open = false" class="absolute top-4 right-4 text-white hover:text-gray-300 p-2">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <img :src="url" @click.away="open = false" class="max-w-full max-h-full object-contain shadow-2xl rounded-lg">
</div>


{{-- Generic Confirmation Modal --}}
<div x-data="{ open: false, form: null, onConfirm: null, title: '', text: '', confirmText: 'Ya', color: 'blue' }" 
     @open-confirm-modal.window="open = true; form = $event.detail.form; onConfirm = $event.detail.onConfirm; title = $event.detail.title; text = $event.detail.text; confirmText = $event.detail.confirmText || 'Ya'; color = $event.detail.color || 'blue'"
     x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div @click.away="open = false" class="bg-white rounded-xl shadow-xl w-full max-w-sm overflow-hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        <div class="p-5 text-center">
            <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4"
                 :class="{
                     'bg-red-100 text-red-600': color === 'red',
                     'bg-blue-100 text-blue-600': color === 'blue',
                     'bg-amber-100 text-amber-600': color === 'amber',
                     'bg-green-100 text-green-600': color === 'green',
                     'bg-gray-100 text-gray-800': color === 'gray'
                 }">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1" x-text="title"></h3>
            <p class="text-sm text-gray-500 mb-6" x-text="text"></p>
            <div class="flex gap-2 justify-center">
                <button type="button" @click="open = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200">Batal</button>
                <button type="button" @click="if(onConfirm) { onConfirm(); open = false; } else if(form) form.submit()" class="px-4 py-2 text-white rounded-lg text-sm font-semibold"
                        :class="{
                            'bg-red-600 hover:bg-red-700': color === 'red',
                            'bg-blue-600 hover:bg-blue-700': color === 'blue',
                            'bg-amber-600 hover:bg-amber-700': color === 'amber',
                            'bg-green-600 hover:bg-green-700': color === 'green',
                            'bg-gray-800 hover:bg-gray-900': color === 'gray'
                        }" x-text="confirmText"></button>
            </div>
        </div>
    </div>
</div>

@endsection
