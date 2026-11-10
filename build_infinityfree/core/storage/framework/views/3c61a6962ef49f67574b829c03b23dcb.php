
<?php $__env->startSection('page-title', 'Seluruh Transaksi Penjualan'); ?>
<?php $__env->startSection('content'); ?>

<div class="mx-auto max-w-6xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="<?php echo e(url()->previous()); ?>" class="btn-ghost"><svg class="w-4 h-4 inline-block -mt-0.5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg><?php echo e(__('messages.back') ?? 'Kembali'); ?></a>
    </div>

    <div class="card-feature mb-6">
        <div class="p-5 border-b border-[var(--color-hairline-soft)] flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="type-subtitle-lg text-[var(--color-ink-deep)]">Seluruh Transaksi Penjualan</h2>
                <p class="type-caption text-[var(--color-slate)] mt-1">Periode <?php echo e(\Carbon\Carbon::parse($startDate)->format('d M Y')); ?> – <?php echo e(\Carbon\Carbon::parse($endDate)->format('d M Y')); ?></p>
            </div>
            <form method="GET" action="<?php echo e(route('reports.sales_revenue.transactions')); ?>" class="flex flex-col md:flex-row md:items-center gap-3 w-full">
                <input type="date" name="start_date" value="<?php echo e($startDate); ?>" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                <span class="text-[var(--color-slate)] text-xs">sampai</span>
                <input type="date" name="end_date" value="<?php echo e($endDate); ?>" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                
                <select name="payment_method" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                    <option value="">Semua Metode</option>
                    <option value="cash" <?php echo e(request('payment_method') == 'cash' ? 'selected' : ''); ?>>Tunai</option>
                    <option value="qris" <?php echo e(request('payment_method') == 'qris' ? 'selected' : ''); ?>>QRIS</option>
                    <option value="ewallet" <?php echo e(request('payment_method') == 'ewallet' ? 'selected' : ''); ?>>E-Wallet</option>
                </select>

                <select name="cashier_id" class="input-field w-full md:w-auto text-xs !py-1.5" onchange="this.form.submit()">
                    <option value="">Semua Kasir</option>
                    <?php $__currentLoopData = $cashiers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c->id); ?>" <?php echo e(request('cashier_id') == $c->id ? 'selected' : ''); ?>><?php echo e($c->username); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="bg-[var(--color-surface-soft)]">
                    <th class="px-5 py-3 text-left table-header">Kode Transaksi</th>
                    <th class="px-5 py-3 text-left table-header">Tanggal</th>
                    <th class="px-5 py-3 text-left table-header">Kasir</th>
                    <th class="px-5 py-3 text-left table-header">Metode Pembayaran</th>
                    <th class="px-5 py-3 text-right table-header">Total</th>
                </tr></thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] transition-colors cursor-pointer" onclick="window.location='<?php echo e(route('transactions.show', $t)); ?>'">
                    <td class="px-5 py-3 type-caption-bold text-[var(--color-primary)]">
                        <a href="<?php echo e(route('transactions.show', $t)); ?>" class="hover:underline"><?php echo e($t->transaction_code); ?></a>
                    </td>
                    <td class="px-5 py-3 type-body-sm text-[var(--color-slate)]"><?php echo e($t->transaction_date->format('d M Y H:i')); ?></td>
                    <td class="px-5 py-3 type-body-sm text-[var(--color-ink)]"><?php echo e($t->cashier->username ?? '-'); ?></td>
                    <td class="px-5 py-3"><span class="badge badge-neutral uppercase"><?php echo e($t->payment_method); ?></span></td>
                    <td class="px-5 py-3 type-body-sm-bold text-right text-[var(--color-ink-deep)]">Rp <?php echo e(number_format($t->total, 0, ',', '.')); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="px-5 py-10 text-center type-body-sm text-[var(--color-slate)]">Tidak ada transaksi ditemukan.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($transactions->hasPages()): ?>
        <div class="p-5 border-t border-[var(--color-hairline-soft)]">
            <?php echo e($transactions->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Office\Developer\Projects\Laravel\BisnisKu\resources\views/reports/sales-revenue/details/transactions.blade.php ENDPATH**/ ?>