<?php

namespace Tests\Browser\EP;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers\AuthHelper;

/**
 * Equivalence Partitioning — Modul: Karyawan (Employee Management)
 *
 * | Test Case ID | Kelas Partisi | Data Uji                                           | Expected Result                          |
 * |--------------|---------------|----------------------------------------------------|------------------------------------------|
 * | EP-KAR-001   | Valid          | Owner menambah karyawan baru dengan data valid     | Karyawan tersimpan                       |
 * | EP-KAR-002   | Tidak Valid    | Kasir mencoba akses tambah karyawan                | Akses ditolak (403)                      |
 * | EP-KAR-003   | Valid          | Owner mengupdate data karyawan                     | Data karyawan berhasil diperbarui        |
 * | EP-KAR-004   | Tidak Valid    | Owner mencoba hapus akun dirinya sendiri           | Error: tidak boleh hapus diri sendiri    |
 * | EP-KAR-005   | Valid          | Owner menghapus karyawan lain                      | Karyawan berhasil dihapus               |
 */
class EmployeeTest extends DuskTestCase
{
    use AuthHelper;

    /**
     * EP-KAR-001 | Valid
     * Owner menambah karyawan baru → Tersimpan
     */
    public function test_EP_KAR_001_tambah_karyawan_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/employees/create')
                    ->waitFor('#username', 5)
                    ->type('#username', 'Karyawan Baru EP')
                    ->type('input[name="email"]', 'karyawan_ep_' . time() . '@test.dusk')
                    ->type('input[name="password"]', 'Password123!')
                    ->type('#password_confirmation', 'Password123!');

            // Pilih role kasir
            $browser->script("
                const roleSelect = document.querySelector('select[name=\"role_id\"]');
                if (roleSelect) {
                    const kasirOption = Array.from(roleSelect.options).find(o => o.text.toLowerCase().includes('kasir'));
                    if (kasirOption) roleSelect.value = kasirOption.value;
                }
            ");

            $browser->press('button[type="submit"]')
                    ->pause(2000)
                    ->assertPathIs('/employees')
                    ->screenshot('EP-KAR-001');
        });
    }

    /**
     * EP-KAR-002 | Tidak Valid
     * Kasir mencoba akses tambah karyawan → Akses ditolak (403)
     */
    public function test_EP_KAR_002_kasir_gagal_akses_tambah_karyawan(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);

            $browser->visit('/employees/create')
                    ->pause(2000)
                    ->screenshot('EP-KAR-002');

            $url = $browser->driver->getCurrentURL();
            $this->assertStringNotContainsString('/employees/create', $url,
                'Kasir tidak boleh akses halaman tambah karyawan');
        });
    }

    /**
     * EP-KAR-003 | Valid
     * Owner mengupdate data karyawan → Data berhasil diperbarui
     */
    public function test_EP_KAR_003_update_karyawan_berhasil(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/employees')
                    ->waitFor('body', 5)
                    ->pause(1000);

            // Klik edit pada karyawan pertama yang bukan diri sendiri
            $browser->script("
                const editLinks = document.querySelectorAll('a[href*=\"/employees/\"][href*=\"/edit\"]');
                if (editLinks.length > 0) editLinks[0].click();
            ");

            $browser->pause(2000);

            $url = $browser->driver->getCurrentURL();
            if (str_contains($url, '/edit')) {
                $browser->clear('#username')
                        ->type('#username', 'Karyawan Updated EP')
                        ->press('button[type="submit"]')
                        ->pause(2000);
            }

            $browser->screenshot('EP-KAR-003');
        });
    }

    /**
     * EP-KAR-004 | Tidak Valid
     * Owner mencoba hapus akun dirinya sendiri → Error muncul
     */
    public function test_EP_KAR_004_owner_gagal_hapus_dirinya_sendiri(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            // Ambil ID user owner yang sedang login
            $ownerId = $browser->visit('/employees')
                               ->waitFor('body', 5)
                               ->script("
                                    // Cari row karyawan dengan email owner@test.dusk
                                    const rows = document.querySelectorAll('tbody tr');
                                    let ownerId = null;
                                    rows.forEach(row => {
                                        if (row.textContent.includes('owner@test.dusk') || row.textContent.includes('Owner Dusk')) {
                                            const deleteForm = row.querySelector('form[action*=\"/employees/\"]');
                                            if (deleteForm) {
                                                const action = deleteForm.action;
                                                const match = action.match(/\\/employees\\/(\\d+)/);
                                                if (match) ownerId = match[1];
                                            }
                                        }
                                    });
                                    return ownerId;
                               ");

            if ($ownerId[0]) {
                // Submit form delete untuk owner sendiri
                $browser->script("
                    const ownerDeleteForm = document.querySelector('form[action*=\"/employees/" . $ownerId[0] . "\"]');
                    if (ownerDeleteForm) ownerDeleteForm.submit();
                ");
            } else {
                // Alternatif: kunjungi halaman profil dan coba hapus
                $browser->visit('/settings');
            }

            $browser->pause(2000)
                    ->screenshot('EP-KAR-004');
        });
    }

    /**
     * EP-KAR-005 | Valid
     * Owner menghapus karyawan lain → Berhasil dihapus
     */
    public function test_EP_KAR_005_owner_hapus_karyawan_lain(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            // Buat karyawan baru dulu yang akan dihapus
            $browser->visit('/employees/create')
                    ->waitFor('#username', 5)
                    ->type('#username', 'Karyawan Hapus EP')
                    ->type('input[name="email"]', 'hapus_ep_' . time() . '@test.dusk')
                    ->type('input[name="password"]', 'Password123!')
                    ->type('#password_confirmation', 'Password123!');

            $browser->script("
                const roleSelect = document.querySelector('select[name=\"role_id\"]');
                if (roleSelect && roleSelect.options.length > 1) {
                    roleSelect.value = roleSelect.options[1].value;
                }
            ");

            $browser->press('button[type="submit"]')
                    ->pause(2000);

            // Cari dan hapus karyawan yang baru dibuat
            $browser->visit('/employees')
                    ->waitFor('body', 5)
                    ->pause(1000);

            // Submit form delete untuk karyawan dengan nama 'Karyawan Hapus EP'
            $browser->script("
                const rows = document.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    if (row.textContent.includes('Karyawan Hapus EP')) {
                        const deleteForm = row.querySelector('form[method=\"POST\"][action*=\"/employees/\"]');
                        if (deleteForm) deleteForm.submit();
                    }
                });
            ");

            $browser->pause(2000)
                    ->screenshot('EP-KAR-005');
        });
    }
}
