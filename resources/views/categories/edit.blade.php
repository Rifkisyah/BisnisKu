@extends('layouts.app')
@section('page-title', __('messages.edit') . ' ' . __('messages.category'))
@section('content')
<div class="mx-auto max-w-lg"><div class="card-feature p-8">
    <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-5">@csrf @method('PUT')
        <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.name') }} *</label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" required minlength="2" maxlength="60" class="input-field"><p class="mt-1 text-xs text-gray-400">2–60 karakter</p></div>

        <div><label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">Type</label>
            <select name="type" class="input-field"><option value="product" {{ $category->type === 'product' ? 'selected' : '' }}>Product</option><option value="service" {{ $category->type === 'service' ? 'selected' : '' }}>Service</option></select></div>
        <div class="flex flex-col sm:flex-row justify-between gap-3 pt-4 mt-6 border-t border-[var(--color-hairline-soft)] w-full">
    <a href="javascript:void(0)" onclick="if(window.history.length > 2) { window.history.back(); } else { window.location.href = '{{ route('dashboard') }}'; }" class="btn-ghost w-full sm:w-auto text-center order-2 sm:order-1">{{ __('messages.back') ?? 'Kembali' }}</a>
    <button type="submit" class="btn-primary w-full sm:w-auto text-center order-1 sm:order-2">{{ __('messages.save') ?? 'Simpan' }}</button>
</div>
    </form>
</div></div>
@endsection
