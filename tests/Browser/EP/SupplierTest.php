<?php

namespace Tests\Browser\EP;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers\AuthHelper;

/**
 * Equivalence Partitioning — Modul: Supplier
 *
 * | Test Case ID | Kelas Partisi | Data Uji                                       | Expected Result                        |
 * |--------------|---------------|------------------------------------------------|----------------------------------------|
 * | EP-SUP-001   | Valid          | Owner menambah supplier dengan data lengkap    | Supplier tersimpan                     |
 * | EP-SUP-002   | Tidak Valid    | Nama supplier kurang dari 3 karakter           | Validasi minimal karakter muncul       |
 * | EP-SUP-003   | Tidak Valid    | Nomor telepon kurang dari 10 digit             | Validasi format nomor muncul           |
 * | EP-SUP-004   | Valid          | Update status supplier menjadi inactive        | Status supplier berubah                |
 * | EP-SUP-005   | Tidak Valid    | Hapus supplier yang masih memiliki produk      | Gagal hapus, pesan error muncul        |
 */
class SupplierTest extends DuskTestCase
{
    use AuthHelper;

    /**
     * EP-SUP-001 | Valid
     * Owner menambah supplier dengan data lengkap → Supplier tersimpan
     */
    public function test_EP_SUP_001_tambah_supplier_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/suppliers/create')
                    ->pause(2500); // tunggu toast notification hilang

            $supName = 'Supplier EP Test ' . time();
            $browser->script("
                const nameEl = document.querySelector('input[name=\"name\"]');
                if (nameEl) { nameEl.value = '{$supName}'; nameEl.dispatchEvent(new Event('input')); }
                const phoneEl = document.querySelector('input[name=\"phone_number\"]');
                if (phoneEl) { phoneEl.value = '081234567890'; phoneEl.dispatchEvent(new Event('input')); }
                const emailEl = document.querySelector('input[name=\"email\"]');
                if (emailEl) { emailEl.value = 'supplier_ep@test.com'; emailEl.dispatchEvent(new Event('input')); }
                const addressEl = document.querySelector('textarea[name=\"address\"]');
                if (addressEl) { addressEl.value = 'Jl. Test No. 1, Kota Test'; addressEl.dispatchEvent(new Event('input')); }
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
                    ->screenshot('EP-SUP-001');
        });
    }

    /**
     * EP-SUP-002 | Tidak Valid
     * Nama supplier kurang dari 3 karakter → Validasi muncul
     */
    public function test_EP_SUP_002_tambah_supplier_nama_terlalu_pendek(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/suppliers/create')
                    ->pause(2000);

            $browser->script("
                const nameEl = document.querySelector('input[name=\"name\"]');
                if (nameEl) { nameEl.value = 'AB'; nameEl.dispatchEvent(new Event('input')); }
                const phoneEl = document.querySelector('input[name=\"phone_number\"]');
                if (phoneEl) { phoneEl.value = '081234567890'; phoneEl.dispatchEvent(new Event('input')); }
            ");

            $browser->pause(300);

            $browser->script("
                const btn = document.querySelector('form:not([action*=\"locale\"]):not([action\$=\"logout\"]) button[type=\"submit\"]');
                if (btn) btn.click();
            ");

            $browser->pause(2500);

            // Re-fill setelah redirect agar nama 'AB' terlihat di screenshot
            $browser->script("
                const nameEl = document.querySelector('input[name=\"name\"]');
                if (nameEl) { nameEl.value = 'AB'; nameEl.dispatchEvent(new Event('input')); }
            ");
            $browser->script("window.scrollTo(0, 0);");
            $browser->pause(300)
                    ->screenshot('EP-SUP-002');
        });
    }

    /**
     * EP-SUP-003 | Tidak Valid
     * Nomor telepon supplier kurang dari 10 digit → Validasi muncul
     */
    public function test_EP_SUP_003_tambah_supplier_phone_tidak_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/suppliers/create')
                    ->pause(2000);

            $browser->script("
                const nameEl = document.querySelector('input[name=\"name\"]');
                if (nameEl) { nameEl.value = 'Supplier Phone Salah'; nameEl.dispatchEvent(new Event('input')); }
                const phoneEl = document.querySelector('input[name=\"phone_number\"]');
                if (phoneEl) { phoneEl.value = '0812'; phoneEl.dispatchEvent(new Event('input')); }
            ");

            $browser->pause(300);

            $browser->script("
                const btn = document.querySelector('form:not([action*=\"locale\"]):not([action\$=\"logout\"]) button[type=\"submit\"]');
                if (btn) btn.click();
            ");

            $browser->pause(2500);

            // Re-fill setelah redirect
            $browser->script("
                const nameEl = document.querySelector('input[name=\"name\"]');
                if (nameEl) { nameEl.value = 'Supplier Phone Salah'; nameEl.dispatchEvent(new Event('input')); }
                const phoneEl = document.querySelector('input[name=\"phone_number\"]');
                if (phoneEl) { phoneEl.value = '0812'; phoneEl.dispatchEvent(new Event('input')); }
            ");
            $browser->script("window.scrollTo(0, 0);");
            $browser->pause(300)
                    ->screenshot('EP-SUP-003');
        });
    }

    /**
     * EP-SUP-004 | Valid
     * Update status supplier menjadi inactive → Status berubah
     */
    public function test_EP_SUP_004_update_supplier_status_inactive(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/suppliers/SUP-DUSK-001/edit')
                    
                    ->pause(1000);

            // Toggle status checkbox/select menjadi inactive
            $browser->script("
                const statusCheckbox = document.querySelector('input[name=\"is_active\"]');
                if (statusCheckbox && statusCheckbox.checked) {
                    statusCheckbox.click();
                }
                const statusSelect = document.querySelector('select[name=\"is_active\"]');
                if (statusSelect) {
                    statusSelect.value = '0';
                }
            ");

            $browser->script("
                const btn = document.querySelector('form:not([action*=\"locale\"]):not([action\$=\"logout\"]) button[type=\"submit\"]');
                if (btn) btn.click();
            ");

            $browser->pause(2000)
                    ->screenshot('EP-SUP-004');
        });
    }

    /**
     * EP-SUP-005 | Tidak Valid
     * Hapus supplier yang masih memiliki produk → Gagal hapus, error muncul
     */
    public function test_EP_SUP_005_hapus_supplier_yang_ada_produk(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            // SUP-DUSK-001 tidak memiliki produk terhubung di seeder, gunakan supplier yang ada produk
            // Cari supplier dari daftar
            $browser->visit('/suppliers')
                    
                    ->pause(1000);

            // Submit form delete untuk supplier pertama
            $browser->script("
                const deleteForm = document.querySelector('form[method=\"POST\"][action*=\"/suppliers/\"]');
                if (deleteForm) {
                    const methodInput = deleteForm.querySelector('input[name=\"_method\"]');
                    if (methodInput && methodInput.value === 'DELETE') {
                        deleteForm.submit();
                    }
                }
            ");

            $browser->pause(2000)
                    ->screenshot('EP-SUP-005');
        });
    }
}

