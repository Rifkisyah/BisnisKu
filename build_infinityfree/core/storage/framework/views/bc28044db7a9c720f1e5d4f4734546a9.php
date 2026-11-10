<?php $__env->startSection('page-title', __('messages.dashboard')); ?>
<?php $__env->startSection('content'); ?>


<form method="GET" class="mb-6 flex flex-col md:flex-row md:flex-wrap items-start md:items-end gap-4 card-feature p-4 md:p-6" id="dashboard-filter-form">
    <div class="flex flex-col sm:flex-row w-full md:w-auto gap-4">
        <div class="flex-1 w-full sm:w-auto">
            <label class="block type-caption-bold text-[var(--color-slate)] mb-1"><?php echo e(__('messages.start_date')); ?></label>
            <input type="date" name="start_date" id="dash-start-date" value="<?php echo e($startDate->format('Y-m-d')); ?>" class="input-field w-full" onchange="this.form.submit()">
        </div>
        <div class="flex-1 w-full sm:w-auto">
            <label class="block type-caption-bold text-[var(--color-slate)] mb-1"><?php echo e(__('messages.end_date')); ?></label>
            <input type="date" name="end_date" id="dash-end-date" value="<?php echo e($endDate->format('Y-m-d')); ?>" class="input-field w-full" onchange="this.form.submit()">
        </div>
    </div>
    
    <div class="flex gap-2 w-full md:w-auto md:ml-2 md:mr-2 md:border-r border-b md:border-b-0 border-[var(--color-hairline-soft)] pb-4 md:pb-0 md:pr-4">
        <?php
            $start = \Carbon\Carbon::parse($startDate)->startOfDay();
            $end = \Carbon\Carbon::parse($endDate)->startOfDay();
            $diffDays = $start->diffInDays($end);
            $activeFilter = null;
            if ($diffDays == 7) $activeFilter = 7;
            elseif ($diffDays == 365) $activeFilter = 365;
            elseif ($diffDays == 30 || !request()->has('start_date')) $activeFilter = 30;
        ?>
        <button type="button" onclick="setDateRange(7)" class="flex-1 text-center justify-center <?php echo e($activeFilter === 7 ? 'bg-black text-white hover:bg-black/80 rounded-full font-semibold shadow-sm border-2 border-transparent' : 'btn-ghost'); ?> !py-2 !px-3 text-xs sm:text-sm"><?php echo e(__('messages.week')); ?></button>
        <button type="button" onclick="setDateRange(30)" class="flex-1 text-center justify-center <?php echo e($activeFilter === 30 ? 'bg-black text-white hover:bg-black/80 rounded-full font-semibold shadow-sm border-2 border-transparent' : 'btn-ghost'); ?> !py-2 !px-3 text-xs sm:text-sm"><?php echo e(__('messages.month')); ?></button>
        <button type="button" onclick="setDateRange(365)" class="flex-1 text-center justify-center <?php echo e($activeFilter === 365 ? 'bg-black text-white hover:bg-black/80 rounded-full font-semibold shadow-sm border-2 border-transparent' : 'btn-ghost'); ?> !py-2 !px-3 text-xs sm:text-sm"><?php echo e(__('messages.year')); ?></button>
    </div>

    <div class="flex w-full md:w-auto md:ml-auto gap-2 flex-wrap sm:flex-nowrap">
        <a href="<?php echo e(route('catalog.store.index', ['store' => app('current_store')->slug])); ?>" target="_blank" class="flex-1 justify-center btn-primary flex items-center gap-1 !py-1.5 !px-2.5 text-[10px] sm:text-xs rounded-[var(--radius-md)]">
            <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
            </svg> <span><?php echo e(app()->getLocale() == 'id' ? 'Lihat Katalog' : 'View Catalog'); ?></span>
        </a>
        <a href="<?php echo e(route('dashboard.export_pdf', request()->all())); ?>" class="flex-1 justify-center btn-ghost flex items-center gap-1 !py-1.5 !px-2.5 text-[var(--color-critical)] border-[var(--color-critical)]/20 hover:bg-[var(--color-critical)]/10 text-[10px] sm:text-xs rounded-[var(--radius-md)]">
            <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg> <span><?php echo e(__('messages.export_pdf')); ?></span>
        </a>
        <button type="button" onclick="window.print()" class="flex-1 justify-center btn-ghost flex items-center gap-1 !py-1.5 !px-2.5 text-[10px] sm:text-xs rounded-[var(--radius-md)]">
            <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
            </svg> <span><?php echo e(__('messages.print')); ?></span>
        </button>
    </div>
</form>


<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 mb-8">
    <div class="card-feature print:break-inside-avoid p-5">
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider truncate" title="Total Keseluruhan (Penjualan + Servis)">Total Keseluruhan</p>
        <p class="mt-3 type-heading-sm text-[var(--color-ink-deep)] truncate">Rp <?php echo e(number_format($totalCombinedRevenue, 0, ',', '.')); ?></p>
        <div class="mt-2 h-1 w-12 rounded-full bg-[var(--color-primary)]"></div>
    </div>
    <div class="card-feature print:break-inside-avoid p-5">
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider truncate" title="<?php echo e(__('messages.total_revenue')); ?>"><?php echo e(__('messages.total_revenue')); ?></p>
        <p class="mt-3 type-heading-sm text-[var(--color-ink-deep)] truncate">Rp <?php echo e(number_format($totalRevenue, 0, ',', '.')); ?></p>
        <div class="mt-2 h-1 w-12 rounded-full bg-[var(--color-primary)]"></div>
    </div>
    <div class="card-feature print:break-inside-avoid p-5">
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider truncate" title="<?php echo e(__('messages.service_revenue')); ?>"><?php echo e(__('messages.service_revenue')); ?></p>
        <p class="mt-3 type-heading-sm text-[var(--color-ink-deep)] truncate">Rp <?php echo e(number_format($serviceRevenue, 0, ',', '.')); ?></p>
        <div class="mt-2 h-1 w-12 rounded-full bg-[var(--color-success)]"></div>
    </div>
    <div class="card-feature print:break-inside-avoid p-5">
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider truncate" title="<?php echo e(__('messages.total_transactions')); ?>"><?php echo e(__('messages.total_transactions')); ?></p>
        <p class="mt-3 type-heading-sm text-[var(--color-ink-deep)] truncate"><?php echo e(number_format($transactionCount)); ?></p>
        <div class="mt-2 h-1 w-12 rounded-full bg-[var(--color-fb-blue)]"></div>
    </div>
    <div class="card-feature print:break-inside-avoid p-5">
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider truncate" title="<?php echo e(__('messages.active_repairs')); ?>"><?php echo e(__('messages.active_repairs')); ?></p>
        <p class="mt-3 type-heading-sm text-[var(--color-ink-deep)] truncate"><?php echo e(number_format($activeRepairs)); ?></p>
        <div class="mt-2 h-1 w-12 rounded-full bg-[var(--color-critical)]"></div>
    </div>
    <div class="card-feature print:break-inside-avoid p-5">
        <p class="type-caption-bold text-[var(--color-slate)] uppercase tracking-wider truncate" title="<?php echo e(__('messages.total_products')); ?>"><?php echo e(__('messages.total_products')); ?></p>
        <p class="mt-3 type-heading-sm text-[var(--color-ink-deep)] truncate"><?php echo e(number_format($productCount)); ?></p>
        <div class="mt-2 h-1 w-12 rounded-full bg-[var(--color-attention)]"></div>
    </div>
</div>


<div class="card-feature print:break-inside-avoid mb-6" x-data="{ view: 'chart', chartType: 'line' }" x-init="$watch('chartType', val => updateTrendChart(val))">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 p-4 md:p-5 border-b border-[var(--color-hairline-soft)]">
        <div class="flex items-center gap-3">
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]">
                <svg class="w-5 h-5 inline-block -mt-1 mr-1 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" /></svg>
                <?php echo e(__('messages.sales_trend_repair')); ?>

            </h3>
        </div>
        <div class="flex flex-col sm:flex-row w-full md:w-auto gap-2 mt-2 md:mt-0">
            <div class="flex w-full sm:w-auto gap-2">
                <a href="<?php echo e(route('reports.sales_revenue', request()->all())); ?>" class="flex-1 text-center justify-center pill-tab !bg-[var(--color-primary)]/10 text-[var(--color-primary)] hover:!bg-[var(--color-primary)]/20 !text-xs"><?php echo e(__('messages.report_sales')); ?> </a>
                <a href="<?php echo e(route('reports.service_analysis', request()->all())); ?>" class="flex-1 text-center justify-center pill-tab !bg-[var(--color-success)]/10 text-[var(--color-success)] hover:!bg-[var(--color-success)]/20 !text-xs sm:mr-2"><?php echo e(__('messages.report_repairs')); ?> </a>
            </div>
            <div class="flex w-full sm:w-auto gap-2">
                <button @click="view = 'chart'" :class="view === 'chart' ? 'pill-tab-active' : ''" class="flex-1 text-center justify-center pill-tab !text-xs"><?php echo e(__('messages.chart')); ?></button>
                <button @click="view = 'table'" :class="view === 'table' ? 'pill-tab-active' : ''" class="flex-1 text-center justify-center pill-tab !text-xs"><?php echo e(__('messages.table')); ?></button>
            </div>
        </div>
    </div>
    
    <div x-show="view === 'chart'" class="p-5">
        <div class="flex flex-col sm:flex-row sm:justify-end gap-2 mb-4 w-full">
            <select x-model="chartType" class="input-field w-full sm:w-auto text-xs !py-1.5">
                <option value="line"><?php echo e(__('messages.line_chart')); ?></option>
                <option value="bar"><?php echo e(__('messages.bar_chart')); ?></option>
            </select>
        </div>
        <?php if(count($salesTrend) > 0 || count($repairTrend) > 0): ?>
        <div style="height:250px;"><canvas id="combinedTrendChart"></canvas></div>
        <?php else: ?>
        <div style="height:250px;" class="flex items-center justify-center text-[var(--color-slate)]"><?php echo e(__('messages.no_data')); ?></div>
        <?php endif; ?>
    </div>

    <div x-show="view === 'table'" class="p-0 overflow-x-auto" style="display: none;">
        <table class="w-full">
            <thead>
                <tr class="bg-[var(--color-surface-soft)] border-b border-[var(--color-hairline-soft)]">
                    <th class="py-3 px-4 text-left type-caption-bold"><?php echo e(__('messages.date')); ?></th>
                    <th class="py-3 px-4 text-right type-caption-bold"><?php echo e(__('messages.total_revenue')); ?></th>
                    <th class="py-3 px-4 text-right type-caption-bold"><?php echo e(__('messages.service_revenue')); ?></th>
                    <th class="py-3 px-4 text-right type-caption-bold"><?php echo e(__('messages.total')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $allDates = array_unique(array_merge($salesTrend->pluck('date')->toArray(), $repairTrend->pluck('date')->toArray()));
                    rsort($allDates);
                ?>
                <?php $__empty_1 = true; $__currentLoopData = $allDates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $sVal = $salesTrend->where('date', $dt)->first()->total ?? 0;
                        $rVal = $repairTrend->where('date', $dt)->first()->total_revenue ?? 0;
                    ?>
                    <tr class="border-b border-[var(--color-hairline-soft)] hover:bg-[var(--color-surface-soft)] transition-colors">
                        <td class="py-3 px-4 type-body-sm"><?php echo e(\Carbon\Carbon::parse($dt)->format('d/m/Y H:i')); ?></td>
                        <td class="py-3 px-4 type-body-sm text-right text-[var(--color-primary)]">Rp <?php echo e(number_format($sVal, 0, ',', '.')); ?></td>
                        <td class="py-3 px-4 type-body-sm text-right text-[var(--color-success)]">Rp <?php echo e(number_format($rVal, 0, ',', '.')); ?></td>
                        <td class="py-3 px-4 type-body-sm text-right font-bold text-[var(--color-ink-deep)]">Rp <?php echo e(number_format($sVal + $rVal, 0, ',', '.')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="py-8 text-center type-body-sm text-[var(--color-slate)]"><?php echo e(__('messages.no_data')); ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    
    
    <div class="card-feature print:break-inside-avoid flex flex-col h-[450px] print:h-auto" x-data="{ view: 'table', chartType: 'doughnut', metric: 'revenue' }" x-init="$watch('chartType', val => updateTopProductsChart(val, metric)); $watch('metric', val => updateTopProductsChart(chartType, val)); $watch('view', val => { if(val === 'chart') setTimeout(() => updateTopProductsChart(chartType, metric), 50) })">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 p-4 md:p-5 border-b border-[var(--color-hairline-soft)]">
            <div class="flex items-center gap-3">
                <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]">
                    <svg class="w-5 h-5 inline-block -mt-1 mr-1 text-[var(--color-attention)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z" /></svg>
                    <?php echo e(__('messages.top_5_products')); ?>

                </h3>
            </div>
            <div class="flex flex-col sm:flex-row w-full md:w-auto gap-2 mt-2 md:mt-0">
                <div class="flex w-full sm:w-auto gap-2">
                    <button @click="view = 'chart'" :class="view === 'chart' ? 'pill-tab-active' : ''" class="flex-1 text-center justify-center pill-tab !text-xs"><?php echo e(__('Grafik')); ?></button>
                    <button @click="view = 'table'" :class="view === 'table' ? 'pill-tab-active' : ''" class="flex-1 text-center justify-center pill-tab !text-xs"><?php echo e(__('Tabel')); ?></button>
                </div>
            </div>
        </div>
        
        <div x-show="view === 'chart'" class="p-5 flex-1 flex flex-col" style="display: none;">
            <div class="flex flex-col sm:flex-row sm:justify-end gap-2 mb-4 w-full">
                <select x-model="chartType" class="input-field w-full sm:w-auto text-xs !py-1.5">
                    <option value="doughnut">Doughnut Chart</option>
                    <option value="pie">Pie Chart</option>
                    <option value="bar">Bar Chart</option>
                </select>
                <select x-model="metric" class="input-field w-full sm:w-auto text-xs !py-1.5">
                    <option value="revenue">Pendapatan</option>
                    <option value="qty">Kuantitas</option>
                </select>
            </div>
            <?php if(count($topProducts) > 0): ?>
            <div style="height:250px;" class="flex items-center justify-center relative w-full"><canvas id="topProductsChart"></canvas></div>
            <?php else: ?>
            <div style="height:250px;" class="text-center text-[var(--color-slate)] w-full flex items-center justify-center"><?php echo e(__('messages.no_data')); ?></div>
            <?php endif; ?>
        </div>

        <div x-show="view === 'table'" class="flex-1 flex flex-col overflow-hidden">
            <div class="p-0 overflow-y-auto print:overflow-visible flex-1">
                <table class="w-full">
                    <thead>
                        <tr class="bg-[var(--color-surface-soft)] border-b border-[var(--color-hairline-soft)] sticky top-0">
                            <th class="py-3 px-4 text-left type-caption-bold"><?php echo e(__('messages.product')); ?></th>
                            <th class="py-3 px-4 text-center type-caption-bold"><?php echo e(__('messages.quantity')); ?></th>
                            <th class="py-3 px-4 text-right type-caption-bold"><?php echo e(__('messages.total_revenue')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $topProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="border-b border-[var(--color-hairline-soft)] hover:bg-[var(--color-surface-soft)] transition-colors">
                            <td class="py-3 px-4 type-body-sm text-[var(--color-ink-deep)] font-medium"><?php echo e($item->name); ?></td>
                            <td class="py-3 px-4 type-body-sm text-center"><?php echo e($item->total_qty); ?></td>
                            <td class="py-3 px-4 type-body-sm text-right">Rp <?php echo e(number_format($item->total_revenue, 0, ',', '.')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="py-8 text-center type-body-sm text-[var(--color-slate)]"><?php echo e(__('messages.no_data')); ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-[var(--color-hairline-soft)] text-center bg-[var(--color-surface)] print:hidden">
                <a href="<?php echo e(route('reports.sales_revenue.products')); ?>" class="bg-[var(--color-ink)] text-white hover:bg-[var(--color-ink-deep)] transition-colors rounded-[var(--radius-full)] font-medium type-body-sm !py-2 !px-6 inline-flex items-center gap-2">
                    Lihat Keseluruhan Data
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </div>

    
    <div class="card-feature print:break-inside-avoid flex flex-col h-[450px] print:h-auto" x-data="{ view: 'table', chartType: 'doughnut', metric: 'revenue' }" x-init="$watch('chartType', val => updateTopCategoriesChart(val, metric)); $watch('metric', val => updateTopCategoriesChart(chartType, val)); $watch('view', val => { if(val === 'chart') setTimeout(() => updateTopCategoriesChart(chartType, metric), 50) })">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 p-4 md:p-5 border-b border-[var(--color-hairline-soft)]">
            <div class="flex items-center gap-3">
                <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]">
                    <svg class="w-5 h-5 inline-block -mt-1 mr-1 text-[var(--color-fb-blue)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
                    Top Kategori
                </h3>
            </div>
            <div class="flex flex-col sm:flex-row w-full md:w-auto gap-2 mt-2 md:mt-0">
                <div class="flex w-full sm:w-auto gap-2">
                    <button @click="view = 'chart'" :class="view === 'chart' ? 'pill-tab-active' : ''" class="flex-1 text-center justify-center pill-tab !text-xs"><?php echo e(__('Grafik')); ?></button>
                    <button @click="view = 'table'" :class="view === 'table' ? 'pill-tab-active' : ''" class="flex-1 text-center justify-center pill-tab !text-xs"><?php echo e(__('Tabel')); ?></button>
                </div>
            </div>
        </div>
        
        <div x-show="view === 'chart'" class="p-5 flex-1 flex flex-col" style="display: none;">
            <div class="flex flex-col sm:flex-row sm:justify-end gap-2 mb-4 w-full">
                <select x-model="chartType" class="input-field w-full sm:w-auto text-xs !py-1.5">
                    <option value="doughnut">Doughnut Chart</option>
                    <option value="pie">Pie Chart</option>
                    <option value="bar">Bar Chart</option>
                </select>
                <select x-model="metric" class="input-field w-full sm:w-auto text-xs !py-1.5">
                    <option value="revenue">Pendapatan</option>
                    <option value="qty">Terjual</option>
                </select>
            </div>
            <?php if(count($topCategories) > 0): ?>
            <div style="height:250px;" class="flex items-center justify-center relative w-full"><canvas id="topCategoriesChart"></canvas></div>
            <?php else: ?>
            <div style="height:250px;" class="text-center text-[var(--color-slate)] w-full flex items-center justify-center"><?php echo e(__('messages.no_data')); ?></div>
            <?php endif; ?>
        </div>

        <div x-show="view === 'table'" class="flex-1 flex flex-col overflow-hidden">
            <div class="p-0 overflow-y-auto print:overflow-visible flex-1">
                <table class="w-full">
                    <thead>
                        <tr class="bg-[var(--color-surface-soft)] border-b border-[var(--color-hairline-soft)] sticky top-0">
                            <th class="py-3 px-4 text-left type-caption-bold">Kategori</th>
                            <th class="py-3 px-4 text-center type-caption-bold">Terjual</th>
                            <th class="py-3 px-4 text-right type-caption-bold">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $topCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="border-b border-[var(--color-hairline-soft)] hover:bg-[var(--color-surface-soft)] transition-colors">
                            <td class="py-3 px-4 type-body-sm text-[var(--color-ink-deep)] font-medium"><?php echo e($item->name); ?></td>
                            <td class="py-3 px-4 type-body-sm text-center"><?php echo e($item->total_qty); ?></td>
                            <td class="py-3 px-4 type-body-sm text-right">Rp <?php echo e(number_format($item->total_revenue, 0, ',', '.')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="py-8 text-center type-body-sm text-[var(--color-slate)]"><?php echo e(__('messages.no_data')); ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-[var(--color-hairline-soft)] text-center bg-[var(--color-surface)] print:hidden">
                <a href="<?php echo e(route('reports.sales_revenue.categories')); ?>" class="bg-[var(--color-ink)] text-white hover:bg-[var(--color-ink-deep)] transition-colors rounded-[var(--radius-full)] font-medium type-body-sm !py-2 !px-6 inline-flex items-center gap-2">
                    Lihat Keseluruhan Data
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </div>
</div>


<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    
    
    <div class="card-feature print:break-inside-avoid flex flex-col h-[450px] print:h-auto">
        <div class="p-4 md:p-5 border-b border-[var(--color-hairline-soft)] flex justify-between items-center">
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]">
                <svg class="w-5 h-5 inline-block -mt-1 mr-1 text-[var(--color-success)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                Transaksi Terbaru
            </h3>
        </div>
        <div class="flex-1 flex flex-col overflow-hidden">
            <div class="p-0 overflow-y-auto print:overflow-visible flex-1">
                <table class="w-full">
                    <thead>
                        <tr class="bg-[var(--color-surface-soft)] border-b border-[var(--color-hairline-soft)] sticky top-0">
                            <th class="py-3 px-4 text-left type-caption-bold">Kode</th>
                            <th class="py-3 px-4 text-center type-caption-bold">Tanggal</th>
                            <th class="py-3 px-4 text-right type-caption-bold">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $recentTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="border-b border-[var(--color-hairline-soft)] hover:bg-[var(--color-surface-soft)] transition-colors">
                            <td class="py-3 px-4 type-body-sm text-[var(--color-ink-deep)] font-medium"><a href="<?php echo e(route('transactions.show', $trx->transaction_code)); ?>" class="text-[var(--color-primary)] hover:underline"><?php echo e($trx->transaction_code); ?></a></td>
                            <td class="py-3 px-4 type-body-sm text-center"><?php echo e(\Carbon\Carbon::parse($trx->transaction_date)->format('d/m/Y H:i')); ?></td>
                            <td class="py-3 px-4 type-body-sm text-right">Rp <?php echo e(number_format($trx->total, 0, ',', '.')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="py-8 text-center type-body-sm text-[var(--color-slate)]"><?php echo e(__('messages.no_data')); ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-[var(--color-hairline-soft)] text-center bg-[var(--color-surface)] print:hidden">
                <a href="<?php echo e(route('transactions.index')); ?>" class="bg-[var(--color-ink)] text-white hover:bg-[var(--color-ink-deep)] transition-colors rounded-[var(--radius-full)] font-medium type-body-sm !py-2 !px-6 inline-flex items-center gap-2">
                    Lihat Keseluruhan Data
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </div>

    
    <div class="card-feature print:break-inside-avoid flex flex-col h-[450px] print:h-auto">
        <div class="p-4 md:p-5 border-b border-[var(--color-hairline-soft)] flex justify-between items-center">
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]">
                <svg class="w-5 h-5 inline-block -mt-1 mr-1 text-[var(--color-critical)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Analisis Hutang (Belum Lunas)
            </h3>
            <div class="type-caption-bold text-[var(--color-critical)] bg-[var(--color-critical)]/10 px-2 py-1 rounded">
                Total: Rp <?php echo e(number_format($totalUnpaidDebtAmount, 0, ',', '.')); ?>

            </div>
        </div>
        <div class="flex-1 flex flex-col overflow-hidden">
            <div class="p-0 overflow-y-auto print:overflow-visible flex-1">
                <table class="w-full">
                    <thead>
                        <tr class="bg-[var(--color-surface-soft)] border-b border-[var(--color-hairline-soft)] sticky top-0">
                            <th class="py-3 px-4 text-left type-caption-bold">Keterangan</th>
                            <th class="py-3 px-4 text-center type-caption-bold">Jatuh Tempo</th>
                            <th class="py-3 px-4 text-right type-caption-bold">Sisa Hutang</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $unpaidDebts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="border-b border-[var(--color-hairline-soft)] hover:bg-[var(--color-surface-soft)] transition-colors">
                            <td class="py-3 px-4 type-body-sm text-[var(--color-ink-deep)] font-medium"><?php echo e($debt->debtor_name ?: 'Hutang'); ?></td>
                            <td class="py-3 px-4 type-body-sm text-center <?php echo e($debt->due_date && \Carbon\Carbon::parse($debt->due_date)->isPast() ? 'text-[var(--color-critical)] font-bold' : ''); ?>">
                                <?php echo e($debt->due_date ? \Carbon\Carbon::parse($debt->due_date)->format('d/m/Y') : '-'); ?>

                            </td>
                            <td class="py-3 px-4 type-body-sm text-right text-[var(--color-critical)]">Rp <?php echo e(number_format($debt->remaining_amount, 0, ',', '.')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="py-8 text-center type-body-sm text-[var(--color-slate)]">Tidak ada hutang belum lunas</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-[var(--color-hairline-soft)] text-center bg-[var(--color-surface)] print:hidden">
                <a href="<?php echo e(route('debts.index')); ?>" class="bg-[var(--color-ink)] text-white hover:bg-[var(--color-ink-deep)] transition-colors rounded-[var(--radius-full)] font-medium type-body-sm !py-2 !px-6 inline-flex items-center gap-2">
                    Lihat Keseluruhan Data
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </div>
</div>


<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    
    
    <div class="card-feature print:break-inside-avoid flex flex-col">
        <div class="p-4 md:p-5 border-b border-[var(--color-hairline-soft)] flex justify-between items-center">
            <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]">
                <svg class="w-5 h-5 inline-block -mt-1 mr-1 text-[var(--color-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                Data Pengadaan
            </h3>
        </div>
        <div class="p-5 flex flex-col gap-4 flex-1 justify-center">
            <div class="flex items-center justify-between p-3 bg-[var(--color-surface-soft)] rounded-lg">
                <span class="type-body-sm font-medium">Total Nilai Pengadaan</span>
                <span class="type-subtitle-md text-[var(--color-primary)]">Rp <?php echo e(number_format($totalProcurementValue, 0, ',', '.')); ?></span>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-3 bg-[var(--color-surface-soft)] rounded-lg text-center">
                    <span class="type-display text-[var(--color-success)]"><?php echo e($receivedProcurements); ?></span>
                    <p class="type-caption text-[var(--color-slate)] mt-1">Diterima</p>
                </div>
                <div class="p-3 bg-[var(--color-surface-soft)] rounded-lg text-center">
                    <span class="type-display text-[var(--color-attention)]"><?php echo e($pendingProcurements); ?></span>
                    <p class="type-caption text-[var(--color-slate)] mt-1">Pending/Proses</p>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card-feature print:break-inside-avoid flex flex-col h-[450px] print:h-auto" x-data="{ view: 'table', chartType: 'doughnut', metric: 'revenue' }" x-init="$watch('chartType', val => updateTopSparepartsChart(val, metric)); $watch('metric', val => updateTopSparepartsChart(chartType, val)); $watch('view', val => { if(val === 'chart') setTimeout(() => updateTopSparepartsChart(chartType, metric), 50) })">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 p-4 md:p-5 border-b border-[var(--color-hairline-soft)]">
            <div class="flex items-center gap-3">
                <h3 class="type-subtitle-lg text-[var(--color-ink-deep)]">
                    <svg class="w-5 h-5 inline-block -mt-1 mr-1 text-[var(--color-stone)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Top Sparepart Digunakan
                </h3>
            </div>
            <div class="flex flex-col sm:flex-row w-full md:w-auto gap-2 mt-2 md:mt-0">
                <div class="flex w-full sm:w-auto gap-2">
                    <button @click="view = 'chart'" :class="view === 'chart' ? 'pill-tab-active' : ''" class="flex-1 text-center justify-center pill-tab !text-xs"><?php echo e(__('Grafik')); ?></button>
                    <button @click="view = 'table'" :class="view === 'table' ? 'pill-tab-active' : ''" class="flex-1 text-center justify-center pill-tab !text-xs"><?php echo e(__('Tabel')); ?></button>
                </div>
            </div>
        </div>
        
        <div x-show="view === 'chart'" class="p-5 flex-1 flex flex-col" style="display: none;">
            <div class="flex flex-col sm:flex-row sm:justify-end gap-2 mb-4 w-full">
                <select x-model="chartType" class="input-field w-full sm:w-auto text-xs !py-1.5">
                    <option value="doughnut">Doughnut Chart</option>
                    <option value="pie">Pie Chart</option>
                    <option value="bar">Bar Chart</option>
                </select>
                <select x-model="metric" class="input-field w-full sm:w-auto text-xs !py-1.5">
                    <option value="revenue">Nilai/Biaya</option>
                    <option value="qty">Jumlah</option>
                </select>
            </div>
            <?php if(count($topSpareparts) > 0): ?>
            <div style="height:250px;" class="flex items-center justify-center relative w-full"><canvas id="topSparepartsChart"></canvas></div>
            <?php else: ?>
            <div style="height:250px;" class="text-center text-[var(--color-slate)] w-full flex items-center justify-center"><?php echo e(__('messages.no_data')); ?></div>
            <?php endif; ?>
        </div>

        <div x-show="view === 'table'" class="flex-1 flex flex-col overflow-hidden">
            <div class="p-0 overflow-y-auto print:overflow-visible flex-1">
                <table class="w-full">
                    <thead>
                        <tr class="bg-[var(--color-surface-soft)] border-b border-[var(--color-hairline-soft)] sticky top-0">
                            <th class="py-3 px-4 text-left type-caption-bold">Sparepart</th>
                            <th class="py-3 px-4 text-center type-caption-bold">Jumlah</th>
                            <th class="py-3 px-4 text-right type-caption-bold">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $topSpareparts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="border-b border-[var(--color-hairline-soft)] hover:bg-[var(--color-surface-soft)] transition-colors">
                            <td class="py-3 px-4 type-body-sm text-[var(--color-ink-deep)] font-medium"><?php echo e($part->name); ?></td>
                            <td class="py-3 px-4 type-body-sm text-center"><?php echo e($part->total_qty); ?></td>
                            <td class="py-3 px-4 type-body-sm text-right">Rp <?php echo e(number_format($part->total_cost, 0, ',', '.')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="py-8 text-center type-body-sm text-[var(--color-slate)]"><?php echo e(__('messages.no_data')); ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-[var(--color-hairline-soft)] text-center bg-[var(--color-surface)] print:hidden">
                <a href="<?php echo e(route('service-repairs.index')); ?>" class="bg-[var(--color-ink)] text-white hover:bg-[var(--color-ink-deep)] transition-colors rounded-[var(--radius-full)] font-medium type-body-sm !py-2 !px-6 inline-flex items-center gap-2">
                    Lihat Keseluruhan Data
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        function setDateRange(days) {
            const startInput = document.getElementById('dash-start-date').value;
            const start = startInput ? new Date(startInput) : new Date();
            const end = new Date(start);
            end.setDate(start.getDate() + days);
            
            document.getElementById('dash-start-date').value = start.toISOString().split('T')[0];
            document.getElementById('dash-end-date').value = end.toISOString().split('T')[0];
            document.getElementById('dashboard-filter-form').submit();
        }

        window.rawChartData = {
            topProducts: <?php echo json_encode($topProducts); ?>,
            topCategories: <?php echo json_encode($topCategories); ?>,
            topSpareparts: <?php echo json_encode($topSpareparts); ?>

        };
        window.appCharts = window.appCharts || {};

        const combinedSales = <?php echo json_encode($salesTrend); ?>;
        const combinedRepairs = <?php echo json_encode($repairTrend); ?>;

        window.updateTrendChart = function(type) {
            const ctx = document.getElementById('combinedTrendChart');
            if (!ctx) return;
            if (window.appCharts['combinedTrendChart']) {
                window.appCharts['combinedTrendChart'].destroy();
            }

            const allDates = [...new Set([...combinedSales.map(i => i.date), ...combinedRepairs.map(i => i.date)])].sort();
            
            const salesData = allDates.map(d => {
                const found = combinedSales.find(i => i.date === d);
                return found ? found.total : 0;
            });
            const repairData = allDates.map(d => {
                const found = combinedRepairs.find(i => i.date === d);
                return found ? found.total_revenue : 0;
            });

            window.appCharts['combinedTrendChart'] = new Chart(ctx, {
                type: type || 'line',
                data: {
                    labels: allDates,
                    datasets: [
                        { label: 'Penjualan', data: salesData, borderColor: '#0064E0', backgroundColor: 'rgba(0,100,224,0.1)', fill: type==='line', tension: 0.4, borderWidth: 2 },
                        { label: 'Perbaikan', data: repairData, borderColor: '#31A24C', backgroundColor: 'rgba(49,162,76,0.1)', fill: type==='line', tension: 0.4, borderWidth: 2 }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw)
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                callback: function(val, index) {
                                    const dateObj = new Date(this.getLabelForValue(val));
                                    return dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) + ' ' + dateObj.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(val) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(val);
                                }
                            }
                        }
                    }
                }
            });
        };

        const WIDGET_COLORS = ['#0064E0', '#31A24C', '#F7B928', '#E41E3F', '#1877F2', '#20B2AA', '#9370DB', '#FF7F50'];

        function createWidgetChart(ctxId, dataObj, type, metric, isRevenue) {
            const ctx = document.getElementById(ctxId);
            if (!ctx || !dataObj.length) return;
            if (window.appCharts[ctxId]) window.appCharts[ctxId].destroy();
            
            const isRadial = type === 'doughnut' || type === 'pie';
            
            const ds = {
                labels: dataObj.map(p => p.name),
                datasets: [{
                    label: isRevenue ? 'Pendapatan/Nilai' : 'Kuantitas',
                    data: dataObj.map(p => parseFloat(isRevenue ? (p.total_revenue || p.total_cost) : p.total_qty)),
                    backgroundColor: WIDGET_COLORS,
                    borderRadius: isRadial ? 0 : 6,
                    borderWidth: isRadial ? 2 : 0,
                    borderColor: isRadial ? '#fff' : 'transparent',
                }]
            };
            
            const options = { 
                indexAxis: isRadial ? 'x' : 'y', 
                responsive: true, 
                maintainAspectRatio: false,
                cutout: type === 'doughnut' ? '60%' : 0,
                plugins: { 
                    legend: { display: isRadial, position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12 } },
                    tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': ' + (isRevenue ? 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw) : ctx.raw) } }
                },
                scales: isRadial ? { x: { display: false }, y: { display: false } } : {
                    x: { beginAtZero: true, grid: { color: 'rgba(148,163,184,0.12)' }, ticks: { color: '#94A3B8', font: { size: 11 } } },
                    y: { grid: { display: false }, ticks: { color: '#475569', font: { size: 11 } } }
                }
            };
            
            window.appCharts[ctxId] = new Chart(ctx, { type: type, data: ds, options: options });
        }

        window.updateTopProductsChart = function(type, metric) {
            createWidgetChart('topProductsChart', window.rawChartData.topProducts, type, metric, metric === 'revenue');
        };
        window.updateTopCategoriesChart = function(type, metric) {
            createWidgetChart('topCategoriesChart', window.rawChartData.topCategories, type, metric, metric === 'revenue');
        };
        window.updateTopSparepartsChart = function(type, metric) {
            createWidgetChart('topSparepartsChart', window.rawChartData.topSpareparts, type, metric, metric === 'revenue');
        };
        
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof updateTrendChart === 'function') {
                setTimeout(() => updateTrendChart('line'), 100);
            }
        });
    </script>
<style>
@media print {
    @page { margin: 5mm; } /* Let user/browser decide orientation */
    body, html, .h-full, .min-h-screen, #app { 
        background: white !important; color: black !important; font-size: 9px !important; 
        height: auto !important; min-height: auto !important; overflow: visible !important; 
    }
    
    /* Hide Layout */
    aside, header, #dashboard-filter-form, .print-hidden, .pill-tab, select, button { display: none !important; }
    main { padding: 0 !important; margin: 0 !important; background: white !important; width: 100% !important; max-width: 100% !important; height: auto !important; overflow: visible !important; }
    
    /* Respect UI State */
    [x-show="view === 'chart'"] { page-break-inside: avoid; }
    [x-show="view === 'table'"] { page-break-inside: auto; }
    
    /* KPI Grid - compact */
    .grid.grid-cols-1.sm\:grid-cols-2.lg\:grid-cols-5 {
        display: flex !important; flex-wrap: nowrap !important; gap: 5px !important; margin-bottom: 5px !important;
    }
    .grid.grid-cols-1.sm\:grid-cols-2.lg\:grid-cols-5 > div {
        flex: 1 1 19% !important; border: 1px solid #000 !important; padding: 5px !important; text-align: center;
    }
    .grid.grid-cols-1.sm\:grid-cols-2.lg\:grid-cols-5 p.type-caption-bold { font-size: 8px !important; margin-bottom: 2px !important; white-space: nowrap; font-weight: bold; }
    .grid.grid-cols-1.sm\:grid-cols-2.lg\:grid-cols-5 p.type-heading-sm { font-size: 12px !important; margin-top: 0 !important; font-weight: bold; }
    .grid.grid-cols-1.sm\:grid-cols-2.lg\:grid-cols-5 .h-1 { display: none !important; }
    
    /* Cards - ultra thin */
    .card-feature { box-shadow: none !important; border: 1px solid #000 !important; page-break-inside: auto; break-inside: auto; margin-bottom: 5px !important; padding: 5px !important; }
    .card-feature .border-b { padding: 0 0 2px 0 !important; margin-bottom: 2px !important; border-bottom: 1px solid #000 !important; }
    .card-feature h3 { font-size: 11px !important; font-weight: bold; color: #000 !important; }
    
    /* Chart empty states */
    .relative > div[style*="height"], div[style*="280px"], div[style*="250px"] { height: auto !important; min-height: 0 !important; }
    
    /* Hide the filter text block in table view */
    [x-show="view === 'table'"] > div.flex { display: none !important; }
    
    /* Tables sizing - dense */
    table { width: 100% !important; border-collapse: collapse !important; margin-top: 2px !important; page-break-inside: auto; }
    th, td { border: 1px solid #000 !important; padding: 2px 4px !important; font-size: 8px !important; color: #000 !important; }
    th { background-color: #f3f4f6 !important; font-weight: bold !important; }
    tr { page-break-inside: avoid; page-break-after: auto; }
    thead { display: table-header-group; }
    tfoot { display: table-footer-group; }
    .overflow-x-auto { overflow: visible !important; }
    
    /* Badges */
    .badge, .badge-success, .badge-attention, .badge-critical { background: transparent !important; color: #000 !important; border: none !important; padding: 0 !important; font-weight: bold !important; }
    
    /* Global fixes */
    * { 
        -webkit-print-color-adjust: exact !important; 
        print-color-adjust: exact !important; 
        border-radius: 0 !important;
    }
}

/* Specific to Portrait Print */
@media print and (orientation: portrait) {
    .grid.grid-cols-1.lg\:grid-cols-2 {
        display: block !important; 
    }
    .grid.grid-cols-1.lg\:grid-cols-2 > div {
        width: 100% !important; margin-bottom: 5px !important;
    }
    canvas { max-height: 120px !important; max-width: 100% !important; margin: 0 auto !important; }
}

/* Specific to Landscape Print */
@media print and (orientation: landscape) {
    .grid.grid-cols-1.lg\:grid-cols-2 {
        display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; gap: 5px !important; margin-bottom: 5px !important;
    }
    .grid.grid-cols-1.lg\:grid-cols-2 > div {
        flex: 1 1 48% !important; width: 48% !important; margin-bottom: 0 !important;
    }
    canvas { max-height: 80px !important; max-width: 100% !important; margin: 0 auto !important; }
}
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Office\Developer\Projects\Laravel\BisnisKu\resources\views/dashboard/owner.blade.php ENDPATH**/ ?>