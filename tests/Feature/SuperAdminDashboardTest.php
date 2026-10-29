<?php

namespace Tests\Feature;

use App\Livewire\SuperAdmin\PlatformDashboard;
use App\Models\BusinessType;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SuperAdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_superadmin_dashboard_renders_successfully()
    {
        $this->get('/admin')
            ->assertStatus(200)
            ->assertSee('Dashboard Platform')
            ->assertSee('1.284')
            ->assertSee('Rp 48,6 jt')
            ->assertSee('86.420')
            ->assertSee('12')
            ->assertSee('Kopi Ruang Pagi');
    }

    public function test_livewire_can_switch_tabs()
    {
        Livewire::test(PlatformDashboard::class)
            ->assertSet('activeTab', 'dashboard')
            ->call('setTab', 'tenants')
            ->assertSet('activeTab', 'tenants')
            ->assertSee('Manajemen Tenant')
            ->call('setTab', 'plans')
            ->assertSet('activeTab', 'plans')
            ->assertSee('Paket')
            ->call('setTab', 'invoices')
            ->assertSet('activeTab', 'invoices')
            ->assertSee('Tagihan');
    }

    public function test_livewire_can_create_new_tenant()
    {
        $bt = BusinessType::first();
        $plan = Plan::first();

        Livewire::test(PlatformDashboard::class)
            ->set('newTenantName', 'Kafe Cahaya Baru')
            ->set('newTenantSlug', 'kafe-cahaya-baru')
            ->set('newTenantBusinessType', $bt->id)
            ->set('newTenantPlan', $plan->id)
            ->set('newTenantOwnerName', 'Bambang Sudirman')
            ->set('newTenantOwnerEmail', 'bambang@cahayabaru.com')
            ->set('newTenantOwnerPhone', '081234567899')
            ->set('newTenantCity', 'Malang')
            ->call('createTenant')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tenants', [
            'name' => 'Kafe Cahaya Baru',
            'slug' => 'kafe-cahaya-baru',
        ]);
    }

    public function test_livewire_can_toggle_tenant_status()
    {
        $tenant = Tenant::first();
        $originalStatus = $tenant->status;

        Livewire::test(PlatformDashboard::class)
            ->call('toggleTenantStatus', $tenant->id);

        $expectedStatus = $originalStatus === 'active' ? 'suspended' : 'active';
        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'status' => $expectedStatus,
        ]);
    }

    public function test_livewire_can_mark_invoice_as_paid()
    {
        $invoice = Invoice::where('status', '!=', 'paid')->first();

        if ($invoice) {
            Livewire::test(PlatformDashboard::class)
                ->call('markInvoicePaid', $invoice->id);

            $this->assertDatabaseHas('invoices', [
                'id' => $invoice->id,
                'status' => 'paid',
            ]);
        }
    }
}
