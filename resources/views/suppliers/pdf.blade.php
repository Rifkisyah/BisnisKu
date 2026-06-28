<!DOCTYPE html><html><head><meta charset="UTF-8">@include('components.pdf-styles')</head>
<body><div class="header"><h1>Laporan Data Supplier</h1></div>
<table class="data-table"><thead><tr><th>Kode</th><th>Nama Supplier</th><th>Kontak WA</th><th>Email</th><th>Alamat</th><th>Status</th></tr></thead>
<tbody>
    @foreach($suppliers as $s)
    <tr>
        <td>{{ $s->supplier_code }}</td>
        <td>{{ $s->name }}</td>
        <td>{{ $s->whatsapp_number ?? '-' }}</td>
        <td>{{ $s->email ?? '-' }}</td>
        <td>{{ $s->address ?? '-' }}</td>
        <td>{{ strtoupper($s->status) }}</td>
    </tr>
    @endforeach
</tbody></table>
</body></html>
