@extends('layouts.app')
@section('page-title', __('messages.service_repairs'))

@php
$statusColors = [
    'draft'         => 'bg-gray-100 text-gray-700',
    'waiting_dp'    => 'bg-purple-100 text-purple-700',
    'diagnosing'    => 'bg-blue-100 text-blue-700',
    'waiting_parts' => 'bg-amber-100 text-amber-700',
    'repairing'     => 'bg-indigo-100 text-indigo-700',
    'ready'         => 'bg-green-100 text-green-700',
    'done'          => 'badge-success',
    'cancelled'     => 'badge-critical',
];
$statusLabels = [
    'draft'         => '<svg class="w-3.5 h-3.5 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg> Draft',
    'waiting_dp'    => '<svg class="w-3.5 h-3.5 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Menunggu DP',
    'diagnosing'    => '<svg class="w-3.5 h-3.5 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg> Diagnosa',
    'waiting_parts' => '<svg class="w-3.5 h-3.5 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Menunggu Sparepart',
    'repairing'     => '<svg class="w-3.5 h-3.5 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> Diproses',
    'ready'         => '<svg class="w-3.5 h-3.5 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Siap Diambil',
    'done'          => '<svg class="w-3.5 h-3.5 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Selesai',
    'cancelled'     => '<svg class="w-3.5 h-3.5 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Dibatalkan',
];
@endphp

@section('content')
<div x-data="{ viewMode: localStorage.getItem('repairView') || (window.innerWidth < 768 ? 'grid' : 'table'), showFilter: false }" x-init="$watch('viewMode', val => localStorage.setItem('repairView', val)); window.addEventListener('resize', () => { if(window.innerWidth < 768 && viewMode !== 'grid') viewMode = 'grid'; })">
    
    <div class="mb-6 bg-[var(--color-canvas)] rounded-[var(--radius-xl)] p-5 border border-[var(--color-hairline-soft)]">
        <!-- Top Bar: Header & Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 pb-4 border-b border-[var(--color-hairline-soft)]">
            <h2 class="type-subtitle-lg text-[var(--color-ink-deep)] hidden sm:block">Aksi & Cari</h2>
            
            <!-- Mobile Toggle & Icons -->
            <div class="flex items-center justify-between w-full sm:hidden">
                <button @click="showFilter = !showFilter" class="btn-ghost !py-2 !px-3 flex items-center" title="{{ __('messages.search') }}">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg> Cari
                </button>
                <div class="flex gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn-ghost !p-2 rounded-full" target="_blank" title="Download PDF"><svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></a>
                    <button type="button" onclick="window.print()" class="btn-ghost !p-2 rounded-full" title="Print"><svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg></button>
                </div>
            </div>

            <!-- Add Button: Large on Mobile -->
            <a href="{{ route('service-repairs.create') }}" class="btn-primary w-full sm:w-auto !py-3 sm:!py-2 !px-4 whitespace-nowrap text-center text-lg sm:text-sm order-first sm:order-last mb-2 sm:mb-0">
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
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode/Pelanggan..." class="input-field w-full">
            </div>
            <div class="w-full sm:w-48">
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1">Status</label>
                <select name="status" class="input-field w-full">
                    <option value="">Semua Status</option>
                    @foreach(['draft','waiting_dp','diagnosing','waiting_parts','repairing','ready','done','cancelled'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ strip_tags($statusLabels[$s] ?? $s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-48">
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1">{{ __('messages.start_date') }}</label>
                <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="input-field w-full">
            </div>
            <div class="w-full sm:w-48">
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1">{{ __('messages.end_date') }}</label>
                <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="input-field w-full">
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
                    <th class="px-5 py-3 text-left table-header">{{ __('messages.customer_name') }}</th>
                    <th class="px-5 py-3 text-left table-header">Tgl. Diterima</th>
                    <th class="px-5 py-3 text-left table-header">{{ __('messages.total_cost') }}</th>
                    <th class="px-5 py-3 text-left table-header">{{ __('messages.status') }}</th>
                    <th class="px-5 py-3 text-left table-header">{{ __('messages.actions') }}</th>
                </tr></thead>
                <tbody>
                @forelse($serviceRepairs as $sr)
                <tr onclick="window.location='{{ route('service-repairs.show', $sr) }}'" class="border-b border-[var(--color-hairline-soft)]/50 transition-colors duration-150 hover:bg-[var(--color-surface-soft)] cursor-pointer">
                    <td class="px-5 py-3 type-caption-bold text-[var(--color-primary)]">{{ $sr->repair_code }}</td>
                    <td class="px-5 py-3 type-body-sm font-medium text-[var(--color-ink)]">
                        <div>{{ $sr->customer_name }}</div>
                        @if($sr->customer_phone)
                        <div class="flex items-center gap-1 mt-0.5">
                            <span class="text-xs text-[var(--color-slate)]">{{ $sr->customer_phone }}</span>
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $sr->customer_phone) }}" target="_blank" onclick="event.stopPropagation()" class="text-green-500 hover:text-green-600" title="Hubungi via WA">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564c.173.087.289.129.332.202.043.073.043.423-.101.827z"/></svg>
                            </a>
                        </div>
                        @endif
                    </td>
                    <td class="px-5 py-3 type-body-sm text-[var(--color-slate)]">{{ $sr->start_date?->format('d/m/Y') ?? '-' }}</td>
                    <td class="px-5 py-3 type-body-sm font-bold text-[var(--color-ink)]">Rp {{ number_format($sr->total_cost, 0, ',', '.') }}</td>
                    <td class="px-5 py-3">
                        <span class="badge {{ $statusColors[$sr->status] ?? 'bg-gray-100 text-gray-700' }} px-3 py-1 text-xs">
                            {!! $statusLabels[$sr->status] ?? $sr->status !!}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('service-repairs.show', $sr) }}" onclick="event.stopPropagation()" class="btn-ghost !py-1.5 !px-3 !text-xs">{{ __('messages.detail') }}</a>
                            @if(!auth()->user()->isTeknisi())
                            <button @click.stop="$dispatch('open-delete-modal', { url: '{{ route('service-repairs.destroy', $sr) }}' })" class="btn-ghost !py-1.5 !px-3 !text-xs !text-[var(--color-critical)] !border-[var(--color-critical)]/20">{{ __('messages.delete') }}</button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-12 text-center type-body-md text-[var(--color-stone)]">{{ __('messages.no_data') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Grid View --}}
    <div x-show="viewMode === 'grid'" x-cloak class="grid grid-cols-2 gap-3 sm:grid-cols-2 sm:gap-4 md:grid-cols-3 lg:grid-cols-4">
        @forelse($serviceRepairs as $sr)
        <div onclick="window.location='{{ route('service-repairs.show', $sr) }}'" class="card flex flex-col p-0 overflow-hidden hover:border-[var(--color-primary)] hover:-translate-y-1 hover:shadow-md transition-all duration-300 cursor-pointer">
            <div class="aspect-square bg-gray-100 overflow-hidden">
                @php $firstItemImages = collect($sr->items)->whereNull('parent_id')->first()?->images; @endphp
                <img src="{{ $firstItemImages && count($firstItemImages) > 0 ? asset('storage/' . $firstItemImages[0]) : 'https://placehold.co/600x400?text=No+Image' }}" class="w-full h-full object-cover">
            </div>
            <div class="p-4 flex flex-col flex-1">
                <div class="flex items-start justify-between mb-3">
                    <span class="type-caption-bold text-[var(--color-primary)]">{{ $sr->repair_code }}</span>
                    <span class="type-caption text-[var(--color-slate)]">{{ $sr->completion_date?->format('d/m/Y') ?? '-' }}</span>
                </div>
                
                <h4 class="type-body-sm sm:type-body-md font-bold text-[var(--color-ink-deep)] mb-1 truncate">{{ $sr->customer_name }}</h4>
                <div class="flex items-center gap-1 mb-3 type-caption text-[var(--color-slate)] truncate">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span class="truncate">{{ $sr->customer_phone ?? 'Tidak ada kontak' }}</span>
                    @if($sr->customer_phone)
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $sr->customer_phone) }}" target="_blank" onclick="event.stopPropagation()" class="text-green-500 hover:text-green-600 ml-1" title="Hubungi via WA">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564c.173.087.289.129.332.202.043.073.043.423-.101.827z"/></svg>
                    </a>
                    @endif
                </div>

            <div class="inline-block self-start badge {{ $statusColors[$sr->status] ?? 'bg-gray-100 text-gray-700' }} px-2 py-1 text-[10px] mb-4">
                {!! $statusLabels[$sr->status] ?? $sr->status !!}
            </div>

            <div class="mt-auto space-y-1">
                <div class="flex flex-col sm:flex-row justify-between type-body-sm font-bold border-t border-[var(--color-hairline-soft)] pt-3 mb-2 gap-1 sm:gap-0">
                    <span class="text-[var(--color-ink)] type-caption sm:type-body-sm">Total</span>
                    <span class="text-[var(--color-primary)]">Rp {{ number_format($sr->total_cost, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-end gap-1">
                    <a href="{{ route('service-repairs.show', $sr) }}" onclick="event.stopPropagation()" class="btn-ghost flex-1 text-center !py-1.5 !px-3 !text-xs">{{ __('messages.detail') }}</a>
                    @if(!auth()->user()->isTeknisi())
                    <button @click.stop="$dispatch('open-delete-modal', { url: '{{ route('service-repairs.destroy', $sr) }}' })" class="btn-ghost !py-1.5 !px-3 !text-xs !text-[var(--color-critical)] !border-[var(--color-critical)]/20">{{ __('messages.delete') }}</button>
                    @endif
                </div>
            </div>
        </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center type-body-md text-[var(--color-stone)]">{{ __('messages.no_data') }}</div>
        @endforelse
    </div>

    <div class="mt-4 border-t border-[var(--color-hairline-soft)] pt-4">{{ $serviceRepairs->links() }}</div>
</div>

{{-- Delete Modal --}}
<div x-data="{ open: false, url: '' }" 
     @open-delete-modal.window="open = true; url = $event.detail.url" 
     x-show="open" x-cloak 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div @click.away="open = false" class="bg-white rounded-xl shadow-xl w-full max-w-sm overflow-hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        <div class="p-5 text-center">
            <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Hapus Data Perbaikan?</h3>
            <p class="text-sm text-gray-500 mb-6">Data yang dihapus tidak dapat dikembalikan. Apakah Anda yakin?</p>
            <div class="flex gap-2 justify-center">
                <button type="button" @click="open = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200">Batal</button>
                <form :action="url" method="POST" class="inline-block">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection



