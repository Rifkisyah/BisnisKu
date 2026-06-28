<!DOCTYPE html><html><head><meta charset="UTF-8">@include('components.pdf-styles')</head>
<body><div class="header"><h1>Laporan Data Karyawan</h1></div>
<table class="data-table"><thead><tr><th>Username</th><th>Email</th><th>No. HP</th><th>Role</th><th>Status</th><th>Terdaftar</th></tr></thead>
<tbody>
    @foreach($employees as $e)
    <tr>
        <td>{{ $e->username }}</td>
        <td>{{ $e->email }}</td>
        <td>{{ $e->phone ?? '-' }}</td>
        <td>{{ strtoupper($e->role->name) }}</td>
        <td>{{ strtoupper($e->status) }}</td>
        <td>{{ $e->created_at->format('d/m/Y') }}</td>
    </tr>
    @endforeach
</tbody></table>
</body></html>
