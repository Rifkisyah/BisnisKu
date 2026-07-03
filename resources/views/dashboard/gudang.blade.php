@extends('layouts.app')
@section('page-title', __('messages.dashboard') . ' (Staff Gudang)')
@section('content')

    @php
        $diffDays = (int) $startDate->diffInDays($endDate) ?: 1;
    @endphp
    <form method="GET" class="mb-6 flex flex-col md:flex-row md:flex-wrap items-start md:items-end gap-4 card-feature p-4 md:p-6" id="dashboard-filter-form">
        <div class="flex flex-col sm:flex-row w-full md:w-auto gap-4">
            <div class="flex-1 w-full sm:w-auto">
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1">{{ __('messages.start_date') }}</label>
                <input type="date" name="start_date" id="dash-start-date" value="{{ $startDate->format('Y-m-d') }}" class="input-field w-full" onchange="this.form.submit()">
            </div>
            <div class="flex-1 w-full sm:w-auto">
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1">{{ __('messages.end_date') }}</label>
                <input type="date" name="end_date" id="dash-end-date" value="{{ $endDate->format('Y-m-d') }}" class="input-field w-full" onchange="this.form.submit()">
            </div>
        </div>
        
        <div class="flex gap-2 w-full md:w-auto md:ml-2 md:mr-2 md:border-r border-b md:border-b-0 border-[var(--color-hairline-soft)] pb-4 md:pb-0 md:pr-4">
            @php
                $activeFilter = null;
                if ($diffDays == 7) $activeFilter = 7;
                elseif ($diffDays == 365) $activeFilter = 365;
                elseif ($diffDays == 30 || !request()->has('start_date')) $activeFilter = 30;
            @endphp
            <button type="button" onclick="setDateRange(7)" class="flex-1 text-center justify-center {{ $activeFilter === 7 ? 'bg-black text-white hover:bg-black/80 rounded-full font-semibold shadow-sm border-2 border-transparent' : 'btn-ghost' }} !py-2 !px-3 text-xs sm:text-sm">{{ __('messages.week') }}</button>
            <button type="button" onclick="setDateRange(30)" class="flex-1 text-center justify-center {{ $activeFilter === 30 ? 'bg-black text-white hover:bg-black/80 rounded-full font-semibold shadow-sm border-2 border-transparent' : 'btn-ghost' }} !py-2 !px-3 text-xs sm:text-sm">{{ __('messages.month') }}</button>
            <button type="button" onclick="setDateRange(365)" class="flex-1 text-center justify-center {{ $activeFilter === 365 ? 'bg-black text-white hover:bg-black/80 rounded-full font-semibold shadow-sm border-2 border-transparent' : 'btn-ghost' }} !py-2 !px-3 text-xs sm:text-sm">{{ __('messages.year') }}</button>
        </div>
    </form>

        <script>
        function setDateRange(days) {
            const startInput = document.getElementById('dash-start-date').value;
            const start = startInput ? new Date(startInput) : new Date();
            const end = new Date(start);
            end.setDate(start.getDate() + days);
            
            document.getElementById('dash-start-date').value = start.toISOString().split('T')[0];
            document.getElementById('dash-end-date').value = end.toISOString().split('T')[0];
            document.getElementById('dashboard-filter-form').submit();
        }
    </script>
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <div class="card-feature p-5">
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">{{ __('messages.total_products') }}</p>
        <p class="mt-3 type-heading-sm text-[var(--color-ink-deep)]">{{ $totalProducts }}</p>
        <div class="mt-2 h-1 w-12 rounded-full bg-[var(--color-primary)]"></div>
    </div>
    <div class="card-feature p-5">
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">{{ __('messages.low_stock_movement') }}</p>
        <p class="mt-3 type-heading-sm text-[var(--color-ink-deep)]">{{ $lowStockProducts->count() }}</p>
        <div class="mt-2 h-1 w-12 rounded-full bg-[var(--color-critical)]"></div>
    </div>
    <div class="card-feature p-5">
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">{{ __('messages.active_procurements') }}</p>
        <p class="mt-3 type-heading-sm text-[var(--color-ink-deep)]">{{ $activeProcurements }}</p>
        <div class="mt-2 h-1 w-12 rounded-full bg-[var(--color-attention)]"></div>
    </div>
    <div class="card-feature p-5">
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">{{ __('messages.suppliers') }}</p>
        <p class="mt-3 type-heading-sm text-[var(--color-ink-deep)]">{{ $totalSuppliers }}</p>
        <div class="mt-2 h-1 w-12 rounded-full bg-[var(--color-success)]"></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Low Stock Products --}}
    <div class="card-feature p-6">
        <h3 class="type-subtitle-lg text-[var(--color-ink-deep)] mb-4">{{ __('messages.low_stock_warning_top_10') }}</h3>
        <div class="overflow-x-auto"><table class="w-full">
            <thead><tr class="border-b border-[var(--color-hairline-soft)]">
                <th class="pb-3 pr-4 text-left table-header">{{ __('messages.product') }}</th>
                <th class="pb-3 text-right table-header">{{ __('messages.remaining_stock') }}</th>
            </tr></thead><tbody>
            @forelse($lowStockProducts as $p)
            <tr class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] transition-colors cursor-pointer" onclick="window.location='{{ route('products.show', $p) }}'">
                <td class="py-3 pr-4 type-body-sm font-medium text-[var(--color-ink)]">{{ $p->name }}<br><span class="text-xs text-[var(--color-slate)]">{{ $p->product_code }}</span></td>
                <td class="py-3 text-right type-heading-sm text-[var(--color-critical)]">{{ $p->stock }}</td>
            </tr>
            @empty<tr><td colspan="2" class="py-8 text-center type-body-sm text-[var(--color-stone)]">{{ __('messages.no_data') }}</td></tr>@endforelse
            </tbody></table>
        </div>
    </div>

    {{-- Recent Procurements --}}
    <div class="card-feature p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]">{{ __('messages.recent_procurements') }}</h3>
            <div class="flex gap-2">
                <a href="{{ route('product-purchases.index') }}" class="btn-ghost !text-xs !py-1.5 !px-3">{{ __('messages.view_all') }}</a>
                <a href="{{ route('product-purchases.create') }}" class="btn-primary !text-xs !py-1.5 !px-3">{{ __('messages.create_procurement') }}</a>
            </div>
        </div>
        <div class="overflow-x-auto"><table class="w-full">
            <thead><tr class="border-b border-[var(--color-hairline-soft)]">
                <th class="pb-3 pr-4 text-left table-header">{{ __('messages.purchase_code') }}</th>
                <th class="pb-3 pr-4 text-left table-header">{{ __('messages.supplier') }}</th>
                <th class="pb-3 pr-4 text-right table-header">{{ __('messages.total') }}</th>
                <th class="pb-3 text-left table-header">{{ __('messages.status') }}</th>
            </tr></thead><tbody>
            @forelse($recentProcurements as $rp)
            <tr class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] transition-colors cursor-pointer" onclick="window.location='{{ route('product-purchases.show', $rp) }}'">
                <td class="py-3 pr-4 type-caption-bold text-[var(--color-primary)]">{{ $rp->product_purchase_code }}</td>
                <td class="py-3 pr-4 type-body-sm text-[var(--color-slate)]">{{ __('messages.multi_source') }}</td>
                <td class="py-3 pr-4 text-right type-body-sm font-bold text-[var(--color-ink)]">Rp {{ number_format($rp->total, 0, ',', '.') }}</td>
                <td class="py-3"><span class="badge {{ $rp->status === 'received' ? 'badge-success' : ($rp->status === 'cancelled' ? 'badge-critical' : 'badge-attention') }}">{{ __('messages.' . $rp->status) }}</span></td>
            </tr>
            @empty<tr><td colspan="4" class="py-8 text-center type-body-sm text-[var(--color-stone)]">{{ __('messages.no_data') }}</td></tr>@endforelse
            </tbody></table>
        </div>
    </div>
</div>
@endsection
