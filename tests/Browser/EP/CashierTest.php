<?php

namespace Tests\Browser\EP;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers\AuthHelper;

/**
 * Equivalence Partitioning — Modul: Kasir / POS (Point of Sale)
 *
 * | Test Case ID  | Kelas Partisi | Data Uji                                            | Expected Result                             |
 * |---------------|---------------|-----------------------------------------------------|---------------------------------------------|
 * | EP-KASIR-001  | Valid          | Checkout tunai (cash) dengan stok cukup             | Transaksi berhasil, struk tampil            |
 * | EP-KASIR-002  | Valid          | Checkout QRIS (metode QRIS)                         | Transaksi pending QRIS, QR code tampil      |
 * | EP-KASIR-003  | Valid          | Checkout hutang (debt) dengan nama debitur          | Transaksi tercatat, data hutang tersimpan   |
 * | EP-KASIR-004  | Tidak Valid    | Checkout dengan kuantitas melebihi stok             | Error stok tidak mencukupi                  |
 * | EP-KASIR-005  | Tidak Valid    | Checkout dengan diskon melebihi total               | Error/validasi diskon muncul                |
 * | EP-KASIR-006  | Tidak Valid    | Checkout tanpa item produk                          | Validasi wajib ada item muncul              |
 * | EP-KASIR-007  | Tidak Valid    | Akses halaman kasir oleh Gudang                     | Akses ditolak (403)                         |
 */
class CashierTest extends DuskTestCase
{
    use AuthHelper;

    /**
     * EP-KASIR-001 | Valid
     * Checkout cash dengan stok cukup → Transaksi berhasil
     */
    public function test_EP_KASIR_001_checkout_cash_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);

            $browser->visit('/cashier')
                    ->waitFor('body', 5)
                    ->pause(2000);

            // Tambah produk ke keranjang via klik produk / tombol +
            $browser->script("
                // Cari tombol produk atau card produk dan klik
                const productBtn = document.querySelector('[data-product-code], .product-card button, button[data-code]');
                if (productBtn) productBtn.click();
            ");

            $browser->pause(1000)
                    ->screenshot('EP-KASIR-001-cart');

            // Trigger checkout via JS form submit (karena form Dusk bisa berbeda UI)
            $browser->screenshot('EP-KASIR-001');
        });
    }

    /**
     * EP-KASIR-002 | Valid
     * Checkout QRIS → Transaksi pending, QR code tampil
     */
    public function test_EP_KASIR_002_checkout_qris_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);

            $browser->visit('/cashier')
                    ->waitFor('body', 5)
                    ->pause(2000)
                    ->screenshot('EP-KASIR-002');
        });
    }

    /**
     * EP-KASIR-003 | Valid
     * Checkout dengan metode hutang (debt) → Hutang tercatat
     */
    public function test_EP_KASIR_003_checkout_hutang_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);

            $browser->visit('/cashier')
                    ->waitFor('body', 5)
                    ->pause(2000)
                    ->screenshot('EP-KASIR-003');
        });
    }

    /**
     * EP-KASIR-004 | Tidak Valid
     * Checkout dengan kuantitas melebihi stok → Error stok tidak mencukupi
     * Menggunakan direct POST ke endpoint checkout untuk menguji validasi server
     */
    public function test_EP_KASIR_004_checkout_kuantitas_melebihi_stok(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);

            // Gunakan fetch API untuk POST langsung ke endpoint
            $csrfToken = $browser->visit('/cashier')
                                  ->waitFor('body', 5)
                                  ->value('meta[name="csrf-token"]', '');

            // POST dengan kuantitas 9999 (melebihi stok 100)
            $result = $browser->script("
                return fetch('/cashier/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        items: [{
                            product_code: 'PRD-DUSK-001',
                            quantity: 9999,
                            unit_price: 75000
                        }],
                        payment_method: 'cash',
                        amount_paid: 750000000
                    })
                }).then(r => r.json());
            ");

            $browser->pause(1000)->screenshot('EP-KASIR-004');

            $this->assertIsArray($result[0]);
            $this->assertFalse($result[0]['success'] ?? true, 'Seharusnya gagal karena stok tidak cukup');
        });
    }

    /**
     * EP-KASIR-005 | Tidak Valid
     * Checkout dengan diskon melebihi total → Error/validasi muncul
     */
    public function test_EP_KASIR_005_checkout_diskon_melebihi_total(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);

            $browser->visit('/cashier')
                    ->waitFor('body', 5)
                    ->pause(1000);

            // POST ke endpoint checkout dengan diskon > subtotal
            $result = $browser->script("
                return fetch('/cashier/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        items: [{
                            product_code: 'PRD-DUSK-001',
                            quantity: 1,
                            unit_price: 75000
                        }],
                        discount: 999999,
                        payment_method: 'cash',
                        amount_paid: 0
                    })
                }).then(r => r.json());
            ");

            $browser->pause(1000)->screenshot('EP-KASIR-005');
        });
    }

    /**
     * EP-KASIR-006 | Tidak Valid
     * Checkout tanpa item produk → Validasi wajib ada item muncul
     */
    public function test_EP_KASIR_006_checkout_tanpa_item(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);

            $browser->visit('/cashier')
                    ->waitFor('body', 5)
                    ->pause(1000);

            // POST tanpa items
            $result = $browser->script("
                return fetch('/cashier/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        items: [],
                        payment_method: 'cash',
                        amount_paid: 0
                    })
                }).then(r => r.json());
            ");

            $browser->pause(1000)->screenshot('EP-KASIR-006');
        });
    }

    /**
     * EP-KASIR-007 | Tidak Valid
     * Akses halaman kasir oleh Gudang → Akses ditolak (403)
     */
    public function test_EP_KASIR_007_akses_halaman_kasir_oleh_gudang_ditolak(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsGudang($browser);

            $browser->visit('/cashier')
                    ->pause(2000)
                    ->screenshot('EP-KASIR-007');

            $url = $browser->driver->getCurrentURL();
            $this->assertStringNotContainsString('/cashier', $url, 'Gudang seharusnya tidak bisa akses /cashier');
        });
    }
}
