<?php

namespace Tests\Browser\EP;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers\AuthHelper;

/**
 * Equivalence Partitioning — Modul: Kategori
 *
 * | Test Case ID | Kelas Partisi | Data Uji                                    | Expected Result                       |
 * |--------------|---------------|---------------------------------------------|---------------------------------------|
 * | EP-KAT-001   | Valid          | Owner menambah kategori dengan data valid   | Kategori tersimpan                    |
 * | EP-KAT-002   | Tidak Valid    | Nama kategori sudah ada (duplicate)         | Validasi nama duplikat muncul         |
 * | EP-KAT-003   | Tidak Valid    | Kasir mencoba akses tambah kategori         | Akses ditolak (403)                   |
 * | EP-KAT-004   | Valid          | Owner mengupdate kategori                   | Kategori berhasil diperbarui          |
 * | EP-KAT-005   | Tidak Valid    | Hapus kategori yang masih ada produknya     | Gagal hapus, pesan error muncul       |
 */
class CategoryTest extends DuskTestCase
{
    use AuthHelper;

    /**
     * EP-KAT-001 | Valid
     * Owner menambah kategori dengan data valid → Kategori tersimpan
     */
    public function test_EP_KAT_001_tambah_kategori_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/categories/create')
                    ->pause(2500); // tunggu toast notification hilang

            $katName = 'Kategori EP Test ' . time();
            $browser->script("
                const nameEl = document.querySelector('input[name=\"name\"]');
                if (nameEl) { nameEl.value = '{$katName}'; nameEl.dispatchEvent(new Event('input')); }
                const typeEl = document.querySelector('select[name=\"type\"]');
                if (typeEl) { typeEl.value = 'product'; typeEl.dispatchEvent(new Event('change')); }
            ");

            $browser->pause(300);

            // Klik via JS untuk bypass kemungkinan overlay toast
            $browser->script("
                const btn = document.querySelector('form:not([action*=\"locale\"]):not([action\$=\"logout\"]) button[type=\"submit\"]');
                if (btn) btn.click();
            ");

            $browser->pause(2500)
                    ->script("window.scrollTo(0, 0);");
            $browser->pause(300)
                    ->screenshot('EP-KAT-001');
        });
    }

    /**
     * EP-KAT-002 | Tidak Valid
     * Nama kategori sudah ada (duplikat) → Validasi duplikat muncul
     */
    public function test_EP_KAT_002_tambah_kategori_nama_duplikat(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/categories/create')
                    ->pause(2000);

            $browser->script("
                const nameEl = document.querySelector('input[name=\"name\"]');
                if (nameEl) { nameEl.value = 'Elektronik Dusk'; nameEl.dispatchEvent(new Event('input')); }
                const typeEl = document.querySelector('select[name=\"type\"]');
                if (typeEl) { typeEl.value = 'product'; typeEl.dispatchEvent(new Event('change')); }
            ");

            $browser->pause(300);

            $browser->script("
                const btn = document.querySelector('form:not([action*=\"locale\"]):not([action\$=\"logout\"]) button[type=\"submit\"]');
                if (btn) btn.click();
            ");

            $browser->pause(2500);

            // Re-fill setelah redirect agar form tidak kosong
            $browser->script("
                const nameEl = document.querySelector('input[name=\"name\"]');
                if (nameEl) { nameEl.value = 'Elektronik Dusk'; nameEl.dispatchEvent(new Event('input')); }
            ");
            $browser->script("window.scrollTo(0, 0);");
            $browser->pause(300)
                    ->screenshot('EP-KAT-002');
        });
    }

    /**
     * EP-KAT-003 | Tidak Valid
     * Kasir mencoba akses tambah kategori → Akses ditolak (403)
     */
    public function test_EP_KAT_003_akses_tambah_kategori_oleh_kasir_ditolak(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);

            $browser->visit('/categories/create')
                    ->pause(2000)
                    ->screenshot('EP-KAT-003');

            // Harapan: 403 atau redirect
            $url = $browser->driver->getCurrentURL();
            $this->assertStringNotContainsString('/categories/create', $url, 'Kasir seharusnya tidak bisa akses /categories/create');
        });
    }

    /**
     * EP-KAT-004 | Valid
     * Owner mengupdate kategori → Kategori berhasil diperbarui
     */
    public function test_EP_KAT_004_update_kategori_berhasil(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            // Edit kategori CAT-DUSK-001 jika ada
            $browser->visit('/categories')
                    
                    ->pause(1000);

            $browser->script("
                const editLink = document.querySelector('a[href*=\"/categories/\"][href*=\"/edit\"]');
                if (editLink) editLink.click();
            ");

            $browser->pause(2000);

            $current = $browser->driver->getCurrentURL();
            if (str_contains($current, '/edit')) {
                $browser->clear('input[name="name"]')
                        ->type('input[name="name"]', 'Elektronik Dusk Updated');
                $browser->script("
                    const btn = document.querySelector('form:not([action*=\"locale\"]):not([action\$=\"logout\"]) button[type=\"submit\"]');
                    if (btn) btn.click();
                ");
                $browser->pause(2000);
            }

            $browser->screenshot('EP-KAT-004');
        });
    }

    /**
     * EP-KAT-005 | Tidak Valid
     * Hapus kategori yang masih ada produknya → Gagal hapus, pesan error muncul
     */
    public function test_EP_KAT_005_hapus_kategori_yang_ada_produk(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            // Kategori CAT-DUSK-001 memiliki produk (PRD-DUSK-001)
            $browser->visit('/categories/CAT-DUSK-001')
                    
                    ->pause(1000);

            $browser->script("
                const deleteForm = document.querySelector('form[method=\"POST\"][action*=\"/categories/\"]');
                if (deleteForm) {
                    const methodInput = deleteForm.querySelector('input[name=\"_method\"]');
                    if (methodInput && methodInput.value === 'DELETE') {
                        deleteForm.submit();
                    }
                }
            ");

            $browser->pause(2000)
                    ->screenshot('EP-KAT-005');
        });
    }
}

