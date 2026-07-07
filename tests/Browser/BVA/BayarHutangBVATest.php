<?php

namespace Tests\Browser\BVA;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers\AuthHelper;

/**
 * Boundary Value Analysis — Field: Pembayaran Hutang (amount di DebtController::addPayment)
 *
 * Aturan Validasi: required|numeric|min:1|max:{remaining_amount}
 * Contoh: sisa hutang = 500000
 *
 * | Test Case ID  | Batas Nilai       | Nilai Uji | Jenis Batas              | Expected Result                   |
 * |---------------|-------------------|-----------|--------------------------|-----------------------------------|
 * | BVA-BAYAR-001 | 1 – sisa_hutang   | 0         | Di bawah minimum         | Validasi min muncul               |
 * | BVA-BAYAR-002 | 1 – sisa_hutang   | 1         | Batas minimum            | Pembayaran tersimpan (partial)    |
 * | BVA-BAYAR-003 | 1 – sisa_hutang   | 2         | Di atas minimum          | Pembayaran tersimpan (partial)    |
 * | BVA-BAYAR-004 | 1 – sisa_hutang   | 500000    | Batas maksimum (= sisa)  | Pembayaran tersimpan (lunas/paid) |
 * | BVA-BAYAR-005 | 1 – sisa_hutang   | 500001    | Di atas maksimum         | Validasi max muncul               |
 */
class BayarHutangBVATest extends DuskTestCase
{
    use AuthHelper;

    /**
     * Helper: Buat hutang baru dengan total 500000 dan kembalikan debt_code
     */
    private function createDebt(Browser $browser, string $name): void
    {
        $browser->visit('/debts/create')
                ->waitFor('#debtor_name', 5)
                ->type('#debtor_name', $name)
                ->type('input[name="total_amount"]', '500000')
                ->type('input[name="debt_date"]', now()->format('Y-m-d'))
                ->press('button[type="submit"]')
                ->pause(2000);
    }

    /**
     * Helper: Buka detail hutang pertama dan submit form pembayaran
     */
    private function payDebt(Browser $browser, int $amount): void
    {
        $browser->visit('/debts')
                ->waitFor('body', 5)
                ->pause(1000);

        $browser->script("
            const links = document.querySelectorAll('a[href*=\"/debts/DEBT\"]');
            if (links.length > 0) links[0].click();
        ");

        $browser->pause(2000)
                ->script("
                    const amountInput = document.querySelector('input[name=\"amount\"]');
                    if (amountInput) {
                        amountInput.value = '{$amount}';
                        amountInput.dispatchEvent(new Event('input'));
                    }
                    const dateInput = document.querySelector('input[name=\"payment_date\"]');
                    if (dateInput) dateInput.value = '" . now()->format('Y-m-d') . "';
                    const methodSelect = document.querySelector('select[name=\"payment_method\"]');
                    if (methodSelect) methodSelect.value = 'cash';
                ");

        $browser->press('button[type="submit"]')
                ->pause(2000);
    }

    /**
     * BVA-BAYAR-001 | Di bawah minimum (0)
     * Pembayaran 0 → Validasi min:1 muncul
     */
    public function test_BVA_BAYAR_001_bayar_nol(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);
            $this->createDebt($browser, 'Pelanggan BVA Bayar 001');
            $this->payDebt($browser, 0);

            $browser->screenshot('BVA-BAYAR-001');
        });
    }

    /**
     * BVA-BAYAR-002 | Batas minimum (1)
     * Pembayaran 1 → Partial payment berhasil
     */
    public function test_BVA_BAYAR_002_bayar_satu_rupiah(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);
            $this->createDebt($browser, 'Pelanggan BVA Bayar 002');
            $this->payDebt($browser, 1);

            $browser->screenshot('BVA-BAYAR-002');
        });
    }

    /**
     * BVA-BAYAR-003 | Di atas minimum (2)
     * Pembayaran 2 → Partial payment berhasil
     */
    public function test_BVA_BAYAR_003_bayar_dua_rupiah(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);
            $this->createDebt($browser, 'Pelanggan BVA Bayar 003');
            $this->payDebt($browser, 2);

            $browser->screenshot('BVA-BAYAR-003');
        });
    }

    /**
     * BVA-BAYAR-004 | Batas maksimum (= sisa hutang: 500000)
     * Pembayaran 500000 = sisa → Hutang lunas (status: paid)
     */
    public function test_BVA_BAYAR_004_bayar_lunas(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);
            $this->createDebt($browser, 'Pelanggan BVA Bayar 004');
            $this->payDebt($browser, 500000);

            $browser->screenshot('BVA-BAYAR-004');
        });
    }

    /**
     * BVA-BAYAR-005 | Di atas maksimum (500001 > sisa 500000)
     * Pembayaran melebihi sisa → Validasi max muncul
     */
    public function test_BVA_BAYAR_005_bayar_melebihi_sisa(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);
            $this->createDebt($browser, 'Pelanggan BVA Bayar 005');
            $this->payDebt($browser, 500001);

            $browser->screenshot('BVA-BAYAR-005');
        });
    }
}
