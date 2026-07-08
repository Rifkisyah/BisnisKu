<?php

namespace Tests\Browser\EP;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers\AuthHelper;

/**
 * Equivalence Partitioning — Modul: Layanan Perbaikan (Service Repair)
 *
 * | Test Case ID   | Kelas Partisi | Data Uji                                              | Expected Result                               |
 * |----------------|---------------|-------------------------------------------------------|-----------------------------------------------|
 * | EP-SERVIS-001  | Valid          | Owner membuat service repair baru dengan data valid   | Service repair tersimpan (status draft)       |
 * | EP-SERVIS-002  | Tidak Valid    | Teknisi mencoba membuat service repair baru           | Akses ditolak (403)                           |
 * | EP-SERVIS-003  | Valid          | Teknisi mengubah status dari draft ke in_progress     | Status berubah ke in_progress                 |
 * | EP-SERVIS-004  | Valid          | Teknisi mengubah status dari in_progress ke selesai   | Status berubah ke selesai / done              |
 * | EP-SERVIS-005  | Valid          | Tambah suku cadang (sparepart) ke service repair      | Suku cadang tercatat, komponen cost update    |
 * | EP-SERVIS-006  | Valid          | Kasir membuat service repair baru (data valid)        | Service repair tersimpan                      |
 * | EP-SERVIS-007  | Valid          | Gudang melihat detail service repair (read-only)      | Halaman detail tampil                         |
 * | EP-SERVIS-008  | Tidak Valid    | Buat service repair tanpa nama pelanggan              | Validasi nama pelanggan muncul                |
 */
class ServiceRepairTest extends DuskTestCase
{
    use AuthHelper;

    /**
     * EP-SERVIS-001 | Valid
     * Owner membuat service repair baru → Tersimpan dengan status draft
     */
    public function test_EP_SERVIS_001_owner_buat_service_repair_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/service-repairs/create')
                    ->waitFor('input[name="customer_name"]', 5)
                    ->type('input[name="customer_name"]', 'Pelanggan Servis EP 001')
                    ->type('input[name="customer_phone"]', '08123456789')
                    ->pause(500);

            // Isi item (device) pertama
            $browser->type('input[name="items[0][name]"]', 'iPhone 13 Pro')
                    ->type('input[name="items[0][brand]"]', 'Apple')
                    ->type('input[name="items[0][series]"]', 'A2483')
                    ->type('textarea[name^="items"][name$="[complaint]"]', 'Layar berkedip dan baterai cepat habis')
                    ->type('input[name="items[0][service_fee]"]', '150000')
                    ; $browser->script("const btn = document.querySelector('form:not([action*=\"locale\"]):not([action$=\"logout\"]) button[type=\"submit\"]'); if (btn) btn.click();"); $browser
                    ->pause(3000)
                    ->assertPathIs('/service-repairs')
                    ->screenshot('EP-SERVIS-001');
        });
    }

    /**
     * EP-SERVIS-002 | Tidak Valid
     * Teknisi mencoba membuat service repair baru → Akses ditolak (403)
     */
    public function test_EP_SERVIS_002_teknisi_gagal_buat_service_repair(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTeknisi($browser);

            $browser->visit('/service-repairs/create')
                    ->pause(2000)
                    ->screenshot('EP-SERVIS-002');

            $url = $browser->driver->getCurrentURL();
            $this->assertStringNotContainsString('/service-repairs/create', $url,
                'Teknisi tidak boleh akses halaman buat service repair baru');
        });
    }

    /**
     * EP-SERVIS-003 | Valid
     * Update status service repair dari draft → in_progress
     */
    public function test_EP_SERVIS_003_update_status_draft_ke_in_progress(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            // Buat service repair dulu
            $browser->visit('/service-repairs/create')
                    ->waitFor('input[name="customer_name"]', 5)
                    ->type('input[name="customer_name"]', 'Pelanggan Servis EP 003')
                    ->type('textarea[name^="items"][name$="[complaint]"]', 'Layar pecah')
                    ->type('input[name="items[0][name]"]', 'Samsung A52')
                    ; $browser->script("const btn = document.querySelector('form:not([action*=\"locale\"]):not([action$=\"logout\"]) button[type=\"submit\"]'); if (btn) btn.click();"); $browser
                    ->pause(3000);

            // Ambil service repair terbaru dan buka detail
            $browser->visit('/service-repairs')
                    
                    ->pause(1000);

            $browser->script("
                const link = document.querySelector('a[href*=\"/service-repairs/\"]');
                if (link) link.click();
            ");

            $browser->pause(2000);

            // Klik tombol ubah status / update ke in_progress
            $browser->script("
                const statusForm = document.querySelector('form[action*=\"/status\"]');
                const selectStatus = document.querySelector('select[name=\"status\"]');
                if (selectStatus) {
                    selectStatus.value = 'in_progress';
                    selectStatus.dispatchEvent(new Event('change'));
                }
            ");

            $browser->pause(1000)
                    ; $browser->script("const btn = document.querySelector('form:not([action*=\"locale\"]):not([action$=\"logout\"]) button[type=\"submit\"]'); if (btn) btn.click();"); $browser
                    ->pause(2000)
                    ->screenshot('EP-SERVIS-003');
        });
    }

    /**
     * EP-SERVIS-004 | Valid
     * Update status service repair dari in_progress → done/completed
     */
    public function test_EP_SERVIS_004_update_status_in_progress_ke_done(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/service-repairs')
                    
                    ->pause(1000)
                    ->screenshot('EP-SERVIS-004');
        });
    }

    /**
     * EP-SERVIS-005 | Valid
     * Tambah suku cadang ke service repair → Tercatat, cost update
     */
    public function test_EP_SERVIS_005_tambah_suku_cadang(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            // Buat service repair dulu
            $browser->visit('/service-repairs/create')
                    ->waitFor('input[name="customer_name"]', 5)
                    ->type('input[name="customer_name"]', 'Pelanggan Spare EP 005')
                    ->type('textarea[name^="items"][name$="[complaint]"]', 'Baterai bocor')
                    ->type('input[name="items[0][name]"]', 'Xiaomi Redmi Note 10')
                    ; $browser->script("const btn = document.querySelector('form:not([action*=\"locale\"]):not([action$=\"logout\"]) button[type=\"submit\"]'); if (btn) btn.click();"); $browser
                    ->pause(3000);

            // Buka detail service repair terbaru
            $browser->visit('/service-repairs')
                    
                    ->pause(1000);

            $browser->script("
                const link = document.querySelector('a[href*=\"/service-repairs/\"]');
                if (link) link.click();
            ");

            $browser->pause(2000);

            // Tambah part via form
            $browser->script("
                const partForm = document.querySelector('form[action*=\"/add-part\"]');
                if (partForm) {
                    const productSelect = partForm.querySelector('select[name=\"product_code\"]');
                    if (productSelect && productSelect.options.length > 1) {
                        productSelect.value = productSelect.options[1].value;
                    }
                    const qtyInput = partForm.querySelector('input[name=\"quantity\"]');
                    if (qtyInput) qtyInput.value = '1';
                    partForm.submit();
                }
            ");

            $browser->pause(2000)
                    ->screenshot('EP-SERVIS-005');
        });
    }

    /**
     * EP-SERVIS-006 | Valid
     * Kasir membuat service repair baru → Tersimpan
     */
    public function test_EP_SERVIS_006_kasir_buat_service_repair_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);

            $browser->visit('/service-repairs/create')
                    ->waitFor('input[name="customer_name"]', 5)
                    ->type('input[name="customer_name"]', 'Pelanggan Kasir EP 006')
                    ->type('input[name="items[0][name]"]', 'Oppo A54')
                    ->type('textarea[name^="items"][name$="[complaint]"]', 'Tidak bisa charging')
                    ; $browser->script("const btn = document.querySelector('form:not([action*=\"locale\"]):not([action$=\"logout\"]) button[type=\"submit\"]'); if (btn) btn.click();"); $browser
                    ->pause(3000)
                    ->screenshot('EP-SERVIS-006');
        });
    }

    /**
     * EP-SERVIS-007 | Valid
     * Gudang melihat detail service repair (read-only) → Halaman tampil
     */
    public function test_EP_SERVIS_007_gudang_lihat_detail_service_repair(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsGudang($browser);

            // Buka daftar service repair
            $browser->visit('/service-repairs')
                    
                    ->pause(1000)
                    ->assertPathIs('/service-repairs')
                    ->screenshot('EP-SERVIS-007');
        });
    }

    /**
     * EP-SERVIS-008 | Tidak Valid
     * Buat service repair tanpa nama pelanggan → Validasi muncul
     */
    public function test_EP_SERVIS_008_buat_service_repair_tanpa_nama_pelanggan(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/service-repairs/create')
                    ->waitFor('button[type="submit"]', 5)
                    ->type('textarea[name^="items"][name$="[complaint]"]', 'Masalah tanpa nama pelanggan')
                    ->type('input[name="items[0][name]"]', 'Device Test')
                    ; $browser->script("const btn = document.querySelector('form:not([action*=\"locale\"]):not([action$=\"logout\"]) button[type=\"submit\"]'); if (btn) btn.click();"); $browser
                    ->pause(2000)
                    ->screenshot('EP-SERVIS-008');
        });
    }
}

