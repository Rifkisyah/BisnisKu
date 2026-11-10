<?php $__env->startSection('page-title', __('messages.procurements')); ?>
<?php $__env->startSection('content'); ?>
<div x-data="{ showFilter: false }" class="mb-6 bg-[var(--color-canvas)] rounded-[var(--radius-xl)] p-5 border border-[var(--color-hairline-soft)] print:hidden print-hidden">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 pb-4 border-b border-[var(--color-hairline-soft)]">
        <div class="flex items-center justify-between w-full sm:w-auto">
            <h2 class="type-subtitle-lg text-[var(--color-ink-deep)]">Aksi & Cari</h2>
        </div>

        <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-2 w-full sm:w-auto justify-end">
            <div class="grid grid-cols-2 gap-2 w-full sm:w-auto sm:flex">
                <a href="<?php echo e(request()->fullUrlWithQuery(['export' => 'pdf'])); ?>" class="btn-ghost !py-2 !px-3 flex items-center justify-center text-xs sm:text-sm w-full sm:w-auto" title="Download PDF">
                    <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> <span>PDF</span>
                </a>
                <a href="<?php echo e(request()->fullUrlWithQuery(['print' => '1'])); ?>" class="btn-ghost !py-2 !px-3 flex items-center justify-center text-xs sm:text-sm w-full sm:w-auto" title="Print">
                    <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg> <span>Print</span>
                </a>
            </div>
            <a href="<?php echo e(route('product-purchases.create')); ?>" class="btn-primary !py-2.5 !px-4 whitespace-nowrap text-center text-xs sm:text-sm w-full sm:w-auto justify-center flex items-center">
                <svg class="h-4 w-4 mr-1.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span class="inline-block"><?php echo e(__('messages.add')); ?></span>
            </a>
        </div>
    </div>

    <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-3 items-stretch sm:items-end w-full">
        <div class="w-full sm:flex-1">
            <label class="block type-caption-bold text-[var(--color-slate)] mb-1">Pencarian</label>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari Kode..." class="input-field w-full">
        </div>
        <div class="grid grid-cols-2 gap-3 w-full sm:w-auto sm:flex sm:flex-wrap">
            <div class="w-full sm:w-48">
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1">Status</label>
                <select name="status" class="input-field w-full">
                    <option value="">Semua Status</option>
                    <option value="draft" <?php if(request('status') === 'draft'): echo 'selected'; endif; ?>>Draft</option>
                    <option value="ordered" <?php if(request('status') === 'ordered'): echo 'selected'; endif; ?>>Ordered</option>
                    <option value="partial_received" <?php if(request('status') === 'partial_received'): echo 'selected'; endif; ?>>Partial Received</option>
                    <option value="received" <?php if(request('status') === 'received'): echo 'selected'; endif; ?>>Received</option>
                    <option value="cancelled" <?php if(in_array(request('status'), ['cancelled', 'canceled'])): echo 'selected'; endif; ?>>Cancelled</option>
                </select>
            </div>
            <div class="w-full sm:w-48">
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1">Urutkan</label>
                <select name="sort" class="input-field w-full" onchange="this.form.submit()">
                    <option value="" <?php echo e(!request('sort') || request('sort') == 'created_at_desc' ? 'selected' : ''); ?>>Dibuat (Terbaru)</option>
                    <option value="created_at_asc" <?php echo e(request('sort') == 'created_at_asc' ? 'selected' : ''); ?>>Dibuat (Terlama)</option>
                    <option value="updated_at_desc" <?php echo e(request('sort') == 'updated_at_desc' ? 'selected' : ''); ?>>Diupdate (Terbaru)</option>
                    <option value="updated_at_asc" <?php echo e(request('sort') == 'updated_at_asc' ? 'selected' : ''); ?>>Diupdate (Terlama)</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3 w-full sm:w-auto sm:flex sm:flex-wrap">
            <div class="w-full sm:w-48">
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1"><?php echo e(__('messages.start_date')); ?></label>
                <input type="date" name="start_date" value="<?php echo e($startDate ?? ''); ?>" class="input-field w-full">
            </div>
            <div class="w-full sm:w-48">
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1"><?php echo e(__('messages.end_date')); ?></label>
                <input type="date" name="end_date" value="<?php echo e($endDate ?? ''); ?>" class="input-field w-full">
            </div>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <button type="submit" class="btn-primary !py-2.5 !px-4 flex-1 sm:flex-initial text-center"><?php echo e(__('messages.filter')); ?></button>
            <?php if(request()->anyFilled(['search', 'status', 'start_date', 'end_date']) && (request('start_date') !== now()->subDays(90)->toDateString() || request('end_date') !== now()->toDateString())): ?>
                <a href="<?php echo e(route('product-purchases.index')); ?>" class="btn-ghost !py-2.5 !px-3 text-center" title="<?php echo e(__('messages.reset')); ?>">
                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full">
    <thead><tr class="border-b border-[var(--color-hairline-soft)] bg-[var(--color-surface-soft)]">
        <th class="px-5 py-3 text-left table-header"><?php echo e(__('messages.code')); ?></th><th class="px-5 py-3 text-left table-header">Sumber & Supplier</th>
        <th class="px-5 py-3 text-left table-header"><?php echo e(__('messages.date')); ?></th><th class="px-5 py-3 text-left table-header"><?php echo e(__('messages.status')); ?></th>
        <th class="px-5 py-3 text-left table-header"><?php echo e(__('messages.total')); ?></th><th class="px-5 py-3 text-left table-header print:hidden print-hidden action-col"><?php echo e(__('messages.actions')); ?></th>
    </tr></thead><tbody>
    <?php $sc=['draft'=>'badge-neutral','ordered'=>'badge-info','partial_received'=>'badge-warning','received'=>'badge-success','cancelled'=>'badge-critical','canceled'=>'badge-critical']; ?>
    <?php $__empty_1 = true; $__currentLoopData = $productPurchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <tr onclick="window.location='<?php echo e(route('product-purchases.show', $p)); ?>'" class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] transition-colors duration-150 cursor-pointer">
        <td class="px-5 py-3 type-caption-bold text-[var(--color-primary)]"><?php echo e($p->product_purchase_code); ?></td>
        <td class="px-5 py-3 type-body-sm text-[var(--color-ink)]">
            <div class="font-medium"><?php echo e($p->getSummarySources()); ?></div>
            <div class="text-xs text-[var(--color-slate)] mt-0.5">Supplier: <?php echo e($p->getSummarySuppliers()); ?></div>
        </td>
        <td class="px-5 py-3 type-body-sm text-[var(--color-slate)]">
            Tgl: <?php echo e($p->purchase_date ? $p->purchase_date->format('d/m/Y') : '-'); ?><br>
            <?php if($p->estimated_arrival_date): ?><span class="text-xs text-indigo-500">Est. Tiba: <?php echo e($p->estimated_arrival_date->format('d/m/Y')); ?></span><?php endif; ?>
        </td>
        <td class="px-5 py-3"><span class="badge <?php echo e($sc[$p->status] ?? 'badge-neutral'); ?>"><?php echo e(__('messages.'.$p->status)); ?></span></td>
        <td class="px-5 py-3 type-body-sm font-bold text-[var(--color-ink)]">Rp <?php echo e(number_format($p->total, 0, ',', '.')); ?></td>
        <td class="px-5 py-3 print:hidden print-hidden action-col"><div class="flex items-center gap-1">
                <a href="<?php echo e(route('product-purchases.show', $p)); ?>" class="btn-ghost !py-1.5 !px-3 !text-xs"><?php echo e(__('messages.detail')); ?></a>
                <?php if($p->status === 'draft' && in_array(auth()->user()->role->name, ['owner', 'gudang'])): ?>
                <a href="<?php echo e(route('product-purchases.edit', $p)); ?>" class="btn-ghost !py-1.5 !px-3 !text-xs !text-blue-600 !border-blue-600/20">Ubah</a>
                <?php endif; ?>
                <?php if(auth()->user()->role->name === 'owner' || ($p->status !== 'cancelled' && auth()->user()->role->name === 'gudang')): ?>
                <button @click.stop="$dispatch('open-delete-modal', { url: '<?php echo e(route('product-purchases.destroy', $p)); ?>' })" class="btn-ghost !py-1.5 !px-3 !text-xs !text-[var(--color-critical)] !border-[var(--color-critical)]/20"><?php echo e(__('messages.delete')); ?></button>
                <?php endif; ?>
            </div></td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="6" class="px-5 py-12 text-center type-body-md text-[var(--color-stone)]"><?php echo e(__('messages.no_data')); ?></td></tr><?php endif; ?>
    </tbody></table></div>
    <?php if(method_exists($productPurchases, 'links')): ?>
    <div class="border-t border-[var(--color-hairline-soft)] px-5 py-3 print:hidden print-hidden"><?php echo e($productPurchases->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Office\Developer\Projects\Laravel\BisnisKu\resources\views/product-purchases/index.blade.php ENDPATH**/ ?>