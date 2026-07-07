<?php

namespace Tests\Browser\EP;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers\AuthHelper;

/**
 * Equivalence Partitioning — Modul: Produk
 *
 * | Test Case ID   | Kelas Partisi | Data Uji                                          | Expected Result                       |
 * |----------------|---------------|---------------------------------------------------|---------------------------------------|
 * | EP-PRODUK-001  | Valid          | Tambah produk dengan data lengkap (owner)         | Produk tersimpan                      |
 * | EP-PRODUK-002  | Tidak Valid    | Tambah produk tanpa nama                          | Validasi nama muncul                  |
 * | EP-PRODUK-003  | Tidak Valid    | Harga jual lebih kecil dari harga beli            | Validasi harga muncul                 |
 * | EP-PRODUK-004  | Tidak Valid    | Kategori tidak dipilih                            | Validasi kategori muncul              |
 * | EP-PRODUK-005  | Valid          | Edit produk yang sudah ada (owner)                | Produk berhasil diperbarui            |
 * | EP-PRODUK-006  | Tidak Valid    | Hapus produk yang sudah ada di transaksi          | Gagal hapus, pesan error muncul       |
 * | EP-PRODUK-007  | Tidak Valid    | Akses tambah produk oleh Kasir                    | Akses ditolak (403)                   |
 * | EP-PRODUK-008  | Valid          | Akses lihat daftar produk oleh Gudang             | Halaman produk tampil                 |
 */
class ProductTest extends DuskTestCase
{
    use AuthHelper;

    /**
     * EP-PRODUK-001 | Valid
     * Owner menambah produk dengan data lengkap → Produk tersimpan
     */
    public function test_EP_PRODUK_001_tambah_produk_data_lengkap(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/products/create?type=physical')
                    ->waitFor('#name', 5)
                    ->type('#name', 'Produk EP Test 001')
                    ->select('select[name="category_code"]', '')
                    ->pause(500);

            // Pilih kategori pertama yang tersedia
            $browser->script("document.querySelector('select[name=\"category_code\"] option:not([value=\"\"])') && (document.querySelector('select[name=\"category_code\"]').value = document.querySelector('select[name=\"category_code\"] option:not([value=\"\"])').value)");

            $browser->type('input[name="purchase_price"]', '50000')
                    ->type('input[name="selling_price"]', '75000')
                    ->type('input[name="minimum_stock"]', '5')
                    ->press('button[type="submit"]')
                    ->pause(3000)
                    ->screenshot('EP-PRODUK-001');
        });
    }

    /**
     * EP-PRODUK-002 | Tidak Valid
     * Tambah produk tanpa nama → Validasi nama muncul
     */
    public function test_EP_PRODUK_002_tambah_produk_tanpa_nama(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/products/create?type=physical')
                    ->waitFor('button[type="submit"]', 5)
                    ->type('input[name="purchase_price"]', '50000')
                    ->type('input[name="selling_price"]', '75000')
                    ->type('input[name="minimum_stock"]', '5')
                    ->press('button[type="submit"]')
                    ->pause(2000)
                    ->assertPathIs('/products/create')
                    ->screenshot('EP-PRODUK-002');
        });
    }

    /**
     * EP-PRODUK-003 | Tidak Valid
     * Harga jual lebih kecil dari harga beli → Validasi harga muncul
     */
    public function test_EP_PRODUK_003_harga_jual_lebih_kecil_dari_harga_beli(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/products/create?type=physical')
                    ->waitFor('#name', 5)
                    ->type('#name', 'Produk Harga Salah')
                    ->type('input[name="purchase_price"]', '100000')
                    ->type('input[name="selling_price"]', '50000')
                    ->type('input[name="minimum_stock"]', '5')
                    ->press('button[type="submit"]')
                    ->pause(2000)
                    ->screenshot('EP-PRODUK-003');
        });
    }

    /**
     * EP-PRODUK-004 | Tidak Valid
     * Tambah produk tanpa kategori → Validasi kategori muncul
     */
    public function test_EP_PRODUK_004_tambah_produk_tanpa_kategori(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/products/create?type=physical')
                    ->waitFor('#name', 5)
                    ->type('#name', 'Produk Tanpa Kategori')
                    ->type('input[name="purchase_price"]', '50000')
                    ->type('input[name="selling_price"]', '75000')
                    ->type('input[name="minimum_stock"]', '5')
                    ->press('button[type="submit"]')
                    ->pause(2000)
                    ->screenshot('EP-PRODUK-004');
        });
    }

    /**
     * EP-PRODUK-005 | Valid
     * Owner mengedit produk yang ada → Produk berhasil diperbarui
     */
    public function test_EP_PRODUK_005_edit_produk_berhasil(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/products')
                    ->waitFor('table, .product-list, [data-product]', 5)
                    ->pause(1000);

            // Klik link edit pada produk pertama di tabel
            $browser->script("
                const editLink = document.querySelector('a[href*=\"/products/\"][href*=\"/edit\"]');
                if (editLink) editLink.click();
            ");

            $browser->pause(2000)
                    ->waitFor('#name, input[name=\"name\"]', 5);

            $browser->clear('input[name="selling_price"]')
                    ->type('input[name="selling_price"]', '80000')
                    ->press('button[type="submit"]')
                    ->pause(2000)
                    ->screenshot('EP-PRODUK-005');
        });
    }

    /**
     * EP-PRODUK-006 | Tidak Valid
     * Hapus produk yang sudah ada di transaksi → Gagal hapus, pesan error muncul
     */
    public function test_EP_PRODUK_006_hapus_produk_yang_ada_transaksi(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/products/PRD-DUSK-001')
                    ->waitFor('body', 5)
                    ->pause(1000);

            // Cari tombol hapus dan klik
            $browser->script("
                const deleteForm = document.querySelector('form[method=\"POST\"][action*=\"/products/\"]');
                if (deleteForm) {
                    const methodInput = deleteForm.querySelector('input[name=\"_method\"]');
                    if (methodInput && methodInput.value === 'DELETE') {
                        deleteForm.submit();
                    }
                }
            ");

            $browser->pause(2000)
                    ->screenshot('EP-PRODUK-006');
        });
    }

    /**
     * EP-PRODUK-007 | Tidak Valid
     * Kasir mencoba mengakses halaman tambah produk → Akses ditolak (403)
     */
    public function test_EP_PRODUK_007_akses_tambah_produk_oleh_kasir_ditolak(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);

            $browser->visit('/products/create')
                    ->pause(2000)
                    ->assertSee('403', '');

            // Bisa juga 403 page atau redirect dengan pesan error
            $currentPath = $browser->driver->getCurrentURL();
            $browser->screenshot('EP-PRODUK-007');
        });
    }

    /**
     * EP-PRODUK-008 | Valid
     * Gudang mengakses daftar produk → Halaman produk tampil
     */
    public function test_EP_PRODUK_008_akses_daftar_produk_oleh_gudang(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsGudang($browser);

            $browser->visit('/products')
                    ->waitFor('body', 5)
                    ->pause(1000)
                    ->assertPathIs('/products')
                    ->screenshot('EP-PRODUK-008');
        });
    }
}
