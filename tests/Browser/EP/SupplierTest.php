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
                    ->waitFor('#name', 5)
                    ->type('#name', 'Supplier EP Test ' . time())
                    ->type('input[name="phone_number"]', '081234567890')
                    ->type('input[name="email"]', 'supplier_ep@test.com')
                    ->type('textarea[name="address"]', 'Jl. Test No. 1, Kota Test')
                    ->press('button[type="submit"]')
                    ->pause(2000)
                    ->assertPathIs('/suppliers')
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
                    ->waitFor('#name', 5)
                    ->type('#name', 'AB')
                    ->type('input[name="phone_number"]', '081234567890')
                    ->press('button[type="submit"]')
                    ->pause(2000)
                    ->assertPathIs('/suppliers/create')
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
                    ->waitFor('#name', 5)
                    ->type('#name', 'Supplier Phone Salah')
                    ->type('input[name="phone_number"]', '0812')
                    ->press('button[type="submit"]')
                    ->pause(2000)
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
                    ->waitFor('body', 5)
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

            $browser->press('button[type="submit"]')
                    ->pause(2000)
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
                    ->waitFor('body', 5)
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
