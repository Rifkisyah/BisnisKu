@extends('layouts.app')
@section('page-title', __('messages.detail') . ' ' . __('messages.product'))
@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="javascript:void(0)" onclick="if(window.history.length > 2) { window.history.back(); } else { window.location.href = '{{ route('dashboard') }}'; }" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
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
                    <a href="{{ route('products.edit', $product) }}" class="rounded-full bg-gray-900 px-8 py-3.5 text-[14px] font-bold text-white hover:bg-gray-800 transition-colors w-full flex justify-center">{{ __('messages.edit') }}</a>
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
        
        <div class="bg-[var(--color-canvas)] rounded-[32px] border border-gray-100 p-8 shadow-sm">
            <h3 class="text-xl font-bold text-[var(--color-ink-deep)] border-b border-[var(--color-hairline-soft)] pb-4 mb-6">Riwayat Pergerakan Stok (10 Terakhir)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-[var(--color-hairline-soft)] text-left text-xs font-medium text-[var(--color-slate)] uppercase">
                        <th class="pb-2">Tanggal</th><th class="pb-2">Jenis</th><th class="pb-2">Qty</th><th class="pb-2">Keterangan</th>
                    </tr></thead>
                    <tbody class="divide-y divide-[var(--color-hairline-soft)]/50">
                    @forelse($product->stockMovements()->latest()->take(10)->get() as $mov)
                    <tr>
                        <td class="py-2">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-2">
                            <span class="badge {{ $mov->type === 'in' ? 'badge-success' : 'badge-critical' }}">{{ strtoupper($mov->type) }}</span>
                        </td>
                        <td class="py-2 font-bold">{{ $mov->quantity }}</td>
                        <td class="py-2 text-[var(--color-slate)]">{{ $mov->notes ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-4 text-center text-gray-500">Belum ada riwayat pergerakan stok.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection
