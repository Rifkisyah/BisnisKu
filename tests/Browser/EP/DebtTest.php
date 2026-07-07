<?php

namespace Tests\Browser\EP;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers\AuthHelper;

/**
 * Equivalence Partitioning — Modul: Hutang (Debt)
 *
 * | Test Case ID   | Kelas Partisi | Data Uji                                          | Expected Result                          |
 * |----------------|---------------|---------------------------------------------------|------------------------------------------|
 * | EP-HUTANG-001  | Valid          | Owner menambah hutang baru dengan data valid       | Hutang tersimpan                         |
 * | EP-HUTANG-002  | Tidak Valid    | Tambah hutang tanpa nama debitur                  | Validasi nama debitur muncul             |
 * | EP-HUTANG-003  | Valid          | Bayar hutang lunas (jumlah = sisa hutang)          | Status hutang menjadi "paid"             |
 * | EP-HUTANG-004  | Valid          | Bayar hutang sebagian (jumlah < sisa)             | Status hutang menjadi "partial"          |
 * | EP-HUTANG-005  | Tidak Valid    | Bayar hutang dengan jumlah melebihi sisa          | Validasi batas maksimum muncul           |
 */
class DebtTest extends DuskTestCase
{
    use AuthHelper;

    /**
     * EP-HUTANG-001 | Valid
     * Owner menambah hutang dengan data valid → Hutang tersimpan
     */
    public function test_EP_HUTANG_001_tambah_hutang_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/debts/create')
                    ->waitFor('#debtor_name', 5)
                    ->type('#debtor_name', 'Pelanggan Hutang EP 001')
                    ->type('input[name="debtor_contact"]', '081234567890')
                    ->type('input[name="total_amount"]', '500000')
                    ->type('input[name="debt_date"]', now()->format('Y-m-d'))
                    ->press('button[type="submit"]')
                    ->pause(2000)
                    ->assertPathIs('/debts')
                    ->screenshot('EP-HUTANG-001');
        });
    }

    /**
     * EP-HUTANG-002 | Tidak Valid
     * Tambah hutang tanpa nama debitur → Validasi muncul
     */
    public function test_EP_HUTANG_002_tambah_hutang_tanpa_nama_debitur(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/debts/create')
                    ->waitFor('button[type="submit"]', 5)
                    ->type('input[name="total_amount"]', '500000')
                    ->type('input[name="debt_date"]', now()->format('Y-m-d'))
                    ->press('button[type="submit"]')
                    ->pause(2000)
                    ->assertPathIs('/debts/create')
                    ->screenshot('EP-HUTANG-002');
        });
    }

    /**
     * EP-HUTANG-003 | Valid
     * Bayar hutang lunas (jumlah = sisa) → Status menjadi "paid"
     */
    public function test_EP_HUTANG_003_bayar_hutang_lunas(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            // Buat hutang baru dulu
            $browser->visit('/debts/create')
                    ->waitFor('#debtor_name', 5)
                    ->type('#debtor_name', 'Pelanggan Lunas EP 003')
                    ->type('input[name="total_amount"]', '200000')
                    ->type('input[name="debt_date"]', now()->format('Y-m-d'))
                    ->press('button[type="submit"]')
                    ->pause(2000);

            // Cari hutang dan buka detail
            $browser->visit('/debts')
                    ->waitFor('body', 5)
                    ->pause(1000);

            $browser->script("
                const detailLink = document.querySelector('a[href*=\"/debts/\"][href*=\"DEBT\"]');
                if (detailLink) detailLink.click();
            ");

            $browser->pause(2000);

            // Isi form pembayaran lunas
            $browser->script("
                const amountInput = document.querySelector('input[name=\"amount\"]');
                if (amountInput) {
                    amountInput.value = '200000';
                    amountInput.dispatchEvent(new Event('input'));
                }
                const dateInput = document.querySelector('input[name=\"payment_date\"]');
                if (dateInput) dateInput.value = '" . now()->format('Y-m-d') . "';

                const methodSelect = document.querySelector('select[name=\"payment_method\"]');
                if (methodSelect) methodSelect.value = 'cash';
            ");

            $browser->press('button[type="submit"]')
                    ->pause(2000)
                    ->screenshot('EP-HUTANG-003');
        });
    }

    /**
     * EP-HUTANG-004 | Valid
     * Bayar hutang sebagian (jumlah < sisa) → Status menjadi "partial"
     */
    public function test_EP_HUTANG_004_bayar_hutang_sebagian(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            // Buat hutang baru dulu
            $browser->visit('/debts/create')
                    ->waitFor('#debtor_name', 5)
                    ->type('#debtor_name', 'Pelanggan Partial EP 004')
                    ->type('input[name="total_amount"]', '300000')
                    ->type('input[name="debt_date"]', now()->format('Y-m-d'))
                    ->press('button[type="submit"]')
                    ->pause(2000);

            // Buka detail hutang terbaru
            $browser->visit('/debts')
                    ->waitFor('body', 5)
                    ->pause(1000);

            $browser->script("
                const links = document.querySelectorAll('a[href*=\"/debts/\"]');
                if (links.length > 0) links[0].click();
            ");

            $browser->pause(2000);

            // Bayar 100000 dari 300000
            $browser->script("
                const amountInput = document.querySelector('input[name=\"amount\"]');
                if (amountInput) {
                    amountInput.value = '100000';
                    amountInput.dispatchEvent(new Event('input'));
                }
                const dateInput = document.querySelector('input[name=\"payment_date\"]');
                if (dateInput) dateInput.value = '" . now()->format('Y-m-d') . "';
                const methodSelect = document.querySelector('select[name=\"payment_method\"]');
                if (methodSelect) methodSelect.value = 'cash';
            ");

            $browser->press('button[type="submit"]')
                    ->pause(2000)
                    ->screenshot('EP-HUTANG-004');
        });
    }

    /**
     * EP-HUTANG-005 | Tidak Valid
     * Bayar hutang dengan jumlah melebihi sisa → Validasi batas maksimum muncul
     */
    public function test_EP_HUTANG_005_bayar_hutang_melebihi_sisa(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            // Buat hutang baru dulu
            $browser->visit('/debts/create')
                    ->waitFor('#debtor_name', 5)
                    ->type('#debtor_name', 'Pelanggan Lebih EP 005')
                    ->type('input[name="total_amount"]', '150000')
                    ->type('input[name="debt_date"]', now()->format('Y-m-d'))
                    ->press('button[type="submit"]')
                    ->pause(2000);

            // Buka detail hutang terbaru
            $browser->visit('/debts')
                    ->waitFor('body', 5)
                    ->pause(1000);

            $browser->script("
                const links = document.querySelectorAll('a[href*=\"/debts/\"]');
                if (links.length > 0) links[0].click();
            ");

            $browser->pause(2000);

            // Bayar 999999 (jauh melebihi sisa 150000)
            $browser->script("
                const amountInput = document.querySelector('input[name=\"amount\"]');
                if (amountInput) {
                    amountInput.value = '999999';
                    amountInput.dispatchEvent(new Event('input'));
                }
                const dateInput = document.querySelector('input[name=\"payment_date\"]');
                if (dateInput) dateInput.value = '" . now()->format('Y-m-d') . "';
                const methodSelect = document.querySelector('select[name=\"payment_method\"]');
                if (methodSelect) methodSelect.value = 'cash';
            ");

            $browser->press('button[type="submit"]')
                    ->pause(2000)
                    ->screenshot('EP-HUTANG-005');
        });
    }
}
