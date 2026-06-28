@extends('layouts.app')
@section('page-title', 'Pengadaan: ' . $productPurchase->product_purchase_code)
@section('content')
@php
$statusColors = [
    'draft'            => 'bg-gray-100 text-gray-700',
    'ordered'          => 'bg-blue-100 text-blue-700',
    'partial_received' => 'bg-amber-100 text-amber-700',
    'received'         => 'badge-success',
    'cancelled'        => 'badge-critical',
];
$statusLabels = [
    'draft'            => 'Draft',
    'ordered'          => 'Ordered',
    'partial_received' => 'Partial',
    'received'         => 'Received',
    'cancelled'        => 'Cancelled',
];
$sourceLabels = [
    'whatsapp'    => 'WhatsApp',
    'marketplace' => 'Marketplace',
    'offline'     => 'Toko Offline',
    'service'     => 'Dari Servis',
    'other'       => 'Lainnya',
];
$isEditable   = $productPurchase->isDraft();
$isFinal      = $productPurchase->isFinal();
$st           = $productPurchase->status;
@endphp

<div class="mb-6">
    <a href="javascript:void(0)" onclick="if(window.history.length > 2) { window.history.back(); } else { window.location.href = '{{ route('dashboard') }}'; }" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    
    {{-- ── Left Sidebar: Details & Source ─────────────────────────────── --}}
    <div class="lg:col-span-1 space-y-6 order-2 lg:order-1">
        
        {{-- Purchase Info --}}
        <div class="bg-[var(--color-canvas)] rounded-[32px] border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="type-heading-sm text-[var(--color-ink-deep)]">{{ $productPurchase->product_purchase_code }}</h3>
                <span class="badge {{ $statusColors[$st] ?? '' }} px-3 py-1.5 text-sm">
                    {{ $statusLabels[$st] ?? $st }}
                </span>
            </div>
            
            <div class="space-y-4 text-sm">
                <div>
                    <p class="text-xs font-bold text-[var(--color-slate)] uppercase tracking-wider">Kode Pengadaan</p>
                    <p class="font-medium mt-0.5 text-[var(--color-ink)]">{{ $productPurchase->product_purchase_code }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-[var(--color-slate)] uppercase tracking-wider">Tanggal Beli</p>
                    <p class="font-medium mt-0.5 text-[var(--color-ink)]">{{ $productPurchase->purchase_date->format('d/m/Y') }}</p>
                </div>
                @if($productPurchase->estimated_arrival_date)
                <div>
                    <p class="text-xs font-bold text-[var(--color-slate)] uppercase tracking-wider">Estimasi Tiba</p>
                    <p class="font-medium mt-0.5 text-indigo-600">{{ $productPurchase->estimated_arrival_date->format('d/m/Y') }}</p>
                </div>
                @endif
                <div>
                    <p class="text-xs font-bold text-[var(--color-slate)] uppercase tracking-wider">Supplier / Sumber</p>
                    <p class="font-medium mt-0.5 flex items-center gap-2 text-[var(--color-ink)]">
                        {{ $sourceLabels[$productPurchase->source] ?? $productPurchase->source }}
                        @if($productPurchase->supplier) <span class="text-[var(--color-stone)]">· {{ $productPurchase->supplier->name }}</span> @endif
                    </p>
                </div>
                @if($productPurchase->repairItem)
                <div>
                    <p class="text-xs font-bold text-[var(--color-slate)] uppercase tracking-wider">Terkait Servis</p>
                    <a href="{{ route('service-repairs.show', $productPurchase->repairItem->repair_code) }}" class="text-xs font-medium text-purple-600 hover:underline mt-0.5 inline-flex items-center gap-1.5 bg-purple-50 px-2.5 py-1 rounded-full">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Lihat Tiket: {{ $productPurchase->repairItem->repair_code }}
                    </a>
                </div>
                @endif
                <div>
                    <p class="text-xs font-bold text-[var(--color-slate)] uppercase tracking-wider">Dibuat Oleh</p>
                    <p class="mt-0.5 text-[var(--color-stone)]">{{ $productPurchase->creator->username }}</p>
                </div>
                
                @if($productPurchase->notes)
                <div class="pt-2">
                    <p class="text-xs font-bold text-[var(--color-slate)] uppercase tracking-wider mb-1">Catatan</p>
                    <p class="text-sm text-[var(--color-charcoal)] bg-gray-50 rounded-lg p-2.5 border border-gray-100">{{ $productPurchase->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Source Specific Detail --}}
        @if(in_array($productPurchase->source, ['marketplace', 'offline', 'other']))
        <div class="bg-[var(--color-canvas)] rounded-[32px] border border-gray-100 p-8">
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-4">Informasi Tambahan</h3>
            @if($productPurchase->source === 'marketplace')
                <div class="space-y-3 text-sm">
                    @if($productPurchase->marketplace_name)<div><p class="text-xs text-[var(--color-slate)]">Platform</p><p class="font-medium">{{ $productPurchase->marketplace_name }}</p></div>@endif
                    @if($productPurchase->marketplace_seller)<div><p class="text-xs text-[var(--color-slate)]">Seller</p><p class="font-medium">{{ $productPurchase->marketplace_seller }}</p></div>@endif
                </div>
            @elseif($productPurchase->source === 'offline')
                <div class="space-y-3 text-sm">
                    @if($productPurchase->store_name)<div><p class="text-xs text-[var(--color-slate)]">Toko</p><p class="font-medium">{{ $productPurchase->store_name }}</p></div>@endif
                    @if($productPurchase->receipt_number)<div><p class="text-xs text-[var(--color-slate)]">No. Nota</p><p class="font-medium font-mono text-xs">{{ $productPurchase->receipt_number }}</p></div>@endif
                    @if($productPurchase->offline_notes)<div><p class="text-xs text-[var(--color-slate)]">Catatan Offline</p><p>{{ $productPurchase->offline_notes }}</p></div>@endif
                </div>
            @endif
        </div>
        @endif
        
        {{-- WhatsApp Section --}}
        @if($productPurchase->source === 'whatsapp')
        <div class="bg-green-50 border border-green-200 rounded-[32px] p-8 mb-4 shadow-sm" x-data="{ copied: false }">
            <div class="flex items-center justify-between mb-4">
                <p class="font-bold text-green-800 flex items-center gap-2"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 0C5.385 0 0 5.384 0 12.031c0 2.128.552 4.195 1.603 6.02L.034 23.992l6.096-1.598A11.97 11.97 0 0012.031 24c6.646 0 12.03-5.385 12.03-12.031C24.062 5.384 18.678 0 12.031 0zm5.503 16.774c-.302-.152-1.785-.881-2.062-.982-.277-.101-.48-.152-.682.152-.202.303-.781.981-.958 1.183-.177.202-.354.227-.656.076-1.391-.655-2.529-1.533-3.486-2.923-.197-.286.195-.27.489-.858.101-.202.051-.38-.025-.532-.076-.152-.682-1.644-.934-2.251-.247-.591-.497-.512-.682-.521-.177-.008-.38-.01-.582-.01-.202 0-.53.076-.808.38-.277.303-1.06 1.036-1.06 2.527 0 1.491 1.085 2.932 1.237 3.134.152.202 2.136 3.262 5.176 4.571 1.637.705 2.502.82 3.42.684 1.026-.151 2.222-.907 2.535-1.783.313-.877.313-1.628.22-1.783-.093-.155-.345-.246-.647-.398z"/></svg> Pesan ke Supplier</p>
                <div class="flex gap-2 items-center">
                    @php $waStatus = $productPurchase->wa_message_status; @endphp
                    @if($waStatus === 'sent') <span class="text-[10px] uppercase font-bold text-green-700 bg-green-200 px-2 py-0.5 rounded-full">Terkirim</span>
                    @elseif($waStatus === 'failed') <span class="text-[10px] uppercase font-bold text-red-700 bg-red-200 px-2 py-0.5 rounded-full">Gagal</span>
                    @elseif($waStatus === 'pending') <span class="text-[10px] uppercase font-bold text-yellow-700 bg-yellow-200 px-2 py-0.5 rounded-full">Belum Dikirim</span>
                    @endif
                </div>
            </div>
            <pre class="text-xs text-green-900 bg-[var(--color-canvas)] rounded-lg p-3 border border-green-100 whitespace-pre-wrap font-sans mb-3">{{ $productPurchase->wa_message_content }}</pre>
            <div class="flex flex-col gap-3 mt-4">
                <button type="button" @click="navigator.clipboard.writeText(`{{ addslashes($productPurchase->wa_message_content) }}`); copied = true; setTimeout(() => copied = false, 2000)"
                    class="rounded-full bg-green-200 hover:bg-green-300 text-green-900 px-6 py-3 font-bold transition-colors w-full flex justify-center items-center gap-2">
                    <span x-show="!copied">📋 Salin Pesan</span><span x-show="copied">✅ Tersalin!</span>
                </button>
                @if($productPurchase->supplier && $productPurchase->supplier->whatsapp_number)
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $productPurchase->supplier->whatsapp_number) }}?text={{ urlencode($productPurchase->wa_message_content) }}" target="_blank" class="rounded-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 font-bold flex justify-center items-center gap-2 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 0C5.385 0 0 5.384 0 12.031c0 2.128.552 4.195 1.603 6.02L.034 23.992l6.096-1.598A11.97 11.97 0 0012.031 24c6.646 0 12.03-5.385 12.03-12.031C24.062 5.384 18.678 0 12.031 0zm5.503 16.774c-.302-.152-1.785-.881-2.062-.982-.277-.101-.48-.152-.682.152-.202.303-.781.981-.958 1.183-.177.202-.354.227-.656.076-1.391-.655-2.529-1.533-3.486-2.923-.197-.286.195-.27.489-.858.101-.202.051-.38-.025-.532-.076-.152-.682-1.644-.934-2.251-.247-.591-.497-.512-.682-.521-.177-.008-.38-.01-.582-.01-.202 0-.53.076-.808.38-.277.303-1.06 1.036-1.06 2.527 0 1.491 1.085 2.932 1.237 3.134.152.202 2.136 3.262 5.176 4.571 1.637.705 2.502.82 3.42.684 1.026-.151 2.222-.907 2.535-1.783.313-.877.313-1.628.22-1.783-.093-.155-.345-.246-.647-.398z"/></svg> 
                    Buka WhatsApp Supplier
                </a>
                @endif
            </div>
        </div>
        @endif
        
    </div>

    {{-- ── Right Main Area: Timeline, Items & Actions ──────────────── --}}
    <div class="lg:col-span-2 space-y-6 order-1 lg:order-2">
        
        {{-- Status Timeline --}}
        @if($st !== 'cancelled')
        <div class="bg-[var(--color-canvas)] rounded-[32px] border border-gray-100 p-8">
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-6">Status Pengadaan</h3>
            <div class="relative">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t-2 border-gray-200"></div>
                </div>
                <div class="relative flex justify-between">
                    {{-- Draft --}}
                    <div>
                        <div class="flex h-8 w-8 items-center justify-center rounded-full {{ in_array($st, ['draft', 'ordered', 'partial_received', 'received']) ? 'bg-blue-600' : 'bg-gray-200' }} ring-4 ring-white">
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </div>
                        <span class="absolute -ml-2 mt-2 text-xs font-semibold {{ in_array($st, ['draft', 'ordered', 'partial_received', 'received']) ? 'text-blue-600' : 'text-gray-500' }}">Draft</span>
                    </div>
                    
                    {{-- Ordered --}}
                    <div>
                        <div class="flex h-8 w-8 items-center justify-center rounded-full {{ in_array($st, ['ordered', 'partial_received', 'received']) ? 'bg-blue-600' : 'bg-gray-200' }} ring-4 ring-white">
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                        </div>
                        <span class="absolute -ml-3 mt-2 text-xs font-semibold {{ in_array($st, ['ordered', 'partial_received', 'received']) ? 'text-blue-600' : 'text-gray-500' }}">Ordered</span>
                    </div>

                    {{-- Received --}}
                    <div>
                        <div class="flex h-8 w-8 items-center justify-center rounded-full {{ $st === 'received' ? 'bg-green-600' : ($st === 'partial_received' ? 'bg-amber-500' : 'bg-gray-200') }} ring-4 ring-white">
                            @if($st === 'partial_received')
                            <span class="text-white font-bold text-xs">½</span>
                            @else
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            @endif
                        </div>
                        <span class="absolute -ml-3 mt-2 text-xs font-semibold {{ $st === 'received' ? 'text-green-600' : ($st === 'partial_received' ? 'text-amber-500' : 'text-gray-500') }}">{{ $st === 'partial_received' ? 'Partial' : 'Received' }}</span>
                    </div>
                </div>
            </div>
            <div class="mt-8 text-center text-xs text-gray-500">
                @if($st === 'draft')
                    Pengadaan dibuat dan siap dikirim ke supplier.
                @elseif($st === 'ordered')
                    Pesanan telah diteruskan ke supplier. Menunggu barang tiba.
                @elseif($st === 'partial_received')
                    Sebagian barang telah diterima. Menunggu sisa pengiriman.
                @elseif($st === 'received')
                    <span class="inline-flex items-center gap-1"><svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Pengadaan selesai. Semua barang telah diperiksa dan stok diperbarui.</span>
                @endif
            </div>
        </div>
        @else
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 flex items-center justify-center gap-2 text-red-700 shadow-sm">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="font-semibold text-sm">Pengadaan ini telah dibatalkan.</span>
        </div>
        @endif

        {{-- Status Actions (If not final) --}}
        @if(!$isFinal)
        <div class="bg-blue-50/50 rounded-[32px] border border-blue-100 p-6 ">
            <h4 class="text-sm font-semibold text-[var(--color-ink)] mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Tindakan Cepat
            </h4>

            <div class="flex flex-col sm:flex-row gap-3">
                @if($productPurchase->isDraft())
                {{-- Draft → Ordered --}}
                <form method="POST" action="{{ route('product-purchases.update-status', $productPurchase) }}" class="flex-1">@csrf @method('PATCH')
                    <input type="hidden" name="status" value="ordered">
                    <button type="submit" class="w-full rounded-full bg-[#0143b5] px-6 py-3.5 text-[14px] font-bold text-white hover:bg-blue-800 transition-colors flex justify-center items-center gap-2 hover:cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                        Kirim ke Supplier (Tandai Ordered)
                    </button>
                </form>
                @endif

                @if($productPurchase->isOrdered() || $productPurchase->isPartial())
                {{-- Ordered / Partial → Received (with qty input) --}}
                <div x-data="{ open: false }" class="flex-1">
                    <button @click="open = !open" type="button" class="w-full rounded-full bg-[#0143b5] px-6 py-3.5 text-[14px] font-bold text-white hover:bg-blue-800 transition-colors flex justify-center items-center gap-2 hover:cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Konfirmasi Penerimaan Barang
                    </button>
                    
                    {{-- Form Penerimaan --}}
                    <div x-show="open" x-transition x-cloak class="mt-4 border border-[var(--color-hairline)] bg-[var(--color-canvas)] rounded-xl shadow-lg p-5">
                        <form method="POST" action="{{ route('product-purchases.update-status', $productPurchase) }}" class="space-y-4">@csrf @method('PATCH')
                            
                            <div class="bg-amber-50 text-amber-800 p-3 rounded-lg text-xs border border-amber-100 flex items-start gap-2">
                                <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Input jumlah barang yang <strong>Diterima</strong> dan <strong>Ditolak</strong> (jika rusak). Stok hanya akan bertambah sejumlah barang yang diterima.</span>
                            </div>

                            <div class="overflow-x-auto border rounded-lg">
                                <table class="w-full text-xs">
                                    <thead>
                                        <tr class="bg-gray-50 text-left text-gray-600 border-b">
                                            <th class="p-2 font-medium">Produk</th>
                                            <th class="p-2 font-medium text-center">Pesan</th>
                                            <th class="p-2 font-medium text-center bg-green-50">Diterima</th>
                                            <th class="p-2 font-medium text-center bg-red-50">Ditolak</th>
                                            <th class="p-2 font-medium">Alasan Tolak</th>
                                            <th class="p-2 font-medium">Harga Aktual (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                    @foreach($productPurchase->items as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="p-2 font-medium text-[var(--color-ink)]">
                                            {{ $item->display_name }}
                                            <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                                        </td>
                                        <td class="p-2 text-center font-bold text-gray-500">{{ $item->quantity }}</td>
                                        <td class="p-2 bg-green-50/30">
                                            <input type="number" name="items[{{ $loop->index }}][quantity_received]" value="{{ $item->quantity_received ?: $item->quantity }}" min="0" max="{{ $item->quantity }}" class="w-full min-w-[60px] border-gray-300 rounded px-2 py-1 text-center text-xs font-semibold focus:border-green-500 focus:ring-green-500">
                                        </td>
                                        <td class="p-2 bg-red-50/30">
                                            <input type="number" name="items[{{ $loop->index }}][quantity_rejected]" value="{{ $item->quantity_rejected }}" min="0" max="{{ $item->quantity }}" class="w-full min-w-[60px] border-gray-300 rounded px-2 py-1 text-center text-xs text-red-600 focus:border-red-500 focus:ring-red-500">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="items[{{ $loop->index }}][rejection_notes]" value="{{ $item->rejection_notes }}" placeholder="Keterangan..." class="w-full border-gray-300 rounded px-2 py-1 text-xs focus:border-blue-500 focus:ring-blue-500">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="items[{{ $loop->index }}][purchase_price]" value="{{ (int)$item->purchase_price }}" class="w-full min-w-[90px] border-gray-300 rounded px-2 py-1 text-xs focus:border-blue-500 focus:ring-blue-500">
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div>
                                <label class="text-xs font-semibold text-gray-600 mb-1 block">Catatan Penerimaan (Global)</label>
                                <textarea name="partial_notes" rows="2" class="w-full border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Kondisi pengiriman, catatan dari kurir, dll...">{{ $productPurchase->partial_notes }}</textarea>
                            </div>

                            <div class="flex gap-3 pt-4">
                                <button type="submit" name="status" value="received" class="flex-1 rounded-full bg-green-600 px-6 py-3.5 text-[14px] font-bold text-white hover:bg-green-700 flex justify-center items-center gap-2 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Tandai Diterima Penuh
                                </button>
                                <button type="submit" name="status" value="partial_received" class="flex-1 rounded-full bg-amber-500 px-6 py-3.5 text-[14px] font-bold text-white hover:bg-amber-600 flex justify-center items-center gap-2 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    Tandai Sebagian Diterima
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                {{-- Cancel --}}
                <form method="POST" action="{{ route('product-purchases.update-status', $productPurchase) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengadaan ini?')" class="flex-shrink-0">@csrf @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="w-full rounded-full border-[2px] border-[rgba(10,19,23,0.12)] px-6 py-3.5 text-[14px] font-bold text-gray-700 hover:border-gray-400 transition-colors flex justify-center items-center gap-2">
                        Batal
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Partial Notes Warning --}}
        @if($productPurchase->partial_notes)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <h4 class="text-sm font-semibold text-amber-800">Catatan Penerimaan Sebagian</h4>
                <p class="text-sm text-amber-700 mt-1">{{ $productPurchase->partial_notes }}</p>
            </div>
        </div>
        @endif

        {{-- Items Table --}}
        <div class="bg-[var(--color-canvas)] rounded-[32px] border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-[var(--color-hairline)] bg-gray-50 flex items-center justify-between">
                <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]">Daftar Produk</h3>
                <span class="text-xs font-semibold text-gray-500 bg-gray-200 px-2.5 py-1 rounded-full">{{ $productPurchase->items->count() }} Item</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-[var(--color-canvas)] border-b border-[var(--color-hairline)] text-left text-xs font-semibold text-[var(--color-slate)] uppercase tracking-wider">
                            <th class="p-4">Produk</th>
                            <th class="p-4 text-center">Qty Pesan</th>
                            <th class="p-4 text-center">Diterima</th>
                            <th class="p-4 text-center">Ditolak</th>
                            <th class="p-4 text-right">Harga Aktual</th>
                            <th class="p-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-hairline-soft)] bg-[var(--color-canvas)]">
                    @foreach($productPurchase->items as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-4">
                            <p class="font-medium text-[var(--color-ink)]">{{ $item->display_name }}</p>
                            @if($item->notes)
                                <div class="flex gap-1.5 mt-1">
                                    <svg class="w-4 h-4 text-[var(--color-slate)] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="text-xs text-[var(--color-slate)]">{{ $item->notes }}</p>
                                </div>
                            @endif
                            @if($item->product && $item->product->status === 'temporary')
                                <a href="{{ route('products.edit', $item->product) }}" class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-amber-700 bg-amber-100 px-2.5 py-1 rounded-full mt-1.5 hover:bg-amber-200 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Lengkapi Lebih Detail Data Produk
                                </a>
                            @endif
                            @if($item->rejection_notes)
                                <div class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-red-600 bg-red-50 px-2.5 py-1 rounded-full border border-red-100 mt-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Tolak: {{ $item->rejection_notes }}
                                </div>
                            @endif
                        </td>
                        <td class="p-4 text-center font-bold text-gray-600">{{ $item->quantity }}</td>
                        <td class="p-4 text-center">
                            @if($item->quantity_received > 0)
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-700 font-bold text-xs">{{ $item->quantity_received }}</span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            @if($item->quantity_rejected > 0)
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-700 font-bold text-xs">{{ $item->quantity_rejected }}</span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="p-4 text-right tabular-nums">Rp {{ number_format($item->purchase_price, 0, ',', '.') }}</td>
                        <td class="p-4 text-right font-bold text-[var(--color-ink)] tabular-nums">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="5" class="p-4 text-right text-sm font-semibold text-[var(--color-slate)] uppercase tracking-wider">Total Pembelian</td>
                            <td class="p-4 text-right text-lg font-bold text-[var(--color-primary)] tabular-nums">Rp {{ number_format($productPurchase->total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
    </div>

</div>


@endsection
