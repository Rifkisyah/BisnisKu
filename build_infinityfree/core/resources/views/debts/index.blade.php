@extends('layouts.app')
@section('page-title', __('messages.debts'))
@section('content')
<div x-data="{ showFilter: false }" class="mb-6 bg-[var(--color-canvas)] rounded-[var(--radius-xl)] p-5 border border-[var(--color-hairline-soft)] print:hidden print-hidden">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 pb-4 border-b border-[var(--color-hairline-soft)]">
        <div class="flex items-center justify-between w-full sm:w-auto">
            <h2 class="type-subtitle-lg text-[var(--color-ink-deep)]">Aksi & Cari</h2>
        </div>
        <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-2 w-full sm:w-auto justify-end">
            <div class="grid grid-cols-2 gap-2 w-full sm:w-auto sm:flex">
                <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn-ghost !py-2 !px-3 flex items-center justify-center text-xs sm:text-sm w-full sm:w-auto" title="Download PDF">
                    <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> <span>PDF</span>
                </a>
                <a href="{{ request()->fullUrlWithQuery(['print' => '1']) }}" class="btn-ghost !py-2 !px-3 flex items-center justify-center text-xs sm:text-sm w-full sm:w-auto" title="Print">
                    <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg> <span>Print</span>
                </a>
            </div>
            <a href="{{ route('debts.create') }}" class="btn-primary !py-2.5 !px-4 whitespace-nowrap text-center text-xs sm:text-sm w-full sm:w-auto justify-center flex items-center">
                <svg class="h-4 w-4 mr-1.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span class="inline-block">{{ __('messages.add') }}</span>
            </a>
        </div>
    </div>
    
    <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-3 items-stretch sm:items-end w-full">
        <div class="w-full sm:flex-1">
            <label class="block type-caption-bold text-[var(--color-slate)] mb-1">Pencarian</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode/Debitur..." class="input-field w-full">
        </div>
        <div class="grid grid-cols-2 gap-3 w-full sm:w-auto sm:flex sm:flex-wrap">
            <div class="w-full sm:w-48">
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1">Status</label>
                <select name="status" class="input-field w-full">
                    <option value="">Semua Status</option>
                    <option value="unpaid" @selected(request('status') === 'unpaid')>Belum Lunas</option>
                    <option value="partial" @selected(request('status') === 'partial')>Bayar Sebagian</option>
                    <option value="paid" @selected(request('status') === 'paid')>Lunas</option>
                </select>
            </div>
            <div class="w-full sm:w-48">
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1">Urutkan</label>
                <select name="sort" class="input-field w-full" onchange="this.form.submit()">
                    <option value="" {{ !request('sort') || request('sort') == 'created_at_desc' ? 'selected' : '' }}>Dibuat (Terbaru)</option>
                    <option value="created_at_asc" {{ request('sort') == 'created_at_asc' ? 'selected' : '' }}>Dibuat (Terlama)</option>
                    <option value="updated_at_desc" {{ request('sort') == 'updated_at_desc' ? 'selected' : '' }}>Diupdate (Terbaru)</option>
                    <option value="updated_at_asc" {{ request('sort') == 'updated_at_asc' ? 'selected' : '' }}>Diupdate (Terlama)</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Abjad (A-Z)</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Abjad (Z-A)</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3 w-full sm:w-auto sm:flex sm:flex-wrap">
            <div class="w-full sm:w-48">
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1">{{ __('messages.start_date') }}</label>
                <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="input-field w-full">
            </div>
            <div class="w-full sm:w-48">
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1">{{ __('messages.end_date') }}</label>
                <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="input-field w-full">
            </div>
        </div>
        <div class="w-full sm:w-auto">
            <button type="submit" class="btn-primary w-full sm:w-auto px-6 !py-2.5">{{ __('messages.search') }}</button>
        </div>
    </form>
</div>
<div class="card overflow-hidden"><div class="overflow-x-auto"><table class="w-full">
    <thead><tr class="border-b border-[var(--color-hairline-soft)] bg-[var(--color-surface-soft)]">
        <th class="px-5 py-3 text-left table-header">{{ __('messages.name') }}</th>
        <th class="px-5 py-3 text-left table-header">{{ __('messages.total') }}</th><th class="px-5 py-3 text-left table-header">{{ __('messages.remaining') }}</th>
        <th class="px-5 py-3 text-left table-header">{{ __('messages.status') }}</th><th class="px-5 py-3 text-left table-header print:hidden print-hidden action-col">{{ __('messages.actions') }}</th>
    </tr></thead><tbody>
    @forelse($debts as $d)
    <tr onclick="window.location='{{ route('debts.show', $d) }}'" class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] transition-colors duration-150 cursor-pointer">
        <td class="px-5 py-3 type-body-sm font-medium text-[var(--color-ink)]">{{ $d->debtor_name }}</td>
        <td class="px-5 py-3 type-body-sm text-[var(--color-charcoal)]">Rp {{ number_format($d->total_amount, 0, ',', '.') }}</td>
        <td class="px-5 py-3 type-body-sm font-bold text-[var(--color-critical)]">Rp {{ number_format($d->remaining_amount, 0, ',', '.') }}</td>
        <td class="px-5 py-3"><span class="badge {{ $d->status === 'paid' ? 'badge-success' : ($d->status === 'partial' ? 'badge-attention' : 'badge-critical') }}">{{ __('messages.'.$d->status) }}</span></td>
        <td class="px-5 py-3 print:hidden print-hidden action-col"><div class="flex items-center gap-1">
                <a href="{{ route('debts.show', $d) }}" class="btn-ghost !py-1.5 !px-3 !text-xs">{{ __('messages.detail') }}</a>
                @if($d->debtor_contact)
                @php
                    $storeName = \App\Models\Setting::get('store_name', 'Toko');
                    $phone = preg_replace('/[^0-9]/', '', $d->debtor_contact);
                    if (str_starts_with($phone, '0')) $phone = '62' . substr($phone, 1);
                    if ($d->status === 'paid') {
                        $msg = "Halo {$d->debtor_name}, kami dari {$storeName} mengonfirmasi bahwa hutang Anda telah LUNAS. Terima kasih.";
                    } else {
                        $due = $d->due_date ? $d->due_date->format('d/m/Y') : '-';
                        $rem = number_format($d->remaining_amount, 0, ',', '.');
                        $msg = "Halo {$d->debtor_name}, kami dari {$storeName} ingin mengingatkan mengenai tagihan hutang sebesar Rp {$rem} yang jatuh tempo pada {$due}. Mohon segera melakukan pelunasan. Terima kasih.";
                    }
                    $waLink = "https://wa.me/{$phone}?text=" . urlencode($msg);
                @endphp
                <a href="{{ $waLink }}" target="_blank" @click.stop class="btn-ghost !py-1.5 !px-3 !text-xs !text-[#25D366] !border-[#25D366]/20 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    WA
                </a>
                @endif
                <button @click.stop="$dispatch('open-delete-modal', { url: '{{ route('debts.destroy', $d) }}' })" class="btn-ghost !py-1.5 !px-3 !text-xs !text-[var(--color-critical)] !border-[var(--color-critical)]/20">{{ __('messages.delete') }}</button>
            </div></td>
    </tr>
    @empty<tr><td colspan="6" class="px-5 py-12 text-center type-body-md text-[var(--color-stone)]">{{ __('messages.no_data') }}</td></tr>@endforelse
    </tbody></table></div>
    @if(method_exists($debts, 'links'))
    <div class="border-t border-[var(--color-hairline-soft)] px-5 py-3 print:hidden print-hidden">{{ $debts->links() }}</div>
    @endif
</div>
@endsection


