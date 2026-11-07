<?php

namespace Tests\Feature;

use App\Livewire\SuperAdmin\PlatformDashboard;
use App\Models\BusinessType;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Outlet;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class SuperAdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('role', 'super_admin')->first();
        auth()->login($this->admin);
    }

    public function test_guest_is_redirected_to_login_when_accessing_admin()
    {
        auth()->logout();
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_superadmin_dashboard_renders_successfully()
    {
        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertStatus(200)
            ->assertSee('Dashboard Platform')
            ->assertSee('Total Tenant')
            ->assertSee('Pendapatan Bulanan')
            ->assertSee('Transaksi Platform')
            ->assertSee('Perlu Perhatian')
            ->assertSee('Kopi Ruang Pagi');
    }

    public function test_livewire_can_switch_tabs()
    {
        Livewire::actingAs($this->admin)
            ->test(PlatformDashboard::class)
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

    public function test_validation_errors_when_creating_tenant_with_empty_fields()
    {
        Livewire::test(PlatformDashboard::class)
            ->call('openCreateTenantModal')
            ->set('newTenantName', '')
            ->set('newTenantSlug', '')
            ->set('newTenantCity', '')
            ->set('newTenantOwnerName', '')
            ->set('newTenantOwnerEmail', '')
            ->set('newTenantOwnerPhone', '')
            ->call('createTenant')
            ->assertHasErrors([
                'newTenantName' => 'required',
                'newTenantSlug' => 'required',
                'newTenantCity' => 'required',
                'newTenantOwnerName' => 'required',
                'newTenantOwnerEmail' => 'required',
                'newTenantOwnerPhone' => 'required',
            ])
            ->assertSee('Ada Kolom Wajib yang Belum Diisi!')
            ->assertSee('Nama bisnis / toko wajib diisi.')
            ->assertSee('Subdomain slug wajib diisi.')
            ->assertSee('Kota asal bisnis wajib diisi.')
            ->assertSee('Nama pemilik (owner) wajib diisi.')
            ->assertSee('Alamat email pemilik wajib diisi.')
            ->assertSee('Nomor WhatsApp pemilik wajib diisi.');
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

    public function test_can_open_reset_password_modal_and_update_password()
    {
        $tenant = Tenant::first();

        Livewire::test(PlatformDashboard::class)
            ->call('openResetPasswordModal', $tenant->owner_email, $tenant->owner_name, $tenant->name, 'Owner')
            ->assertSet('showResetPasswordModal', true)
            ->assertSet('resetUserEmail', $tenant->owner_email)
            ->set('newPassword', 'NewSecret123!')
            ->set('newPassword_confirmation', 'NewSecret123!')
            ->call('updatePassword')
            ->assertHasNoErrors()
            ->assertSet('showResetPasswordModal', false);

        $user = User::where('email', $tenant->owner_email)->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('NewSecret123!', $user->password));
    }

    public function test_reset_password_fails_when_validation_not_met()
    {
        $tenant = Tenant::first();

        // 1. Empty password
        Livewire::test(PlatformDashboard::class)
            ->call('openResetPasswordModal', $tenant->owner_email, $tenant->owner_name, $tenant->name, 'Owner')
            ->set('newPassword', '')
            ->set('newPassword_confirmation', '')
            ->call('updatePassword')
            ->assertHasErrors(['newPassword' => 'required']);

        // 2. Mismatch confirmation
        Livewire::test(PlatformDashboard::class)
            ->call('openResetPasswordModal', $tenant->owner_email, $tenant->owner_name, $tenant->name, 'Owner')
            ->set('newPassword', 'password123')
            ->set('newPassword_confirmation', 'differentPassword')
            ->call('updatePassword')
            ->assertHasErrors(['newPassword' => 'confirmed']);
    }

    public function test_can_generate_random_password()
    {
        Livewire::test(PlatformDashboard::class)
            ->call('openResetPasswordModal', 'test@demo.com', 'Test User', 'Test Tenant', 'Owner')
            ->call('generateRandomPassword')
            ->assertSet('showPasswordText', true);
    }

    public function test_can_create_new_tenant_user_account()
    {
        $tenant = Tenant::first();

        Livewire::test(PlatformDashboard::class)
            ->call('openCreateUserModal')
            ->set('createUserName', 'Kasir Baru')
            ->set('createUserEmail', 'kasirbaru@kopiruangpagi.id')
            ->set('createUserTenantId', $tenant->id)
            ->set('createUserRole', 'cashier')
            ->set('createUserPassword', 'KasirPass123')
            ->set('createUserPassword_confirmation', 'KasirPass123')
            ->call('createUser')
            ->assertHasNoErrors()
            ->assertSet('showCreateUserModal', false);

        $this->assertDatabaseHas('users', [
            'email' => 'kasirbaru@kopiruangpagi.id',
            'role' => 'cashier',
            'tenant_id' => $tenant->id,
        ]);
    }

    public function test_can_close_reset_password_modal()
    {
        Livewire::test(PlatformDashboard::class)
            ->call('openResetPasswordModal', 'test@demo.com', 'Test User', 'Test Tenant', 'Owner')
            ->assertSet('showResetPasswordModal', true)
            ->call('closeResetPasswordModal')
            ->assertSet('showResetPasswordModal', false);
    }

    public function test_can_close_create_user_modal()
    {
        Livewire::test(PlatformDashboard::class)
            ->call('openCreateUserModal')
            ->assertSet('showCreateUserModal', true)
            ->call('closeCreateUserModal')
            ->assertSet('showCreateUserModal', false);
    }

    public function test_can_close_create_tenant_modal()
    {
        Livewire::test(PlatformDashboard::class)
            ->call('openCreateTenantModal')
            ->assertSet('showCreateTenantModal', true)
            ->call('closeCreateTenantModal')
            ->assertSet('showCreateTenantModal', false);
    }

    public function test_superadmin_renders_sidebar_toggler()
    {
        $this->get('/admin')
            ->assertStatus(200)
            ->assertSee('toggleSidebarExpanded', false)
            ->assertSee('Perkecil / Perbesar Sidebar');
    }

    public function test_superadmin_can_delete_suspended_tenant()
    {
        $suspendedTenant = Tenant::where('status', 'suspended')->first();
        $this->assertNotNull($suspendedTenant);

        Livewire::actingAs($this->admin)
            ->test(PlatformDashboard::class)
            ->call('deleteTenant', $suspendedTenant->id);

        $this->assertNull(Tenant::find($suspendedTenant->id));
    }

    public function test_active_tenant_cannot_be_deleted()
    {
        $activeTenant = Tenant::where('status', 'active')->first();
        $this->assertNotNull($activeTenant);

        Livewire::actingAs($this->admin)
            ->test(PlatformDashboard::class)
            ->call('deleteTenant', $activeTenant->id)
            ->assertSee('hanya dapat dihapus setelah berstatus Suspended');

        $this->assertNotNull($activeTenant->fresh());
    }

    public function test_transaction_history_cleanup_stays_locked_until_correct_credentials()
    {
        config(['transaction_cleanup.username' => 'pembersih', 'transaction_cleanup.password' => 'rahasia-uji']);

        Livewire::actingAs($this->admin)
            ->test(PlatformDashboard::class)
            ->call('setTab', 'history-cleanup')
            ->set('cleanupLoginUsername', 'pembersih')
            ->set('cleanupLoginPassword', 'salah')
            ->call('unlockCleanup')
            ->assertSee('Username atau password salah')
            ->assertSee('Menu Terkunci');

        Livewire::actingAs($this->admin)
            ->test(PlatformDashboard::class)
            ->call('setTab', 'history-cleanup')
            ->set('cleanupLoginUsername', 'pembersih')
            ->set('cleanupLoginPassword', 'rahasia-uji')
            ->call('unlockCleanup')
            ->assertSee('Menu terbuka');
    }

    public function test_transaction_history_cleanup_deletes_only_selected_tenant_orders()
    {
        config(['transaction_cleanup.username' => 'pembersih', 'transaction_cleanup.password' => 'rahasia-uji']);

        $tenantA = Tenant::where('slug', 'kopiruangpagi')->first();
        $tenantB = Tenant::where('slug', 'studiorona')->first();
        $outletA = Outlet::where('tenant_id', $tenantA->id)->first();
        $outletB = Outlet::where('tenant_id', $tenantB->id)->first();

        $orderA = $this->makeTransactionOrder($tenantA, $outletA, 'ORD-CLEAN-A1');
        $this->makeTransactionOrder($tenantA, $outletA, 'ORD-CLEAN-A2');
        $orderB = $this->makeTransactionOrder($tenantB, $outletB, 'ORD-CLEAN-B1');
        $tenantBOrderCountBefore = Order::where('tenant_id', $tenantB->id)->count();

        Livewire::actingAs($this->admin)
            ->test(PlatformDashboard::class)
            ->call('setTab', 'history-cleanup')
            ->set('cleanupLoginUsername', 'pembersih')
            ->set('cleanupLoginPassword', 'rahasia-uji')
            ->call('unlockCleanup')
            ->set('cleanupTenantId', $tenantA->id)
            ->set('cleanupConfirmed', true)
            ->call('deleteTenantTransactionHistory');

        $this->assertDatabaseMissing('orders', ['id' => $orderA->id]);
        $this->assertDatabaseMissing('order_items', ['order_id' => $orderA->id]);
        $this->assertDatabaseMissing('order_payments', ['order_id' => $orderA->id]);
        $this->assertSame(0, Order::where('tenant_id', $tenantA->id)->count());
        $this->assertSame($tenantBOrderCountBefore, Order::where('tenant_id', $tenantB->id)->count());
        $this->assertNotNull($orderB->fresh());
    }

    public function test_transaction_history_cleanup_requires_unlock_before_deleting()
    {
        config(['transaction_cleanup.username' => 'pembersih', 'transaction_cleanup.password' => 'rahasia-uji']);

        $tenant = Tenant::where('slug', 'kopiruangpagi')->first();
        $outlet = Outlet::where('tenant_id', $tenant->id)->first();
        $order = $this->makeTransactionOrder($tenant, $outlet, 'ORD-CLEAN-LOCKED');

        Livewire::actingAs($this->admin)
            ->test(PlatformDashboard::class)
            ->set('cleanupTenantId', $tenant->id)
            ->set('cleanupConfirmed', true)
            ->call('deleteTenantTransactionHistory');

        $this->assertNotNull($order->fresh());
    }

    private function makeTransactionOrder(Tenant $tenant, Outlet $outlet, string $orderNumber)
    {
        $order = Order::create([
            'tenant_id' => $tenant->id,
            'outlet_id' => $outlet->id,
            'user_id' => $this->admin->id,
            'order_number' => $orderNumber,
            'order_type' => 'dine_in',
            'subtotal' => 20000,
            'total_amount' => 20000,
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        OrderItem::create([
            'tenant_id' => $tenant->id,
            'order_id' => $order->id,
            'product_id' => Product::where('tenant_id', $tenant->id)->value('id'),
            'product_name' => 'Item Uji',
            'qty' => 1,
            'unit_price' => 20000,
            'subtotal' => 20000,
        ]);

        OrderPayment::create([
            'tenant_id' => $tenant->id,
            'order_id' => $order->id,
            'payment_method' => 'cash',
            'amount' => 20000,
            'cash_tendered' => 20000,
            'change_due' => 0,
            'reference_number' => 'REF-'.$orderNumber,
        ]);

        return $order;
    }

    public function test_create_user_tenant_dropdown_lists_tenants_beyond_first_page()
    {
        foreach (range(1, 10) as $number) {
            Tenant::create([
                'name' => "Bisnis Uji {$number}",
                'slug' => "bisnis-uji-{$number}",
                'business_type_id' => BusinessType::first()->id,
                'plan_id' => Plan::first()->id,
                'owner_name' => 'Pemilik Uji',
                'owner_email' => "owner{$number}@uji.id",
                'owner_phone' => '081200000000',
                'status' => 'active',
                'city' => 'Jakarta',
            ]);
        }

        Livewire::actingAs($this->admin)
            ->test(PlatformDashboard::class)
            ->call('openCreateUserModal')
            ->assertSee("Bisnis Uji {$number}");
    }
}
