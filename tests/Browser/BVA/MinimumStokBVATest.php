<?php

namespace Tests\Browser\BVA;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers\AuthHelper;

/**
 * Boundary Value Analysis — Field: Minimum Stok Produk (minimum_stock)
 *
 * Aturan Validasi: required|integer|min:0
 *
 * | Test Case ID    | Batas Nilai | Nilai Uji | Jenis Batas      | Expected Result         |
 * |-----------------|-------------|-----------|------------------|-------------------------|
 * | BVA-MINSTOK-001 | min: 0      | -1        | Di bawah minimum | Validasi min muncul     |
 * | BVA-MINSTOK-002 | min: 0      | 0         | Batas minimum    | Produk tersimpan        |
 * | BVA-MINSTOK-003 | min: 0      | 1         | Di atas minimum  | Produk tersimpan        |
 * | BVA-MINSTOK-004 | min: 0      | 9999      | Nilai besar      | Produk tersimpan        |
 */
class MinimumStokBVATest extends DuskTestCase
{
    use AuthHelper;

    /**
     * Helper: submit form produk dengan minimum_stock tertentu
     */
    private function submitProductWithMinStok(Browser $browser, int $minStok): void
    {
        $browser->visit('/products/create?type=physical')
                ->waitFor('input[name="name"]', 5)
                ->type('input[name="name"]', 'Produk BVA MinStok ' . $minStok)
                ->type('input[name="purchase_price"]', '10000')
                ->type('input[name="selling_price"]', '15000')
                ->type('input[name="minimum_stock"]', (string) $minStok);

        $browser->script("
            const catSelect = document.querySelector('select[name=\"category_code\"]');
            if (catSelect && catSelect.options.length > 1) {
                catSelect.value = catSelect.options[1].value;
            }
        ");

        $browser->click('form:not([action*="locale"]):not([action$="logout"]) button[type="submit"]')
                ->pause(2000);
    }

    /**
     * BVA-MINSTOK-001 | Di bawah minimum (-1)
     * Minimum stok -1 → Validasi muncul
     */
    public function test_BVA_MINSTOK_001_minimum_stok_negatif(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);
            $this->submitProductWithMinStok($browser, -1);

            $url = $browser->driver->getCurrentURL();
            $browser->screenshot('BVA-MINSTOK-001');
            $this->assertStringContainsString('/products/create', $url,
                'BVA-MINSTOK-001: Minimum stok -1 seharusnya ditolak');
        });
    }

    /**
     * BVA-MINSTOK-002 | Batas minimum (0)
     * Minimum stok 0 → Valid (produk tidak punya batas minimum)
     */
    public function test_BVA_MINSTOK_002_minimum_stok_nol(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);
            $this->submitProductWithMinStok($browser, 0);

            $url = $browser->driver->getCurrentURL();
            $browser->screenshot('BVA-MINSTOK-002');
            $this->assertStringNotContainsString('/products/create', $url,
                'BVA-MINSTOK-002: Minimum stok 0 seharusnya diterima (batas minimum)');
        });
    }

    /**
     * BVA-MINSTOK-003 | Di atas minimum (1)
     * Minimum stok 1 → Valid
     */
    public function test_BVA_MINSTOK_003_minimum_stok_satu(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);
            $this->submitProductWithMinStok($browser, 1);

            $url = $browser->driver->getCurrentURL();
            $browser->screenshot('BVA-MINSTOK-003');
            $this->assertStringNotContainsString('/products/create', $url,
                'BVA-MINSTOK-003: Minimum stok 1 seharusnya diterima');
        });
    }

    /**
     * BVA-MINSTOK-004 | Nilai besar (9999)
     * Minimum stok 9999 → Valid
     */
    public function test_BVA_MINSTOK_004_minimum_stok_nilai_besar(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);
            $this->submitProductWithMinStok($browser, 9999);

            $url = $browser->driver->getCurrentURL();
            $browser->screenshot('BVA-MINSTOK-004');
            $this->assertStringNotContainsString('/products/create', $url,
                'BVA-MINSTOK-004: Minimum stok 9999 seharusnya diterima');
        });
    }
}

