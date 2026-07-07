<?php

namespace Tests\Browser\BVA;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers\AuthHelper;

/**
 * Boundary Value Analysis — Field: Nama Produk (name)
 *
 * Aturan Validasi: required|string|min:3|max:100
 *
 * | Test Case ID  | Batas Nilai | Nilai Uji            | Jenis Batas       | Expected Result         |
 * |---------------|-------------|----------------------|-------------------|-------------------------|
 * | BVA-NAMA-001  | 3–100 char  | 2 char ("AB")        | Di bawah minimum  | Validasi min muncul     |
 * | BVA-NAMA-002  | 3–100 char  | 3 char ("ABC")       | Batas minimum     | Produk tersimpan        |
 * | BVA-NAMA-003  | 3–100 char  | 100 char (100×"A")   | Batas maksimum    | Produk tersimpan        |
 * | BVA-NAMA-004  | 3–100 char  | 101 char (101×"A")   | Di atas maksimum  | Validasi max muncul     |
 */
class NamaProdukBVATest extends DuskTestCase
{
    use AuthHelper;

    private function submitProductWithName(Browser $browser, string $name): void
    {
        $browser->visit('/products/create?type=physical')
                ->waitFor('input[name="name"], #name', 5)
                ->type('input[name="name"]', $name)
                ->type('input[name="purchase_price"]', '10000')
                ->type('input[name="selling_price"]', '15000')
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
     * BVA-NAMA-001 | Di bawah minimum (2 karakter)
     * Nama "AB" (2 char) → Validasi min:3 muncul
     */
    public function test_BVA_NAMA_001_nama_terlalu_pendek(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);
            $this->submitProductWithName($browser, 'AB');

            $url = $browser->driver->getCurrentURL();
            $browser->screenshot('BVA-NAMA-001');
            $this->assertStringContainsString('/products/create', $url,
                'BVA-NAMA-001: Nama 2 char seharusnya ditolak (di bawah min:3)');
        });
    }

    /**
     * BVA-NAMA-002 | Batas minimum (3 karakter)
     * Nama "ABC" (3 char) → Valid, produk tersimpan
     */
    public function test_BVA_NAMA_002_nama_tiga_karakter(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);
            $this->submitProductWithName($browser, 'ABC');

            $url = $browser->driver->getCurrentURL();
            $browser->screenshot('BVA-NAMA-002');
            $this->assertStringNotContainsString('/products/create', $url,
                'BVA-NAMA-002: Nama 3 char seharusnya diterima (batas minimum)');
        });
    }

    /**
     * BVA-NAMA-003 | Batas maksimum (100 karakter)
     * Nama 100 char → Valid, produk tersimpan
     */
    public function test_BVA_NAMA_003_nama_seratus_karakter(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);
            $namaSepuluhKali = 'ProdukNama'; // 10 char
            $namaSeratus = str_repeat($namaSepuluhKali, 10); // 100 char

            $this->submitProductWithName($browser, $namaSeratus);

            $url = $browser->driver->getCurrentURL();
            $browser->screenshot('BVA-NAMA-003');
            $this->assertStringNotContainsString('/products/create', $url,
                'BVA-NAMA-003: Nama 100 char seharusnya diterima (batas maksimum)');
        });
    }

    /**
     * BVA-NAMA-004 | Di atas maksimum (101 karakter)
     * Nama 101 char → Validasi max:100 muncul
     */
    public function test_BVA_NAMA_004_nama_seratus_satu_karakter(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);
            $namaSeratusLebih = str_repeat('A', 101); // 101 char

            $this->submitProductWithName($browser, $namaSeratusLebih);

            $url = $browser->driver->getCurrentURL();
            $browser->screenshot('BVA-NAMA-004');
            $this->assertStringContainsString('/products/create', $url,
                'BVA-NAMA-004: Nama 101 char seharusnya ditolak (melebihi max:100)');
        });
    }
}
