<!DOCTYPE html><html><head><meta charset="UTF-8">@include('components.pdf-styles')</head>
<body><div class="header"><h1>Laporan Layanan Perbaikan</h1><p>{{ $startDate ?? '-' }} s/d {{ $endDate ?? '-' }}</p></div>
<table class="data-table"><thead><tr><th>Kode</th><th>Tanggal Masuk</th><th>Pelanggan</th><th>Teknisi</th><th>Status</th><th class="text-right">Total Biaya</th></tr></thead>
<tbody>
    @php $totalRevenue = 0; @endphp
    @foreach($repairs as $r)
    @if(in_array($r->status, ['completed', 'picked_up']))
        @php $totalRevenue += $r->total_cost; @endphp
    @endif
    <tr>
        <td>{{ $r->repair_code }}</td>
        <td>{{ \Carbon\Carbon::parse($r->start_date)->format('d/m/Y H:i') }}</td>
        <td>{{ $r->customer_name }}</td>
        <td>{{ $r->technician->username ?? '-' }}</td>
        <td>{{ strtoupper($r->status) }}</td>
        <td class="text-right">Rp {{ number_format($r->total_cost,0,',','.') }}</td>
    </tr>
    @endforeach
</tbody></table>
<div class="text-right" style="font-weight:bold;margin-top:10px">Total Pendapatan (Selesai/Diambil): Rp {{ number_format($totalRevenue,0,',','.') }}</div></body></html>
