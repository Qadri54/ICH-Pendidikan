<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AuthenticationTest extends DuskTestCase
{
    public function test_login_with_valid_credentials(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                ->visit('/login')
                ->type('email', 'admin@iqra.com')
                ->type('password', 'password123')
                ->click('button[type=submit]')
                ->waitForLocation('/dashboard')
                ->assertPathIs('/dashboard')
                ->screenshot('auth/01-login-success');
        });
    }

    public function test_login_with_wrong_password(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                ->visit('/login')
                ->type('email', 'admin@iqra.com')
                ->type('password', 'wrongpassword')
                ->click('button[type=submit]')
                ->pause(1000)
                ->screenshot('auth/02-login-wrong-password');
        });
    }

    public function test_login_with_unregistered_email(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                ->visit('/login')
                ->type('email', 'random@iqra.com')
                ->type('password', 'password123')
                ->click('button[type=submit]')
                ->pause(1000)
                ->screenshot('auth/03-login-unregistered');
        });
    }

    public function test_register_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                ->visit('/register')
                ->assertSee('Daftar')
                ->screenshot('auth/04-register-page');
        });
    }

    public function test_logout(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/dashboard')
                ->waitForText('Dashboard')
                ->screenshot('auth/05-before-logout')
                ->logout()
                ->visit('/dashboard')
                ->assertPathIs('/login')
                ->screenshot('auth/05-after-logout');
        });
    }

    public function test_unauthenticated_redirect_to_login(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                ->visit('/dashboard')
                ->assertPathIs('/login')
                ->screenshot('auth/06-redirect-to-login');
        });
    }

    public function test_forgot_password_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                ->visit('/forgot-password')
                ->screenshot('auth/07-forgot-password');
        });
    }
}
