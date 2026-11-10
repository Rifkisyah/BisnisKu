
<?php $__env->startSection('page-title', 'Laporan Layanan Perbaikan'); ?>
<?php $__env->startSection('content'); ?>
<div class="print:hidden">




<div class="mb-6 flex items-center justify-between">
    <a href="<?php echo e(route('reports.index')); ?>" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg><?php echo e(__('messages.back') ?? 'Kembali'); ?></a>
</div>

<div class="mb-6 card-feature p-5">
    <div class="flex flex-col xl:flex-row xl:items-start justify-between gap-6">
        <div class="xl:w-1/3">
            <h1 class="type-heading-sm !text-xl sm:!text-2xl text-[var(--color-ink-deep)] flex items-center gap-2">
                <svg class="w-6 h-6 text-[var(--color-primary)] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Analisis Layanan Perbaikan
            </h1>
            <p class="type-caption text-[var(--color-slate)] mt-2">Statistik perbaikan dan penggunaan sparepart · Periode <?php echo e(\Carbon\Carbon::parse($startDate)->format('d M Y')); ?> <b>-</b> <?php echo e(\Carbon\Carbon::parse($endDate)->format('d M Y')); ?></p>
        </div>
        
        <div class="xl:w-2/3 w-full">
            <form method="GET" class="flex flex-row flex-wrap xl:justify-end items-end gap-3 w-full" id="service-filter-form">
                <div class="flex flex-col">
                    <label for="service-start-date" class="type-caption-bold text-[var(--color-slate)] mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="service-start-date" value="<?php echo e(\Carbon\Carbon::parse($startDate)->format('Y-m-d')); ?>" class="input-field text-xs !py-1.5 min-w-[120px]" onchange="this.form.submit()">
                </div>
                <div class="flex flex-col">
                    <label for="service-end-date" class="type-caption-bold text-[var(--color-slate)] mb-1">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="service-end-date" value="<?php echo e(\Carbon\Carbon::parse($endDate)->format('Y-m-d')); ?>" class="input-field text-xs !py-1.5 min-w-[120px]" onchange="this.form.submit()">
                </div>
                
                <div class="flex flex-row items-center gap-2 mt-2 md:mt-0 xl:border-r border-[var(--color-hairline-soft)] xl:pr-3">
                <?php
                    $diffDays = \Carbon\Carbon::parse($startDate)->startOfDay()->diffInDays(\Carbon\Carbon::parse($endDate)->startOfDay());
                    $activeFilter = null;
                    if ($diffDays == 7) $activeFilter = 7;
                    elseif ($diffDays == 30 || !request()->has('start_date')) $activeFilter = 30;
                    elseif ($diffDays == 365) $activeFilter = 365;
                ?>
                <button type="button" onclick="setDateRange(7)" class="w-[84px] text-center justify-center <?php echo e($activeFilter === 7 ? 'bg-black text-white hover:bg-black/80 rounded-full font-semibold shadow-sm border-2 border-transparent' : 'btn-ghost'); ?> !py-2 !px-3 text-xs"><?php echo e(__('messages.week') ?? 'Minggu'); ?></button>
                <button type="button" onclick="setDateRange(30)" class="w-[84px] text-center justify-center <?php echo e($activeFilter === 30 ? 'bg-black text-white hover:bg-black/80 rounded-full font-semibold shadow-sm border-2 border-transparent' : 'btn-ghost'); ?> !py-2 !px-3 text-xs"><?php echo e(__('messages.month') ?? 'Bulan'); ?></button>
                <button type="button" onclick="setDateRange(365)" class="w-[84px] text-center justify-center <?php echo e($activeFilter === 365 ? 'bg-black text-white hover:bg-black/80 rounded-full font-semibold shadow-sm border-2 border-transparent' : 'btn-ghost'); ?> !py-2 !px-3 text-xs"><?php echo e(__('messages.year') ?? 'Tahun'); ?></button>
                </div>

                <div class="flex flex-row items-center gap-2 mt-2 md:mt-0">
                <button type="button" onclick="downloadPdf()" class="w-[130px] justify-center btn-ghost !py-2 !px-3 text-xs flex items-center gap-1 text-[var(--color-critical)] border-[var(--color-critical)]/20 hover:bg-[var(--color-critical)]/10 rounded-[var(--radius-md)]" title="PDF">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    PDF
                </button>
                <button type="button" onclick="window.print()" class="w-[130px] justify-center btn-ghost !py-2 !px-3 text-xs flex items-center gap-1 rounded-[var(--radius-md)]" title="Cetak">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak
                </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="card-feature p-5 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-500 to-blue-300 rounded-t-[var(--radius-xxxl)]"></div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Total Perbaikan</p>
        <p class="mt-2 type-heading-sm text-[var(--color-ink-deep)]"><?php echo e($serviceStats['total']); ?></p>
        <p class="mt-1 type-caption text-[var(--color-stone)]">Dibuka dalam periode ini</p>
    </div>
    <div class="card-feature p-5 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-amber-500 to-amber-300 rounded-t-[var(--radius-xxxl)]"></div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Aktif</p>
        <p class="mt-2 type-heading-sm <?php echo e($serviceStats['active'] > 0 ? 'text-[var(--color-attention)]' : 'text-[var(--color-ink-deep)]'); ?>"><?php echo e($serviceStats['active']); ?></p>
        <p class="mt-1 type-caption text-[var(--color-stone)]">Sedang dalam proses</p>
    </div>
    <div class="card-feature p-5 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-green-500 to-green-300 rounded-t-[var(--radius-xxxl)]"></div>
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider">Selesai</p>
        <p class="mt-2 type-heading-sm text-[var(--color-success)]"><?php echo e($serviceStats['done']); ?></p>
        <p class="mt-1 type-caption text-[var(--color-stone)]">Perbaikan done/picked up</p>
    </div>
    <div class="card-feature p-5 relative overflow-hidden border-2 border-[var(--color-primary)]/20 bg-[var(--color-primary)]/5">
        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-primary-deep)] rounded-t-[var(--radius-xxxl)]"></div>
        <p class="type-caption-bold text-[var(--color-primary)] uppercase tracking-wider">Revenue Service</p>
        <p class="mt-2 type-heading-sm text-[var(--color-primary)]">Rp <?php echo e(number_format($serviceStats['revenue'], 0, ',', '.')); ?></p>
        <p class="mt-1 type-caption text-[var(--color-primary)]/70"><?php echo e(__('messages.service_and_components_completed')); ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    
    <div class="card-feature flex flex-col min-h-[500px]" x-data="{ view: 'chart', chartType: 'line_bar', metric: 'both' }" x-init="$watch('view', val => { if(val === 'chart') setTimeout(() => window.dispatchEvent(new Event('resize')), 50) }); $watch('chartType', val => updateServiceTrendChart(val, metric)); $watch('metric', val => updateServiceTrendChart(chartType, val)); updateServiceTrendChart(chartType, metric)">
        <div class="flex flex-col md:flex-row md:items-center justify-between p-4 md:p-5 border-b border-[var(--color-hairline-soft)] gap-3">
            <div>
                <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]"><?php echo e(__('messages.monthly_service_trend')); ?></h3>
                <p class="type-caption text-[var(--color-slate)]"><?php echo e(__('messages.ticket_count_and_revenue')); ?></p>
            </div>
            <div class="flex gap-2 mt-2 sm:mt-0 w-full sm:w-auto">
                <button @click="view = 'chart'" :class="view === 'chart' ? 'pill-tab-active' : ''" class="pill-tab flex-1 sm:flex-none !text-xs">Grafik</button>
                <button @click="view = 'table'" :class="view === 'table' ? 'pill-tab-active' : ''" class="pill-tab flex-1 sm:flex-none !text-xs">Tabel</button>
            </div>
        </div>
        <div x-show="view === 'chart'" class="p-5 flex-1 flex flex-col">
            <div class="flex flex-col sm:flex-row gap-4 mb-4 justify-center">
                <select x-model="chartType" class="input-field w-full md:w-auto text-xs !py-1.5">
                    <option value="line_bar" x-show="metric === 'both'">Line & Bar (Kombinasi)</option>
                    <option value="line">Line Chart</option>
                    <option value="bar">Bar Chart</option>
                    <option value="doughnut">Doughnut Chart</option>
                    <option value="pie">Pie Chart</option>
                </select>
                <select x-model="metric" class="input-field w-full md:w-auto text-xs !py-1.5" @change="if(metric === 'both' && (chartType === 'doughnut' || chartType === 'pie')) chartType = 'line_bar'">
                    <option value="both" x-show="chartType !== 'doughnut' && chartType !== 'pie'"><?php echo e(__('messages.both_tickets_revenue')); ?></option>
                    <option value="count"><?php echo e(__('messages.ticket_count')); ?></option>
                    <option value="revenue">Total Revenue</option>
                </select>
            </div>
            <div class="flex-1 min-h-[220px] relative">
                <?php if(count($serviceTrend) > 0): ?>
                <canvas id="serviceTrendChart"></canvas>
                <?php else: ?>
                <div class="absolute inset-0 flex items-center justify-center bg-[var(--color-surface-soft)] rounded-lg border border-dashed border-[var(--color-hairline-soft)]">
                    <p class="type-body-sm text-[var(--color-slate)]"><?php echo e(__('messages.no_data_to_display')); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div x-show="view === 'table'" x-cloak class="flex-1 flex flex-col">
            <div class="overflow-x-auto flex-1">
                <table class="w-full">
                    <thead><tr class="bg-[var(--color-surface-soft)]">
                        <th class="px-4 py-3 text-left table-header"><?php echo e(__('messages.month')); ?></th>
                        <th class="px-4 py-3 text-right table-header"><?php echo e(__('messages.tickets')); ?></th>
                        <th class="px-4 py-3 text-right table-header"><?php echo e(__('messages.revenue')); ?></th>
                    </tr></thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $serviceTrend; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)]">
                        <td class="px-4 py-2.5 type-body-sm font-medium text-[var(--color-ink)]"><?php echo e($st->month); ?></td>
                        <td class="px-4 py-2.5 text-right"><span class="badge badge-info"><?php echo e($st->count); ?></span></td>
                        <td class="px-4 py-2.5 type-body-sm-bold text-right text-[var(--color-ink-deep)]">Rp <?php echo e(number_format($st->revenue, 0, ',', '.')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="3" class="px-4 py-8 text-center type-body-sm text-[var(--color-slate)]">Belum ada data servis.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-[var(--color-hairline-soft)] text-center mt-auto">
                <a href="<?php echo e(route('service-repairs.index', request()->all())); ?>" class="bg-[var(--color-ink)] text-white hover:bg-[var(--color-ink-deep)] transition-colors rounded-[var(--radius-full)] font-medium type-body-sm !py-2 !px-6 inline-flex items-center gap-2">
                    Lihat Keseluruhan Data
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </div>

    
    <div class="card-feature flex flex-col min-h-[500px]" x-data="{ view: 'chart', chartType: 'bar', metric: 'qty' }" x-init="$watch('view', val => { if(val === 'chart') setTimeout(() => window.dispatchEvent(new Event('resize')), 50) }); $watch('chartType', val => updateSparepartChart(val, metric)); $watch('metric', val => updateSparepartChart(chartType, val)); updateSparepartChart(chartType, metric)">
        <div class="flex flex-col md:flex-row md:items-center justify-between p-4 md:p-5 border-b border-[var(--color-hairline-soft)] gap-3">
            <div>
                <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]"><?php echo e(__('messages.top_spareparts_used')); ?></h3>
                <p class="type-caption text-[var(--color-slate)]">Berdasarkan Jumlah pemakaian dari stok</p>
            </div>
            <div class="flex gap-2 mt-2 sm:mt-0 w-full sm:w-auto">
                <button @click="view = 'chart'" :class="view === 'chart' ? 'pill-tab-active' : ''" class="pill-tab flex-1 sm:flex-none !text-xs">Grafik</button>
                <button @click="view = 'table'" :class="view === 'table' ? 'pill-tab-active' : ''" class="pill-tab flex-1 sm:flex-none !text-xs">Tabel</button>
            </div>
        </div>
        <div x-show="view === 'chart'" class="p-5 flex-1 flex flex-col">
            <div class="flex flex-col sm:flex-row gap-4 mb-4 justify-center">
                <select x-model="chartType" class="input-field w-full md:w-auto text-xs !py-1.5">
                    <option value="bar">Bar Chart</option>
                    <option value="doughnut">Doughnut Chart</option>
                    <option value="pie">Pie Chart</option>
                </select>
                <select x-model="metric" class="input-field w-full md:w-auto text-xs !py-1.5">
                    <option value="qty">Jumlah Digunakan</option>
                    <option value="cost">Total Harga</option>
                </select>
            </div>
            <div class="flex-1 min-h-[220px] relative">
                <?php if(count($topSpareparts) > 0): ?>
                <canvas id="sparepartChart"></canvas>
                <?php else: ?>
                <div class="absolute inset-0 flex items-center justify-center bg-[var(--color-surface-soft)] rounded-lg border border-dashed border-[var(--color-hairline-soft)]">
                    <p class="type-body-sm text-[var(--color-slate)]"><?php echo e(__('messages.no_data_to_display')); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div x-show="view === 'table'" x-cloak class="flex-1 flex flex-col">
            <div class="overflow-x-auto flex-1">
                <table class="w-full">
                    <thead><tr class="bg-[var(--color-surface-soft)]">
                        <th class="px-4 py-3 text-left table-header">Nama Sparepart</th>
                        <th class="px-4 py-3 text-center table-header">Kode</th>
                        <th class="px-4 py-3 text-right table-header">Jumlah</th>
                        <th class="px-4 py-3 text-right table-header">Total Harga</th>
                    </tr></thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $topSpareparts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr onclick="window.location='<?php echo e(route('products.show', $ts->component_code)); ?>'" class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] cursor-pointer transition-colors">
                        <td class="px-4 py-2.5 type-body-sm font-medium text-[var(--color-ink)]"><?php echo e($ts->name); ?></td>
                        <td class="px-4 py-2.5 text-center"><span class="badge"><?php echo e($ts->component_code); ?></span></td>
                        <td class="px-4 py-2.5 text-right"><span class="type-caption-bold text-[var(--color-ink)]"><?php echo e($ts->total_qty); ?></span></td>
                        <td class="px-4 py-2.5 text-right type-caption text-[var(--color-slate)]">Rp <?php echo e(number_format($ts->total_cost, 0, ',', '.')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="px-4 py-8 text-center type-body-sm text-[var(--color-slate)]">Belum ada penggunaan sparepart.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-[var(--color-hairline-soft)] text-center mt-auto">
                <a href="<?php echo e(route('service-repairs.index', request()->all())); ?>" class="bg-[var(--color-ink)] text-white hover:bg-[var(--color-ink-deep)] transition-colors rounded-[var(--radius-full)] font-medium type-body-sm !py-2 !px-6 inline-flex items-center gap-2">
                    Lihat Keseluruhan Data
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </div>
</div>

</div>
<!-- END print:hidden -->




<div class="hidden print:block w-full text-black bg-white" style="margin:0;padding:0;font-family:'Inter',sans-serif">
<style>
@media print {
    @page { size: A4 landscape; margin: 12mm 15mm; }
    body { background: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .print\:hidden { display: none !important; }
    .print\:block { display: block !important; }
    
    .pr-container { width: 100%; max-width: 100%; margin: 0 auto; color: #0f172a; }
    .pr-header { display: table; width: 100%; border-bottom: 2px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 15px; }
    .pr-header-left { display: table-cell; vertical-align: bottom; }
    .pr-header-right { display: table-cell; vertical-align: bottom; text-align: right; }
    .pr-title { font-size: 22px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; margin: 0 0 4px 0; }
    .pr-subtitle { font-size: 11px; color: #64748b; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; margin: 0; }
    .pr-meta { font-size: 12px; color: #475569; margin: 2px 0; }
    
    .pr-section { margin-bottom: 20px; break-inside: avoid; }
    .pr-sh { display: table; width: 100%; margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; }
    .pr-snum { display: table-cell; width: 24px; vertical-align: middle; font-size: 14px; font-weight: 800; color: #0064E0; }
    .pr-stitle { display: table-cell; vertical-align: middle; font-size: 14px; font-weight: 700; color: #1e293b; }
    
    .pr-kpi { display: table; width: 100%; table-layout: fixed; border-collapse: separate; border-spacing: 10px 0; margin-left: -10px; margin-right: -10px; margin-bottom: 15px; }
    .pr-kpi-item { display: table-cell; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; }
    .pr-kpi-label { font-size: 10px; color: #64748b; font-weight: 600; text-transform: uppercase; margin-bottom: 4px; }
    .pr-kpi-val { font-size: 18px; font-weight: 800; color: #0f172a; }
    
    .prt { width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 10px; }
    .prt th { background: #f1f5f9; color: #475569; font-weight: 600; text-align: left; padding: 6px 8px; border-bottom: 2px solid #cbd5e1; }
    .prt td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; color: #1e293b; }
    .prt .c { text-align: center; }
    .prt .r { text-align: right; }
    
    .pr-chart-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; text-align: center; margin-bottom: 15px; }
    .pr-row { display: table; width: 100%; table-layout: fixed; border-spacing: 15px 0; margin-left: -15px; margin-right: -15px; }
    .pr-col { display: table-cell; vertical-align: top; }
    
    .pr-footer { margin-top: 30px; padding-top: 10px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #94a3b8; display: flex; justify-content: space-between; }
    .pr-pb { page-break-after: always; }
}
</style>

<div class="pr-container">
    <div class="pr-header">
        <div class="pr-header-left">
            <h1 class="pr-title">Analisis Layanan Perbaikan</h1>
            <p class="pr-subtitle"><?php echo e(\App\Models\Setting::get('store_name', 'Nama Toko')); ?></p>
        </div>
        <div class="pr-header-right">
            <p class="pr-meta"><strong>Periode:</strong> <?php echo e(\Carbon\Carbon::parse($startDate)->format('d M Y')); ?> – <?php echo e(\Carbon\Carbon::parse($endDate)->format('d M Y')); ?></p>
            <p class="pr-meta"><strong>Dicetak:</strong> <?php echo e(now()->format('d M Y H:i')); ?></p>
        </div>
    </div>

    
    <div class="pr-section">
        <div class="pr-sh"><div class="pr-snum">1</div><div class="pr-stitle">Ringkasan Tiket Layanan</div></div>
        <div class="pr-kpi">
            <div class="pr-kpi-item" style="border-top:3px solid #3b82f6">
                <div class="pr-kpi-label">Total Perbaikan</div>
                <div class="pr-kpi-val"><?php echo e($serviceStats['total']); ?></div>
            </div>
            <div class="pr-kpi-item" style="border-top:3px solid #f59e0b">
                <div class="pr-kpi-label">Aktif</div>
                <div class="pr-kpi-val" style="color:#f59e0b"><?php echo e($serviceStats['active']); ?></div>
            </div>
            <div class="pr-kpi-item" style="border-top:3px solid #10b981">
                <div class="pr-kpi-label">Selesai</div>
                <div class="pr-kpi-val" style="color:#10b981"><?php echo e($serviceStats['done']); ?></div>
            </div>
            <div class="pr-kpi-item" style="border-top:3px solid #0ea5e9; background:#f0f9ff; border-color:#bae6fd">
                <div class="pr-kpi-label">Revenue Service</div>
                <div class="pr-kpi-val" style="color:#0ea5e9">Rp <?php echo e(number_format($serviceStats['revenue'], 0, ',', '.')); ?></div>
            </div>
        </div>
    </div>

    
    <div class="pr-section">
        <div class="pr-sh"><div class="pr-snum">2</div><div class="pr-stitle">Tren Bulanan & Penggunaan Sparepart</div></div>
        
        <div class="pr-row">
            <div class="pr-col" style="width:50%">
                <div class="pr-chart-box" id="print-service-trend-container">
                    <p style="font-size:10px;color:#94a3b8;margin-bottom:5px">Tren Bulanan Perbaikan</p>
                    <!-- Image injected via JS -->
                </div>
            </div>
            <div class="pr-col" style="width:50%">
                <?php if(count($topSpareparts ?? []) > 0): ?>
                <p style="font-size:11px;font-weight:bold;margin-bottom:5px">Penggunaan Sparepart Terbanyak (Top 10)</p>
                <table class="prt">
                    <thead><tr>
                        <th>Sparepart</th>
                        <th class="c">Qty</th>
                        <th class="r">Total Biaya</th>
                    </tr></thead>
                    <tbody>
                    <?php $__currentLoopData = $topSpareparts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td style="font-weight:bold"><?php echo e($ts->name); ?></td>
                        <td class="c"><?php echo e($ts->total_qty); ?></td>
                        <td class="r">Rp <?php echo e(number_format($ts->total_cost, 0, ',', '.')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div style="background:#f8fafc;padding:10px;border-radius:6px;font-size:11px;border:1px dashed #cbd5e1;text-align:center">Belum ada penggunaan sparepart.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="pr-footer">
        <span><?php echo e(\App\Models\Setting::get('store_name', 'Nama Toko')); ?> · Laporan Layanan Perbaikan</span>
        <span>Dicetak: <?php echo e(now()->format('d M Y H:i')); ?></span>
    </div>
</div>

<form id="pdf-export-form" method="POST" action="<?php echo e(route('reports.service_analysis.export_pdf')); ?>" style="display:none;">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="start_date" id="pdf_start_date">
    <input type="hidden" name="end_date" id="pdf_end_date">
    <input type="hidden" name="chart_service_trend" id="pdf_chart_service_trend">
</form>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
window.appCharts = {};
window.rawServiceData = {
    serviceTrend: <?php echo json_encode($serviceTrend, 15, 512) ?>,
    topSpareparts: <?php echo json_encode($topSpareparts, 15, 512) ?>
};

const PALETTE = ['#0064E0','#10B981','#F59E0B','#6366F1','#F43F5E','#06B6D4','#8B5CF6','#14B8A6','#F97316','#EC4899'];

window.updateServiceTrendChart = function(type, metric) {
    const ctx = document.getElementById('serviceTrendChart');
    if (!ctx) return;
    if (window.appCharts.trend) window.appCharts.trend.destroy();

    const data = window.rawServiceData.serviceTrend;
    let datasets = [];
    const isRadial = type === 'doughnut' || type === 'pie';
    
    // Prevent invalid combination
    if (metric === 'both' && isRadial) {
        metric = 'count';
    }
    
    if (metric === 'both' && type === 'line_bar') {
        datasets = [
            { label: 'Revenue (Rp)', type: 'bar', data: data.map(d => parseInt(d.revenue)), backgroundColor: 'rgba(0,100,224,0.1)', borderColor: 'rgba(0,100,224,0.3)', borderWidth: 1, yAxisID: 'y2', order: 2 },
            { label: 'Jumlah Perbaikan', type: 'line', data: data.map(d => parseInt(d.count)), borderColor: '#10B981', backgroundColor: '#10B981', tension: 0.3, yAxisID: 'y', order: 1 }
        ];
    } else {
        const valueKey = metric === 'count' ? 'count' : 'revenue';
        const labelStr = metric === 'count' ? 'Jumlah Perbaikan' : 'Revenue (Rp)';
        datasets = [{
            label: labelStr,
            data: data.map(d => parseInt(d[valueKey])),
            backgroundColor: isRadial ? PALETTE : (type === 'line' ? 'rgba(16,185,129,0.1)' : PALETTE),
            borderColor: isRadial ? PALETTE : (type === 'line' ? '#10B981' : PALETTE),
            borderWidth: isRadial ? 0 : 2,
            tension: 0.3, fill: type === 'line'
        }];
    }

    const options = {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: (metric === 'both' || isRadial) } }
    };
    
    if (!isRadial) {
        if (metric === 'both') {
            options.scales = {
                y: { position: 'left' },
                y2: { position: 'right', grid: { drawOnChartArea: false } }
            };
        } else {
            options.scales = { y: { beginAtZero: true } };
        }
    }

    const renderType = type === 'line_bar' ? 'line' : type;

    window.appCharts.trend = new Chart(ctx, { type: renderType, data: { labels: data.map(d => d.month), datasets }, options });
};

window.updateSparepartChart = function(type, metric) {
    const ctx = document.getElementById('sparepartChart');
    if (!ctx) return;
    if (window.appCharts.sparepart) window.appCharts.sparepart.destroy();

    const data = window.rawServiceData.topSpareparts;
    const valueKey = metric === 'qty' ? 'total_qty' : 'total_cost';
    const labelStr = metric === 'qty' ? 'Qty Digunakan' : 'Total Harga (Rp)';
    
    const isRadial = type === 'doughnut' || type === 'pie';

    window.appCharts.sparepart = new Chart(ctx, {
        type: type,
        data: {
            labels: data.map(d => d.name),
            datasets: [{ label: labelStr, data: data.map(d => parseInt(d[valueKey])), backgroundColor: PALETTE }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            indexAxis: (!isRadial && type === 'bar') ? 'y' : 'x',
            plugins: { legend: { display: isRadial } },
            scales: isRadial ? {} : { x: { beginAtZero: true }, y: { beginAtZero: true } }
        }
    });
};

document.addEventListener('DOMContentLoaded', function() {
    // Initial render handled by Alpine.js x-init or can be called here if needed.
});

function setDateRange(days) {
    const startInput = document.getElementById('service-start-date').value;
    const start = startInput ? new Date(startInput) : new Date();
    
    const end = new Date(start);
    end.setDate(end.getDate() + days);
    
    const fmt = d => {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };
    
    document.getElementById('service-start-date').value = fmt(start);
    document.getElementById('service-end-date').value   = fmt(end);
    document.getElementById('service-filter-form').submit();
}

function downloadPdf() {
    document.getElementById('pdf_start_date').value = document.getElementById('service-start-date').value;
    document.getElementById('pdf_end_date').value = document.getElementById('service-end-date').value;
    
    const trendChart = document.getElementById('serviceTrendChart');
    if (trendChart) document.getElementById('pdf_chart_service_trend').value = trendChart.toDataURL('image/png');
    
    document.getElementById('pdf-export-form').submit();
}

window.addEventListener('beforeprint', () => {
    const trendChart = document.getElementById('serviceTrendChart');
    if (trendChart) {
        document.getElementById('print-service-trend-container').innerHTML = '<img src="' + trendChart.toDataURL('image/png') + '" style="max-height: 200px; width: auto; margin: 0 auto; display: block;">';
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Office\Developer\Projects\Laravel\BisnisKu\resources\views/reports/service-analysis/index.blade.php ENDPATH**/ ?>