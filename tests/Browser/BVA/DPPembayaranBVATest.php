<?php

namespace Tests\Browser\BVA;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers\AuthHelper;

/**
 * Boundary Value Analysis — Field: DP Pembayaran Service Repair (down_payment)
 *
 * Aturan Validasi: nullable|numeric|min:0
 * Catatan bisnis: DP tidak boleh melebihi total biaya service
 *   Contoh: service_fee = 150000, maka DP max = 150000
 *
 * | Test Case ID | Batas Nilai      | Nilai Uji | Jenis Batas            | Expected Result                  |
 * |--------------|------------------|-----------|------------------------|----------------------------------|
 * | BVA-DP-001   | 0 – total_biaya  | -1        | Di bawah minimum       | Validasi muncul                  |
 * | BVA-DP-002   | 0 – total_biaya  | 0         | Batas minimum (no DP)  | Service repair tersimpan         |
 * | BVA-DP-003   | 0 – total_biaya  | 1         | Di atas minimum        | Service repair tersimpan         |
 * | BVA-DP-004   | 0 – total_biaya  | 150000    | Batas maksimum (=total)| Service repair tersimpan (lunas) |
 * | BVA-DP-005   | 0 – total_biaya  | 150001    | Di atas maksimum       | Validasi DP melebihi biaya       |
 */
class DPPembayaranBVATest extends DuskTestCase
{
    use AuthHelper;

    /**
     * Helper: Buat service repair dan set down_payment via update
     */
    private function createServiceRepairAndSetDP(Browser $browser, int $dp): void
    {
        // Buat service repair baru
        $browser->visit('/service-repairs/create')
                ->waitFor('#customer_name', 5)
                ->type('#customer_name', 'Pelanggan DP BVA ' . $dp)
                ->type('input[name="items[0][name]"]', 'Perangkat Test')
                ->type('textarea[name="items[0][complaint]"]', 'Test DP BVA')
                ->type('input[name="items[0][service_fee"]', '150000')
                ->click('form:not([action*="locale"]):not([action$="logout"]) button[type="submit"]')
                ->pause(3000);
    }

    /**
     * BVA-DP-001 | Di bawah minimum (-1)
     * DP -1 → Validasi muncul
     */
    public function test_BVA_DP_001_dp_negatif(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/service-repairs/create')
                    ->waitFor('#customer_name', 5)
                    ->type('#customer_name', 'DP BVA Negatif')
                    ->type('input[name="items[0][name]"]', 'Device Test DP')
                    ->type('textarea[name="items[0][complaint]"]', 'Test keluhan')
                    ->click('form:not([action*="locale"]):not([action$="logout"]) button[type="submit"]')
                    ->pause(3000);

            // Buka edit service repair dan set DP = -1
            $browser->visit('/service-repairs')
                    
                    ->pause(1000);

            $browser->script("
                const editLink = document.querySelector('a[href*=\"/service-repairs/\"][href*=\"/edit\"]');
                if (editLink) editLink.click();
            ");

            $browser->pause(2000)
                    ->type('input[name="down_payment"]', '-1')
                    ->click('form:not([action*="locale"]):not([action$="logout"]) button[type="submit"]')
                    ->pause(2000)
                    ->screenshot('BVA-DP-001');
        });
    }

    /**
     * BVA-DP-002 | Batas minimum (0)
     * DP 0 (tidak ada DP) → Valid
     */
    public function test_BVA_DP_002_dp_nol(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/service-repairs/create')
                    ->waitFor('#customer_name', 5)
                    ->type('#customer_name', 'DP BVA Nol')
                    ->type('input[name="items[0][name]"]', 'Device DP Nol')
                    ->type('textarea[name="items[0][complaint]"]', 'Test keluhan')
                    ->click('form:not([action*="locale"]):not([action$="logout"]) button[type="submit"]')
                    ->pause(3000);

            $browser->visit('/service-repairs')
                    
                    ->pause(1000);

            $browser->script("
                const editLink = document.querySelector('a[href*=\"/service-repairs/\"][href*=\"/edit\"]');
                if (editLink) editLink.click();
            ");

            $browser->pause(2000)
                    ->type('input[name="down_payment"]', '0')
                    ->click('form:not([action*="locale"]):not([action$="logout"]) button[type="submit"]')
                    ->pause(2000)
                    ->screenshot('BVA-DP-002');
        });
    }

    /**
     * BVA-DP-003 | Di atas minimum (1)
     * DP 1 → Valid
     */
    public function test_BVA_DP_003_dp_satu(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/service-repairs/create')
                    ->waitFor('#customer_name', 5)
                    ->type('#customer_name', 'DP BVA Satu')
                    ->type('input[name="items[0][name]"]', 'Device DP Satu')
                    ->type('textarea[name="items[0][complaint]"]', 'Test keluhan')
                    ->click('form:not([action*="locale"]):not([action$="logout"]) button[type="submit"]')
                    ->pause(3000);

            $browser->visit('/service-repairs')
                    
                    ->pause(1000);

            $browser->script("
                const editLink = document.querySelector('a[href*=\"/service-repairs/\"][href*=\"/edit\"]');
                if (editLink) editLink.click();
            ");

            $browser->pause(2000)
                    ->type('input[name="down_payment"]', '1')
                    ->click('form:not([action*="locale"]):not([action$="logout"]) button[type="submit"]')
                    ->pause(2000)
                    ->screenshot('BVA-DP-003');
        });
    }

    /**
     * BVA-DP-004 | Batas maksimum (= total biaya service)
     * DP = total biaya → Service lunas DP
     */
    public function test_BVA_DP_004_dp_sama_dengan_total_biaya(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/service-repairs/create')
                    ->waitFor('#customer_name', 5)
                    ->type('#customer_name', 'DP BVA Total')
                    ->type('input[name="items[0][name]"]', 'Device DP Total')
                    ->type('input[name="items[0][service_fee"]', '150000')
                    ->type('textarea[name="items[0][complaint]"]', 'Test keluhan DP max')
                    ->click('form:not([action*="locale"]):not([action$="logout"]) button[type="submit"]')
                    ->pause(3000);

            $browser->visit('/service-repairs')
                    
                    ->pause(1000);

            $browser->script("
                const editLink = document.querySelector('a[href*=\"/service-repairs/\"][href*=\"/edit\"]');
                if (editLink) editLink.click();
            ");

            $browser->pause(2000)
                    ->type('input[name="down_payment"]', '150000')
                    ->click('form:not([action*="locale"]):not([action$="logout"]) button[type="submit"]')
                    ->pause(2000)
                    ->screenshot('BVA-DP-004');
        });
    }

    /**
     * BVA-DP-005 | Di atas maksimum (total_biaya + 1)
     * DP melebihi total biaya → Validasi muncul
     */
    public function test_BVA_DP_005_dp_melebihi_total_biaya(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/service-repairs/create')
                    ->waitFor('#customer_name', 5)
                    ->type('#customer_name', 'DP BVA Lebih')
                    ->type('input[name="items[0][name]"]', 'Device DP Lebih')
                    ->type('input[name="items[0][service_fee"]', '150000')
                    ->type('textarea[name="items[0][complaint]"]', 'Test keluhan DP over')
                    ->click('form:not([action*="locale"]):not([action$="logout"]) button[type="submit"]')
                    ->pause(3000);

            $browser->visit('/service-repairs')
                    
                    ->pause(1000);

            $browser->script("
                const editLink = document.querySelector('a[href*=\"/service-repairs/\"][href*=\"/edit\"]');
                if (editLink) editLink.click();
            ");

            $browser->pause(2000)
                    ->type('input[name="down_payment"]', '150001')
                    ->click('form:not([action*="locale"]):not([action$="logout"]) button[type="submit"]')
                    ->pause(2000)
                    ->screenshot('BVA-DP-005');
        });
    }
}

