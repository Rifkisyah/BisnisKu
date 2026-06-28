@extends('layouts.app')
@section('page-title', __('messages.edit') . ' ' . __('messages.service_repair') . ' - ' . $serviceRepair->repair_code)
@section('content')
<div class="mx-auto max-w-2xl" x-data="repairEditForm()">
    <div class="card-feature p-6">
        <form method="POST" action="{{ route('service-repairs.update', $serviceRepair) }}" class="space-y-5" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Customer Info --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.customer_name') }} *</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name', $serviceRepair->customer_name) }}" required minlength="3" maxlength="100" class="input-field"><p class="mt-1 text-xs text-gray-400">3–100 karakter</p>
                </div>
                <div>
                    <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">No. Telepon / WhatsApp</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone', $serviceRepair->customer_phone) }}" class="input-field" placeholder="081234567890" inputmode="numeric" minlength="7" maxlength="20"><p class="mt-1 text-xs text-gray-400">7–20 karakter</p>
                </div>
                <div>
                    <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">Status</label>
                    <select name="status" class="input-field">
                        @foreach(['received','in_progress','waiting_for_parts','completed','canceled','picked_up'] as $st)
                        <option value="{{ $st }}" {{ $serviceRepair->status === $st ? 'selected' : '' }}>{{ __('messages.'.$st) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">{{ __('messages.notes') }}</label>
                    <textarea name="notes" rows="2" maxlength="500" class="input-field" placeholder="Catatan umum...">{{ old('notes', $serviceRepair->notes) }}</textarea><p class="mt-1 text-xs text-gray-400">Maks. 500 karakter</p>
                </div>
                <div class="sm:col-span-2">
                    <label class="block type-body-sm-bold text-[var(--color-ink)] mb-1.5">Tambah/Ubah Foto Bukti Kerusakan</label>
                    @if($serviceRepair->images)
                        <div class="mb-2 flex gap-2 flex-wrap">
                            @foreach($serviceRepair->images as $img)
                                <img src="{{ asset('storage/' . $img) }}" class="w-16 h-16 object-cover rounded border border-[var(--color-hairline)]">
                            @endforeach
                        </div>
                        <p class="text-xs text-[var(--color-slate)] mb-2">Mengunggah gambar baru akan menggantikan gambar lama.</p>
                    @endif
                    <input type="file" name="images[]" multiple accept="image/*" class="input-field !py-2.5">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">{{ __('messages.save') }}</button>
                <a href="javascript:void(0)" onclick="if(window.history.length > 2) { window.history.back(); } else { window.location.href = '{{ route('dashboard') }}'; }" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>{{ __('messages.back') ?? 'Kembali' }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
function repairEditForm() {
    return {}
}
</script>
@endpush
