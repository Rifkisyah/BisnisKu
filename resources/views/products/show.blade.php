@extends('layouts.app')
@section('page-title', __('messages.detail') . ' ' . __('messages.product'))
@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('products.index') }}" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1">
            <div class="bg-[var(--color-canvas)] rounded-[32px] border border-gray-100 p-8 text-center shadow-sm">
                <div class="flex flex-col items-center gap-3 mb-5">
                    <h3 class="type-heading-sm text-[var(--color-ink-deep)] text-center break-words w-full">{{ $product->name }}</h3>
                    <div class="flex flex-wrap justify-center gap-2">
                        <span class="badge badge-info">{{ $product->category->name }}</span>
                        <span class="badge {{ $product->status === 'active' ? 'badge-success' : 'badge-critical' }}">{{ $product->status }}</span>
                    </div>
                </div>
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" class="w-full aspect-square object-cover rounded-xl border border-[var(--color-hairline-soft)] shadow-sm mb-6">
                @else
                    <div class="w-full aspect-square rounded-xl bg-[var(--color-surface-soft)] text-[var(--color-slate)] flex flex-col items-center justify-center mb-6 border border-[var(--color-hairline-soft)]">
                        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-sm mt-3 font-medium">Tanpa Gambar</span>
                    </div>
                @endif
                <div class="flex flex-col gap-2 mb-3 text-center">
                    <p class="type-caption text-[var(--color-primary)] font-bold">{{ $product->product_code }}</p>
                </div>
                
                <div class="mt-8 flex flex-col gap-3">
                    @if(!auth()->user()->isKasir())
                    <a href="{{ route('products.edit', $product) }}" class="rounded-full bg-gray-900 px-8 py-3.5 text-[14px] font-bold text-white hover:bg-gray-800 transition-colors w-full flex justify-center">{{ __('messages.edit') }}</a>
                    @endif
                </div>
        </div>
    </div>
    
    <div class="md:col-span-2 space-y-6">
        <div class="bg-[var(--color-canvas)] rounded-[32px] border border-gray-100 p-8 shadow-sm">
            <h3 class="text-2xl font-bold text-[var(--color-ink-deep)] border-b border-[var(--color-hairline-soft)] pb-4 mb-6">Informasi Produk</h3>
            <div class="grid grid-cols-2 gap-y-4 gap-x-6">
                <div>
                    <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider mb-1">Tipe Produk</p>
                    <p class="type-body-sm font-medium text-[var(--color-ink)]">{{ $product->type === 'physical' ? 'Fisik' : 'Digital' }}</p>
                </div>
                <div>
                    <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider mb-1">Supplier</p>
                    <p class="type-body-sm font-medium text-[var(--color-ink)]">{{ $product->supplier ? $product->supplier->name : 'Tanpa Supplier' }}</p>
                </div>
                <div>
                    <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider mb-1">Harga Beli</p>
                    <p class="type-body-sm font-medium text-[var(--color-ink)]">Rp {{ number_format($product->purchase_price,0,',','.') }}</p>
                </div>
                <div>
                    <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider mb-1">Harga Jual</p>
                    <p class="type-body-sm font-bold text-[var(--color-primary)]">Rp {{ number_format($product->selling_price,0,',','.') }}</p>
                </div>
                <div>
                    <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider mb-1">Stok Saat Ini</p>
                    <p class="type-body-sm font-bold {{ $product->isLowStock() ? 'text-[var(--color-critical)]' : 'text-[var(--color-ink)]' }}">{{ $product->stock }}</p>
                </div>
                <div>
                    <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider mb-1">Batas Stok Minimum</p>
                    <p class="type-body-sm font-medium text-[var(--color-ink)]">{{ $product->minimum_stock }}</p>
                </div>
                <div class="col-span-2 mt-2">
                    <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider mb-1">Deskripsi Lengkap</p>
                    <p class="type-body-sm text-[var(--color-ink)] whitespace-pre-wrap">{{ $product->description ?: 'Tidak ada deskripsi.' }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-[var(--color-canvas)] rounded-[32px] border border-gray-100 p-8 shadow-sm" x-data="{ showHistory: false }">
            <div class="flex justify-between items-center border-b border-[var(--color-hairline-soft)] pb-4 mb-6">
                <h3 class="text-xl font-bold text-[var(--color-ink-deep)]">Riwayat Pergerakan Stok (10 Terakhir)</h3>
                <button @click="showHistory = !showHistory" class="type-body-sm font-medium text-[var(--color-primary)] hover:underline flex items-center gap-1">
                    <span x-text="showHistory ? 'Sembunyikan' : 'Tampilkan'"></span>
                    <svg class="w-4 h-4 transition-transform" :class="showHistory ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
            </div>
            <div x-show="showHistory" x-transition class="overflow-x-auto" style="display: none;">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-[var(--color-hairline-soft)] text-xs font-medium text-[var(--color-slate)] uppercase whitespace-nowrap">
                        <th class="pb-2 pr-4 text-left">Tanggal</th>
                        <th class="pb-2 pr-4 text-center">{{ app()->getLocale() == 'id' ? 'Jenis' : 'Type' }}</th>
                        <th class="pb-2 pr-4 text-center">{{ app()->getLocale() == 'id' ? 'Jumlah' : 'Quantity' }}</th>
                        <th class="pb-2 min-w-[200px] text-left">Keterangan</th>
                    </tr></thead>
                    <tbody class="divide-y divide-[var(--color-hairline-soft)]/50">
                    @forelse($product->stockMovements()->latest()->take(10)->get() as $mov)
                    <tr>
                        <td class="py-3 pr-4 whitespace-nowrap text-left text-[var(--color-ink)]">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 pr-4 text-center">
                            @php
                                $typeLabel = strtoupper($mov->type);
                                if (app()->getLocale() == 'id') {
                                    if ($mov->type === 'in') $typeLabel = 'MASUK';
                                    elseif ($mov->type === 'out') $typeLabel = 'KELUAR';
                                    elseif ($mov->type === 'adjustment') $typeLabel = 'PENYESUAIAN';
                                }
                            @endphp
                            <span class="badge {{ $mov->type === 'in' ? 'badge-success' : ($mov->type === 'out' ? 'badge-critical' : 'badge-attention') }}">{{ $typeLabel }}</span>
                        </td>
                        <td class="py-3 pr-4 text-center font-bold {{ $mov->type === 'in' ? 'text-[var(--color-success)]' : ($mov->type === 'out' ? 'text-[var(--color-critical)]' : 'text-[var(--color-ink)]') }}">
                            {{ $mov->type === 'in' ? '+' : ($mov->type === 'out' ? '-' : '') }}{{ $mov->total_stock }}
                        </td>
                        <td class="py-3 text-left text-[var(--color-slate)]">
                            @if($mov->reference_type === 'product_purchase' && $mov->reference_code)
                                <a href="{{ route('product-purchases.show', $mov->reference_code) }}" class="text-[var(--color-primary)] hover:underline font-medium block mb-0.5">Pengadaan: {{ $mov->reference_code }}</a>
                            @elseif($mov->reference_type === 'transaction' && $mov->reference_code)
                                <a href="{{ route('transactions.receipt', $mov->reference_code) }}" class="text-[var(--color-primary)] hover:underline font-medium block mb-0.5">Penjualan: {{ $mov->reference_code }}</a>
                            @elseif($mov->reference_type === 'service_repair' && $mov->reference_code)
                                <a href="{{ route('service-repairs.show', $mov->reference_code) }}" class="text-[var(--color-primary)] hover:underline font-medium block mb-0.5">Servis: {{ $mov->reference_code }}</a>
                            @else
                                <span class="block mb-0.5 font-medium text-[var(--color-ink)]">{{ $mov->reference_type ? ucfirst(str_replace('_', ' ', $mov->reference_type)) : 'Manual / Lainnya' }}</span>
                            @endif
                            <span class="text-xs">{{ $mov->notes ?: '-' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-4 text-center text-[var(--color-slate)]">Belum ada riwayat pergerakan stok.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection
