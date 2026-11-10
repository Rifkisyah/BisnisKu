<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Dashboard</title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.5; font-size: 12px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; color: #0064E0; }
        .header p { margin: 5px 0 0 0; color: #666; }
        .metrics-grid { width: 100%; margin-bottom: 20px; }
        .metrics-grid td { padding: 10px; border: 1px solid #ddd; text-align: center; background: #f8f9fa; }
        .metric-title { font-size: 10px; text-transform: uppercase; color: #666; font-weight: bold; }
        .metric-value { font-size: 16px; font-weight: bold; color: #111; margin-top: 5px; }
        .section-title { font-size: 14px; font-weight: bold; margin: 20px 0 10px 0; border-bottom: 1px solid #ccc; padding-bottom: 5px; color: #0064E0; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 11px; }
        table.data-table th, table.data-table td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        table.data-table th { background-color: #f1f5f9; font-weight: bold; }
        table.data-table tr { page-break-inside: avoid; }
        .section-title { page-break-after: avoid; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .chart-container { text-align: center; margin-bottom: 15px; }
        .chart-container img { max-width: 100%; height: auto; max-height: 250px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    @php
        // 1. Trend Chart
        $allDates = array_unique(array_merge($salesTrend->pluck('date')->toArray(), $repairTrend->pluck('date')->toArray()));
        sort($allDates);
        $trendLabels = [];
        $sVals = [];
        $rVals = [];
        foreach($allDates as $dt) {
            $trendLabels[] = \Carbon\Carbon::parse($dt)->format('d/m');
            $sVals[] = $salesTrend->where('date', $dt)->first()->total ?? 0;
            $rVals[] = $repairTrend->where('date', $dt)->first()->total_revenue ?? 0;
        }
        $trendChartConfig = [
            'type' => 'line',
            'data' => [
                'labels' => $trendLabels,
                'datasets' => [
                    ['label' => 'Penjualan', 'data' => $sVals, 'borderColor' => '#0064E0', 'fill' => false, 'borderWidth' => 2],
                    ['label' => 'Servis', 'data' => $rVals, 'borderColor' => '#31A24C', 'fill' => false, 'borderWidth' => 2]
                ]
            ],
            'options' => [
                'plugins' => ['datalabels' => ['display' => false]]
            ]
        ];
        $trendChartUrl = 'https://quickchart.io/chart?w=600&h=250&c=' . urlencode(json_encode($trendChartConfig));

        // 2. Top Products Chart
        $tpNames = $topProducts->take(10)->pluck('name')->toArray();
        $tpQty = $topProducts->take(10)->pluck('total_qty')->toArray();
        $tpChartConfig = [
            'type' => 'pie',
            'data' => [
                'labels' => $tpNames,
                'datasets' => [['data' => $tpQty, 'backgroundColor' => ['#0064E0', '#1877F2', '#31A24C', '#F7B928', '#E41E3F', '#6c757d', '#17a2b8', '#ffc107', '#28a745', '#dc3545']]]
            ],
            'options' => [
                'plugins' => ['datalabels' => ['display' => true, 'color' => '#fff']]
            ]
        ];
        $tpChartUrl = 'https://quickchart.io/chart?w=400&h=250&c=' . urlencode(json_encode($tpChartConfig));

        // 3. Payment Breakdown Chart
        $pbNames = $paymentBreakdown->pluck('payment_method')->map(function($p) {
            return $p == 'cash' ? 'Tunai' : strtoupper($p);
        })->toArray();
        $pbQty = $paymentBreakdown->pluck('count')->toArray();
        $pbChartConfig = [
            'type' => 'pie',
            'data' => [
                'labels' => $pbNames,
                'datasets' => [['data' => $pbQty, 'backgroundColor' => ['#0064E0', '#31A24C', '#F7B928', '#E41E3F']]]
            ],
            'options' => ['plugins' => ['datalabels' => ['display' => true, 'color' => '#fff']]]
        ];
        $pbChartUrl = 'https://quickchart.io/chart?w=400&h=250&c=' . urlencode(json_encode($pbChartConfig));

        // 4. Top Categories Chart
        $tcNames = $topCategories->take(10)->pluck('name')->toArray();
        $tcQty = $topCategories->take(10)->pluck('total_qty')->toArray();
        $tcChartConfig = [
            'type' => 'pie',
            'data' => [
                'labels' => $tcNames,
                'datasets' => [['data' => $tcQty, 'backgroundColor' => ['#31A24C', '#0064E0', '#E41E3F', '#F7B928', '#6c757d']]]
            ],
            'options' => ['plugins' => ['datalabels' => ['display' => true, 'color' => '#fff']]]
        ];
        $tcChartUrl = 'https://quickchart.io/chart?w=400&h=250&c=' . urlencode(json_encode($tcChartConfig));

    @endphp

    <div class="header">
        <h1>Laporan Ringkasan Bisnis (Dashboard)</h1>
        <p>Periode: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
    </div>

    <table class="metrics-grid">
        <tr>
            <td>
                <div class="metric-title">Total Pendapatan</div>
                <div class="metric-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="metric-title">Pendapatan Servis</div>
                <div class="metric-value">Rp {{ number_format($serviceRevenue, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="metric-title">Total Transaksi</div>
                <div class="metric-value">{{ number_format($transactionCount) }}</div>
            </td>
            <td>
                <div class="metric-title">Perbaikan Aktif</div>
                <div class="metric-value">{{ number_format($activeRepairs) }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Tren Pendapatan</div>
    <div class="chart-container">
        <img src="{{ $trendChartUrl }}" alt="Trend Chart" />
    </div>

    <table width="100%">
        <tr>
            <td width="50%" valign="top">
                <div class="section-title">Produk Terlaris</div>
                <div class="chart-container">
                    <img src="{{ $tpChartUrl }}" alt="Top Products Chart" />
                </div>
            </td>
            <td width="50%" valign="top">
                <div class="section-title">Metode Pembayaran</div>
                <div class="chart-container">
                    <img src="{{ $pbChartUrl }}" alt="Payment Breakdown Chart" />
                </div>
            </td>
        </tr>
    </table>
    
    <div class="section-title">Top Kategori</div>
    <div class="chart-container">
        <img src="{{ $tcChartUrl }}" alt="Top Categories Chart" />
    </div>

    <div class="page-break"></div>
    
    <div class="header">
        <h1>Rincian Data Tabel</h1>
        <p>Periode: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
    </div>

    <div class="section-title">1. Produk Terlaris</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th class="text-center">Kuantitas Terjual</th>
                <th class="text-right">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topProducts as $idx => $item)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $item->name }}</td>
                <td class="text-center">{{ $item->total_qty }}</td>
                <td class="text-right">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">2. Kategori Terlaris</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Kategori</th>
                <th class="text-center">Kuantitas Terjual</th>
                <th class="text-right">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topCategories as $idx => $item)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $item->name }}</td>
                <td class="text-center">{{ $item->total_qty }}</td>
                <td class="text-right">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>
    <div class="section-title">3. Metode Pembayaran</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Metode</th>
                <th class="text-center">Jumlah Transaksi</th>
                <th class="text-right">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($paymentBreakdown as $idx => $item)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $item->payment_method == 'cash' ? 'Tunai' : strtoupper($item->payment_method) }}</td>
                <td class="text-center">{{ $item->count }}</td>
                <td class="text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">4. Suku Cadang Sering Digunakan (Servis)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk (Suku Cadang)</th>
                <th class="text-center">Kuantitas Digunakan</th>
                <th class="text-right">Total Biaya</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topSpareparts as $idx => $item)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $item->name }}</td>
                <td class="text-center">{{ $item->total_qty }}</td>
                <td class="text-right">Rp {{ number_format($item->total_cost, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>
    <div class="section-title">5. Transaksi Terbaru</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>No Transaksi</th>
                <th>Waktu</th>
                <th>Kasir</th>
                <th>Metode</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentTransactions as $idx => $trx)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $trx->receipt_no ?? $trx->transaction_code }}</td>
                <td>{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d/m/Y H:i') }}</td>
                <td>{{ $trx->cashier->name ?? '-' }}</td>
                <td>{{ $trx->payment_method }}</td>
                <td class="text-right">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>
    <div class="section-title">6. Analisis Hutang / Piutang</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama (Kreditur/Debitur)</th>
                <th>Jenis</th>
                <th class="text-right">Sisa Hutang</th>
            </tr>
        </thead>
        <tbody>
            @forelse($unpaidDebts as $idx => $debt)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($debt->debt_date)->format('d/m/Y') }}</td>
                <td>{{ $debt->type == 'payable' ? ($debt->supplier->name ?? '-') : ($debt->customer->name ?? $debt->debtor_name ?? '-') }}</td>
                <td>{{ $debt->type == 'payable' ? 'Hutang Toko' : 'Piutang Pelanggan' }}</td>
                <td class="text-right text-red-600 font-bold">Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">7. Pengadaan Barang (Pembelian)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>No Faktur</th>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th>Status</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($procurements as $idx => $proc)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $proc->invoice_no }}</td>
                <td>{{ \Carbon\Carbon::parse($proc->purchase_date)->format('d/m/Y') }}</td>
                <td>{{ $proc->supplier->name ?? '-' }}</td>
                <td>{{ strtoupper($proc->status) }}</td>
                <td class="text-right">Rp {{ number_format($proc->total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>


    <div style="margin-top: 30px; text-align: right; font-size: 10px; color: #666;">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>