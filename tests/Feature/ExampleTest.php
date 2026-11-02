<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_root(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_super_admin_is_redirected_to_dashboard(): void
    {
        $this->seed();
        $admin = User::where('role', 'super_admin')->first();

        $response = $this->actingAs($admin)->get('/');
        $response->assertRedirect(route('superadmin.dashboard'));
    }
}
