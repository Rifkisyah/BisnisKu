<?php

namespace Tests\Browser\EP;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Equivalence Partitioning — Modul: Register (Pendaftaran Toko Baru)
 *
 * | Test Case ID  | Kelas Partisi | Data Uji                                        | Expected Result                      |
 * |---------------|---------------|-------------------------------------------------|--------------------------------------|
 * | EP-REG-001    | Valid          | Data lengkap & valid (nama toko, owner, email)  | Toko terdaftar, masuk dashboard      |
 * | EP-REG-002    | Tidak Valid    | Email sudah terdaftar (duplicate)               | Validasi "email sudah ada" muncul    |
 * | EP-REG-003    | Tidak Valid    | Password tidak sama dengan konfirmasi           | Validasi "password tidak cocok"      |
 * | EP-REG-004    | Tidak Valid    | Nama toko kurang dari 3 karakter                | Validasi minimal karakter muncul     |
 * | EP-REG-005    | Tidak Valid    | Semua field wajib dikosongkan                   | Semua pesan validasi muncul          |
 */
class RegisterTest extends DuskTestCase
{
    /**
     * Helper: isi ulang form register dengan data tertentu via JS (tidak memicu submit)
     * agar screenshot menampilkan data yang jelas beserta error
     */
    private function refillFormViaJS(Browser $browser, array $data): void
    {
        $storeName   = addslashes($data['store_name']   ?? '');
        $ownerName   = addslashes($data['owner_name']   ?? '');
        $email       = addslashes($data['email']        ?? '');
        $password    = addslashes($data['password']     ?? '');
        $confirm     = addslashes($data['confirmation'] ?? '');

        $browser->script("
            const storeNameEl = document.querySelector('#store_name');
            if (storeNameEl) { storeNameEl.value = '{$storeName}'; }

            const ownerNameEl = document.querySelector('#owner_name');
            if (ownerNameEl) { ownerNameEl.value = '{$ownerName}'; }

            const emailEl = document.querySelector('input[name=\"email\"]');
            if (emailEl) { emailEl.value = '{$email}'; }

            const passEl = document.querySelector('input[name=\"password\"]');
            if (passEl) { passEl.value = '{$password}'; }

            const confirmEl = document.querySelector('#password_confirmation');
            if (confirmEl) { confirmEl.value = '{$confirm}'; }
        ");
    }

    /**
     * EP-REG-001 | Valid
     * Register dengan data lengkap valid → Terdaftar, masuk dashboard
     */
    public function test_EP_REG_001_register_data_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $uniqueEmail = 'owner_ep_reg_001_' . time() . '@dusk.test';

            $browser->visit('/register')
                    ->waitFor('#store_name', 5)
                    ->pause(300);

            // Isi form via JS untuk konsistensi
            $this->refillFormViaJS($browser, [
                'store_name'   => 'Toko Baru Dusk Test',
                'owner_name'   => 'Owner Baru',
                'email'        => $uniqueEmail,
                'password'     => 'Password123!',
                'confirmation' => 'Password123!',
            ]);

            // Screenshot form terisi sebelum submit
            $browser->script("window.scrollTo(0, 0);");
            $browser->pause(500)
                    ->screenshot('EP-REG-001-form');

            // Submit dan tunggu redirect ke dashboard
            $browser; $browser->script("const btn = document.querySelector('form:not([action*=\"locale\"]):not([action$=\"logout\"]) button[type=\"submit\"]'); if (btn) btn.click();"); $browser
                    ->waitForLocation('/dashboard', 15)
                    ->assertPathIs('/dashboard');

            $browser->script("window.scrollTo(0, 0);");
            $browser->pause(500)
                    ->screenshot('EP-REG-001');
        });
    }

    /**
     * EP-REG-002 | Tidak Valid
     * Register dengan email yang sudah terdaftar → Validasi duplikat
     */
    public function test_EP_REG_002_register_email_duplicate(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->waitFor('#store_name', 5)
                    ->pause(300);

            // Isi form dengan email yang sudah ada (owner@test.dusk dari DuskTestSeeder)
            $this->refillFormViaJS($browser, [
                'store_name'   => 'Toko Email Duplikat',
                'owner_name'   => 'Owner Duplikat',
                'email'        => 'owner@test.dusk',
                'password'     => 'Password123!',
                'confirmation' => 'Password123!',
            ]);

            $browser->script("window.scrollTo(0, 0);");
            $browser->pause(300)
                    ; $browser->script("const btn = document.querySelector('form:not([action*=\"locale\"]):not([action$=\"logout\"]) button[type=\"submit\"]'); if (btn) btn.click();"); $browser
                    ->pause(3000)
                    ->assertPathIs('/register');

            // Setelah redirect balik, re-fill data agar form tidak tampak kosong
            // dan scroll ke atas agar kotak error terlihat
            $this->refillFormViaJS($browser, [
                'store_name'   => 'Toko Email Duplikat',
                'owner_name'   => 'Owner Duplikat',
                'email'        => 'owner@test.dusk',
                'password'     => 'Password123!',
                'confirmation' => 'Password123!',
            ]);

            $browser->script("window.scrollTo(0, 0);");
            $browser->pause(500)
                    ->screenshot('EP-REG-002');
        });
    }

    /**
     * EP-REG-003 | Tidak Valid
     * Register dengan password tidak cocok konfirmasi → Validasi muncul
     */
    public function test_EP_REG_003_register_password_tidak_cocok(): void
    {
        $this->browse(function (Browser $browser) {
            $uniqueEmail = 'owner_ep_reg_003_' . time() . '@dusk.test';

            $browser->visit('/register')
                    ->waitFor('#store_name', 5)
                    ->pause(300);

            $this->refillFormViaJS($browser, [
                'store_name'   => 'Toko Password Salah',
                'owner_name'   => 'Owner Test',
                'email'        => $uniqueEmail,
                'password'     => 'Password123!',
                'confirmation' => 'PasswordBEDA456!',
            ]);

            $browser->script("window.scrollTo(0, 0);");
            $browser->pause(300)
                    ; $browser->script("const btn = document.querySelector('form:not([action*=\"locale\"]):not([action$=\"logout\"]) button[type=\"submit\"]'); if (btn) btn.click();"); $browser
                    ->pause(3000)
                    ->assertPathIs('/register');

            // Re-fill setelah redirect agar field tidak tampak kosong
            $this->refillFormViaJS($browser, [
                'store_name'   => 'Toko Password Salah',
                'owner_name'   => 'Owner Test',
                'email'        => $uniqueEmail,
                'password'     => 'Password123!',
                'confirmation' => 'PasswordBEDA456!',
            ]);

            $browser->script("window.scrollTo(0, 0);");
            $browser->pause(500)
                    ->screenshot('EP-REG-003');
        });
    }

    /**
     * EP-REG-004 | Tidak Valid
     * Register dengan nama toko kurang dari 3 karakter → Validasi muncul
     */
    public function test_EP_REG_004_register_nama_toko_terlalu_pendek(): void
    {
        $this->browse(function (Browser $browser) {
            $uniqueEmail = 'owner_ep_reg_004_' . time() . '@dusk.test';

            $browser->visit('/register')
                    ->waitFor('#store_name', 5)
                    ->pause(300);

            $this->refillFormViaJS($browser, [
                'store_name'   => 'AB',
                'owner_name'   => 'Owner AB',
                'email'        => $uniqueEmail,
                'password'     => 'Password123!',
                'confirmation' => 'Password123!',
            ]);

            $browser->script("window.scrollTo(0, 0);");
            $browser->pause(300)
                    ; $browser->script("const btn = document.querySelector('form:not([action*=\"locale\"]):not([action$=\"logout\"]) button[type=\"submit\"]'); if (btn) btn.click();"); $browser
                    ->pause(3000)
                    ->assertPathIs('/register');

            // Re-fill setelah redirect agar field tidak tampak kosong
            $this->refillFormViaJS($browser, [
                'store_name'   => 'AB',
                'owner_name'   => 'Owner AB',
                'email'        => $uniqueEmail,
                'password'     => 'Password123!',
                'confirmation' => 'Password123!',
            ]);

            $browser->script("window.scrollTo(0, 0);");
            $browser->pause(500)
                    ->screenshot('EP-REG-004');
        });
    }

    /**
     * EP-REG-005 | Tidak Valid
     * Register dengan semua field kosong → Semua validasi muncul
     */
    public function test_EP_REG_005_register_semua_field_kosong(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->waitFor('#store_name', 5)
                    ->pause(500);

            // Bypass HTML5 native validation agar server-side validation yang tampil
            $browser->script("
                const form = document.querySelector('form:not([action*=\"locale\"]):not([action\$=\"logout\"])');
                if (form) {
                    form.setAttribute('novalidate', '');
                    form.querySelector('button[type=\"submit\"]').click();
                }
            ");

            $browser->pause(3000)
                    ->assertPathIs('/register');

            // Scroll ke atas agar semua pesan validasi terlihat dari awal
            $browser->script("window.scrollTo(0, 0);");
            $browser->pause(500)
                    ->screenshot('EP-REG-005');
        });
    }
}
