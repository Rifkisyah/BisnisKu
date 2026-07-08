<?php

namespace Tests\Browser\Helpers;

use Laravel\Dusk\Browser;

/**
 * AuthHelper — trait untuk login cepat berbagai role di test Dusk.
 *
 * Penggunaan:
 *   use Tests\Browser\Helpers\AuthHelper;
 *   class FooTest extends DuskTestCase {
 *       use AuthHelper;
 *       public function test_foo() {
 *           $this->browse(function (Browser $browser) {
 *               $this->loginAsOwner($browser);
 *               // ...
 *           });
 *       }
 *   }
 */
trait AuthHelper
{
    // ── Credential constants ────────────────────────────────────────────────

    protected string $ownerEmail    = 'owner@test.dusk';
    protected string $kasirEmail    = 'kasir@test.dusk';
    protected string $teknisiEmail  = 'teknisi@test.dusk';
    protected string $gudangEmail   = 'gudang@test.dusk';
    protected string $inactiveEmail = 'inactive@test.dusk';
    protected string $defaultPassword = 'password';

    // ── Login Helpers ───────────────────────────────────────────────────────

    protected function loginAsOwner(Browser $browser): Browser
    {
        return $this->loginAs($browser, $this->ownerEmail, $this->defaultPassword);
    }

    protected function loginAsKasir(Browser $browser): Browser
    {
        return $this->loginAs($browser, $this->kasirEmail, $this->defaultPassword);
    }

    protected function loginAsTeknisi(Browser $browser): Browser
    {
        return $this->loginAs($browser, $this->teknisiEmail, $this->defaultPassword);
    }

    protected function loginAsGudang(Browser $browser): Browser
    {
        return $this->loginAs($browser, $this->gudangEmail, $this->defaultPassword);
    }

    /**
     * Login generik dengan email dan password.
     */
    protected function loginAs(Browser $browser, string $email, string $password): Browser
    {
        return $browser
            ->visit('/login')
            ->waitFor('input[name="email"]', 5)
            ->clear('input[name="email"]')
            ->type('input[name="email"]', $email)
            ->clear('input[name="password"]')
            ->type('input[name="password"]', $password)
            ->click('form:not([action*="locale"]):not([action$="logout"]) button[type="submit"]')
            ->pause(2000);
            if ($browser->driver->getCurrentURL() !== url('/dashboard')) {
                throw new \Exception('Login failed! URL is: ' . $browser->driver->getCurrentURL() . ' Text: ' . $browser->text('body'));
            }
            return $browser;
    }

    /**
     * Logout dari sesi yang aktif.
     */
    protected function logout(Browser $browser): Browser
    {
        return $browser
            ->visit('/dashboard')
            ->click('form[action*="logout"] button, button[form*="logout"], #logout-btn')
            ->waitForLocation('/login', 5);
    }
}




