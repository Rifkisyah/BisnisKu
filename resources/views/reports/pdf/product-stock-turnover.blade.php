<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Perputaran Stok Produk</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 9px; color: #1e293b; background: #fff; margin: 0; padding: 0; line-height: 1.4; }
        .pr-header { background: #0f172a; color: #fff; padding: 18px 24px 14px; margin-bottom: 0; }
        .pr-header .store-name { font-size: 11px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; color: #93c5fd; }
        .pr-header .store-contact { font-size: 7.5px; color: #cbd5e1; }
        .pr-header h1 { font-size: 16px; font-weight: bold; color: #fff; margin-top: 6px; margin-bottom: 0;}
        .pr-header .sub { font-size: 8px; color: #bfdbfe; margin-top: 3px; }
        .pr-header .meta { margin-top: 10px; padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.2); font-size: 8px; overflow: hidden; }
        .pr-header .meta .period { color: #bfdbfe; float: left; }
        .pr-header .meta .period span { font-size: 10px; font-weight: bold; color: #fff; display: block; }
        .pr-header .meta .docinfo { text-align: right; color: #93c5fd; float: right; }
        
        .pr-section { padding: 10px 18px 4px; }
        .pr-sh { margin-bottom: 7px; padding-bottom: 5px; border-bottom: 2px solid #2563EB; overflow: hidden; }
        .pr-snum { background: #2563EB; color: #fff; width: 17px; height: 17px; border-radius: 50%; display: inline-block; text-align: center; line-height: 17px; font-size: 7.5px; font-weight: bold; margin-right: 6px; vertical-align: top; }
        .pr-stitle { font-size: 10.5px; font-weight: bold; color: #0f172a; display: block; }
        .pr-ssub { font-size: 7.5px; color: #64748b; display: block; }
        
        .pr-kpi { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .pr-kpi td { padding: 4px; vertical-align: top; width: 25%; }
        .pr-card { border: 1px solid #e2e8f0; border-radius: 4px; padding: 6px 8px; background: #f8fafc; border-left: 3px solid #2563EB; }
        .pr-card.g { border-left-color: #10b981; }
        .pr-card.a { border-left-color: #f59e0b; }
        .pr-card.r { border-left-color: #ef4444; }
        .pr-card.p { border-left-color: #8b5cf6; }
        .pr-cl { font-size: 7px; color: #64748b; text-transform: uppercase; font-weight: bold; }
        .pr-cv { font-size: 13px; font-weight: bold; color: #0f172a; margin-top: 2px; }
        .pr-cd { font-size: 6.5px; color: #94a3b8; margin-top: 1px; }
        
        table.prt { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 8px; }
        table.prt thead tr { background: #1e3a5f; color: #fff; }
        table.prt thead th { padding: 5px 7px; text-align: left; font-size: 7px; font-weight: bold; text-transform: uppercase; }
        table.prt thead th.r { text-align: right; }
        table.prt thead th.c { text-align: center; }
        table.prt tbody tr { border-bottom: 1px solid #f1f5f9; }
        table.prt tbody tr:nth-child(even) { background: #f8fafc; }
        table.prt tbody td { padding: 4px 7px; color: #334155; }
        table.prt tbody td.r { text-align: right; }
        table.prt tbody td.c { text-align: center; }
        table.prt tfoot tr { background: #f1f5f9; border-top: 2px solid #cbd5e1; }
        table.prt tfoot td { padding: 5px 7px; font-weight: bold; }
        
        .pb { display: inline-block; padding: 2px 5px; border-radius: 8px; font-size: 7px; font-weight: bold; }
        .pb-h, .pb-rec { background: #dcfce7; color: #166534; }
        .pb-m, .pb-pen { background: #fef9c3; color: #854d0e; }
        .pb-l, .pb-can, .pb-dead { background: #fee2e2; color: #991b1b; }
        .pb-ord { background: #eff6ff; color: #1d4ed8; }
        .pb-dft { background: #f3f4f6; color: #4b5563; }
        .pb-rst { background: #fef3c7; color: #92400e; }
        
        .pr-bw { background: #e2e8f0; border-radius: 3px; height: 5px; width: 70px; display: inline-block; vertical-align: middle; }
        .pr-bf { height: 5px; border-radius: 3px; background: #2563EB; }
        .pr-bf.g { background: #10b981; }
        .pr-bf.a { background: #f59e0b; }
        
        .pr-alert { background: #fffbeb; border: 1px solid #fcd34d; border-left: 3px solid #f59e0b; border-radius: 3px; padding: 5px 8px; margin-bottom: 6px; font-size: 7.5px; color: #78350f; }
        .pr-alert.info { background: #eff6ff; border-color: #93c5fd; border-left-color: #2563EB; color: #1e3a5f; }
        .pr-alert.danger { background: #fef2f2; border-color: #fca5a5; border-left-color: #ef4444; color: #7f1d1d; }
        
        .pr-sep { border: none; border-top: 1px dashed #e2e8f0; margin: 6px 18px; }
        .pr-chart-container { text-align: center; margin-bottom: 12px; width: 100%; }
        .pr-chart-container img { max-height: 180px; width: auto; max-width: 100%; display: inline-block; margin: 0 auto; }
        .pr-pb { page-break-before: always; }
        
        .pr-footer { border-top: 1px solid #cbd5e1; padding: 5px 18px; font-size: 7px; color: #94a3b8; margin-top: 8px; }
        .pr-footer .fl { display: inline-block; width: 49%; }
        .pr-footer .fr { display: inline-block; width: 49%; text-align: right; }
    </style>
</head>
<body>

@php
    $printStatusLabels = [
        'draft'            => __('messages.draft') ?? 'Draft',
        'ordered'          => __('messages.ordered') ?? 'Dipesan',
        'partial_received' => __('messages.partial_received') ?? 'Sebagian Diterima',
        'received'         => __('messages.received') ?? 'Diterima',
        'cancelled'        => __('messages.cancelled') ?? 'Dibatalkan',
    ];
    $categoryFilter = request('category');
    $categoryName = $categoryFilter ? (\App\Models\Category::where('category_code', $categoryFilter)->value('name') ?? 'Semua') : 'Semua Kategori';
    $clusterResultsTagged = $clusterResults;
    $totalProductsSold = count(array_filter($clusterResults, fn($r) => $r['total_qty_sold'] > 0));
    
    $totalRevPrint = ($topProducts->sum('total_revenue') ?? 0) + ($serviceRevenue ?? 0);
@endphp

{{-- ── COVER HEADER ─────────────────────────────────────── --}}
<div class="pr-header">
    <div class="store-name">{{ $storeName }}</div>
    <div class="store-contact">{{ $storeAddress }}{{ $storePhone ? ' · Tel: ' . $storePhone : '' }}</div>
    <h1>{{ __('messages.report_stock_turnover') ?? 'Perputaran Stok Produk' }}</h1>
    <div class="sub">Business Intelligence — Analisis Populer · Klasifikasi K-Means · Rekomendasi Restock SMA</div>
    <div class="meta">
        <div class="period">Periode<span>{{ $startDate->format('d M Y') }} — {{ $endDate->format('d M Y') }}</span>Kategori: {{ $categoryName }}</div>
        <div class="docinfo">Dicetak: {{ now()->format('d M Y, H:i') }}<br><strong>CONFIDENTIAL</strong></div>
    </div>
</div>

{{-- ── KPI SUMMARY ──────────────────────────────────────── --}}
<div class="pr-section">
    <div class="pr-sh">
        <div class="pr-snum">★</div>
        <div style="display:inline-block; width: 85%; vertical-align: top;">
            <div class="pr-stitle">Ringkasan Eksekutif (KPI)</div>
            <div class="pr-ssub">Indikator kinerja kunci periode {{ $startDate->format('d M Y') }} – {{ $endDate->format('d M Y') }}</div>
        </div>
    </div>
    <table class="pr-kpi">
        <tr>
            <td><div class="pr-card g"><div class="pr-cl">Total Pendapatan Produk</div><div class="pr-cv">Rp {{ number_format($topProducts->sum('total_revenue') ?? 0, 0, ',', '.') }}</div><div class="pr-cd">Dari transaksi lunas</div></div></td>
            <td><div class="pr-card p"><div class="pr-cl">Pendapatan Servis</div><div class="pr-cv">Rp {{ number_format($serviceRevenue ?? 0, 0, ',', '.') }}</div><div class="pr-cd">Perbaikan selesai</div></div></td>
            <td><div class="pr-card a"><div class="pr-cl">Perputaran Stok</div><div class="pr-cv">{{ $inventoryTurnover ?? 0 }}×</div><div class="pr-cd">Stok Keluar ÷ Rata-rata Stok</div></div></td>
            <td><div class="pr-card r"><div class="pr-cl">Stok Tidak Aktif</div><div class="pr-cv">{{ count($deadStockProducts ?? []) }} produk</div><div class="pr-cd">&gt;90 hari tanpa penjualan</div></div></td>
        </tr>
        <tr>
            <td><div class="pr-card"><div class="pr-cl">Stok Masuk</div><div class="pr-cv">{{ number_format($stockIn ?? 0, 0, ',', '.') }} unit</div><div class="pr-cd">Total diterima</div></div></td>
            <td><div class="pr-card r"><div class="pr-cl">Stok Keluar</div><div class="pr-cv">{{ number_format($stockOut ?? 0, 0, ',', '.') }} unit</div><div class="pr-cd">Terjual & terpakai</div></div></td>
            <td><div class="pr-card g"><div class="pr-cl">Nilai Pengadaan</div><div class="pr-cv" style="font-size:10px">Rp {{ number_format($totalProcurementValue ?? 0, 0, ',', '.') }}</div><div class="pr-cd">{{ $procurements->count() }} PO dalam periode</div></div></td>
            <td><div class="pr-card"><div class="pr-cl">Total Produk Aktif</div><div class="pr-cv">{{ count($clusterResultsTagged) }}</div><div class="pr-cd">{{ $totalProductsSold }} terjual, {{ count($clusterResultsTagged) - $totalProductsSold }} tidak</div></div></td>
        </tr>
    </table>
</div>

<hr class="pr-sep">

{{-- ── SECTION 1: TOP PRODUK ────────────────────────────── --}}
<div class="pr-section">
    <div class="pr-sh">
        <div class="pr-snum">1</div>
        <div style="display:inline-block; width: 85%; vertical-align: top;">
            <div class="pr-stitle">Top 10 Produk Terlaris</div>
            <div class="pr-ssub">Berdasarkan jumlah unit terjual & kontribusi pendapatan</div>
        </div>
    </div>
    
    @if(!empty($chart_popular))
    <div class="pr-chart-container">
        <img src="{{ $chart_popular }}">
    </div>
    @endif
    
    @if(count($topProducts) > 0)
    @php
        $topMaxQty = $topProducts->max('total_qty') ?: 1;
        $totalTopRevPrint = $topProducts->sum('total_revenue') ?: 1;
    @endphp
    <table class="prt">
        <thead><tr>
            <th style="width:22px">#</th>
            <th>Nama Produk</th>
            <th class="r">Qty Terjual</th>
            <th class="r">Pendapatan</th>
            <th class="r">% Kontribusi</th>
            <th class="c" style="width:80px">Performa</th>
        </tr></thead>
        <tbody>
        @foreach($topProducts as $i => $p)
        <tr>
            <td class="c" style="font-weight:bold; color:{{ $i===0?'#f59e0b':($i===1?'#94a3b8':($i===2?'#cd7f32':'#64748b')) }}">{{ $i+1 }}</td>
            <td style="font-weight:{{ $i<3?'bold':'normal' }}">{{ $p->name }}</td>
            <td class="r" style="font-weight:bold">{{ number_format($p->total_qty,0,',','.') }}</td>
            <td class="r">Rp {{ number_format($p->total_revenue,0,',','.') }}</td>
            <td class="r" style="color:#0064E0;font-weight:bold">{{ round(($p->total_revenue/$totalTopRevPrint)*100,1) }}%</td>
            <td class="c">
                @php $pct = min(100, round(($p->total_qty/$topMaxQty)*100)); @endphp
                <div class="pr-bw"><div class="pr-bf {{ $i<3?'g':'a' }}" style="width:{{ $pct }}%"></div></div>
            </td>
        </tr>
        @endforeach
        </tbody>
        <tfoot><tr>
            <td colspan="2" style="text-align:right">TOTAL</td>
            <td class="r">{{ number_format($topProducts->sum('total_qty'),0,',','.') }}</td>
            <td class="r">Rp {{ number_format($topProducts->sum('total_revenue'),0,',','.') }}</td>
            <td colspan="2"></td>
        </tr></tfoot>
    </table>
    @else
    <p style="text-align:center;color:#94a3b8;font-style:italic;padding:10px">Tidak ada data produk terjual dalam periode ini.</p>
    @endif
</div>

<hr class="pr-sep">

{{-- ── SECTION 2: KATEGORI ─────────────────────────────── --}}
<div class="pr-section">
    <div class="pr-sh">
        <div class="pr-snum">2</div>
        <div style="display:inline-block; width: 85%; vertical-align: top;">
            <div class="pr-stitle">Kontribusi Penjualan per Kategori</div>
            <div class="pr-ssub">Distribusi pendapatan per kategori produk</div>
        </div>
    </div>
    
    @if(!empty($chart_category))
    <div class="pr-chart-container">
        <img src="{{ $chart_category }}">
    </div>
    @endif
    
    @if(count($categoryBreakdown ?? []) > 0)
    <table class="prt">
        <thead><tr>
            <th>#</th>
            <th>Kategori</th>
            <th class="r">Qty Terjual</th>
            <th class="r">Pendapatan</th>
            <th class="r">% Total</th>
            <th class="c" style="width:80px">Distribusi</th>
        </tr></thead>
        <tbody>
        @foreach($categoryBreakdown as $i => $cat)
        <tr>
            <td class="c">{{ $i+1 }}</td>
            <td style="font-weight:bold">{{ $cat->name }}</td>
            <td class="r">{{ number_format($cat->total_qty,0,',','.') }}</td>
            <td class="r">Rp {{ number_format($cat->total_revenue,0,',','.') }}</td>
            <td class="r" style="color:#0064E0;font-weight:bold">{{ $cat->revenue_pct }}%</td>
            <td class="c"><div class="pr-bw"><div class="pr-bf" style="width:{{ $cat->revenue_pct }}%"></div></div></td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @else
    <p style="text-align:center;color:#94a3b8;font-style:italic;padding:10px">Tidak ada data kategori dalam periode ini.</p>
    @endif
</div>

{{-- ── PAGE BREAK ───────────────────────────────────────── --}}
<div class="pr-pb"></div>

{{-- ── SECTION 3: K-MEANS CLUSTER ─────────────────────── --}}
@php
    $highCount      = count(array_filter($clusterResults, fn($r) => $r['cluster'] === 'high'));
    $medCount       = count(array_filter($clusterResults, fn($r) => $r['cluster'] === 'medium'));
    $lowCount       = count(array_filter($clusterResults, fn($r) => $r['cluster'] === 'low'));
@endphp
<div class="pr-section">
    <div class="pr-sh">
        <div class="pr-snum">3</div>
        <div style="display:inline-block; width: 85%; vertical-align: top;">
            <div class="pr-stitle">Klasifikasi Pergerakan Stok (K-Means)</div>
            <div class="pr-ssub">Pengelompokan produk berdasarkan frekuensi & volume penjualan</div>
        </div>
    </div>
    <table class="pr-kpi" style="margin-bottom:6px">
        <tr>
            <td><div class="pr-card g"><div class="pr-cl">🔥 Pergerakan Tinggi</div><div class="pr-cv">{{ $highCount }}</div><div class="pr-cd">{{ count($clusterResultsTagged)>0?round(($highCount/count($clusterResultsTagged))*100,1):0 }}% dari total</div></div></td>
            <td><div class="pr-card a"><div class="pr-cl">📊 Pergerakan Sedang</div><div class="pr-cv">{{ $medCount }}</div><div class="pr-cd">{{ count($clusterResultsTagged)>0?round(($medCount/count($clusterResultsTagged))*100,1):0 }}% dari total</div></div></td>
            <td><div class="pr-card r"><div class="pr-cl">❄️ Pergerakan Rendah</div><div class="pr-cv">{{ $lowCount }}</div><div class="pr-cd">{{ count($clusterResultsTagged)>0?round(($lowCount/count($clusterResultsTagged))*100,1):0 }}% dari total</div></div></td>
            <td><div class="pr-card"><div class="pr-cl">📦 Total Produk</div><div class="pr-cv">{{ count($clusterResultsTagged) }}</div><div class="pr-cd">Semua produk aktif</div></div></td>
        </tr>
    </table>
    <div class="pr-alert info" style="margin-bottom:6px"><strong>Metode:</strong> Produk diklasifikasikan berdasarkan % penjualan relatif terhadap produk terlaris: Tinggi (≥50%), Sedang (15–49%), Rendah (&lt;15%).</div>

    @if(count($clusterResultsTagged) > 0)
    <table class="prt">
        <thead><tr>
            <th style="width:20px">#</th>
            <th>Nama Produk</th>
            <th class="r">Stok Saat Ini</th>
            <th class="r">Qty Terjual</th>
            <th class="r">Pendapatan</th>
            <th class="r">Frekuensi</th>
            <th class="c">Cluster</th>
        </tr></thead>
        <tbody>
        @foreach($clusterResultsTagged as $i => $cr)
        @php
            $clBadge = match($cr['cluster']) { 'high'=>'pb-h','medium'=>'pb-m',default=>'pb-l' };
            $clLabel = match($cr['cluster']) { 'high'=>(__('messages.high')??'Tinggi'),'medium'=>(__('messages.medium')??'Sedang'),default=>(__('messages.low')??'Rendah') };
        @endphp
        <tr>
            <td class="c" style="color:#94a3b8;font-size:7px">{{ $i+1 }}</td>
            <td>{{ $cr['product_name'] }}</td>
            <td class="r">{{ number_format($cr['current_stock']??0,0,',','.') }}</td>
            <td class="r" style="font-weight:bold">{{ number_format($cr['total_qty_sold'],0,',','.') }}</td>
            <td class="r">Rp {{ number_format($cr['total_revenue'],0,',','.') }}</td>
            <td class="r">{{ $cr['transaction_frequency'] }}×</td>
            <td class="c"><span class="pb {{ $clBadge }}">{{ $clLabel }}</span></td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @else
    <p style="text-align:center;color:#94a3b8;font-style:italic;padding:10px">Tidak ada data produk.</p>
    @endif
</div>

{{-- ── PAGE BREAK ───────────────────────────────────────── --}}
<div class="pr-pb"></div>

{{-- ── SECTION 4: SMA RESTOCK ──────────────────────────── --}}
<div class="pr-section">
    <div class="pr-sh">
        <div class="pr-snum">4</div>
        <div style="display:inline-block; width: 85%; vertical-align: top;">
            <div class="pr-stitle">Rekomendasi Restock (Simple Moving Average)</div>
            <div class="pr-ssub">Produk yang diprediksi membutuhkan restok berdasarkan tren penjualan</div>
        </div>
    </div>
    @if(count($smaResults ?? []) > 0)
    <div class="pr-alert" style="margin-bottom:6px">⚠️ <strong>Perhatian:</strong> {{ count($smaResults) }} produk membutuhkan restok segera berdasarkan analisis SMA.</div>
    <table class="prt">
        <thead><tr>
            <th>#</th>
            <th>Nama Produk</th>
            <th class="r">Stok Saat Ini</th>
            <th class="r">Rata-rata Jual/Hari</th>
            <th class="r">Rekomendasi Restock</th>
            <th class="c">Status</th>
        </tr></thead>
        <tbody>
        @foreach($smaResults as $i => $sma)
        <tr>
            <td class="c" style="color:#94a3b8;font-size:7px">{{ $i+1 }}</td>
            <td style="font-weight:bold">{{ $sma['product_name']??'-' }}</td>
            <td class="r">{{ number_format($sma['current_stock']??0,0,',','.') }}</td>
            <td class="r">{{ number_format($sma['sma_value']??0,1,',','.') }}</td>
            <td class="r" style="color:#d97706;font-weight:bold">{{ number_format($sma['restock_qty']??0,0,',','.') }}</td>
            <td class="c"><span class="pb pb-rst">Perlu Restock</span></td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @else
    <div class="pr-alert info">✅ Semua produk berada dalam kondisi stok yang cukup. Tidak ada rekomendasi restock.</div>
    @endif
</div>

<hr class="pr-sep">

{{-- ── SECTION 5: PENGADAAN ────────────────────────────── --}}
<div class="pr-section">
    <div class="pr-sh">
        <div class="pr-snum">5</div>
        <div style="display:inline-block; width: 85%; vertical-align: top;">
            <div class="pr-stitle">Ringkasan Pengadaan (Purchase Order)</div>
            <div class="pr-ssub">Data seluruh PO dalam periode laporan</div>
        </div>
    </div>
    
    @if(!empty($chart_procurement))
    <div class="pr-chart-container">
        <img src="{{ $chart_procurement }}">
    </div>
    @endif
    
    <table class="pr-kpi" style="margin-bottom:6px">
        <tr>
            <td><div class="pr-card"><div class="pr-cl">Total PO</div><div class="pr-cv">{{ $procurements->count() }}</div><div class="pr-cd">Purchase order</div></div></td>
            <td><div class="pr-card g"><div class="pr-cl">Diterima</div><div class="pr-cv">{{ $receivedProcurements }}</div><div class="pr-cd">PO selesai diterima</div></div></td>
            <td><div class="pr-card a"><div class="pr-cl">Dalam Proses</div><div class="pr-cv">{{ $pendingProcurements }}</div><div class="pr-cd">Draft/Ordered/Sebagian</div></div></td>
            <td><div class="pr-card"><div class="pr-cl">Total Nilai</div><div class="pr-cv" style="font-size:10px">Rp {{ number_format($totalProcurementValue,0,',','.') }}</div><div class="pr-cd">Nilai seluruh PO</div></div></td>
        </tr>
    </table>
    @if($procurements->count() > 0)
    <table class="prt">
        <thead><tr>
            <th>#</th>
            <th>Kode PO</th>
            <th>Tanggal</th>
            <th>Dibuat Oleh</th>
            <th class="c">Status</th>
            <th class="r">Total Nilai</th>
        </tr></thead>
        <tbody>
        @foreach($procurements as $i => $po)
        @php
            $poBadge = match($po->status) {
                'received'        => 'pb-rec',
                'ordered'         => 'pb-ord',
                'partial_received'=> 'pb-pen',
                'cancelled'       => 'pb-can',
                default           => 'pb-dft',
            };
            $poLabel = strtoupper($printStatusLabels[$po->status] ?? $po->status);
        @endphp
        <tr>
            <td class="c" style="color:#94a3b8;font-size:7px">{{ $i+1 }}</td>
            <td style="font-family:monospace;font-size:8px;color:#0064E0;font-weight:bold">{{ $po->product_purchase_code }}</td>
            <td>{{ $po->purchase_date?->format('d/m/Y') ?? '-' }}</td>
            <td>{{ $po->creator->username ?? '-' }}</td>
            <td class="c"><span class="pb {{ $poBadge }}" style="font-size:6.5px">{{ $poLabel }}</span></td>
            <td class="r" style="font-weight:bold">Rp {{ number_format($po->total,0,',','.') }}</td>
        </tr>
        @endforeach
        </tbody>
        <tfoot><tr>
            <td colspan="5" style="text-align:right;font-weight:bold">TOTAL NILAI PENGADAAN</td>
            <td class="r" style="font-weight:bold;color:#0064E0">Rp {{ number_format($totalProcurementValue,0,',','.') }}</td>
        </tr></tfoot>
    </table>
    @else
    <p style="text-align:center;color:#94a3b8;font-style:italic;padding:10px">Tidak ada data pengadaan dalam periode ini.</p>
    @endif
</div>

{{-- ── PAGE BREAK ───────────────────────────────────────── --}}
<div class="pr-pb"></div>

{{-- ── SECTION 6: DEAD STOCK ───────────────────────────── --}}
<div class="pr-section">
    <div class="pr-sh">
        <div class="pr-snum">6</div>
        <div style="display:inline-block; width: 85%; vertical-align: top;">
            <div class="pr-stitle">Peringatan Stok Tidak Aktif (Dead Stock)</div>
            <div class="pr-ssub">Produk aktif yang tidak ada penjualan dalam 90 hari terakhir</div>
        </div>
    </div>
    @if(count($deadStockProducts ?? []) > 0)
    <div class="pr-alert danger" style="margin-bottom:6px">⚠️ <strong>Perlu Tindakan:</strong> {{ count($deadStockProducts) }} produk tidak terjual dalam 90 hari. Pertimbangkan promosi, bundling, atau penyesuaian harga.</div>
    <table class="prt">
        <thead><tr>
            <th>#</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th class="r">Stok Saat Ini</th>
            <th class="c">Rekomendasi</th>
        </tr></thead>
        <tbody>
        @foreach($deadStockProducts as $i => $dp)
        <tr>
            <td class="c" style="color:#94a3b8;font-size:7px">{{ $i+1 }}</td>
            <td style="font-weight:bold">{{ $dp->name }}</td>
            <td style="color:#64748b">{{ $dp->category->name ?? '-' }}</td>
            <td class="r" style="color:{{ $dp->stock===0?'#94a3b8':'#ef4444' }};font-weight:bold">{{ number_format($dp->stock,0,',','.') }}</td>
            <td class="c">
                @if($dp->stock === 0)
                <span class="pb pb-rec">Habis</span>
                @else
                <span class="pb pb-dead">Promosi/Diskon</span>
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @else
    <div class="pr-alert info">✅ Tidak ada produk dead stock dalam periode ini. Semua produk aktif terjual.</div>
    @endif
</div>

{{-- ── FOOTER ───────────────────────────────────────────── --}}
<div class="pr-footer">
    <div class="fl">{{ $storeName }} · Laporan Perputaran Stok Produk · Periode: {{ $startDate->format('d M Y') }} – {{ $endDate->format('d M Y') }} · Kategori: {{ $categoryName }}</div>
    <div class="fr">Dicetak: {{ now()->format('d M Y H:i') }}</div>
</div>

</body>
</html>
