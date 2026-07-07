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
     * EP-REG-001 | Valid
     * Register dengan data lengkap valid → Terdaftar, masuk dashboard
     */
    public function test_EP_REG_001_register_data_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $uniqueEmail = 'owner_ep_reg_001_' . time() . '@dusk.test';

            $browser->visit('/register')
                    ->waitFor('#store_name', 5)
                    ->type('#store_name', 'Toko Baru Dusk Test')
                    ->type('#owner_name', 'Owner Baru')
                    ->type('input[name="email"]', $uniqueEmail)
                    ->type('input[name="password"]', 'Password123!')
                    ->type('#password_confirmation', 'Password123!')
                    ->press('button[type="submit"]')
                    ->waitForLocation('/dashboard', 15)
                    ->assertPathIs('/dashboard')
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
                    ->type('#store_name', 'Toko Email Duplikat')
                    ->type('#owner_name', 'Owner Duplikat')
                    ->type('input[name="email"]', 'owner@test.dusk')
                    ->type('input[name="password"]', 'Password123!')
                    ->type('#password_confirmation', 'Password123!')
                    ->press('button[type="submit"]')
                    ->pause(2000)
                    ->assertPathIs('/register')
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
                    ->type('#store_name', 'Toko Password Salah')
                    ->type('#owner_name', 'Owner Test')
                    ->type('input[name="email"]', $uniqueEmail)
                    ->type('input[name="password"]', 'Password123!')
                    ->type('#password_confirmation', 'PasswordBEDA456!')
                    ->press('button[type="submit"]')
                    ->pause(2000)
                    ->assertPathIs('/register')
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
                    ->type('#store_name', 'AB')
                    ->type('#owner_name', 'Owner AB')
                    ->type('input[name="email"]', $uniqueEmail)
                    ->type('input[name="password"]', 'Password123!')
                    ->type('#password_confirmation', 'Password123!')
                    ->press('button[type="submit"]')
                    ->pause(2000)
                    ->assertPathIs('/register')
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
                    ->waitFor('button[type="submit"]', 5)
                    ->press('button[type="submit"]')
                    ->pause(1500)
                    ->assertPathIs('/register')
                    ->screenshot('EP-REG-005');
        });
    }
}
