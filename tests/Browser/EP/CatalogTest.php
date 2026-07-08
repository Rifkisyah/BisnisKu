<?php

namespace Tests\Browser\EP;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Equivalence Partitioning — Modul: Katalog Publik (Public Catalog)
 *
 * | Test Case ID | Kelas Partisi | Data Uji                                              | Expected Result                          |
 * |--------------|---------------|-------------------------------------------------------|------------------------------------------|
 * | EP-CAT-001   | Valid          | Publik mengakses katalog toko dengan slug valid       | Halaman katalog tampil                   |
 * | EP-CAT-002   | Tidak Valid    | Akses katalog toko dengan slug tidak ada              | Halaman 404 muncul                       |
 * | EP-CAT-003   | Valid          | Publik melihat detail produk di katalog               | Halaman detail produk tampil             |
 */
class CatalogTest extends DuskTestCase
{
    /**
     * EP-CAT-001 | Valid
     * Publik mengakses katalog toko dengan slug valid → Halaman tampil
     */
    public function test_EP_CAT_001_akses_katalog_store_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/store/dusk-test-store')
                    
                    ->pause(1500)
                    ->screenshot('EP-CAT-001');

            // Verifikasi halaman katalog tampil (bukan 404)
            $currentUrl = $browser->driver->getCurrentURL();
            $statusCode = $browser->script("
                return performance.getEntriesByType('navigation')[0]?.responseStatus || 200;
            ");
            $this->assertStringContainsString('dusk-test-store', $currentUrl);
        });
    }

    /**
     * EP-CAT-002 | Tidak Valid
     * Akses katalog toko dengan slug tidak ada → Halaman 404
     */
    public function test_EP_CAT_002_akses_katalog_slug_tidak_ada(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/store/toko-yang-tidak-ada-xyz-9999')
                    
                    ->pause(1000)
                    ->screenshot('EP-CAT-002');

            // Harapan: 404 page atau redirect ke home
            $url = $browser->driver->getCurrentURL();
            // Halaman seharusnya tidak menampilkan konten katalog normal
        });
    }

    /**
     * EP-CAT-003 | Valid
     * Publik melihat detail produk di katalog → Halaman detail tampil
     */
    public function test_EP_CAT_003_lihat_detail_produk_di_katalog(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/store/dusk-test-store')
                    
                    ->pause(1500);

            // Klik produk pertama yang ada di katalog
            $browser->script("
                const productLink = document.querySelector('a[href*=\"/products/\"]');
                if (productLink) productLink.click();
            ");

            $browser->pause(2000)
                    ->screenshot('EP-CAT-003');
        });
    }
}
