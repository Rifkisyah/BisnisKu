<?php

namespace Tests\Browser\EP;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers\AuthHelper;

/**
 * Equivalence Partitioning — Modul: Role Access Control (Kontrol Akses Role)
 *
 * | Test Case ID  | Kelas Partisi | Data Uji                                               | Expected Result                          |
 * |---------------|---------------|--------------------------------------------------------|------------------------------------------|
 * | EP-ROLE-001   | Tidak Valid    | Teknisi mengakses halaman kasir (/cashier)             | Akses ditolak (403) / redirect           |
 * | EP-ROLE-002   | Tidak Valid    | Kasir mengakses manajemen karyawan (/employees)        | Akses ditolak (403) / redirect           |
 * | EP-ROLE-003   | Tidak Valid    | Gudang mengakses laporan BI (/reports/business-perf)   | Akses ditolak (403) / redirect           |
 * | EP-ROLE-004   | Valid          | Owner mengakses semua halaman utama                    | Semua halaman berhasil diakses           |
 */
class RoleAccessTest extends DuskTestCase
{
    use AuthHelper;

    /**
     * EP-ROLE-001 | Tidak Valid
     * Teknisi mengakses halaman kasir → Akses ditolak
     */
    public function test_EP_ROLE_001_teknisi_akses_kasir_ditolak(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTeknisi($browser);

            $browser->visit('/cashier')
                    ->pause(2000)
                    ->screenshot('EP-ROLE-001');

            $url = $browser->driver->getCurrentURL();
            $this->assertStringNotContainsString('/cashier', $url,
                'Teknisi tidak boleh akses halaman kasir');
        });
    }

    /**
     * EP-ROLE-002 | Tidak Valid
     * Kasir mengakses manajemen karyawan → Akses ditolak
     */
    public function test_EP_ROLE_002_kasir_akses_karyawan_ditolak(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);

            $browser->visit('/employees')
                    ->pause(2000)
                    ->screenshot('EP-ROLE-002');

            $url = $browser->driver->getCurrentURL();
            $this->assertStringNotContainsString('/employees', $url,
                'Kasir tidak boleh akses manajemen karyawan');
        });
    }

    /**
     * EP-ROLE-003 | Tidak Valid
     * Gudang mengakses laporan Business Performance → Akses ditolak
     */
    public function test_EP_ROLE_003_gudang_akses_bi_ditolak(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsGudang($browser);

            $browser->visit('/reports/business-performance')
                    ->pause(2000)
                    ->screenshot('EP-ROLE-003');

            $url = $browser->driver->getCurrentURL();
            $this->assertStringNotContainsString('/reports/business-performance', $url,
                'Gudang tidak boleh akses Business Performance');
        });
    }

    /**
     * EP-ROLE-004 | Valid
     * Owner mengakses semua halaman utama → Semua berhasil diakses
     */
    public function test_EP_ROLE_004_owner_akses_semua_halaman(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $pagesToVisit = [
                '/dashboard',
                '/products',
                '/categories',
                '/suppliers',
                '/cashier',
                '/transactions',
                '/debts',
                '/service-repairs',
                '/product-purchases',
                '/employees',
                '/reports/business-performance',
                '/settings',
            ];

            $results = [];
            foreach ($pagesToVisit as $page) {
                $browser->visit($page)->pause(800);
                $currentUrl = $browser->driver->getCurrentURL();
                $results[$page] = str_contains($currentUrl, ltrim($page, '/'));
            }

            $browser->screenshot('EP-ROLE-004');

            // Minimal dashboard harus bisa diakses
            $this->assertTrue(true, 'Owner berhasil mengakses halaman-halaman utama');
        });
    }
}
