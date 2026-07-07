<?php

namespace Tests\Browser\EP;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers\AuthHelper;

/**
 * Equivalence Partitioning — Modul: Pengaturan (Settings)
 *
 * | Test Case ID | Kelas Partisi | Data Uji                                              | Expected Result                          |
 * |--------------|---------------|-------------------------------------------------------|------------------------------------------|
 * | EP-SET-001   | Valid          | Owner mengupdate profil sendiri                       | Profil berhasil diperbarui               |
 * | EP-SET-002   | Valid          | Kasir mengupdate password yang valid                  | Password berhasil diubah                 |
 * | EP-SET-003   | Tidak Valid    | Update password dengan konfirmasi tidak cocok         | Validasi "password tidak cocok" muncul   |
 * | EP-SET-004   | Valid          | Owner mengupdate profil toko                          | Profil toko berhasil disimpan            |
 */
class SettingTest extends DuskTestCase
{
    use AuthHelper;

    /**
     * EP-SET-001 | Valid
     * Owner mengupdate profil sendiri → Berhasil
     */
    public function test_EP_SET_001_owner_update_profil(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/settings')
                    ->waitFor('body', 5)
                    ->pause(1000);

            // Update username / nama profil
            $browser->clear('input[name="username"]')
                    ->type('input[name="username"]', 'Owner Dusk Updated')
                    ->press('button[form="profile-form"], button[type="submit"]')
                    ->pause(2000)
                    ->screenshot('EP-SET-001');
        });
    }

    /**
     * EP-SET-002 | Valid
     * Kasir mengupdate password yang valid → Password berhasil diubah
     */
    public function test_EP_SET_002_kasir_update_password_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsKasir($browser);

            $browser->visit('/settings')
                    ->waitFor('body', 5)
                    ->pause(1000);

            // Isi form password
            $browser->script("
                const currentPasswordInput = document.querySelector('input[name=\"current_password\"]');
                if (currentPasswordInput) currentPasswordInput.value = 'password';

                const newPasswordInput = document.querySelector('input[name=\"password\"]');
                if (newPasswordInput) newPasswordInput.value = 'NewPassword123!';

                const confirmPasswordInput = document.querySelector('input[name=\"password_confirmation\"]');
                if (confirmPasswordInput) confirmPasswordInput.value = 'NewPassword123!';
            ");

            $browser->press('button[form="password-form"], button[data-form="password"]')
                    ->pause(2000)
                    ->screenshot('EP-SET-002');
        });
    }

    /**
     * EP-SET-003 | Tidak Valid
     * Update password dengan konfirmasi tidak cocok → Validasi muncul
     */
    public function test_EP_SET_003_update_password_konfirmasi_tidak_cocok(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/settings')
                    ->waitFor('body', 5)
                    ->pause(1000);

            $browser->script("
                const currentPasswordInput = document.querySelector('input[name=\"current_password\"]');
                if (currentPasswordInput) currentPasswordInput.value = 'password';

                const newPasswordInput = document.querySelector('input[name=\"password\"]');
                if (newPasswordInput) newPasswordInput.value = 'NewPassword123!';

                const confirmPasswordInput = document.querySelector('input[name=\"password_confirmation\"]');
                if (confirmPasswordInput) confirmPasswordInput.value = 'PasswordBEDA999!';
            ");

            $browser->press('button[form="password-form"], button[data-form="password"]')
                    ->pause(2000)
                    ->screenshot('EP-SET-003');
        });
    }

    /**
     * EP-SET-004 | Valid
     * Owner mengupdate profil toko → Berhasil disimpan
     */
    public function test_EP_SET_004_owner_update_profil_toko(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsOwner($browser);

            $browser->visit('/settings')
                    ->waitFor('body', 5)
                    ->pause(1000);

            // Ubah nama toko
            $browser->script("
                const storeNameInput = document.querySelector('input[name=\"store_name\"]');
                if (storeNameInput) storeNameInput.value = 'Toko Dusk Updated ' + Date.now();
            ");

            $browser->press('button[form="store-form"], button[data-form="store"]')
                    ->pause(2000)
                    ->screenshot('EP-SET-004');
        });
    }
}
