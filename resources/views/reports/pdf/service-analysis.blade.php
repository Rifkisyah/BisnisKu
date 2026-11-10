<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Layanan Perbaikan</title>
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

{{-- ── COVER HEADER ─────────────────────────────────────── --}}
<div class="pr-header">
    <div class="store-name">{{ $storeName }}</div>
    <div class="store-contact">{{ $storeAddress }}{{ $storePhone ? ' · Tel: ' . $storePhone : '' }}</div>
    <h1>Analisis Layanan Perbaikan</h1>
    <div class="sub">Statistik perbaikan, performa pendapatan servis, serta penggunaan komponen cadangan.</div>
    <div class="meta">
        <div class="period">
            Periode Laporan
            <span>{{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</span>
        </div>
        <div class="docinfo">
            Dicetak: {{ now()->format('d M Y H:i') }}<br>
            Oleh: {{ auth()->user()->username ?? 'Admin' }}
        </div>
    </div>
</div>

{{-- ── SECTION 1: RINGKASAN PERBAIKAN ──────────────────── --}}
<div class="pr-section">
    <div class="pr-sh"><div class="pr-snum">1</div><div class="pr-stitle">Ringkasan Tiket Layanan</div></div>
    
    <table class="pr-kpi"><tr>
        <td>
            <div class="pr-card">
                <div class="pr-cl">Total Perbaikan</div>
                <div class="pr-cv" style="color:#2563EB">{{ $serviceStats['total'] }}</div>
                <div class="pr-cd">Dibuka dalam periode ini</div>
            </div>
        </td>
        <td>
            <div class="pr-card a">
                <div class="pr-cl">Aktif</div>
                <div class="pr-cv" style="color:#f59e0b">{{ $serviceStats['active'] }}</div>
                <div class="pr-cd">Sedang dalam proses</div>
            </div>
        </td>
        <td>
            <div class="pr-card g">
                <div class="pr-cl">Selesai</div>
                <div class="pr-cv" style="color:#10b981">{{ $serviceStats['done'] }}</div>
                <div class="pr-cd">Perbaikan done / picked up</div>
            </div>
        </td>
        <td>
            <div class="pr-card" style="border-left-color:#0ea5e9; background:#f0f9ff;">
                <div class="pr-cl" style="color:#0284c7">Revenue Service</div>
                <div class="pr-cv" style="color:#0ea5e9">Rp {{ number_format($serviceStats['revenue'], 0, ',', '.') }}</div>
                <div class="pr-cd" style="color:#0284c7">Jasa + Komponen Selesai</div>
            </div>
        </td>
    </tr></table>
</div>

{{-- ── SECTION 2: TREN & SPAREPART ──────────────────────── --}}
<div class="pr-section">
    <div class="pr-sh"><div class="pr-snum">2</div><div class="pr-stitle">Tren Bulanan & Penggunaan Sparepart</div></div>
    
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="width:50%; padding-right:15px; vertical-align:top;">
                @if(!empty($chart_service_trend))
                <div class="pr-chart-container" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px; padding:10px;">
                    <p style="font-size:8px; color:#64748b; margin-top:0; margin-bottom:5px; text-align:left; font-weight:bold;">TREN BULANAN PERBAIKAN</p>
                    <img src="{{ $chart_service_trend }}" style="max-height: 180px;">
                </div>
                @endif
            </td>
            <td style="width:50%; vertical-align:top;">
                <p style="font-size:9px; font-weight:bold; margin:0 0 5px 0;">PENGGUNAAN SPAREPART TERBANYAK</p>
                @if(count($topSpareparts ?? []) > 0)
                <table class="prt">
                    <thead><tr>
                        <th>Sparepart</th>
                        <th class="c">Qty</th>
                        <th class="r">Total Biaya</th>
                    </tr></thead>
                    <tbody>
                    @foreach($topSpareparts as $ts)
                    <tr>
                        <td style="font-weight:bold">{{ $ts->name }}</td>
                        <td class="c">{{ $ts->total_qty }}</td>
                        <td class="r">Rp {{ number_format($ts->total_cost, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                @else
                <div class="pr-alert info" style="text-align:center">Belum ada penggunaan sparepart.</div>
                @endif
            </td>
        </tr>
    </table>
</div>

{{-- ── FOOTER ───────────────────────────────────────────── --}}
<div class="pr-footer">
    <div class="fl">{{ $storeName }} · Laporan Layanan Perbaikan</div>
    <div class="fr">Halaman 1/1</div>
</div>

</body>
</html>
