@extends('layouts.app')
@section('page-title', 'Detail & Modifikasi Kategori')
@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="javascript:void(0)" onclick="if(window.history.length > 2) { window.history.back(); } else { window.location.href = '{{ route('dashboard') }}'; }" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
    </div>

    <div class="bg-[var(--color-canvas)] rounded-[32px] border border-gray-100 p-8 shadow-sm">
        <h3 class="text-2xl font-bold text-[var(--color-ink-deep)] mb-6 border-b border-[var(--color-hairline-soft)] pb-4">Modifikasi Kategori</h3>

        <form action="{{ route('categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-5">
                <div>
                    <label for="name" class="block text-sm font-bold text-[var(--color-ink)] mb-2">{{ __('messages.name') }} *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required class="input-field" placeholder="Nama kategori">
                    @error('name') <p class="mt-1 text-xs text-[var(--color-critical)]">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-bold text-[var(--color-ink)] mb-2">{{ __('messages.type') }} *</label>
                    <select name="type" id="type" required class="input-field">
                        <option value="product" {{ (old('type', $category->type) === 'product') ? 'selected' : '' }}>Produk</option>
                        <option value="service" {{ (old('type', $category->type) === 'service') ? 'selected' : '' }}>Layanan</option>
                    </select>
                    @error('type') <p class="mt-1 text-xs text-[var(--color-critical)]">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-bold text-[var(--color-ink)] mb-2">{{ __('messages.description') }}</label>
                    <textarea name="description" id="description" rows="3" class="input-field">{{ old('description', $category->description) }}</textarea>
                    @error('description') <p class="mt-1 text-xs text-[var(--color-critical)]">{{ $message }}</p> @enderror
                </div>
                
                <div class="flex items-center gap-3 p-4 bg-[var(--color-surface-soft)] rounded-[var(--radius-xl)] border border-[var(--color-hairline-soft)] mt-4">
                    <input type="hidden" name="is_active" value="0">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[var(--color-success)]"></div>
                    </label>
                    <span class="type-body-sm font-medium text-[var(--color-ink)]">Status Aktif Kategori</span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-between gap-3 pt-4 mt-6 border-t border-[var(--color-hairline-soft)] w-full">
    <a href="javascript:void(0)" onclick="if(window.history.length > 2) { window.history.back(); } else { window.location.href = '{{ route('dashboard') }}'; }" class="btn-ghost w-full sm:w-auto text-center order-2 sm:order-1">{{ __('messages.back') ?? 'Kembali' }}</a>
    <button type="submit" class="btn-primary w-full sm:w-auto text-center order-1 sm:order-2">{{ __('messages.save') ?? 'Simpan' }}</button>
</div>
        </form>
    </div>
</div>
@endsection
