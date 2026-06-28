<!DOCTYPE html><html><head><meta charset="UTF-8">@include('components.pdf-styles')</head>
<body><div class="header"><h1>Laporan Analisis Layanan Perbaikan</h1><p>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p></div>

<div class="section-title">Ringkasan Layanan</div>
<table class="data-table">
    <tr><th>Total Tiket Servis</th><td class="text-right">{{ $serviceStats['total'] }}</td></tr>
    <tr><th>Tiket Aktif (Proses)</th><td class="text-right">{{ $serviceStats['active'] }}</td></tr>
    <tr><th>Tiket Selesai</th><td class="text-right">{{ $serviceStats['done'] }}</td></tr>
    <tr><th>Total Pendapatan (Jasa + Komponen)</th><td class="text-right">Rp {{ number_format($serviceStats['revenue'], 0, ',', '.') }}</td></tr>
</table>

<div class="section-title">Daftar Tiket Perbaikan</div>
<table class="data-table">
    <thead><tr><th>Kode Tiket</th><th>Tanggal</th><th>Teknisi</th><th>Status</th><th class="text-right">Total Biaya</th></tr></thead>
    <tbody>
        @foreach($repairs as $r)
        <tr>
            <td>{{ $r->repair_code }}</td>
            <td>{{ \Carbon\Carbon::parse($r->start_date)->format('d/m/Y H:i') }}</td>
            <td>{{ $r->technician ? $r->technician->name : '-' }}</td>
            <td>{{ ucwords(str_replace('_', ' ', $r->status)) }}</td>
            <td class="text-right">Rp {{ number_format($r->total_cost, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="section-title">Top 10 Sparepart Terpakai</div>
<table class="data-table">
    <thead><tr><th style="width:30px">#</th><th>Nama Sparepart</th><th class="text-center">Kode Komponen</th><th class="text-right">Qty Terpakai</th><th class="text-right">Total Biaya</th></tr></thead>
    <tbody>
        @foreach($topSpareparts as $idx => $ts)
        <tr>
            <td>{{ $idx + 1 }}</td>
            <td>{{ $ts->name }}</td>
            <td class="text-center">{{ $ts->component_code }}</td>
            <td class="text-right">{{ $ts->total_qty }}</td>
            <td class="text-right">Rp {{ number_format($ts->total_cost, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body></html>
