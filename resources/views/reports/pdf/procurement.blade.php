<!DOCTYPE html><html><head><meta charset="UTF-8">@include('components.pdf-styles')</head>
<body><div class="header"><h1>Laporan Pengadaan</h1><p>{{ $startDate }} s/d {{ $endDate }}</p></div>
<table class="data-table"><thead><tr><th>Kode</th><th>Supplier</th><th>Tanggal</th><th>Status</th><th class="text-right">Total</th></tr></thead>
<tbody>@foreach($procurements as $p)<tr><td>{{ $p->procurement_code }}</td><td>{{ $p->supplier->name }}</td><td>{{ $p->procurement_date->format('d/m/Y') }}</td><td>{{ $p->status }}</td><td class="text-right">Rp {{ number_format($p->total,0,',','.') }}</td></tr>@endforeach</tbody></table></body></html>
