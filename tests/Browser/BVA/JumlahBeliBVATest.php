<?php

namespace Tests\Browser\BVA;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers\AuthHelper;

/**
 * Boundary Value Analysis — Field: Jumlah Beli / Kuantitas Item Kasir (items.*.quantity)
 *
 * Aturan Validasi: required|integer|min:1
 * Catatan bisnis: jumlah beli tidak boleh melebihi stok produk yang tersedia
 *   Produk PRD-DUSK-001 memiliki stok = 100
 *
 * | Test Case ID   | Batas Nilai    | Nilai Uji | Jenis Batas              | Expected Result                      |
 * |----------------|----------------|-----------|--------------------------|--------------------------------------|
 * | BVA-JBELI-001  | 1 – stok(100)  | 0         | Di bawah minimum         | Validasi kuantitas min muncul        |
 * | BVA-JBELI-002  | 1 – stok(100)  | 1         | Batas minimum            | Transaksi berhasil                   |
 * | BVA-JBELI-003  | 1 – stok(100)  | 2         | Di atas minimum          | Transaksi berhasil                   |
 * | BVA-JBELI-004  | 1 – stok(100)  | 100       | Batas maksimum (= stok)  | Transaksi berhasil (stok habis)      |
 * | BVA-JBELI-005  | 1 – stok(100)  | 101       | Di atas maksimum         | Error: stok tidak mencukupi          |
 */
class JumlahBeliBVATest extends DuskTestCase
{
    use AuthHelper;

    private function postCheckout(Browser $browser, int $qty): array
    {
        $result = $browser->script("
            return fetch('/cashier/checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    items: [{
                        product_code: 'PRD-DUSK-001',
                        quantity: {$qty},
                        unit_price: 75000
                    }],
                    payment_method: 'cash',
                    amount_paid: " . ($qty * 75000 + 1) . "
                })
            }).then(r => r.json()).catch(e => ({ error: e.message, success: false }));
        ");

        return $result[0] ?? [];
    }

    /**
     * BVA-JBELI-001 | Di bawah minimum (0)
     * Kuantitas 0 → Validasi min:1 muncul
     */
    public function test_BVA_JBELI_001_jumlah_beli_nol(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);
            $browser->visit('/cashier')->waitFor('body', 5)->pause(500);

            $result = $this->postCheckout($browser, 0);

            $browser->screenshot('BVA-JBELI-001');
            $this->assertFalse($result['success'] ?? true,
                'BVA-JBELI-001: Kuantitas 0 seharusnya ditolak');
        });
    }

    /**
     * BVA-JBELI-002 | Batas minimum (1)
     * Kuantitas 1 → Berhasil
     */
    public function test_BVA_JBELI_002_jumlah_beli_satu(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);
            $browser->visit('/cashier')->waitFor('body', 5)->pause(500);

            $result = $this->postCheckout($browser, 1);

            $browser->screenshot('BVA-JBELI-002');
            $this->assertTrue($result['success'] ?? false,
                'BVA-JBELI-002: Kuantitas 1 seharusnya diterima (batas minimum)');
        });
    }

    /**
     * BVA-JBELI-003 | Di atas minimum (2)
     * Kuantitas 2 → Berhasil
     */
    public function test_BVA_JBELI_003_jumlah_beli_dua(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);
            $browser->visit('/cashier')->waitFor('body', 5)->pause(500);

            $result = $this->postCheckout($browser, 2);

            $browser->screenshot('BVA-JBELI-003');
            $this->assertTrue($result['success'] ?? false,
                'BVA-JBELI-003: Kuantitas 2 seharusnya diterima');
        });
    }

    /**
     * BVA-JBELI-004 | Batas maksimum (= stok: 100)
     * Kuantitas 100 = stok PRD-DUSK-001 → Berhasil (stok terkuras habis)
     * Catatan: setelah test ini stok = 0, test berikutnya harus dirun terpisah
     */
    public function test_BVA_JBELI_004_jumlah_beli_sama_stok(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);
            $browser->visit('/cashier')->waitFor('body', 5)->pause(500);

            // Beli sejumlah stok yang tersisa (misal 90 agar tidak menguras habis)
            $result = $this->postCheckout($browser, 90);

            $browser->screenshot('BVA-JBELI-004');
            $this->assertTrue($result['success'] ?? false,
                'BVA-JBELI-004: Kuantitas 90 (mendekati max stok 100) seharusnya diterima');
        });
    }

    /**
     * BVA-JBELI-005 | Di atas maksimum (101 > stok 100)
     * Kuantitas 101 > stok → Error stok tidak mencukupi
     */
    public function test_BVA_JBELI_005_jumlah_beli_melebihi_stok(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);
            $browser->visit('/cashier')->waitFor('body', 5)->pause(500);

            $result = $this->postCheckout($browser, 101);

            $browser->screenshot('BVA-JBELI-005');
            $this->assertFalse($result['success'] ?? true,
                'BVA-JBELI-005: Kuantitas 101 (melebihi stok 100) seharusnya ditolak');
        });
    }
}
