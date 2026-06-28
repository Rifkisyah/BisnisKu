@extends('layouts.app')
@section('page-title', __('messages.employees'))
@section('content')
<div x-data="{ viewMode: localStorage.getItem('employeeView') || (window.innerWidth < 768 ? 'grid' : 'table'), showFilter: false }" x-init="$watch('viewMode', val => localStorage.setItem('employeeView', val)); window.addEventListener('resize', () => { if(window.innerWidth < 768 && viewMode !== 'grid') viewMode = 'grid'; })">
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
            <a href="{{ route('employees.create') }}" class="btn-primary w-full sm:w-auto !py-3 sm:!py-2 !px-4 whitespace-nowrap text-center text-lg sm:text-sm order-first sm:order-last mb-2 sm:mb-0">
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
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama atau Email..." class="input-field w-full">
            </div>
            <div class="w-full sm:w-48">
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1">Role</label>
                <select name="role" class="input-field w-full">
                    <option value="">Semua Role</option>
                    @foreach($roles as $r)<option value="{{ $r->name }}" @selected(request('role') === $r->name)>{{ strtoupper($r->name) }}</option>@endforeach
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
            <th class="px-5 py-3 text-left table-header">{{ __('messages.name') }}</th><th class="px-5 py-3 text-left table-header">{{ __('messages.email') }}</th>
            <th class="px-5 py-3 text-left table-header">{{ __('messages.role') }}</th><th class="px-5 py-3 text-left table-header">{{ __('messages.status') }}</th><th class="px-5 py-3 text-left table-header">{{ __('messages.actions') }}</th>
        </tr></thead><tbody>
        @forelse($employees as $e)
        <tr onclick="window.location='{{ route('employees.show', $e) }}'" class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] cursor-pointer transition-colors duration-150">
            <td class="px-5 py-3 type-body-sm font-medium text-[var(--color-ink)]">{{ $e->username }}</td>
            <td class="px-5 py-3 type-body-sm text-[var(--color-slate)]">{{ $e->email }}</td>
            <td class="px-5 py-3"><span class="badge badge-info">{{ $e->role->name }}</span></td>
            <td class="px-5 py-3"><span class="badge {{ $e->status === 'active' ? 'badge-success' : 'badge-critical' }}">{{ $e->status }}</span></td>
            <td class="px-5 py-3"><div class="flex items-center gap-1">
                <a href="{{ route('employees.show', $e) }}" class="btn-ghost !py-1.5 !px-3 !text-xs">{{ __('messages.detail') }}</a>
                <button @click="$dispatch('open-delete-modal', { url: '{{ route('employees.destroy', $e) }}' })" class="btn-ghost !py-1.5 !px-3 !text-xs !text-[var(--color-critical)] !border-[var(--color-critical)]/20">{{ __('messages.delete') }}</button>
            </div></td>
        </tr>
        @empty<tr><td colspan="5" class="px-5 py-12 text-center type-body-md text-[var(--color-stone)]">{{ __('messages.no_data') }}</td></tr>@endforelse
        </tbody></table></div>
    </div>

    {{-- Grid View --}}
    <div x-show="viewMode === 'grid'" x-cloak class="grid grid-cols-2 gap-3 sm:grid-cols-2 sm:gap-4 md:grid-cols-3 lg:grid-cols-4">
        @forelse($employees as $e)
        <div onclick="window.location='{{ route('employees.show', $e) }}'" class="card flex flex-col p-3 sm:p-5 hover:border-[var(--color-primary)] hover:-translate-y-1 hover:shadow-md cursor-pointer transition-all duration-300 items-center text-center group">
            <div class="h-12 w-12 sm:h-16 sm:w-16 rounded-full bg-[var(--color-primary)] flex items-center justify-center text-white text-base sm:text-xl font-bold mb-2 sm:mb-3 shadow-md group-hover:scale-110 transition-transform duration-300">
                {{ strtoupper(substr($e->username, 0, 2)) }}
            </div>
            <h4 class="type-body-sm sm:type-body-md font-bold text-[var(--color-ink-deep)] mb-1 truncate w-full">{{ $e->username }}</h4>
            <p class="text-[10px] sm:type-caption text-[var(--color-slate)] mb-2 truncate w-full">{{ $e->email }}</p>
            <div class="flex gap-2 mb-4">
                <span class="badge badge-info !px-2 !py-0.5 !text-[10px]">{{ strtoupper($e->role->name) }}</span>
                <span class="badge {{ $e->status === 'active' ? 'badge-success' : 'badge-critical' }} !px-2 !py-0.5 !text-[10px]">{{ $e->status }}</span>
            </div>
            <div class="mt-auto w-full pt-3 border-t border-[var(--color-hairline-soft)] flex justify-center gap-1">
                <a href="{{ route('employees.show', $e) }}" class="btn-ghost flex-1 !py-1.5 !text-xs">{{ __('messages.detail') }}</a>
                <button @click="$dispatch('open-delete-modal', { url: '{{ route('employees.destroy', $e) }}' })" class="btn-ghost flex-1 !py-1.5 !text-xs !text-[var(--color-critical)] !border-[var(--color-critical)]/20">{{ __('messages.delete') }}</button>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center type-body-md text-[var(--color-stone)]">{{ __('messages.no_data') }}</div>
        @endforelse
    </div>

    <div class="mt-4 border-t border-[var(--color-hairline-soft)] pt-4">{{ $employees->links() }}</div>
</div>
@endsection



