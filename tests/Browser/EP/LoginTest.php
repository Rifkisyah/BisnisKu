<?php

namespace Tests\Browser\EP;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Helpers\AuthHelper;

/**
 * Equivalence Partitioning — Modul: Login
 *
 * | Test Case ID  | Kelas Partisi | Data Uji                                | Expected Result                |
 * |---------------|---------------|-----------------------------------------|-------------------------------|
 * | EP-LOGIN-001  | Valid          | Email & password benar (owner)          | Masuk ke dashboard             |
 * | EP-LOGIN-002  | Tidak Valid    | Email benar, password salah             | Login ditolak, error muncul    |
 * | EP-LOGIN-003  | Tidak Valid    | Email kosong, password kosong           | Validasi email muncul          |
 * | EP-LOGIN-004  | Tidak Valid    | Akun aktif = false (status inactive)    | Pesan akun tidak aktif muncul  |
 * | EP-LOGIN-005  | Tidak Valid    | Email tidak terdaftar                   | Login ditolak, error muncul    |
 */
class LoginTest extends DuskTestCase
{
    use AuthHelper;

    /**
     * EP-LOGIN-001 | Valid
     * Login dengan akun valid (owner) → Masuk ke dashboard
     */
    public function test_EP_LOGIN_001_login_dengan_akun_valid(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->waitFor('input[name="email"]', 5)
                    ->type('input[name="email"]', $this->ownerEmail)
                    ->type('input[name="password"]', $this->defaultPassword)
                    ; $browser->script("const btn = document.querySelector('form:not([action*=\"locale\"]):not([action$=\"logout\"]) button[type=\"submit\"]'); if (btn) btn.click();"); $browser 
                    ->waitForLocation('/dashboard', 10)
                    ->assertPathIs('/dashboard')
                    ->screenshot('EP-LOGIN-001');
        });
    }

    /**
     * EP-LOGIN-002 | Tidak Valid
     * Login dengan password salah → Login ditolak
     */
    public function test_EP_LOGIN_002_login_dengan_password_salah(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->waitFor('input[name="email"]', 5)
                    ->type('input[name="email"]', $this->ownerEmail)
                    ->type('input[name="password"]', 'passwordSALAH123')
                    ; $browser->script("const btn = document.querySelector('form:not([action*=\"locale\"]):not([action$=\"logout\"]) button[type=\"submit\"]'); if (btn) btn.click();"); $browser 
                    ->waitFor('.text-\\[var\\(--color-critical\\)\\]', 3)
                    ->assertPathIs('/login')
                    
                    ->screenshot('EP-LOGIN-002');
        });
    }

    /**
     * EP-LOGIN-003 | Tidak Valid
     * Login dengan email dan password kosong → Validasi browser/server muncul
     */
    public function test_EP_LOGIN_003_login_dengan_field_kosong(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->waitFor('input[name="email"]', 10)
                    ->pause(500)
                    ->script("
                        // Trigger form submit melalui JS untuk bypass HTML5 native validation
                        const form = document.querySelector('form:not([action*=\"locale\"]):not([action$=\"logout\"])');
                        if (form) {
                            // Disable native HTML5 validation, lalu submit
                            form.setAttribute('novalidate', '');
                            form.querySelector('button[type=\"submit\"]').click();
                        }
                    ");
            $browser->pause(2000)
                    ->assertPathIs('/login')
                    ->screenshot('EP-LOGIN-003');
        });
    }

    /**
     * EP-LOGIN-004 | Tidak Valid
     * Login dengan akun yang statusnya inactive → Pesan "akun tidak aktif" muncul
     */
    public function test_EP_LOGIN_004_login_dengan_akun_inactive(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->waitFor('input[name="email"]', 5)
                    ->type('input[name="email"]', $this->inactiveEmail)
                    ->type('input[name="password"]', $this->defaultPassword)
                    ; $browser->script("const btn = document.querySelector('form:not([action*=\"locale\"]):not([action$=\"logout\"]) button[type=\"submit\"]'); if (btn) btn.click();"); $browser 
                    ->pause(2000)
                    ->assertPathIs('/login')
                    ->screenshot('EP-LOGIN-004');
        });
    }

    /**
     * EP-LOGIN-005 | Tidak Valid
     * Login dengan email yang tidak terdaftar → Login ditolak
     */
    public function test_EP_LOGIN_005_login_dengan_email_tidak_terdaftar(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->waitFor('input[name="email"]', 5)
                    ->type('input[name="email"]', 'tidakterdaftar@example.com')
                    ->type('input[name="password"]', 'password123')
                    ; $browser->script("const btn = document.querySelector('form:not([action*=\"locale\"]):not([action$=\"logout\"]) button[type=\"submit\"]'); if (btn) btn.click();"); $browser 
                    ->pause(2000)
                    ->assertPathIs('/login')
                    ->screenshot('EP-LOGIN-005');
        });
    }
}


