<!DOCTYPE html><html><head><meta charset="UTF-8">@include('components.pdf-styles')</head>
<body><div class="header"><h1>Laporan Data Kategori</h1></div>
<table class="data-table"><thead><tr><th>Kode</th><th>Nama Kategori</th><th>Tipe</th><th>Deskripsi</th><th>Status</th></tr></thead>
<tbody>
    @foreach($categories as $c)
    <tr>
        <td>{{ $c->category_code }}</td>
        <td>{{ $c->name }}</td>
        <td>{{ ucfirst($c->type) }}</td>
        <td>{{ $c->description ?? '-' }}</td>
        <td>{{ strtoupper($c->status) }}</td>
    </tr>
    @endforeach
</tbody></table>
</body></html>
