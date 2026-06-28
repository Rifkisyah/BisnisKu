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
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data-table th, table.data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table.data-table th { background-color: #f1f5f9; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .chart-container { text-align: center; margin-bottom: 15px; }
        .chart-container img { max-width: 100%; height: auto; max-height: 250px; }
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
        $tpNames = $topProducts->pluck('name')->toArray();
        $tpQty = $topProducts->pluck('total_qty')->toArray();
        $tpChartConfig = [
            'type' => 'pie',
            'data' => [
                'labels' => $tpNames,
                'datasets' => [['data' => $tpQty, 'backgroundColor' => ['#0064E0', '#1877F2', '#31A24C', '#F7B928', '#E41E3F']]]
            ],
            'options' => [
                'plugins' => ['datalabels' => ['display' => true, 'color' => '#fff']]
            ]
        ];
        $tpChartUrl = 'https://quickchart.io/chart?w=400&h=250&c=' . urlencode(json_encode($tpChartConfig));

        // 3. K-Means Chart
        $kmCounts = ['fast_moving' => 0, 'medium_moving' => 0, 'slow_moving' => 0, 'dead_stock' => 0];
        foreach($clusterResults as $i) {
            if(isset($kmCounts[$i['cluster_label']])) $kmCounts[$i['cluster_label']]++;
        }
        $kmLabelsStr = [__('messages.fast_moving'), __('messages.medium_moving'), __('messages.slow_moving'), __('messages.dead_stock')];
        $kmChartConfig = [
            'type' => 'doughnut',
            'data' => [
                'labels' => $kmLabelsStr,
                'datasets' => [['data' => array_values($kmCounts), 'backgroundColor' => ['#31A24C', '#F7B928', '#E41E3F', '#666666']]]
            ],
            'options' => [
                'plugins' => ['datalabels' => ['display' => true, 'color' => '#fff']]
            ]
        ];
        $kmChartUrl = 'https://quickchart.io/chart?w=400&h=250&c=' . urlencode(json_encode($kmChartConfig));

        // 4. SMA Restock Chart
        $smaCounts = ['restock' => 0, 'ok' => 0];
        foreach($smaResults as $r) {
            if ($r['needs_restock']) $smaCounts['restock']++;
            else $smaCounts['ok']++;
        }
        $smaChartConfig = [
            'type' => 'doughnut',
            'data' => [
                'labels' => ['Perlu Restock', 'Stok Aman'],
                'datasets' => [['data' => [$smaCounts['restock'], $smaCounts['ok']], 'backgroundColor' => ['#E41E3F', '#31A24C']]]
            ],
            'options' => [
                'plugins' => ['datalabels' => ['display' => true, 'color' => '#fff']]
            ]
        ];
        $smaChartUrl = 'https://quickchart.io/chart?w=400&h=250&c=' . urlencode(json_encode($smaChartConfig));
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

    <div class="section-title">5 Produk Terlaris (Berdasarkan Volume)</div>
    <div class="chart-container">
        <img src="{{ $tpChartUrl }}" alt="Top Products Chart" />
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th class="text-center">Kuantitas Terjual</th>
                <th class="text-right">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topProducts as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td class="text-center">{{ $item->total_qty }}</td>
                <td class="text-right">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="page-break-before: always;"></div>

    <div class="section-title">Klasifikasi Pergerkan Stok Produk</div>
    <div class="chart-container">
        <img src="{{ $kmChartUrl }}" alt="K-Means Chart" />
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th class="text-center">Terjual</th>
                <th>Kategori Pergerakan Stok Produk</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clusterResults as $item)
            <tr>
                <td>{{ $item['product_name'] }}</td>
                <td class="text-center">{{ $item['total_qty_sold'] }}</td>
                <td>{{ __('messages.' . $item['cluster_label']) }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Rekomendasi isi ulang Stok</div>
    <div class="chart-container">
        <img src="{{ $smaChartUrl }}" alt="SMA Restock Chart" />
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th class="text-center">Sisa Stok</th>
                <th class="text-center">Batas Minimum</th>
                <th class="text-center">Saran Restock</th>
            </tr>
        </thead>
        <tbody>
            @forelse($restockRecommendations as $item)
            <tr>
                <td>{{ $item['product_name'] }}</td>
                <td class="text-center" style="color:red;font-weight:bold;">{{ $item['current_stock'] }}</td>
                <td class="text-center">{{ $item['minimum_stock'] }}</td>
                <td class="text-center">+{{ $item['restock_recommendation'] }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">Tidak ada produk yang perlu di-restock.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right; font-size: 10px; color: #666;">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
