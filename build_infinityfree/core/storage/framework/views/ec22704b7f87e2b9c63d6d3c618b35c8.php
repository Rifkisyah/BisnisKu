
<?php $__env->startSection('page-title', 'Detail Tren Perbaikan'); ?>
<?php $__env->startSection('content'); ?>

<div class="mx-auto max-w-6xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="<?php echo e(route('reports.service_analysis', request()->except('page'))); ?>" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg><?php echo e(__('Kembali')); ?></a>
    </div>

    <div class="card-feature mb-6">
        <div class="p-5 border-b border-[var(--color-hairline-soft)] flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="type-subtitle-lg text-[var(--color-ink-deep)]">Seluruh Data Tren Perbaikan</h2>
                <p class="type-caption text-[var(--color-slate)] mt-1">Periode <?php echo e(\Carbon\Carbon::parse($startDate)->format('d M Y')); ?> – <?php echo e(\Carbon\Carbon::parse($endDate)->format('d M Y')); ?></p>
            </div>
            <form method="GET" action="<?php echo e(route('reports.service_analysis.trends')); ?>" class="flex flex-col md:flex-row md:items-center gap-3 w-full">
                <input type="date" name="start_date" value="<?php echo e(\Carbon\Carbon::parse($startDate)->format('Y-m-d')); ?>" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                <span class="text-[var(--color-slate)] text-xs">sampai</span>
                <input type="date" name="end_date" value="<?php echo e(\Carbon\Carbon::parse($endDate)->format('Y-m-d')); ?>" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                
                <select name="status" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                    <option value="in_progress" <?php echo e(request('status') == 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
                    <option value="waiting_part" <?php echo e(request('status') == 'waiting_part' ? 'selected' : ''); ?>>Waiting Part</option>
                    <option value="done" <?php echo e(request('status') == 'done' ? 'selected' : ''); ?>>Selesai</option>
                    <option value="picked_up" <?php echo e(request('status') == 'picked_up' ? 'selected' : ''); ?>>Diambil</option>
                    <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>Dibatalkan</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="bg-[var(--color-surface-soft)]">
                    <th class="px-5 py-3 text-left table-header">#</th>
                    <th class="px-5 py-3 text-left table-header">Kode Perbaikan</th>
                    <th class="px-5 py-3 text-left table-header">Tanggal Servis</th>
                    <th class="px-5 py-3 text-left table-header">Teknisi</th>
                    <th class="px-5 py-3 text-center table-header">Status</th>
                    <th class="px-5 py-3 text-right table-header">Revenue</th>
                </tr></thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $repairs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] transition-colors">
                    <td class="px-5 py-3 type-body-sm text-[var(--color-slate)]"><?php echo e($repairs->firstItem() + $idx); ?></td>
                    <td class="px-5 py-3 type-caption-bold text-[var(--color-primary)]"><?php echo e($r->repair_code); ?></td>
                    <td class="px-5 py-3 type-body-sm text-[var(--color-ink)]"><?php echo e(\Carbon\Carbon::parse($r->start_date)->format('d M Y H:i')); ?></td>
                    <td class="px-5 py-3 type-body-sm text-[var(--color-ink)]"><?php echo e($r->technician ? $r->technician->name : '-'); ?></td>
                    <td class="px-5 py-3 text-center">
                        <?php
                            $badgeColor = match($r->status) {
                                'pending' => 'badge-neutral',
                                'in_progress', 'waiting_part' => 'badge-attention',
                                'done', 'picked_up' => 'badge-success',
                                'cancelled' => 'badge-critical',
                                default => 'badge-neutral',
                            };
                            $statusText = ucwords(str_replace('_', ' ', $r->status));
                        ?>
                        <span class="badge <?php echo e($badgeColor); ?>"><?php echo e($statusText); ?></span>
                    </td>
                    <td class="px-5 py-3 type-body-sm-bold text-right text-[var(--color-ink-deep)]">Rp <?php echo e(number_format($r->total_cost, 0, ',', '.')); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="px-5 py-10 text-center type-body-sm text-[var(--color-slate)]">Tidak ada data servis pada periode ini.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($repairs->hasPages()): ?>
        <div class="p-5 border-t border-[var(--color-hairline-soft)]">
            <?php echo e($repairs->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Office\Developer\Projects\Laravel\BisnisKu\resources\views/reports/service-analysis/details/trends.blade.php ENDPATH**/ ?>