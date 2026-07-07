<?php

namespace Tests\Browser\BVA;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers\AuthHelper;

/**
 * Boundary Value Analysis — Field: Harga Beli Produk (purchase_price)
 *
 * Aturan Validasi: required|numeric|min:0
 * (Harga beli boleh 0 untuk produk layanan/jasa)
 *
 * | Test Case ID   | Batas Nilai | Nilai Uji | Jenis Batas           | Expected Result            |
 * |----------------|-------------|-----------|-----------------------|----------------------------|
 * | BVA-BELI-001   | min: 0      | -1        | Di bawah minimum      | Validasi min muncul        |
 * | BVA-BELI-002   | min: 0      | 0         | Batas minimum         | Produk tersimpan           |
 * | BVA-BELI-003   | min: 0      | 1         | Di atas minimum       | Produk tersimpan           |
 * | BVA-BELI-004   | min: 0      | 999999999 | Nilai sangat besar    | Produk tersimpan           |
 */
class HargaBeliBVATest extends DuskTestCase
{
    use AuthHelper;

    /**
     * Helper: submit form produk dengan harga beli tertentu
     */
    private function submitProductWithHargaBeli(Browser $browser, int|float $hargaBeli): void
    {
        $hargaJual = max($hargaBeli, 1);

        $browser->visit('/products/create?type=physical')
                ->waitFor('#name', 5)
                ->type('#name', 'Produk BVA Beli ' . $hargaBeli)
                ->type('input[name="purchase_price"]', (string) $hargaBeli)
                ->type('input[name="selling_price"]', (string) $hargaJual)
                ->type('input[name="minimum_stock"]', '1');

        $browser->script("
            const catSelect = document.querySelector('select[name=\"category_code\"]');
            if (catSelect && catSelect.options.length > 1) {
                catSelect.value = catSelect.options[1].value;
            }
        ");

        $browser->press('button[type="submit"]')
                ->pause(2000);
    }

    /**
     * BVA-BELI-001 | Di bawah minimum (-1)
     * Harga beli -1 → Validasi min muncul
     */
    public function test_BVA_BELI_001_harga_beli_negatif(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);
            $this->submitProductWithHargaBeli($browser, -1);

            $url = $browser->driver->getCurrentURL();
            $browser->screenshot('BVA-BELI-001');
            $this->assertStringContainsString('/products/create', $url,
                'BVA-BELI-001: Harga beli -1 seharusnya ditolak');
        });
    }

    /**
     * BVA-BELI-002 | Batas minimum (0)
     * Harga beli 0 → Valid (produk jasa/layanan diperbolehkan harga beli 0)
     */
    public function test_BVA_BELI_002_harga_beli_nol(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);
            $this->submitProductWithHargaBeli($browser, 0);

            $url = $browser->driver->getCurrentURL();
            $browser->screenshot('BVA-BELI-002');
            $this->assertStringNotContainsString('/products/create', $url,
                'BVA-BELI-002: Harga beli 0 seharusnya diterima (batas minimum)');
        });
    }

    /**
     * BVA-BELI-003 | Di atas minimum (1)
     * Harga beli 1 → Valid
     */
    public function test_BVA_BELI_003_harga_beli_satu(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);
            $this->submitProductWithHargaBeli($browser, 1);

            $url = $browser->driver->getCurrentURL();
            $browser->screenshot('BVA-BELI-003');
            $this->assertStringNotContainsString('/products/create', $url,
                'BVA-BELI-003: Harga beli 1 seharusnya diterima');
        });
    }

    /**
     * BVA-BELI-004 | Nilai sangat besar (999999999)
     * Harga beli 999999999 → Valid
     */
    public function test_BVA_BELI_004_harga_beli_sangat_besar(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);
            $this->submitProductWithHargaBeli($browser, 999999999);

            $url = $browser->driver->getCurrentURL();
            $browser->screenshot('BVA-BELI-004');
            $this->assertStringNotContainsString('/products/create', $url,
                'BVA-BELI-004: Harga beli 999999999 seharusnya diterima');
        });
    }
}
