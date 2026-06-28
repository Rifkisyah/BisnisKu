@extends('layouts.app')
@section('page-title', __('messages.reports'))
@section('content')
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @php $reports = [
        [
            'route'=>'reports.business_performance',
            'icon'=>'<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>',
            'title'=>'Laporan Analisis Bisnis',
            'desc'=>'Laporan analisis pergerakan produk dan Rekomendasi pengisian ulang stok',
            'accent'=>'var(--color-primary)',
            'bg'=>'rgba(0,100,224,0.1)'
        ],
        [
            'route'=>'reports.sales_revenue',
            'icon'=>'<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
            'title'=>'Laporan Penjualan & Pendapatan',
            'desc'=>'Laporan transaksi penjualan dan analisis hutang piutang',
            'accent'=>'var(--color-success)',
            'bg'=>'rgba(16,185,129,0.1)'
        ],
        [
            'route'=>'reports.service_analysis',
            'icon'=>'<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
            'title'=>'Laporan Layanan Perbaikan',
            'desc'=>'Statistik perbaikan, revenue servis, dan penggunaan komponen',
            'accent'=>'#F59E0B',
            'bg'=>'rgba(245,158,11,0.1)'
        ],
    ]; @endphp
    @foreach($reports as $r)
    <a href="{{ route($r['route']) }}" class="group card-feature p-6 hover:-translate-y-1 transition-all duration-300" style="border-bottom: 3px solid transparent;" onmouseover="this.style.borderBottomColor='{{ $r['accent'] }}'" onmouseout="this.style.borderBottomColor='transparent'">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-5 transition-transform duration-300 group-hover:scale-110" style="background: {{ $r['bg'] }}; color: {{ $r['accent'] }}">
            {!! $r['icon'] !!}
        </div>
        <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]">{{ $r['title'] }}</h3>
        <p class="mt-2 type-caption text-[var(--color-slate)] leading-relaxed">{{ $r['desc'] }}</p>
    </a>
    @endforeach
</div>
@endsection
