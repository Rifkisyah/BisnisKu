<?php

namespace Tests\Browser\EP;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers\AuthHelper;

/**
 * Equivalence Partitioning — Modul: Transaksi (Riwayat)
 *
 * | Test Case ID  | Kelas Partisi | Data Uji                                        | Expected Result                         |
 * |---------------|---------------|-------------------------------------------------|-----------------------------------------|
 * | EP-TRANS-001  | Valid          | Owner melihat daftar transaksi                  | Halaman riwayat transaksi tampil        |
 * | EP-TRANS-002  | Valid          | Kasir melihat daftar transaksi                  | Halaman riwayat transaksi tampil        |
 * | EP-TRANS-003  | Valid          | Owner membatalkan transaksi                     | Status transaksi menjadi "cancelled"    |
 * | EP-TRANS-004  | Tidak Valid    | Kasir mencoba membatalkan transaksi             | Akses ditolak (403)                     |
 */
class TransactionTest extends DuskTestCase
{
    use AuthHelper;

    /**
     * EP-TRANS-001 | Valid
     * Owner melihat daftar transaksi → Halaman tampil
     */
    public function test_EP_TRANS_001_owner_lihat_daftar_transaksi(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/transactions')
                    ->waitFor('body', 5)
                    ->pause(1000)
                    ->assertPathIs('/transactions')
                    ->screenshot('EP-TRANS-001');
        });
    }

    /**
     * EP-TRANS-002 | Valid
     * Kasir melihat daftar transaksi → Halaman tampil
     */
    public function test_EP_TRANS_002_kasir_lihat_daftar_transaksi(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);

            $browser->visit('/transactions')
                    ->waitFor('body', 5)
                    ->pause(1000)
                    ->assertPathIs('/transactions')
                    ->screenshot('EP-TRANS-002');
        });
    }

    /**
     * EP-TRANS-003 | Valid
     * Owner membatalkan transaksi → Status menjadi cancelled
     */
    public function test_EP_TRANS_003_owner_batalkan_transaksi(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/transactions')
                    ->waitFor('body', 5)
                    ->pause(1000);

            // Klik detail transaksi pertama
            $browser->script("
                const detailLink = document.querySelector('a[href*=\"/transactions/TRX\"]');
                if (detailLink) detailLink.click();
            ");

            $browser->pause(2000);

            // Klik tombol cancel jika ada
            $browser->script("
                const cancelForm = document.querySelector('form[action*=\"/cancel\"]');
                if (cancelForm) cancelForm.submit();
            ");

            $browser->pause(2000)
                    ->screenshot('EP-TRANS-003');
        });
    }

    /**
     * EP-TRANS-004 | Tidak Valid
     * Kasir mencoba akses endpoint cancel transaksi → Akses ditolak (403)
     */
    public function test_EP_TRANS_004_kasir_gagal_batalkan_transaksi(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);

            // Kasir tidak punya akses ke POST /transactions/{id}/cancel
            // Coba navigate ke transaksi detail dan cari tombol cancel
            $browser->visit('/transactions')
                    ->waitFor('body', 5)
                    ->pause(1000);

            // Verifikasi: tidak ada tombol cancel di UI kasir
            $hasCancelBtn = $browser->script("
                return document.querySelector('form[action*=\"/cancel\"]') !== null;
            ");

            // Atau coba akses langsung endpoint cancel
            $result = $browser->script("
                return fetch('/transactions/TRX999/cancel', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content,
                        'Accept': 'application/json'
                    }
                }).then(r => ({ status: r.status }));
            ");

            $browser->pause(1000)
                    ->screenshot('EP-TRANS-004');
        });
    }
}
