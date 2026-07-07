<?php

namespace Tests\Browser\BVA;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers\AuthHelper;

/**
 * Boundary Value Analysis — Field: Harga Jual Produk (selling_price)
 *
 * Aturan Validasi: required|numeric|min:0|gte:purchase_price
 * Catatan: harga beli ditetapkan 50000, maka harga jual min = 50000
 *          Uji nilai batas: 0 (di bawah harga beli), 49999, 50000, 50001, dan nilai sangat besar
 *
 * | Test Case ID   | Batas Nilai    | Nilai Uji | Jenis Batas           | Expected Result            |
 * |----------------|----------------|-----------|-----------------------|----------------------------|
 * | BVA-HARGA-001  | ≥ harga_beli   | 0         | Di bawah harga beli   | Validasi harga muncul      |
 * | BVA-HARGA-002  | ≥ harga_beli   | 49999     | Di bawah harga beli   | Validasi harga muncul      |
 * | BVA-HARGA-003  | ≥ harga_beli   | 50000     | Sama dengan harga beli| Produk tersimpan           |
 * | BVA-HARGA-004  | ≥ harga_beli   | 50001     | Di atas harga beli    | Produk tersimpan           |
 * | BVA-HARGA-005  | ≥ harga_beli   | 999999999 | Nilai sangat besar    | Produk tersimpan           |
 */
class HargaJualBVATest extends DuskTestCase
{
    use AuthHelper;

    /**
     * Helper: isi form tambah produk dengan harga jual tertentu dan submit
     */
    private function submitProductWithHargaJual(Browser $browser, int|float $hargaJual): void
    {
        $browser->visit('/products/create?type=physical')
                ->waitFor('#name', 5)
                ->type('#name', 'Produk BVA Harga ' . $hargaJual)
                ->type('input[name="purchase_price"]', '50000')
                ->type('input[name="selling_price"]', (string) $hargaJual)
                ->type('input[name="minimum_stock"]', '1');

        // Pilih kategori pertama
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
     * BVA-HARGA-001 | Di bawah harga beli (0)
     * Harga jual 0 < harga beli 50000 → Validasi muncul
     */
    public function test_BVA_HARGA_001_harga_jual_di_bawah_harga_beli_nol(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);
            $this->submitProductWithHargaJual($browser, 0);

            $url = $browser->driver->getCurrentURL();
            $browser->screenshot('BVA-HARGA-001');
            $this->assertStringContainsString('/products/create', $url,
                'BVA-HARGA-001: Harga jual 0 seharusnya ditolak (di bawah harga beli 50000)');
        });
    }

    /**
     * BVA-HARGA-002 | Di bawah harga beli (49999)
     * Harga jual 49999 < harga beli 50000 → Validasi muncul
     */
    public function test_BVA_HARGA_002_harga_jual_satu_di_bawah_harga_beli(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);
            $this->submitProductWithHargaJual($browser, 49999);

            $url = $browser->driver->getCurrentURL();
            $browser->screenshot('BVA-HARGA-002');
            $this->assertStringContainsString('/products/create', $url,
                'BVA-HARGA-002: Harga jual 49999 seharusnya ditolak (di bawah harga beli 50000)');
        });
    }

    /**
     * BVA-HARGA-003 | Sama dengan harga beli (50000)
     * Harga jual = harga beli → Valid, produk tersimpan
     */
    public function test_BVA_HARGA_003_harga_jual_sama_dengan_harga_beli(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);
            $this->submitProductWithHargaJual($browser, 50000);

            $url = $browser->driver->getCurrentURL();
            $browser->screenshot('BVA-HARGA-003');
            $this->assertStringNotContainsString('/products/create', $url,
                'BVA-HARGA-003: Harga jual 50000 (= harga beli) seharusnya diterima');
        });
    }

    /**
     * BVA-HARGA-004 | Di atas harga beli (50001)
     * Harga jual 50001 > harga beli 50000 → Valid, produk tersimpan
     */
    public function test_BVA_HARGA_004_harga_jual_satu_di_atas_harga_beli(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);
            $this->submitProductWithHargaJual($browser, 50001);

            $url = $browser->driver->getCurrentURL();
            $browser->screenshot('BVA-HARGA-004');
            $this->assertStringNotContainsString('/products/create', $url,
                'BVA-HARGA-004: Harga jual 50001 seharusnya diterima');
        });
    }

    /**
     * BVA-HARGA-005 | Nilai sangat besar (999999999)
     * Harga jual 999999999 → Valid (tidak ada batas atas), produk tersimpan
     */
    public function test_BVA_HARGA_005_harga_jual_sangat_besar(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);
            $this->submitProductWithHargaJual($browser, 999999999);

            $url = $browser->driver->getCurrentURL();
            $browser->screenshot('BVA-HARGA-005');
            $this->assertStringNotContainsString('/products/create', $url,
                'BVA-HARGA-005: Harga jual 999999999 seharusnya diterima');
        });
    }
}
