@extends('layouts.app')
@section('page-title', 'Detail Tren Perbaikan')
@section('content')

<div class="mx-auto max-w-6xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('reports.service_analysis', request()->except('page')) }}" class="btn-ghost">{{ __('Kembali') }}</a>
    </div>

    <div class="card-feature mb-6">
        <div class="p-5 border-b border-[var(--color-hairline-soft)] flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="type-subtitle-lg text-[var(--color-ink-deep)]">Seluruh Data Tren Perbaikan</h2>
                <p class="type-caption text-[var(--color-slate)] mt-1">Periode {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
            </div>
            <form method="GET" action="{{ route('reports.service_analysis.trends') }}" class="flex flex-col md:flex-row md:items-center gap-3 w-full">
                <input type="date" name="start_date" value="{{ \Carbon\Carbon::parse($startDate)->format('Y-m-d') }}" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                <span class="text-[var(--color-slate)] text-xs">sampai</span>
                <input type="date" name="end_date" value="{{ \Carbon\Carbon::parse($endDate)->format('Y-m-d') }}" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                
                <select name="status" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="waiting_part" {{ request('status') == 'waiting_part' ? 'selected' : '' }}>Waiting Part</option>
                    <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Selesai</option>
                    <option value="picked_up" {{ request('status') == 'picked_up' ? 'selected' : '' }}>Diambil</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="bg-[var(--color-surface-soft)]">
                    <th class="px-5 py-3 text-left table-header">#</th>
                    <th class="px-5 py-3 text-left table-header">Kode Tiket</th>
                    <th class="px-5 py-3 text-left table-header">Tanggal Servis</th>
                    <th class="px-5 py-3 text-left table-header">Teknisi</th>
                    <th class="px-5 py-3 text-center table-header">Status</th>
                    <th class="px-5 py-3 text-right table-header">Revenue</th>
                </tr></thead>
                <tbody>
                @forelse($repairs as $idx => $r)
                <tr class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] transition-colors">
                    <td class="px-5 py-3 type-body-sm text-[var(--color-slate)]">{{ $repairs->firstItem() + $idx }}</td>
                    <td class="px-5 py-3 type-caption-bold text-[var(--color-primary)]">{{ $r->repair_code }}</td>
                    <td class="px-5 py-3 type-body-sm text-[var(--color-ink)]">{{ \Carbon\Carbon::parse($r->start_date)->format('d M Y H:i') }}</td>
                    <td class="px-5 py-3 type-body-sm text-[var(--color-ink)]">{{ $r->technician ? $r->technician->name : '-' }}</td>
                    <td class="px-5 py-3 text-center">
                        @php
                            $badgeColor = match($r->status) {
                                'pending' => 'badge-neutral',
                                'in_progress', 'waiting_part' => 'badge-attention',
                                'done', 'picked_up' => 'badge-success',
                                'cancelled' => 'badge-critical',
                                default => 'badge-neutral',
                            };
                            $statusText = ucwords(str_replace('_', ' ', $r->status));
                        @endphp
                        <span class="badge {{ $badgeColor }}">{{ $statusText }}</span>
                    </td>
                    <td class="px-5 py-3 type-body-sm-bold text-right text-[var(--color-ink-deep)]">Rp {{ number_format($r->total_cost, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-10 text-center type-body-sm text-[var(--color-slate)]">Tidak ada data servis pada periode ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($repairs->hasPages())
        <div class="p-5 border-t border-[var(--color-hairline-soft)]">
            {{ $repairs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
