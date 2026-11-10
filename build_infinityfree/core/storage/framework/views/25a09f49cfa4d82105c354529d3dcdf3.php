<?php $__env->startSection('page-title', __('messages.employees')); ?>
<?php $__env->startSection('content'); ?>
<div x-data="{ viewMode: localStorage.getItem('employeeView') || (window.innerWidth < 768 ? 'grid' : 'table'), showFilter: false }" x-init="$watch('viewMode', val => localStorage.setItem('employeeView', val)); window.addEventListener('resize', () => { if(window.innerWidth < 768 && viewMode !== 'grid') viewMode = 'grid'; })">
    <div class="mb-6 bg-[var(--color-canvas)] rounded-[var(--radius-xl)] p-5 border border-[var(--color-hairline-soft)] print:hidden print-hidden">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 pb-4 border-b border-[var(--color-hairline-soft)]">
            <div class="flex items-center justify-between w-full sm:w-auto">
                <h2 class="type-subtitle-lg text-[var(--color-ink-deep)]">Aksi & Cari</h2>
                <div class="flex sm:hidden bg-[var(--color-surface-soft)] rounded-lg p-1 border border-[var(--color-hairline-soft)]">
                    <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-[var(--color-canvas)] shadow-sm text-[var(--color-ink-deep)]' : 'text-[var(--color-slate)]'" class="px-2 py-1 rounded-md transition-all" title="Table View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                    <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-[var(--color-canvas)] shadow-sm text-[var(--color-ink-deep)]' : 'text-[var(--color-slate)]'" class="px-2 py-1 rounded-md transition-all" title="Grid View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </button>
                </div>
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
                <div class="hidden sm:flex bg-[var(--color-surface-soft)] rounded-lg p-1 border border-[var(--color-hairline-soft)]">
                    <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-[var(--color-canvas)] shadow-sm text-[var(--color-ink-deep)]' : 'text-[var(--color-slate)]'" class="px-2 py-1.5 rounded-md transition-all" title="Table View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                    <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-[var(--color-canvas)] shadow-sm text-[var(--color-ink-deep)]' : 'text-[var(--color-slate)]'" class="px-2 py-1.5 rounded-md transition-all" title="Grid View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </button>
                </div>
                <a href="<?php echo e(route('employees.create')); ?>" class="btn-primary !py-2.5 !px-4 whitespace-nowrap text-center text-xs sm:text-sm w-full sm:w-auto justify-center flex items-center">
                    <svg class="h-4 w-4 mr-1.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span class="inline-block"><?php echo e(__('messages.add')); ?></span>
                </a>
            </div>
        </div>

        <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-3 items-stretch sm:items-end w-full">
            <div class="w-full sm:flex-1">
                <label class="block type-caption-bold text-[var(--color-slate)] mb-1">Pencarian</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari Nama atau Email..." class="input-field w-full">
            </div>
            <div class="grid grid-cols-2 gap-3 w-full sm:w-auto sm:flex sm:flex-wrap">
                <div class="w-full sm:w-48">
                    <label class="block type-caption-bold text-[var(--color-slate)] mb-1">Role</label>
                    <select name="role" class="input-field w-full">
                        <option value="">Semua Role</option>
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($r->name); ?>" <?php if(request('role') === $r->name): echo 'selected'; endif; ?>><?php echo e(strtoupper($r->display_name)); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="w-full sm:w-48">
                    <label class="block type-caption-bold text-[var(--color-slate)] mb-1">Urutkan</label>
                    <select name="sort" class="input-field w-full" onchange="this.form.submit()">
                        <option value="" <?php echo e(!request('sort') || request('sort') == 'created_at_desc' ? 'selected' : ''); ?>>Dibuat (Terbaru)</option>
                        <option value="created_at_asc" <?php echo e(request('sort') == 'created_at_asc' ? 'selected' : ''); ?>>Dibuat (Terlama)</option>
                        <option value="updated_at_desc" <?php echo e(request('sort') == 'updated_at_desc' ? 'selected' : ''); ?>>Diupdate (Terbaru)</option>
                        <option value="updated_at_asc" <?php echo e(request('sort') == 'updated_at_asc' ? 'selected' : ''); ?>>Diupdate (Terlama)</option>
                        <option value="name_asc" <?php echo e(request('sort') == 'name_asc' ? 'selected' : ''); ?>>Abjad (A-Z)</option>
                        <option value="name_desc" <?php echo e(request('sort') == 'name_desc' ? 'selected' : ''); ?>>Abjad (Z-A)</option>
                    </select>
                </div>
            </div>
            <div class="w-full sm:w-auto">
                <button type="submit" class="btn-primary w-full sm:w-auto px-6 !py-2.5"><?php echo e(__('messages.search')); ?></button>
            </div>
        </form>
    </div>
    
    
    <div x-show="viewMode === 'table'" class="card overflow-hidden">
        <div class="overflow-x-auto"><table class="w-full">
        <thead><tr class="border-b border-[var(--color-hairline-soft)] bg-[var(--color-surface-soft)]">
            <th class="px-5 py-3 text-left table-header"><?php echo e(__('messages.name')); ?></th><th class="px-5 py-3 text-left table-header"><?php echo e(__('messages.email')); ?></th>
            <th class="px-5 py-3 text-left table-header"><?php echo e(__('messages.role')); ?></th><th class="px-5 py-3 text-left table-header"><?php echo e(__('messages.status')); ?></th><th class="px-5 py-3 text-left table-header print:hidden print-hidden action-col"><?php echo e(__('messages.actions')); ?></th>
        </tr></thead><tbody>
        <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr onclick="window.location='<?php echo e(route('employees.show', $e)); ?>'" class="border-b border-[var(--color-hairline-soft)]/50 hover:bg-[var(--color-surface-soft)] cursor-pointer transition-colors duration-150">
            <td class="px-5 py-3 type-body-sm font-medium text-[var(--color-ink)]"><?php echo e($e->username); ?></td>
            <td class="px-5 py-3 type-body-sm text-[var(--color-slate)]"><?php echo e($e->email); ?></td>
            <td class="px-5 py-3"><span class="badge badge-info"><?php echo e($e->role->display_name); ?></span></td>
            <td class="px-5 py-3"><span class="badge <?php echo e($e->status === 'active' ? 'badge-success' : 'badge-critical'); ?>"><?php echo e($e->status); ?></span></td>
            <td class="px-5 py-3 print:hidden print-hidden action-col"><div class="flex items-center gap-1">
                <a href="<?php echo e(route('employees.show', $e)); ?>" class="btn-ghost !py-1.5 !px-3 !text-xs"><?php echo e(__('messages.detail')); ?></a>
                <button @click="$dispatch('open-delete-modal', { url: '<?php echo e(route('employees.destroy', $e)); ?>' })" class="btn-ghost !py-1.5 !px-3 !text-xs !text-[var(--color-critical)] !border-[var(--color-critical)]/20"><?php echo e(__('messages.delete')); ?></button>
            </div></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="5" class="px-5 py-12 text-center type-body-md text-[var(--color-stone)]"><?php echo e(__('messages.no_data')); ?></td></tr><?php endif; ?>
        </tbody></table></div>
    </div>

    
    <div x-show="viewMode === 'grid'" x-cloak class="grid grid-cols-2 gap-3 sm:grid-cols-2 sm:gap-4 md:grid-cols-3 lg:grid-cols-4 print:hidden print-hidden">
        <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div onclick="window.location='<?php echo e(route('employees.show', $e)); ?>'" class="card flex flex-col p-3 sm:p-5 hover:border-[var(--color-primary)] hover:-translate-y-1 hover:shadow-md cursor-pointer transition-all duration-300 items-center text-center group">
            <div class="h-12 w-12 sm:h-16 sm:w-16 rounded-full bg-[var(--color-primary)] flex items-center justify-center text-white text-base sm:text-xl font-bold mb-2 sm:mb-3 shadow-md group-hover:scale-110 transition-transform duration-300">
                <?php echo e(strtoupper(substr($e->username, 0, 2))); ?>

            </div>
            <h4 class="type-body-sm sm:type-body-md font-bold text-[var(--color-ink-deep)] mb-1 truncate w-full"><?php echo e($e->username); ?></h4>
            <p class="text-[10px] sm:type-caption text-[var(--color-slate)] mb-2 truncate w-full"><?php echo e($e->email); ?></p>
            <div class="flex gap-2 mb-4">
                <span class="badge badge-info !px-2 !py-0.5 !text-[10px]"><?php echo e(strtoupper($e->role->display_name)); ?></span>
                <span class="badge <?php echo e($e->status === 'active' ? 'badge-success' : 'badge-critical'); ?> !px-2 !py-0.5 !text-[10px]"><?php echo e($e->status); ?></span>
            </div>
            <div class="mt-auto w-full pt-3 border-t border-[var(--color-hairline-soft)] flex justify-center gap-1">
                <a href="<?php echo e(route('employees.show', $e)); ?>" class="btn-ghost flex-1 !py-1.5 !text-xs"><?php echo e(__('messages.detail')); ?></a>
                <button @click="$dispatch('open-delete-modal', { url: '<?php echo e(route('employees.destroy', $e)); ?>' })" class="btn-ghost flex-1 !py-1.5 !text-xs !text-[var(--color-critical)] !border-[var(--color-critical)]/20"><?php echo e(__('messages.delete')); ?></button>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full py-12 text-center type-body-md text-[var(--color-stone)]"><?php echo e(__('messages.no_data')); ?></div>
        <?php endif; ?>
    </div>

    <?php if(method_exists($employees, 'links')): ?>
    <div class="mt-4 border-t border-[var(--color-hairline-soft)] pt-4 print:hidden print-hidden"><?php echo e($employees->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Office\Developer\Projects\Laravel\BisnisKu\resources\views/employees/index.blade.php ENDPATH**/ ?>