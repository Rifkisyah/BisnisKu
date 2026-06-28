@extends('layouts.app')
@section('page-title', __('messages.products'))
@section('content')
<div x-data="{ viewMode: localStorage.getItem('productView') || (window.innerWidth < 768 ? 'grid' : 'table'), showFilter: false }" x-init="$watch('viewMode', val => localStorage.setItem('productView', val)); window.addEventListener('resize', () => { if(window.innerWidth < 768 && viewMode !== 'grid') viewMode = 'grid'; })">
    
    <div class="mb-6 bg-[var(--color-canvas)] rounded-[var(--radius-xl)] p-5 border border-[var(--color-hairline-soft)]">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 pb-4 border-b border-[var(--color-hairline-soft)]">
            <h2 class="type-subtitle-lg text-[var(--color-ink-deep)] hidden sm:block">Aksi & Cari</h2>
            
            <!-- Mobile Toggle & Icons -->
            <div class="flex items-center justify-between w-full sm:hidden">
                <button @click="showFilter = !showFilter" class="btn-ghost !py-2 !px-3 flex items-center" title="Cari">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg> Cari
                </button>
                <div class="flex gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn-ghost !p-2 rounded-full" target="_blank" title="Download PDF"><svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></a>
                    <button type="button" onclick="window.print()" class="btn-ghost !p-2 rounded-full" title="Print"><svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg></button>
                </div>
            </div>

            <!-- Add Button: Large on Mobile -->
            <a href="{{ route('products.create') }}" class="btn-primary w-full sm:w-auto !py-3 sm:!py-2 !px-4 whitespace-nowrap text-center text-lg sm:text-sm order-first sm:order-last mb-2 sm:mb-0">
                <svg class="h-5 w-5 sm:h-4 sm:w-4 mr-2 sm:mr-1.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span class="inline-block">{{ __('messages.add') }}</span>
            </a>

            <!-- Desktop Actions -->
            <div class="hidden sm:flex flex-wrap items-center gap-2">
                <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn-ghost !py-2 !px-3" target="_blank" title="Download PDF">
                    <svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> <span>PDF</span>
                </a>
                <button type="button" onclick="window.print()" class="btn-ghost !py-2 !px-3" title="Print">
                    <svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg> <span>Print</span>
                </button>
                <div class="flex bg-[var(--color-surface-soft)] rounded-lg p-1 border border-[var(--color-hairline-soft)]">
                    <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-[var(--color-canvas)] shadow-sm text-[var(--color-ink-deep)]' : 'text-[var(--color-slate)]'" class="px-2 py-1.5 rounded-md transition-all" title="Table View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                    <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-[var(--color-canvas)] shadow-sm text-[var(--color-ink-deep)]' : 'text-[var(--color-slate)]'" class="px-2 py-1.5 rounded-md transition-all" title="Grid View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <form method="GET" :class="{ 'hidden sm:flex': !showFilter, 'flex': showFilter }" class="flex-col sm:flex-row flex-wrap gap-3 items-end mt-4 sm:mt-0 w-full">
            <div class="w-full sm:flex-1">
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1">Pencarian</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search') }}..." class="input-field w-full">
            </div>
            <div class="w-full sm:w-48">
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1">Tipe Produk</label>
                <select name="type" class="input-field w-full">
                    <option value="">{{ __('messages.all') }}</option>
                    <option value="physical" {{ request('type') === 'physical' ? 'selected' : '' }}>{{ __('messages.physical') }}</option>
                    <option value="digital" {{ request('type') === 'digital' ? 'selected' : '' }}>{{ __('messages.digital') }}</option>
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <button type="submit" class="btn-primary w-full sm:w-auto px-6 sm:w-auto px-6 !py-2.5">{{ __('messages.search') }}</button>
            </div>
        </form>
    </div>

    {{-- Table View --}}
    <div x-show="viewMode === 'table'" class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="border-b border-[var(--color-hairline-soft)] bg-[var(--color-surface-soft)]">
                    <th class="px-5 py-3 text-left table-header">{{ __('messages.code') }}</th>
                    <th class="px-5 py-3 text-left table-header">{{ __('messages.name') }}</th>
                    <th class="px-5 py-3 text-left table-header">{{ __('messages.category') }}</th>
                    <th class="px-5 py-3 text-left table-header">{{ __('messages.purchase_price') }}</th>
                    <th class="px-5 py-3 text-left table-header">{{ __('messages.selling_price') }}</th>
                    <th class="px-5 py-3 text-left table-header">{{ __('messages.stock') }}</th>
                    <th class="px-5 py-3 text-left table-header">{{ __('messages.actions') }}</th>
                </tr></thead>
                <tbody>
                @forelse($products as $p)
                <tr onclick="window.location='{{ route('products.show', $p) }}'" class="border-b border-[var(--color-hairline-soft)]/50 transition-colors duration-150 hover:bg-[var(--color-surface-soft)] cursor-pointer">
                    <td class="px-5 py-3 type-caption-bold text-[var(--color-primary)]">{{ $p->product_code }}</td>
                    <td class="px-5 py-3 type-body-sm font-medium text-[var(--color-ink)]">{{ $p->name }}</td>
                    <td class="px-5 py-3 type-body-sm text-[var(--color-slate)]">{{ $p->category->name }}</td>
                    <td class="px-5 py-3 type-body-sm text-[var(--color-charcoal)]">Rp {{ number_format($p->purchase_price, 0, ',', '.') }}</td>
                    <td class="px-5 py-3 type-body-sm font-bold text-[var(--color-ink)]">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</td>
                    <td class="px-5 py-3">
                        @if($p->isLowStock())<span class="badge badge-critical">{{ $p->stock }}</span>
                        @else<span class="type-body-sm font-medium text-[var(--color-ink)]">{{ $p->stock }}</span>@endif
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('products.show', $p) }}" onclick="event.stopPropagation()" class="btn-ghost !py-1.5 !px-3 !text-xs">{{ __('messages.detail') }}</a>
                            <button onclick="event.stopPropagation(); $dispatch('open-delete-modal', { url: '{{ route('products.destroy', $p) }}' })" class="btn-ghost !py-1.5 !px-3 !text-xs !text-[var(--color-critical)] !border-[var(--color-critical)]/20">{{ __('messages.delete') }}</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-12 text-center type-body-md text-[var(--color-stone)]">{{ __('messages.no_data') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Grid View --}}
    <div x-show="viewMode === 'grid'" x-cloak class="grid grid-cols-2 gap-3 sm:grid-cols-2 sm:gap-4 md:grid-cols-3 lg:grid-cols-4">
        @forelse($products as $p)
        <div onclick="window.location='{{ route('products.show', $p) }}'" class="card flex flex-col p-3 sm:p-4 hover:border-[var(--color-primary)] hover:-translate-y-1 hover:shadow-md transition-all duration-300 cursor-pointer">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-2 sm:mb-3 gap-1">
                <span class="type-caption-bold text-[var(--color-primary)] text-[10px] sm:text-xs truncate">{{ $p->product_code }}</span>
                @if($p->isLowStock())<span class="badge badge-critical !px-1.5 !py-0.5 !text-[10px]">Stok: {{ $p->stock }}</span>
                @else<span class="badge badge-neutral !px-1.5 !py-0.5 !text-[10px]">Stok: {{ $p->stock }}</span>@endif
            </div>
            <div class="aspect-square bg-[var(--color-surface-soft)] rounded-lg mb-2 sm:mb-3 flex items-center justify-center">
                <span class="aspect-square bg-gray-100 overflow-hidden w-full h-full rounded-lg"><img src="https://placehold.co/600x400?text=No+Image" class="w-full h-full object-cover"></span>
            </div>
            <h4 class="type-caption-bold sm:type-body-sm font-medium text-[var(--color-ink)] line-clamp-2 mb-1">{{ $p->name }}</h4>
            <p class="text-[10px] sm:type-caption text-[var(--color-slate)] mb-2 sm:mb-3">{{ $p->category->name }}</p>
            <div class="mt-auto space-y-1">
                <div class="flex flex-col sm:flex-row justify-between text-[10px] sm:type-caption">
                    <span class="text-[var(--color-slate)] hidden sm:inline">Beli</span>
                    <span class="text-[var(--color-charcoal)] sm:ml-auto">Rp {{ number_format($p->purchase_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex flex-col sm:flex-row justify-between type-caption-bold sm:type-body-sm">
                    <span class="text-[var(--color-slate)] hidden sm:inline">Jual</span>
                    <span class="font-bold text-[var(--color-ink)] text-[11px] sm:text-sm sm:ml-auto">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</span>
                </div>
                <div class="pt-3 mt-3 border-t border-[var(--color-hairline-soft)] flex justify-end gap-1">
                    <a href="{{ route('products.show', $p) }}" onclick="event.stopPropagation()" class="btn-ghost !py-1.5 !px-3 !text-xs">{{ __('messages.detail') }}</a>
                    <button onclick="event.stopPropagation(); $dispatch('open-delete-modal', { url: '{{ route('products.destroy', $p) }}' })" class="btn-ghost !py-1.5 !px-3 !text-xs !text-[var(--color-critical)] !border-[var(--color-critical)]/20">{{ __('messages.delete') }}</button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center type-body-md text-[var(--color-stone)]">{{ __('messages.no_data') }}</div>
        @endforelse
    </div>

    <div class="mt-4 border-t border-[var(--color-hairline-soft)] pt-4">{{ $products->links() }}</div>
</div>
@endsection



