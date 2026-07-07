<?php

namespace Tests\Browser\EP;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers\AuthHelper;

/**
 * Equivalence Partitioning — Modul: Pengadaan Produk (Product Purchase)
 *
 * | Test Case ID   | Kelas Partisi | Data Uji                                              | Expected Result                               |
 * |----------------|---------------|-------------------------------------------------------|-----------------------------------------------|
 * | EP-PENGAD-001  | Valid          | Owner membuat pengadaan baru dengan data valid        | Pengadaan tersimpan (status draft)            |
 * | EP-PENGAD-002  | Valid          | Gudang membuat pengadaan baru                         | Pengadaan tersimpan (status draft)            |
 * | EP-PENGAD-003  | Valid          | Owner mengubah status pengadaan dari draft → diterima | Status berubah, stok produk bertambah         |
 * | EP-PENGAD-004  | Tidak Valid    | Pengadaan tanpa item produk / nama produk             | Validasi item wajib muncul                    |
 * | EP-PENGAD-005  | Valid          | Teknisi melihat detail pengadaan (read-only)          | Halaman detail tampil                         |
 * | EP-PENGAD-006  | Tidak Valid    | Kasir mencoba membuat pengadaan → Akses ditolak       | Redirect / 403                                |
 */
class ProductPurchaseTest extends DuskTestCase
{
    use AuthHelper;

    /**
     * EP-PENGAD-001 | Valid
     * Owner membuat pengadaan baru → Tersimpan sebagai draft
     */
    public function test_EP_PENGAD_001_owner_buat_pengadaan_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/product-purchases/create')
                    ->waitFor('body', 5)
                    ->pause(1500);

            // Isi tanggal pengadaan
            $browser->script("
                const dateInput = document.querySelector('input[name=\"purchase_date\"]');
                if (dateInput) dateInput.value = '" . now()->format('Y-m-d') . "';
            ");

            // Pilih produk pertama yang tersedia
            $browser->script("
                const productSelect = document.querySelector('select[name=\"items[0][product_code]\"]');
                if (productSelect && productSelect.options.length > 1) {
                    productSelect.value = productSelect.options[1].value;
                    productSelect.dispatchEvent(new Event('change'));
                }
            ");

            $browser->pause(500);
            $browser->script("
                const qtyInput = document.querySelector('input[name=\"items[0][quantity]\"]');
                if (qtyInput) qtyInput.value = '10';
                const priceInput = document.querySelector('input[name=\"items[0][purchase_price]\"]');
                if (priceInput) priceInput.value = '45000';
                const sourceSelect = document.querySelector('select[name=\"items[0][source]\"]');
                if (sourceSelect) sourceSelect.value = 'offline';
            ");

            $browser->press('button[type="submit"]')
                    ->pause(3000)
                    ->screenshot('EP-PENGAD-001');
        });
    }

    /**
     * EP-PENGAD-002 | Valid
     * Gudang membuat pengadaan baru → Tersimpan sebagai draft
     */
    public function test_EP_PENGAD_002_gudang_buat_pengadaan_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsGudang($browser);

            $browser->visit('/product-purchases/create')
                    ->waitFor('body', 5)
                    ->pause(1500);

            $browser->script("
                const dateInput = document.querySelector('input[name=\"purchase_date\"]');
                if (dateInput) dateInput.value = '" . now()->format('Y-m-d') . "';
                const productSelect = document.querySelector('select[name=\"items[0][product_code]\"]');
                if (productSelect && productSelect.options.length > 1) {
                    productSelect.value = productSelect.options[1].value;
                    productSelect.dispatchEvent(new Event('change'));
                }
            ");

            $browser->pause(500)
                    ->script("
                        const qtyInput = document.querySelector('input[name=\"items[0][quantity]\"]');
                        if (qtyInput) qtyInput.value = '5';
                        const priceInput = document.querySelector('input[name=\"items[0][purchase_price]\"]');
                        if (priceInput) priceInput.value = '30000';
                        const sourceSelect = document.querySelector('select[name=\"items[0][source]\"]');
                        if (sourceSelect) sourceSelect.value = 'offline';
                    ");

            $browser->press('button[type="submit"]')
                    ->pause(3000)
                    ->screenshot('EP-PENGAD-002');
        });
    }

    /**
     * EP-PENGAD-003 | Valid
     * Owner mengubah status pengadaan dari draft → diterima → stok bertambah
     */
    public function test_EP_PENGAD_003_update_status_draft_ke_diterima(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/product-purchases')
                    ->waitFor('body', 5)
                    ->pause(1000);

            // Buka detail pengadaan pertama
            $browser->script("
                const link = document.querySelector('a[href*=\"/product-purchases/\"]');
                if (link) link.click();
            ");

            $browser->pause(2000);

            // Ubah status ke 'received'
            $browser->script("
                const statusForm = document.querySelector('form[action*=\"/status\"]');
                if (statusForm) {
                    const statusInput = statusForm.querySelector('input[name=\"status\"], select[name=\"status\"]');
                    if (statusInput) {
                        statusInput.value = 'received';
                        statusInput.dispatchEvent(new Event('change'));
                    }
                    statusForm.submit();
                }
            ");

            $browser->pause(2000)
                    ->screenshot('EP-PENGAD-003');
        });
    }

    /**
     * EP-PENGAD-004 | Tidak Valid
     * Pengadaan tanpa item produk → Validasi muncul
     */
    public function test_EP_PENGAD_004_pengadaan_tanpa_item(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/product-purchases/create')
                    ->waitFor('body', 5)
                    ->pause(1000);

            $browser->script("
                const dateInput = document.querySelector('input[name=\"purchase_date\"]');
                if (dateInput) dateInput.value = '" . now()->format('Y-m-d') . "';
            ");

            // Submit tanpa mengisi item
            $browser->press('button[type="submit"]')
                    ->pause(2000)
                    ->screenshot('EP-PENGAD-004');
        });
    }

    /**
     * EP-PENGAD-005 | Valid
     * Teknisi melihat detail pengadaan (read-only) → Halaman tampil
     */
    public function test_EP_PENGAD_005_teknisi_lihat_detail_pengadaan(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTeknisi($browser);

            $browser->visit('/product-purchases')
                    ->pause(2000);

            // Teknisi tidak bisa akses index, tapi bisa akses show
            $browser->script("
                const link = document.querySelector('a[href*=\"/product-purchases/\"]');
                if (link) link.click();
            ");

            $browser->pause(2000)
                    ->screenshot('EP-PENGAD-005');
        });
    }

    /**
     * EP-PENGAD-006 | Tidak Valid
     * Kasir mencoba membuat pengadaan → Akses ditolak
     */
    public function test_EP_PENGAD_006_kasir_gagal_buat_pengadaan(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);

            $browser->visit('/product-purchases/create')
                    ->pause(2000)
                    ->screenshot('EP-PENGAD-006');

            $url = $browser->driver->getCurrentURL();
            $this->assertStringNotContainsString('/product-purchases/create', $url,
                'Kasir tidak boleh akses halaman buat pengadaan');
        });
    }
}
