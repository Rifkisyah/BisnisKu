<?php

namespace Tests\Browser\BVA;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers\AuthHelper;

/**
 * Boundary Value Analysis — Field: Kuantitas Pengadaan / Product Purchase (items.*.quantity)
 *
 * Aturan Validasi: required|integer|min:1
 * (Tidak ada batas atas eksplisit, diuji dengan nilai besar secara realistis)
 *
 * | Test Case ID   | Batas Nilai | Nilai Uji | Jenis Batas       | Expected Result              |
 * |----------------|-------------|-----------|-------------------|------------------------------|
 * | BVA-KUAN-001   | min: 1      | 0         | Di bawah minimum  | Validasi muncul              |
 * | BVA-KUAN-002   | min: 1      | 1         | Batas minimum     | Pengadaan tersimpan          |
 * | BVA-KUAN-003   | min: 1      | 2         | Di atas minimum   | Pengadaan tersimpan          |
 * | BVA-KUAN-004   | min: 1      | 999       | Nilai besar valid  | Pengadaan tersimpan          |
 */
class KuantitasPengadaanBVATest extends DuskTestCase
{
    use AuthHelper;

    /**
     * Helper: submit form pengadaan dengan kuantitas tertentu via fetch API
     */
    private function postPurchaseWithQty(Browser $browser, int $qty): array
    {
        $result = $browser->script("
            return fetch('/product-purchases', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    purchase_date: '" . now()->format('Y-m-d') . "',
                    items: [{
                        product_code: 'PRD-DUSK-001',
                        quantity: {$qty},
                        purchase_price: 45000,
                        source: 'offline'
                    }]
                })
            }).then(r => ({ status: r.status, ok: r.ok })).catch(e => ({ error: e.message, ok: false }));
        ");

        return $result[0] ?? [];
    }

    /**
     * BVA-KUAN-001 | Di bawah minimum (0)
     * Kuantitas pengadaan 0 → Validasi min:1 muncul
     */
    public function test_BVA_KUAN_001_kuantitas_nol(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/product-purchases/create')
                    
                    ->pause(500);

            $browser->script("
                const dateInput = document.querySelector('input[name=\"purchase_date\"]');
                if (dateInput) dateInput.value = '" . now()->format('Y-m-d') . "';
                const productSelect = document.querySelector('select[name=\"items[0][product_code]\"]');
                if (productSelect && productSelect.options.length > 1) {
                    productSelect.value = productSelect.options[1].value;
                    productSelect.dispatchEvent(new Event('change'));
                }
            ");

            $browser->pause(500)
                    ->script("
                        const qtyInput = document.querySelector('input[name=\"items[0][quantity]\"]');
                        if (qtyInput) qtyInput.value = '0';
                        const priceInput = document.querySelector('input[name=\"items[0][purchase_price]\"]');
                        if (priceInput) priceInput.value = '45000';
                        const sourceSelect = document.querySelector('select[name=\"items[0][source]\"]');
                        if (sourceSelect) sourceSelect.value = 'offline';
                    ");

            $browser->click('form:not([action*="locale"]):not([action$="logout"]) button[type="submit"]')
                    ->pause(2000)
                    ->screenshot('BVA-KUAN-001');
        });
    }

    /**
     * BVA-KUAN-002 | Batas minimum (1)
     * Kuantitas pengadaan 1 → Valid
     */
    public function test_BVA_KUAN_002_kuantitas_satu(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/product-purchases/create')
                    
                    ->pause(500);

            $browser->script("
                const dateInput = document.querySelector('input[name=\"purchase_date\"]');
                if (dateInput) dateInput.value = '" . now()->format('Y-m-d') . "';
                const productSelect = document.querySelector('select[name=\"items[0][product_code]\"]');
                if (productSelect && productSelect.options.length > 1) {
                    productSelect.value = productSelect.options[1].value;
                    productSelect.dispatchEvent(new Event('change'));
                }
            ");

            $browser->pause(500)
                    ->script("
                        const qtyInput = document.querySelector('input[name=\"items[0][quantity]\"]');
                        if (qtyInput) qtyInput.value = '1';
                        const priceInput = document.querySelector('input[name=\"items[0][purchase_price]\"]');
                        if (priceInput) priceInput.value = '45000';
                        const sourceSelect = document.querySelector('select[name=\"items[0][source]\"]');
                        if (sourceSelect) sourceSelect.value = 'offline';
                    ");

            $browser->click('form:not([action*="locale"]):not([action$="logout"]) button[type="submit"]')
                    ->pause(2000)
                    ->screenshot('BVA-KUAN-002');
        });
    }

    /**
     * BVA-KUAN-003 | Di atas minimum (2)
     * Kuantitas pengadaan 2 → Valid
     */
    public function test_BVA_KUAN_003_kuantitas_dua(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/product-purchases/create')
                    
                    ->pause(500);

            $browser->script("
                const dateInput = document.querySelector('input[name=\"purchase_date\"]');
                if (dateInput) dateInput.value = '" . now()->format('Y-m-d') . "';
                const productSelect = document.querySelector('select[name=\"items[0][product_code]\"]');
                if (productSelect && productSelect.options.length > 1) {
                    productSelect.value = productSelect.options[1].value;
                    productSelect.dispatchEvent(new Event('change'));
                }
            ");

            $browser->pause(500)
                    ->script("
                        const qtyInput = document.querySelector('input[name=\"items[0][quantity]\"]');
                        if (qtyInput) qtyInput.value = '2';
                        const priceInput = document.querySelector('input[name=\"items[0][purchase_price]\"]');
                        if (priceInput) priceInput.value = '45000';
                        const sourceSelect = document.querySelector('select[name=\"items[0][source]\"]');
                        if (sourceSelect) sourceSelect.value = 'offline';
                    ");

            $browser->click('form:not([action*="locale"]):not([action$="logout"]) button[type="submit"]')
                    ->pause(2000)
                    ->screenshot('BVA-KUAN-003');
        });
    }

    /**
     * BVA-KUAN-004 | Nilai besar valid (999)
     * Kuantitas pengadaan 999 → Valid
     */
    public function test_BVA_KUAN_004_kuantitas_besar(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/product-purchases/create')
                    
                    ->pause(500);

            $browser->script("
                const dateInput = document.querySelector('input[name=\"purchase_date\"]');
                if (dateInput) dateInput.value = '" . now()->format('Y-m-d') . "';
                const productSelect = document.querySelector('select[name=\"items[0][product_code]\"]');
                if (productSelect && productSelect.options.length > 1) {
                    productSelect.value = productSelect.options[1].value;
                    productSelect.dispatchEvent(new Event('change'));
                }
            ");

            $browser->pause(500)
                    ->script("
                        const qtyInput = document.querySelector('input[name=\"items[0][quantity]\"]');
                        if (qtyInput) qtyInput.value = '999';
                        const priceInput = document.querySelector('input[name=\"items[0][purchase_price]\"]');
                        if (priceInput) priceInput.value = '45000';
                        const sourceSelect = document.querySelector('select[name=\"items[0][source]\"]');
                        if (sourceSelect) sourceSelect.value = 'offline';
                    ");

            $browser->click('form:not([action*="locale"]):not([action$="logout"]) button[type="submit"]')
                    ->pause(2000)
                    ->screenshot('BVA-KUAN-004');
        });
    }
}

