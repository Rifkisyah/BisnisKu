@extends('layouts.app')
@section('page-title', __('messages.categories'))
@section('content')
<div x-data="{ showFilter: false }" class="mb-6 bg-[var(--color-canvas)] rounded-[var(--radius-xl)] p-5 border border-[var(--color-hairline-soft)]">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 pb-4 border-b border-[var(--color-hairline-soft)]">
        <h2 class="type-subtitle-lg text-[var(--color-ink-deep)] hidden sm:block">Aksi & Cari</h2>
        
        <!-- Mobile Toggle & Icons -->
        <div class="flex items-center justify-between w-full sm:hidden">
            <button @click="showFilter = !showFilter" class="btn-ghost !py-2 !px-3 flex items-center" title="Cari">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg> Cari
            </button>
        </div>

        <!-- Add Button: Large on Mobile -->
        <a href="{{ route('categories.create') }}" class="btn-primary w-full sm:w-auto !py-3 sm:!py-2 !px-4 whitespace-nowrap text-center text-lg sm:text-sm order-first sm:order-last mb-2 sm:mb-0">
            <svg class="h-5 w-5 sm:h-4 sm:w-4 mr-2 sm:mr-1.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="inline-block">{{ __('messages.add') }}</span>
        </a>
    </div>
    
    <form method="GET" :class="{ 'hidden sm:flex': !showFilter, 'flex': showFilter }" class="flex-col sm:flex-row flex-wrap gap-3 items-end mt-4 sm:mt-0 w-full">
        <div class="w-full sm:col-span-1 md:col-span-3">
            <label class="block type-caption-bold text-[var(--color-slate)] mb-1">Pencarian</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search') }}..." class="input-field w-full">
        </div>
        <div class="w-full sm:w-auto">
            <button type="submit" class="btn-primary w-full sm:w-auto px-6 sm:w-auto px-6 !py-2.5">{{ __('messages.search') }}</button>
        </div>
    </form>
</div>
<div class="card overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full">
        <thead><tr class="border-b border-[var(--color-hairline-soft)] bg-[var(--color-surface-soft)]">
            <th class="px-5 py-3 text-left table-header">{{ __('messages.name') }}</th><th class="px-5 py-3 text-left table-header">Slug</th>
            <th class="px-5 py-3 text-left table-header">Type</th><th class="px-5 py-3 text-left table-header">{{ __('messages.total_products') }}</th><th class="px-5 py-3 text-left table-header">{{ __('messages.actions') }}</th>
        </tr></thead><tbody>
        @forelse($categories as $c)
        <tr onclick="window.location='{{ route('categories.show', $c) }}'" class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] transition-colors duration-150 cursor-pointer">
            <td class="px-5 py-3 type-body-sm font-medium text-[var(--color-ink)]">{{ $c->name }}</td>
            <td class="px-5 py-3 type-caption text-[var(--color-steel)]">{{ $c->slug }}</td>
            <td class="px-5 py-3"><span class="badge badge-neutral">{{ $c->type }}</span></td>
            <td class="px-5 py-3 type-body-sm text-[var(--color-charcoal)]">{{ $c->products_count }}</td>
            <td class="px-5 py-3"><div class="flex items-center gap-1">
                <button onclick="event.stopPropagation(); $dispatch('open-delete-modal', { url: '{{ route('categories.destroy', $c) }}' })" class="btn-ghost !py-1.5 !px-3 !text-xs !text-[var(--color-critical)] !border-[var(--color-critical)]/20">{{ __('messages.delete') }}</button>
            </div></td>
        </tr>
        @empty<tr><td colspan="5" class="px-5 py-12 text-center type-body-md text-[var(--color-stone)]">{{ __('messages.no_data') }}</td></tr>@endforelse
        </tbody></table></div>
    <div class="border-t border-[var(--color-hairline-soft)] px-5 py-3">{{ $categories->links() }}</div>
</div>
@endsection


