@extends('layouts.app')
@section('page-title', $serviceRepair->repair_code)
@section('content')
@php
$statusColors = [
    'draft'         => 'bg-gray-100 text-gray-600',
    'waiting_dp'    => 'bg-red-100 text-red-700',
    'diagnosing'    => 'bg-blue-100 text-blue-700',
    'waiting_parts' => 'bg-amber-100 text-amber-700',
    'repairing'     => 'bg-indigo-100 text-indigo-700',
    'ready'         => 'bg-green-100 text-green-700',
    'done'          => 'badge-success',
    'cancelled'     => 'badge-critical',
];
$statusLabels = [
    'draft'         => '<svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg> Draft',
    'waiting_dp'    => '<svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Menunggu DP',
    'diagnosing'    => '<svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg> Diagnosa',
    'waiting_parts' => '<svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Menunggu Sparepart',
    'repairing'     => '<svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> Diproses',
    'ready'         => '<svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Siap Diambil',
    'done'          => '<svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Selesai',
    'cancelled'     => '<svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> {{ __('messages.canceled') }}',
];
$st = $serviceRepair->status;
$isFinal = $serviceRepair->isFinal();
$dpOk = $serviceRepair->isDpSufficient();
@endphp

<div class="mb-6">
    <a href="javascript:void(0)" onclick="if(window.history.length > 2) { window.history.back(); } else { window.location.href = '{{ route('dashboard') }}'; }" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
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
            @if(!$isFinal)
            <div class="mt-5 pt-4 border-t border-[var(--color-hairline-soft)] space-y-3">

                {{-- DP Notice --}}
                @if(in_array($st, ['draft','waiting_dp']))
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 space-y-2">
                    <p class="text-xs font-bold text-red-700">💰 {{ __('messages.dp_confirmation') }}</p>
                    <form method="POST" action="{{ route('service-repairs.update', $serviceRepair) }}" class="flex gap-2 items-center">
                        @csrf @method('PUT')
                        <div class="relative flex-1">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs font-bold text-gray-500">Rp</span>
                            <input type="text" name="down_payment" value="{{ $serviceRepair->down_payment > 0 ? $serviceRepair->down_payment : '' }}" placeholder="Nominal DP" class="input-field !h-9 !pl-9 !py-1 !text-xs input-rupiah">
                        </div>
                        <button type="submit" class="rounded-lg bg-red-600 px-3 py-2 text-xs font-medium text-white hover:bg-red-700 shrink-0">Catat DP</button>
                    </form>
                    <p class="text-[10px] text-red-500">Minimum DP 50%: Rp {{ number_format($serviceRepair->total_cost * 0.5, 0, ',', '.') }} dari total Rp {{ number_format($serviceRepair->total_cost, 0, ',', '.') }}</p>
                    @if($dpOk || $serviceRepair->total_cost == 0)
                    <form method="POST" action="{{ route('service-repairs.update-status', $serviceRepair) }}">@csrf @method('PATCH')
                        <input type="hidden" name="status" value="diagnosing">
                        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 w-full"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg> Mulai Diagnosa →</button>
                    </form>
                    @endif
                </div>
                @endif

                {{-- Diagnosing → request parts or start repair --}}
                @if($st === 'diagnosing')
                <div class="flex gap-2 flex-wrap">
                    <form method="POST" action="{{ route('service-repairs.update-status', $serviceRepair) }}">@csrf @method('PATCH')
                        <input type="hidden" name="status" value="repairing">
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">🔧 Mulai Perbaikan</button>
                    </form>
                </div>
                @endif

                {{-- Waiting Parts --}}
                @if($st === 'waiting_parts')
                <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-700">
                    ⏳ Menunggu sparepart yang diajukan datang. Status akan otomatis berubah ke "Perbaikan" saat semua sparepart tersedia.
                </div>
                @endif

                {{-- Repairing → Ready --}}
                @if($st === 'repairing')
                    @if($serviceRepair->allPartsAvailable())
                    <form method="POST" action="{{ route('service-repairs.update-status', $serviceRepair) }}">@csrf @method('PATCH')
                        <input type="hidden" name="status" value="ready">
                        <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">✅ Selesaikan Perbaikan (→ Siap Diambil)</button>
                    </form>
                    @else
                    <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-700">
                        ⏳ Menunggu sparepart yang diajukan datang. Tidak dapat menyelesaikan perbaikan.
                    </div>
                    @endif
                @endif

                {{-- Ready → WhatsApp + Handover --}}
                @if($st === 'ready')
                <div class="space-y-3">
                    @if($serviceRepair->customer_phone)
                    <a href="{{ $serviceRepair->buildWhatsAppUrl($shopName) }}" target="_blank"
                       class="flex items-center gap-2 rounded-xl bg-green-500 hover:bg-green-600 px-5 py-3 text-sm font-semibold text-white transition-colors">
                        📱 Hubungi via WhatsApp (Buka chat, tidak auto-send)
                    </a>
                    @endif
                    <form method="POST" action="{{ route('service-repairs.update', $serviceRepair) }}" class="flex gap-2 items-center bg-blue-50 border border-blue-200 rounded-xl p-3">
                        @csrf @method('PUT')
                        <select name="payment_method" class="input-field !h-9 !py-1 !text-xs flex-1">
                            <option value="">Metode Pelunasan</option>
                            <option value="cash">Cash</option>
                            <option value="qris">QRIS</option>
                        </select>
                        {{-- Also update status to done via this form --}}
                    </form>
                    <form method="POST" action="{{ route('service-repairs.update-status', $serviceRepair) }}">@csrf @method('PATCH')
                        <input type="hidden" name="status" value="done">
                        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 w-full" onclick="return confirm('Konfirmasi barang sudah diambil pelanggan?')">
                            📦 {{ __('messages.confirm_handover') }}
                        </button>
                    </form>
                </div>
                @endif

                {{-- Cancel (always available unless final) --}}
                @if(in_array($st, ['draft','waiting_dp','diagnosing']))
                <form method="POST" action="{{ route('service-repairs.update-status', $serviceRepair) }}" onsubmit="return confirm('Batalkan tiket ini?')">@csrf @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="text-xs text-red-600 hover:underline">❌ {{ __('messages.cancel_ticket') }}</button>
                </form>
                @endif
            </div>
            @else
            <div class="mt-4 rounded-xl bg-gray-50 border border-gray-200 px-4 py-3 text-sm text-center text-gray-500">
                {{ $st === 'done' ? '🏁 {{ __('messages.ticket_completed') }}' : '❌ {{ __('messages.ticket_canceled') }}' }}
            </div>
            @endif

            {{-- Documents --}}
            @if(!$isFinal || $st === 'done')
            <div class="mt-4 pt-4 border-t border-[var(--color-hairline-soft)] flex gap-2 flex-wrap">
                <a href="{{ route('service-repairs.receipt', $serviceRepair) }}" target="_blank" class="btn-ghost !py-1.5 !px-3 !text-xs"><svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg> Cetak Nota</a>
                <a href="{{ route('service-repairs.receipt.pdf', $serviceRepair) }}" class="btn-ghost !py-1.5 !px-3 !text-xs"><svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> PDF</a>
            </div>
            @endif
        </div>

        {{-- ── Device Items ────────────────────────────────────────────── --}}
        <div class="card-feature p-6">
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-4">🔧 Detail Perbaikan & Diagnosa</h3>
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
                        <div class="flex h-full transition-transform duration-300" :style="'transform: translateX(-' + (activeSlide * 100) + '%)'">
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
                    @if($serviceRepair->isTechEditable() || $serviceRepair->isDiagnosing())
                    <form method="POST" action="{{ route('service-repairs.update', $serviceRepair) }}" enctype="multipart/form-data" class="space-y-3">@csrf @method('PUT')
                        <input type="hidden" name="items[{{ $idx }}][id]" value="{{ $item->id }}">
                        <div>
                            <label class="block text-xs font-semibold text-[var(--color-slate)] mb-1">Hasil Diagnosa</label>
                            <textarea name="items[{{ $idx }}][diagnosis_result]" rows="2" class="input-field !h-auto !py-2" placeholder="Tuliskan hasil diagnosa...">{{ $item->diagnosis_result }}</textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-[var(--color-slate)] mb-1">Biaya Jasa</label>
                                <input type="text" name="items[{{ $idx }}][service_fee]" value="{{ $item->service_fee }}" class="input-field input-rupiah">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[var(--color-slate)] mb-1">Tambah Foto</label>
                                <input type="file" name="items[{{ $idx }}][images][]" multiple accept="image/*" class="input-field !py-1.5 text-xs">
                            </div>
                        </div>
                        <button type="submit" class="btn-primary w-full !py-2 !text-xs justify-center flex">💾 Simpan Diagnosa & Biaya</button>
                    </form>
                    @elseif($item->diagnosis_result)
                    <div class="rounded-lg bg-blue-50 border border-blue-100 px-3 py-2 text-sm text-blue-800">
                        <p class="font-semibold text-xs text-blue-500 mb-1">DIAGNOSA:</p>
                        {{ $item->diagnosis_result }}
                    </div>
                    @endif

                    {{-- ── Sparepart Section ───────────────────────────── --}}
                    <div x-data="{ open: {{ $item->children->count() > 0 ? 'true' : 'false' }} }" class="border border-[var(--color-hairline)] rounded-lg overflow-hidden">
                        <button type="button" @click="open = !open" class="w-full flex items-center justify-between p-3 bg-[var(--color-surface-soft)] hover:bg-gray-100 transition-colors">
                            <span class="text-sm font-semibold text-[var(--color-ink-deep)]">
                                🔩 Sparepart ({{ $item->children->count() }})
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
                                            <a href="{{ route('product-purchases.show', $latestPO) }}" class="text-blue-600 hover:underline text-[10px]">📦 Lihat Pengadaan: {{ $latestPO->product_purchase_code }}</a>
                                        @endif
                                    </td>
                                    <td class="py-2 text-center">
                                        @if($part->isFromStock()) <span class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">Stok</span>
                                        @elseif($part->isRequested()) <span class="bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded text-[10px]">Pengajuan</span>
                                        @else <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="py-2 text-center">
                                        @if($part->sparepart_status === 'available') <span class="bg-green-100 text-green-700 px-1.5 py-0.5 rounded text-[10px]">✅ Tersedia</span>
                                        @elseif($part->sparepart_status === 'pending') <span class="bg-amber-100 text-amber-600 px-1.5 py-0.5 rounded text-[10px]">⏳ Menunggu</span>
                                        @elseif($part->sparepart_status === 'used') <span class="bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded text-[10px]">✔ Dipakai</span>
                                        @else <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="py-2 text-center">{{ $part->quantity }}</td>
                                    <td class="py-2 text-right font-medium">Rp {{ number_format($part->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                            @else
                            <p class="text-xs text-gray-400 text-center py-2">Belum ada sparepart.</p>
                            @endif

                            {{-- Add sparepart form — only when diagnosing / repairing --}}
                            @if($serviceRepair->isDiagnosing() || $serviceRepair->isRepairing())
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
                                    <button type="button" @click="mode='from_stock'; selectedCode=''" class="text-xs px-3 py-1.5 rounded-lg font-medium transition-colors" :class="mode==='from_stock' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'">📦 Dari Stok</button>
                                    <button type="button" @click="mode='requested'; selectedCode=''" class="text-xs px-3 py-1.5 rounded-lg font-medium transition-colors" :class="mode==='requested' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-600'">🔖 Pengajuan</button>
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
                                            <div class="col-span-2"><label class="text-[10px] text-gray-500">Harga</label><input type="text" name="unit_price" x-model="unitPrice" class="input-field input-rupiah !h-8 !py-1 !text-xs w-full" readonly></div>
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
                                    @if($requestedParts->count() > 0)
                                    <div class="mt-2 border border-purple-200 rounded-lg p-3 bg-purple-50 space-y-2">
                                        <p class="text-xs font-bold text-purple-800">Sparepart belum diajukan ke pengadaan:</p>
                                        @foreach($requestedParts as $rPart)
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-medium">{{ $rPart->name }} ({{ $rPart->quantity }}x)</span>
                                            <form method="POST" action="{{ route('service-repairs.request-part', $serviceRepair) }}">@csrf
                                                <input type="hidden" name="sparepart_item_id" value="{{ $rPart->id }}">
                                                <button type="submit" class="text-xs bg-purple-600 text-white rounded px-2 py-1 hover:bg-purple-700">🛒 Ajukan Pengadaan</button>
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

        {{-- Global notes form --}}
        <div class="card-feature p-6">
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-4">Catatan Global</h3>
            <form method="POST" action="{{ route('service-repairs.update', $serviceRepair) }}" class="space-y-3">@csrf @method('PUT')
                <textarea name="notes" rows="3" class="input-field !h-auto !py-2" placeholder="Catatan umum tiket...">{{ $serviceRepair->notes }}</textarea>
                <div class="text-right"><button type="submit" class="btn-primary !py-2 !px-6 text-sm">Simpan Catatan</button></div>
            </form>
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
                @php $remaining = max(0, $serviceRepair->total_cost - $serviceRepair->down_payment); @endphp
                <div class="border-t pt-2 flex justify-between font-bold"><span>Sisa Tagihan</span><span class="{{ $remaining > 0 ? 'text-red-600' : 'text-green-600' }}">Rp {{ number_format($remaining, 0, ',', '.') }}</span></div>
            </div>

            {{-- DP Progress Bar --}}
            @if($serviceRepair->total_cost > 0)
            @php $dpPct = min(100, round(($serviceRepair->down_payment / $serviceRepair->total_cost) * 100)); @endphp
            <div>
                <div class="flex justify-between text-xs text-gray-500 mb-1"><span>DP Progress</span><span>{{ $dpPct }}%</span></div>
                <div class="w-full bg-gray-200 rounded-full h-2"><div class="h-2 rounded-full transition-all {{ $dpPct >= 50 ? 'bg-green-500' : 'bg-amber-500' }}" style="width:{{ $dpPct }}%"></div></div>
                <p class="text-[10px] mt-1 {{ $dpPct >= 50 ? 'text-green-600' : 'text-amber-600' }}">{{ $dpPct >= 50 ? '✅ DP sudah ≥ 50%' : '⚠️ Minimal 50% untuk lanjut diagnosa' }}</p>
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

@endsection


