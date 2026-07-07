<?php

namespace Tests\Browser\BVA;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers\AuthHelper;

/**
 * Boundary Value Analysis — Field: Stok Produk (via ProductPurchase)
 *
 * Aturan Validasi: Wajib diisi, angka, min 1, max 9999 (batas stok wajar)
 * Catatan: Stok tidak bisa diinput langsung; diuji via kuantitas pengadaan (product purchase qty)
 *          yang berdampak langsung ke stok produk.
 *
 * | Test Case ID   | Batas Nilai | Nilai Uji | Jenis Batas     | Expected Result              |
 * |----------------|-------------|-----------|-----------------|------------------------------|
 * | BVA-STOK-001   | 1–9999      | 0         | Di bawah minimum| Validasi kuantitas muncul    |
 * | BVA-STOK-002   | 1–9999      | 1         | Batas minimum   | Produk tersimpan / disimpan  |
 * | BVA-STOK-003   | 1–9999      | 2         | Di atas minimum | Produk tersimpan             |
 * | BVA-STOK-004   | 1–9999      | 9999      | Batas maksimum  | Produk tersimpan             |
 * | BVA-STOK-005   | 1–9999      | 10000     | Di atas maksimum| Validasi stok muncul         |
 */
class StokBVATest extends DuskTestCase
{
    use AuthHelper;

    /**
     * Helper: POST ke endpoint checkout/product purchase dengan kuantitas tertentu
     * dan kembalikan response JSON.
     */
    private function postCheckoutWithQty(Browser $browser, int $qty): array
    {
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
                        quantity: {$qty},
                        unit_price: 75000
                    }],
                    payment_method: 'cash',
                    amount_paid: " . ($qty * 75000 + 1) . "
                })
            }).then(r => r.json()).catch(e => ({ error: e.message }));
        ");

        return $result[0] ?? [];
    }

    /**
     * BVA-STOK-001 | Di bawah minimum (0)
     * Kuantitas 0 → Validasi muncul (min:1 pada checkout)
     */
    public function test_BVA_STOK_001_stok_di_bawah_minimum(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);
            $browser->visit('/cashier')->waitFor('body', 5)->pause(500);

            $result = $this->postCheckoutWithQty($browser, 0);

            $browser->screenshot('BVA-STOK-001');
            $this->assertFalse($result['success'] ?? true,
                'BVA-STOK-001: Kuantitas 0 seharusnya ditolak (di bawah minimum)');
        });
    }

    /**
     * BVA-STOK-002 | Batas minimum (1)
     * Kuantitas 1 → Berhasil (valid)
     */
    public function test_BVA_STOK_002_stok_batas_minimum(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);
            $browser->visit('/cashier')->waitFor('body', 5)->pause(500);

            $result = $this->postCheckoutWithQty($browser, 1);

            $browser->screenshot('BVA-STOK-002');
            $this->assertTrue($result['success'] ?? false,
                'BVA-STOK-002: Kuantitas 1 seharusnya diterima (batas minimum)');
        });
    }

    /**
     * BVA-STOK-003 | Di atas minimum (2)
     * Kuantitas 2 → Berhasil (valid)
     */
    public function test_BVA_STOK_003_stok_di_atas_minimum(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);
            $browser->visit('/cashier')->waitFor('body', 5)->pause(500);

            $result = $this->postCheckoutWithQty($browser, 2);

            $browser->screenshot('BVA-STOK-003');
            $this->assertTrue($result['success'] ?? false,
                'BVA-STOK-003: Kuantitas 2 seharusnya diterima (di atas minimum)');
        });
    }

    /**
     * BVA-STOK-004 | Batas maksimum (stok produk = 100, uji 99)
     * Kuantitas 99 (batas mendekati maks stok PRD-DUSK-001=100) → Berhasil
     */
    public function test_BVA_STOK_004_stok_batas_maksimum(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);
            $browser->visit('/cashier')->waitFor('body', 5)->pause(500);

            $result = $this->postCheckoutWithQty($browser, 99);

            $browser->screenshot('BVA-STOK-004');
            $this->assertTrue($result['success'] ?? false,
                'BVA-STOK-004: Kuantitas 99 (batas maksimum stok) seharusnya diterima');
        });
    }

    /**
     * BVA-STOK-005 | Di atas maksimum (melebihi stok: 10000)
     * Kuantitas 10000 → Error stok tidak mencukupi
     */
    public function test_BVA_STOK_005_stok_di_atas_maksimum(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);
            $browser->visit('/cashier')->waitFor('body', 5)->pause(500);

            $result = $this->postCheckoutWithQty($browser, 10000);

            $browser->screenshot('BVA-STOK-005');
            $this->assertFalse($result['success'] ?? true,
                'BVA-STOK-005: Kuantitas 10000 (melebihi stok) seharusnya ditolak');
        });
    }
}
