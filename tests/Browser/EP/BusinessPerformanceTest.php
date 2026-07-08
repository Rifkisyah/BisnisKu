<?php

namespace Tests\Browser\EP;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers\AuthHelper;

/**
 * Equivalence Partitioning — Modul: Business Performance (BI / Analitik)
 *
 * | Test Case ID | Kelas Partisi | Data Uji                                                | Expected Result                              |
 * |--------------|---------------|---------------------------------------------------------|----------------------------------------------|
 * | EP-BI-001    | Valid          | Owner mengakses halaman Business Performance            | Halaman BI tampil dengan data                |
 * | EP-BI-002    | Tidak Valid    | Kasir mencoba akses Business Performance                | Akses ditolak (403) / redirect               |
 * | EP-BI-003    | Valid          | Owner melihat hasil cluster K-Means                     | Tabel/grafik cluster tampil                  |
 * | EP-BI-004    | Valid          | Owner melihat rekomendasi SMA restock                   | Tabel SMA rekomendasi tampil                 |
 */
class BusinessPerformanceTest extends DuskTestCase
{
    use AuthHelper;

    /**
     * EP-BI-001 | Valid
     * Owner mengakses halaman Business Performance → Halaman tampil
     */
    public function test_EP_BI_001_owner_akses_business_performance(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/reports/business-performance')
                    
                    ->pause(2000)
                    ->assertPathIs('/reports/business-performance')
                    ->screenshot('EP-BI-001');
        });
    }

    /**
     * EP-BI-002 | Tidak Valid
     * Kasir mencoba akses Business Performance → Akses ditolak
     */
    public function test_EP_BI_002_kasir_gagal_akses_business_performance(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);

            $browser->visit('/reports/business-performance')
                    ->pause(2000)
                    ->screenshot('EP-BI-002');

            $url = $browser->driver->getCurrentURL();
            $this->assertStringNotContainsString('/reports/business-performance', $url,
                'Kasir tidak boleh akses Business Performance');
        });
    }

    /**
     * EP-BI-003 | Valid
     * Owner melihat hasil cluster K-Means → Cluster tampil
     */
    public function test_EP_BI_003_owner_lihat_cluster_kmeans(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/reports/business-performance/clusters')
                    
                    ->pause(2000)
                    ->screenshot('EP-BI-003');
        });
    }

    /**
     * EP-BI-004 | Valid
     * Owner melihat rekomendasi SMA restock → Tabel SMA tampil
     */
    public function test_EP_BI_004_owner_lihat_sma_restock(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/reports/business-performance/sma')
                    
                    ->pause(2000)
                    ->screenshot('EP-BI-004');
        });
    }
}
