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
    'draft'            => __('messages.draft'),
    'ordered'          => __('messages.ordered'),
    'partial_received' => __('messages.partial_received'),
    'received'         => __('messages.received'),
    'cancelled'        => __('messages.canceled'),
];
$sourceLabels = [
    'whatsapp'    => 'Supplier',
    'marketplace' => 'Marketplace',
    'offline'     => 'Toko Offline',
    'service'     => 'Dari Servis',
    'other'       => 'Lainnya',
];
$isEditable   = $productPurchase->isDraft();
$isFinal      = $productPurchase->isFinal();
$st           = $productPurchase->status;
@endphp

<div class="mb-6 flex justify-between items-center">
    @if(auth()->user()->isTeknisi() && $productPurchase->repairItem && $productPurchase->repairItem->serviceRepair)
        <a href="{{ route('service-repairs.show', $productPurchase->repairItem->serviceRepair) }}" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
    @else
        <a href="{{ route('product-purchases.index') }}" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
    @endif
    
    <div class="flex gap-2">
        @if($st === 'draft' && in_array(auth()->user()->role->name, ['owner', 'gudang']))
        <a href="{{ route('product-purchases.edit', $productPurchase) }}" class="btn-ghost !text-blue-600 !border-blue-600/20 !px-4 text-xs font-bold flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg> Ubah
        </a>
        @endif
        
        @if($st !== 'cancelled' && in_array(auth()->user()->role->name, ['owner', 'gudang']))
        <button @click="$dispatch('open-delete-modal', { url: '{{ route('product-purchases.destroy', $productPurchase) }}' })" class="btn-ghost !text-[var(--color-critical)] !border-[var(--color-critical)]/20 !px-4 text-xs font-bold flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> {{ __('messages.delete') }}</button>
        @endif
    </div>
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
                    <p class="text-xs font-bold text-[var(--color-slate)] uppercase tracking-wider">Sumber Pembelian</p>
                    <p class="font-medium mt-0.5 text-[var(--color-ink)]">{{ $productPurchase->getSummarySources() }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-[var(--color-slate)] uppercase tracking-wider">Supplier</p>
                    <p class="font-medium mt-0.5 text-[var(--color-ink)]">{{ $productPurchase->getSummarySuppliers() }}</p>
                </div>
                @if($productPurchase->repairItem)
                <div>
                    <p class="text-xs font-bold text-[var(--color-slate)] uppercase tracking-wider">Asal Layanan Perbaikan</p>
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

        {{-- WhatsApp Section --}}
        @php
            $whatsappSuppliers = $productPurchase->getWhatsappSuppliers();
        @endphp
        @if(!empty($whatsappSuppliers) && in_array(auth()->user()->role->name, ['owner', 'gudang']))
        <div class="bg-[var(--color-canvas)] rounded-[32px] border border-gray-100 p-8">
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-4">Pesan WhatsApp</h3>
            
            <div class="space-y-6">
                @foreach($whatsappSuppliers as $ws)
                <div class="bg-green-50 border border-green-200 rounded-2xl p-5 shadow-sm" x-data='{ copied: false, message: {{ json_encode($ws['message']) }} }'>
                    <div class="flex items-center justify-between mb-3">
                        <p class="font-bold text-green-800 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 0C5.385 0 0 5.384 0 12.031c0 2.128.552 4.195 1.603 6.02L.034 23.992l6.096-1.598A11.97 11.97 0 0012.031 24c6.646 0 12.03-5.385 12.03-12.031C24.062 5.384 18.678 0 12.031 0zm5.503 16.774c-.302-.152-1.785-.881-2.062-.982-.277-.101-.48-.152-.682.152-.202.303-.781.981-.958 1.183-.177.202-.354.227-.656.076-1.391-.655-2.529-1.533-3.486-2.923-.197-.286.195-.27.489-.858.101-.202.051-.38-.025-.532-.076-.152-.682-1.644-.934-2.251-.247-.591-.497-.512-.682-.521-.177-.008-.38-.01-.582-.01-.202 0-.53.076-.808.38-.277.303-1.06 1.036-1.06 2.527 0 1.491 1.085 2.932 1.237 3.134.152.202 2.136 3.262 5.176 4.571 1.637.705 2.502.82 3.42.684 1.026-.151 2.222-.907 2.535-1.783.313-.877.313-1.628.22-1.783-.093-.155-.345-.246-.647-.398z"/></svg> 
                            {{ $ws['supplier']->name }}
                        </p>
                    </div>
                    <textarea x-model="message" class="w-full text-xs text-green-900 bg-white rounded-lg p-3 border border-green-100 whitespace-pre-wrap font-sans mb-3 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition-all min-h-[200px]" rows="8"></textarea>
                    <div class="flex flex-col gap-2">
                        <button type="button" @click="navigator.clipboard.writeText(message); copied = true; setTimeout(() => copied = false, 2000)"
                            class="rounded-full bg-green-200 hover:bg-green-300 text-green-900 px-4 py-2 text-sm font-bold transition-colors w-full flex justify-center items-center gap-2 cursor-pointer">
                            <span x-show="!copied">📋 Salin Pesan</span><span x-show="copied">✅ Tersalin!</span>
                        </button>
                        @if($ws['supplier']->whatsapp_number)
                        <a :href="`https://wa.me/{{ preg_replace('/\D/', '', $ws['supplier']->whatsapp_number) }}?text=${encodeURIComponent(message)}`" target="_blank" class="rounded-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 text-sm font-bold flex justify-center items-center gap-2 transition-colors cursor-pointer">
                            Buka WhatsApp
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
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
                        <div class="flex h-8 w-8 items-center justify-center rounded-full {{ in_array($st, ['draft', 'ordered', 'partial_received', 'received']) ? 'bg-black' : 'bg-gray-200' }} ring-4 ring-white">
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </div>
                        <span class="absolute -ml-2 mt-2 text-xs font-semibold {{ in_array($st, ['draft', 'ordered', 'partial_received', 'received']) ? 'text-black' : 'text-gray-500' }}">Draft</span>
                    </div>
                    
                    {{-- Ordered --}}
                    <div>
                        <div class="flex h-8 w-8 items-center justify-center rounded-full {{ in_array($st, ['ordered', 'partial_received', 'received']) ? 'bg-black' : 'bg-gray-200' }} ring-4 ring-white">
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                        </div>
                        <span class="absolute -ml-3 mt-2 text-xs font-semibold {{ in_array($st, ['ordered', 'partial_received', 'received']) ? 'text-black' : 'text-gray-500' }}">Ordered</span>
                    </div>

                    {{-- Received --}}
                    <div>
                        <div class="flex h-8 w-8 items-center justify-center rounded-full {{ $st === 'received' ? 'bg-black' : ($st === 'partial_received' ? 'bg-amber-500' : 'bg-gray-200') }} ring-4 ring-white">
                            @if($st === 'partial_received')
                            <span class="text-white font-bold text-xs">½</span>
                            @else
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            @endif
                        </div>
                        <span class="absolute -ml-3 mt-2 text-xs font-semibold {{ $st === 'received' ? 'text-black' : ($st === 'partial_received' ? 'text-amber-500' : 'text-gray-500') }}">{{ $st === 'partial_received' ? 'Partial' : 'Received' }}</span>
                    </div>
                </div>
            </div>
            <div class="mt-8 text-center text-xs text-gray-500">
                @if($st === 'draft')
                    {{ __('messages.procurement_draft_desc') }}
                @elseif($st === 'ordered')
                    {{ __('messages.procurement_ordered_desc') }}
                @elseif($st === 'partial_received')
                    Sebagian barang telah {{ __('messages.received') }}. Menunggu sisa pengiriman.
                @elseif($st === 'received')
                    <span class="inline-flex items-center gap-1"><svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> {{ __('messages.procurement_received_desc') }}</span>
                @endif
            </div>
        </div>
        @else
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 flex items-center justify-center gap-2 text-red-700 shadow-sm">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="font-semibold text-sm">{{ __('messages.procurement_cancelled_desc') }}</span>
        </div>
        @endif

        {{-- Status Actions (If not final, only owner or gudang can execute actions) --}}
        @if(!$isFinal && in_array(auth()->user()->role->name, ['owner', 'gudang']))
        <div class="bg-blue-50/50 rounded-[32px] border border-blue-100 p-6 ">
            <h4 class="text-sm font-semibold text-[var(--color-ink)] mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                {{ __('messages.quick_actions') }}
            </h4>

            <div class="flex flex-col sm:flex-row gap-3">
                @if($productPurchase->isDraft())
                {{-- Draft → Ordered --}}
                <form method="POST" action="{{ route('product-purchases.update-status', $productPurchase) }}" class="flex-1">@csrf @method('PATCH')
                    <input type="hidden" name="status" value="ordered">
                    <button type="button" @click="$dispatch('open-action-confirm', { title: 'Tandai Ordered?', message: 'Apakah Anda yakin pesanan ini sudah diteruskan ke supplier?', formEl: $el.closest('form') })" class="w-full rounded-full bg-black px-6 py-3.5 text-[14px] font-bold text-white hover:bg-gray-800 transition-colors flex justify-center items-center gap-2 hover:cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                        Kirim Pengajuan Pengadaan (Tandai Ordered)
                    </button>
                </form>
                @endif

                @if($productPurchase->isOrdered() || $productPurchase->isPartial())
                {{-- Ordered / Partial → Received (with qty input) --}}
                <div x-data="{ open: false }" class="flex-1">
                    <button @click="open = !open" type="button" class="w-full rounded-full bg-black px-6 py-3.5 text-[14px] font-bold text-white hover:bg-gray-800 transition-colors flex justify-center items-center gap-2 hover:cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Konfirmasi Penerimaan Barang
                    </button>
                    
                    {{-- Form Penerimaan --}}
                    <div x-show="open" x-transition x-cloak class="mt-4 border border-[var(--color-hairline)] bg-[var(--color-canvas)] rounded-xl shadow-lg p-5">
                        <form method="POST" action="{{ route('product-purchases.update-status', $productPurchase) }}" class="space-y-4">@csrf @method('PATCH')
                            
                            <div class="bg-amber-50 text-amber-800 p-3 rounded-lg text-xs border border-amber-100 flex items-start gap-2">
                                <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Input jumlah barang yang <strong>{{ __('messages.received') }}</strong> dan <strong>{{ __('messages.rejected') }}</strong> (jika rusak). Stok hanya akan bertambah sejumlah barang yang {{ __('messages.received') }}.</span>
                            </div>

                            <div class="overflow-x-auto border rounded-lg">
                                <table class="w-full text-xs">
                                    <thead>
                                        <tr class="bg-gray-50 text-left text-gray-600 border-b">
                                            <th class="p-2 font-medium">Produk</th>
                                            <th class="p-2 font-medium text-center">Pesan</th>
                                            <th class="p-2 font-medium text-center bg-green-50">{{ __('messages.received') }}</th>
                                            <th class="p-2 font-medium text-center bg-red-50">{{ __('messages.rejected') }}</th>
                                            <th class="p-2 font-medium">{{ __('messages.reason_rejected') }}</th>
                                            <th class="p-2 font-medium">{{ __('messages.actual_price') }}</th>
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
                                <button type="button" @click="$dispatch('open-action-confirm', { title: 'Terima Penuh?', message: 'Stok barang akan otomatis bertambah.', formEl: $el.closest('form'), btnName: 'status', btnValue: 'received' })" name="status" value="received" class="flex-1 rounded-full bg-green-600 px-6 py-3.5 text-[14px] font-bold text-white hover:bg-green-700 flex justify-center items-center gap-2 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ __('messages.mark_fully_received') }}
                                </button>
                                <button type="button" @click="$dispatch('open-action-confirm', { title: 'Terima Sebagian?', message: 'Stok barang akan bertambah sesuai input, namun status pengadaan belum selesai.', formEl: $el.closest('form'), btnName: 'status', btnValue: 'partial_received' })" name="status" value="partial_received" class="flex-1 rounded-full bg-amber-500 px-6 py-3.5 text-[14px] font-bold text-white hover:bg-amber-600 flex justify-center items-center gap-2 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    {{ __('messages.mark_partially_received') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                {{-- Cancel --}}
                <form method="POST" action="{{ route('product-purchases.update-status', $productPurchase) }}"  class="flex-shrink-0">@csrf @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="button" @click="$dispatch('open-action-confirm', { title: 'Batalkan Pengadaan?', message: 'Pengadaan yang dibatalkan tidak bisa dilanjutkan lagi. Anda yakin?', formEl: $el.closest('form') })" class="w-full rounded-full border-[2px] border-[rgba(10,19,23,0.12)] px-6 py-3.5 text-[14px] font-bold text-gray-700 hover:border-gray-400 transition-colors flex justify-center items-center gap-2">Batal</button>
                </form>
            </div>
        </div>
        @endif

        {{-- Partial Notes Warning --}}
        @if($productPurchase->partial_notes)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <h4 class="text-sm font-semibold text-amber-800">Catatan Penerimaan</h4>
                <p class="text-sm text-amber-700 mt-1">{{ $productPurchase->partial_notes }}</p>
            </div>
        </div>
        @endif

        {{-- Items Table --}}
        <div class="bg-[var(--color-canvas)] rounded-[32px] border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-[var(--color-hairline)] bg-gray-50 flex items-center justify-between">
                <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]">{{ __('messages.product_list') }}</h3>
                <span class="text-xs font-semibold text-gray-500 bg-gray-200 px-2.5 py-1 rounded-full">{{ $productPurchase->items->count() }} Item</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-[var(--color-canvas)] border-b border-[var(--color-hairline)] text-left text-xs font-semibold text-[var(--color-slate)] uppercase tracking-wider">
                            <th class="p-4">Produk</th>
                            <th class="p-4 text-center">{{ __('messages.order_qty') }}</th>
                            <th class="p-4 text-center">{{ __('messages.received') }}</th>
                            <th class="p-4 text-center">{{ __('messages.rejected') }}</th>
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
                            @if($item->product && $item->product->status === 'temporary' && in_array(auth()->user()->role->name, ['owner', 'kasir', 'gudang']))
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
                            <div class="mt-2 text-[10px] text-[var(--color-stone)] border-t border-[var(--color-hairline)] pt-2">
                                <span class="font-bold">{{ $sourceLabels[$item->source] ?? $item->source }}:</span>
                                @if($item->source === 'whatsapp' && $item->supplier)
                                    {{ $item->supplier->name }}
                                @elseif($item->source === 'marketplace')
                                    {{ $item->marketplace_name }} {{ $item->marketplace_seller ? "({$item->marketplace_seller})" : '' }}
                                @elseif($item->source === 'offline')
                                    {{ $item->store_name }}
                                @else
                                    {{ $item->other_source }}
                                @endif
                            </div>
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
                            <td colspan="5" class="p-4 text-right text-sm font-semibold text-[var(--color-slate)] uppercase tracking-wider">{{ __('messages.total_purchase') }}</td>
                            <td class="p-4 text-right text-lg font-bold text-[var(--color-primary)] tabular-nums">Rp {{ number_format($productPurchase->total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
    </div>

</div>



<!-- Modal Konfirmasi {{ __('messages.quick_actions') }} -->
<div x-data="{
    openAction: false,
    title: '',
    message: '',
    formEl: null,
    btnName: null,
    btnValue: null,
    confirmAction() {
        if(this.formEl) {
            if(this.btnName) {
                let hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = this.btnName;
                hidden.value = this.btnValue;
                this.formEl.appendChild(hidden);
            }
            this.formEl.submit();
        }
    }
}"
@open-action-confirm.window="
    openAction = true;
    title = $event.detail.title;
    message = $event.detail.message;
    formEl = $event.detail.formEl;
    btnName = $event.detail.btnName;
    btnValue = $event.detail.btnValue;
"
class="fixed inset-0 z-[90] flex items-center justify-center p-4" x-show="openAction" x-cloak>
    <div x-show="openAction" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-[var(--color-ink-deep)]/40 backdrop-blur-sm" @click="openAction = false"></div>
    <div x-show="openAction" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="relative w-full max-w-sm rounded-[var(--radius-xxxl)] bg-[var(--color-canvas)] p-8 shadow-2xl text-center">
        <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]" x-text="title"></h3>
        <p class="mt-2 type-body-sm text-[var(--color-slate)] mx-auto" x-text="message"></p>
        <div class="mt-6 flex gap-3">
            <button @click="openAction = false" type="button" class="btn-ghost flex-1">Batal</button>
            <button @click="confirmAction()" type="button" class="w-full inline-flex items-center justify-center rounded-[var(--radius-full)] bg-[#0143b5] px-6 py-3 text-sm font-bold text-white transition-all duration-150">Ya, Lanjutkan</button>
        </div>
    </div>
</div>
@endsection
