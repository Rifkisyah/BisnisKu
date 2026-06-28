@extends('layouts.app')
@section('page-title', __('messages.suppliers'))
@section('content')
<div x-data="{ viewMode: localStorage.getItem('supplierView') || (window.innerWidth < 768 ? 'grid' : 'table'), showFilter: false }" x-init="$watch('viewMode', val => localStorage.setItem('supplierView', val)); window.addEventListener('resize', () => { if(window.innerWidth < 768 && viewMode !== 'grid') viewMode = 'grid'; })">
    <div class="mb-6 bg-[var(--color-canvas)] rounded-[var(--radius-xl)] p-5 border border-[var(--color-hairline-soft)]">
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
            <a href="{{ route('suppliers.create') }}" class="btn-primary w-full sm:w-auto !py-3 sm:!py-2 !px-4 whitespace-nowrap text-center text-lg sm:text-sm order-first sm:order-last mb-2 sm:mb-0">
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
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode atau Nama..." class="input-field w-full">
            </div>
            <div class="w-full sm:w-48">
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1">Status</label>
                <select name="status" class="input-field w-full">
                    <option value="">Semua Status</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <button type="submit" class="btn-primary w-full sm:w-auto px-6 sm:w-auto px-6 !py-2.5">{{ __('messages.search') }}</button>
            </div>
        </form>
    </div>
    
    {{-- Table View --}}
    <div x-show="viewMode === 'table'" class="card overflow-hidden">
        <div class="overflow-x-auto"><table class="w-full">
        <thead><tr class="border-b border-[var(--color-hairline-soft)] bg-[var(--color-surface-soft)]">
            <th class="px-5 py-3 text-left table-header">Logo</th>
            <th class="px-5 py-3 text-left table-header">{{ __('messages.name') }}</th><th class="px-5 py-3 text-left table-header">{{ __('messages.contact') }}</th>
            <th class="px-5 py-3 text-left table-header">{{ __('messages.address') }}</th><th class="px-5 py-3 text-left table-header">{{ __('messages.status') }}</th><th class="px-5 py-3 text-left table-header">{{ __('messages.actions') }}</th>
        </tr></thead><tbody>
        @forelse($suppliers as $s)
        <tr onclick="window.location='{{ route('suppliers.show', $s) }}'" class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] transition-colors duration-150 cursor-pointer">
            <td class="px-5 py-3">
                @if($s->image)<img src="{{ asset('storage/' . $s->image) }}" class="w-10 h-10 object-cover rounded-full border border-[var(--color-hairline-soft)]">
                @else<div class="w-10 h-10 rounded-full bg-[var(--color-surface-soft)] flex items-center justify-center type-caption font-bold text-[var(--color-stone)]">{{ substr($s->name, 0, 2) }}</div>@endif
            </td>
            <td class="px-5 py-3 type-body-sm font-medium text-[var(--color-ink)]">{{ $s->name }}</td>
            <td class="px-5 py-3 type-body-sm text-[var(--color-slate)]">
                @if($s->whatsapp_number)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $s->whatsapp_number) }}" target="_blank" onclick="event.stopPropagation()" class="text-green-600 hover:underline">{{ $s->whatsapp_number }}</a>
                @else
                    -
                @endif
            </td>
            <td class="px-5 py-3 type-body-sm text-[var(--color-slate)] truncate max-w-[200px]">{{ $s->address ?? '-' }}</td>
            <td class="px-5 py-3"><span class="badge {{ $s->status === 'active' ? 'badge-success' : 'badge-critical' }}">{{ $s->status }}</span></td>
            <td class="px-5 py-3"><div class="flex items-center gap-1">
                <a href="{{ route('suppliers.show', $s) }}" onclick="event.stopPropagation()" class="btn-ghost !py-1.5 !px-3 !text-xs">{{ __('messages.detail') }}</a>
                <button onclick="event.stopPropagation(); $dispatch('open-delete-modal', { url: '{{ route('suppliers.destroy', $s) }}' })" class="btn-ghost !py-1.5 !px-3 !text-xs !text-[var(--color-critical)] !border-[var(--color-critical)]/20">{{ __('messages.delete') }}</button>
            </div></td>
        </tr>
        @empty<tr><td colspan="6" class="px-5 py-12 text-center type-body-md text-[var(--color-stone)]">{{ __('messages.no_data') }}</td></tr>@endforelse
        </tbody></table></div>
    </div>

    {{-- Grid View --}}
    <div x-show="viewMode === 'grid'" x-cloak class="grid grid-cols-2 gap-3 sm:grid-cols-2 sm:gap-4 md:grid-cols-3 lg:grid-cols-4">
        @forelse($suppliers as $s)
        <div onclick="window.location='{{ route('suppliers.show', $s) }}'" class="card flex flex-col p-3 sm:p-5 hover:border-[var(--color-primary)] hover:-translate-y-1 hover:shadow-md transition-all duration-300 items-center text-center cursor-pointer">
            @if($s->image)
            <img src="{{ asset('storage/' . $s->image) }}" class="w-12 h-12 sm:w-20 sm:h-20 object-cover rounded-full border-2 border-[var(--color-surface)] shadow-md mb-2 sm:mb-3">
            @else
            <div class="w-12 h-12 sm:w-20 sm:h-20 rounded-full bg-[var(--color-primary)]/10 text-[var(--color-primary)] flex items-center justify-center text-lg sm:text-2xl font-bold mb-2 sm:mb-3 shadow-sm border-2 border-[var(--color-surface)]">
                {{ substr($s->name, 0, 2) }}
            </div>
            @endif
            <h4 class="type-body-sm font-bold text-[var(--color-ink-deep)] mb-1">{{ $s->name }}</h4>
            <div class="flex items-center gap-1 mb-2 type-caption text-green-600 font-medium">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.711.927 3.15.927 3.178 0 5.768-2.587 5.769-5.766 0-3.181-2.588-5.769-5.77-5.769zM12.031 16.5c-1.076 0-2.022-.296-2.887-.852l-.208-.124-1.306.342.349-1.272-.136-.216c-.612-.968-.934-2.091-.934-3.266 0-3.18 2.589-5.769 5.77-5.769 3.181 0 5.769 2.587 5.769 5.768 0 3.182-2.588 5.769-5.769 5.769zm3.173-4.341c-.174-.087-1.029-.508-1.189-.566-.159-.058-.275-.087-.391.087-.116.174-.449.566-.55.682-.101.116-.203.13-.377.043-.174-.087-.734-.271-1.398-.863-.515-.46-.863-1.029-.964-1.203-.101-.174-.011-.269.076-.356.079-.079.174-.203.261-.304.087-.101.116-.174.174-.29.058-.116.029-.217-.014-.304-.044-.087-.391-.943-.536-1.291-.141-.341-.285-.295-.391-.3-.099-.005-.214-.005-.33-.005-.116 0-.304.043-.464.217s-.608.594-.608 1.448c0 .855.623 1.68 7.1 1.776 1.157 1.258 1.611 1.353 1.901 1.353.29 0 .941-.384 1.072-.754.13-.371.13-.688.092-.754-.038-.066-.144-.109-.318-.196z"/></svg>
                @if($s->whatsapp_number)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $s->whatsapp_number) }}" target="_blank" onclick="event.stopPropagation()" class="hover:underline">{{ $s->whatsapp_number }}</a>
                @else
                    Belum ada WA
                @endif
            </div>
            <p class="type-caption text-[var(--color-slate)] mb-3 line-clamp-2 px-2">{{ $s->address ?? 'Tidak ada alamat' }}</p>
            <span class="badge {{ $s->status === 'active' ? 'badge-success' : 'badge-critical' }} !px-2 !py-0.5 !text-[10px] mb-4">{{ $s->status }}</span>
            <div class="mt-auto w-full pt-3 border-t border-[var(--color-hairline-soft)] flex justify-center gap-1">
                <a href="{{ route('suppliers.show', $s) }}" onclick="event.stopPropagation()" class="btn-ghost flex-1 !py-1.5 !text-xs">{{ __('messages.detail') }}</a>
                <button onclick="event.stopPropagation(); $dispatch('open-delete-modal', { url: '{{ route('suppliers.destroy', $s) }}' })" class="btn-ghost flex-1 !py-1.5 !text-xs !text-[var(--color-critical)] !border-[var(--color-critical)]/20">{{ __('messages.delete') }}</button>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center type-body-md text-[var(--color-stone)]">{{ __('messages.no_data') }}</div>
        @endforelse
    </div>

    <div class="mt-4 border-t border-[var(--color-hairline-soft)] pt-4">{{ $suppliers->links() }}</div>
</div>
@endsection



