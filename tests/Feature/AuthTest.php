<?php

namespace Tests\Feature;

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_page_renders_successfully()
    {
        $this->get('/login')
            ->assertStatus(200)
            ->assertSee('Lokara')
            ->assertSee('Masuk ke Sistem Lokara POS')
            ->assertSee('Alamat Email')
            ->assertSee('Kata Sandi')
            ->assertSee('Masuk ke Aplikasi');
    }

    public function test_super_admin_can_login_with_valid_credentials()
    {
        Livewire::test(Login::class)
            ->set('email', 'admin@lokarapos.id')
            ->set('password', 'password')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('superadmin.dashboard'));

        $this->assertAuthenticated();
    }

    public function test_login_fails_with_invalid_credentials()
    {
        Livewire::test(Login::class)
            ->set('email', 'admin@lokarapos.id')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_quick_fill_demo_buttons_work()
    {
        Livewire::test(Login::class)
            ->call('fillDemoAdmin')
            ->assertSet('email', 'admin@lokarapos.id')
            ->assertSet('password', 'password')
            ->call('fillDemoSupport')
            ->assertSet('email', 'support@lokarapos.id')
            ->assertSet('password', 'password');
    }

    public function test_can_login_via_http_post()
    {
        $response = $this->post('/login', [
            'email' => 'admin@lokarapos.id',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('superadmin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_livewire_and_sw_assets_are_served_with_200_ok()
    {
        $this->get('/js/livewire/livewire.min.js')
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/javascript; charset=utf-8');

        $this->get('/livewire/livewire.min.js')
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/javascript; charset=utf-8');

        $this->get('/sw.js')
            ->assertStatus(200);
    }
}


