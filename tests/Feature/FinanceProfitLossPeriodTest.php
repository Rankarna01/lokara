<?php

namespace Tests\Feature;

use App\Livewire\Tenant\BackofficeDashboard;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinanceProfitLossPeriodTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_opex_table_applies_its_own_daily_and_range_period(): void
    {
        [$owner] = $this->salonOwnerWithCommissionService();
        $expenseDate = now()->subDays(3)->toDateString();
        Expense::create([
            'tenant_id' => $owner->tenant_id,
            'outlet_id' => Outlet::where('tenant_id', $owner->tenant_id)->value('id'),
            'category' => 'Operasional',
            'title' => 'Beli Tisu Periode Uji',
            'amount' => 35000,
            'expense_date' => $expenseDate,
            'payment_method' => 'Kas Toko / Tunai',
        ]);

        $component = Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'finance')
            ->set('financeDateFilter', 'all')
            ->call('setFinancePeriodMode', 'opex', 'day')
            ->set('financePeriods.opex.date', now()->toDateString())
            ->assertDontSee('Beli Tisu Periode Uji')
            ->set('financePeriods.opex.date', $expenseDate)
            ->assertSee('Beli Tisu Periode Uji');

        $component->call('setFinancePeriodMode', 'opex', 'range')
            ->set('financePeriods.opex.start', now()->subDays(5)->toDateString())
            ->set('financePeriods.opex.end', now()->subDays(1)->toDateString())
            ->assertSee('Beli Tisu Periode Uji')
            ->set('financePeriods.opex.start', now()->subDays(10)->toDateString())
            ->set('financePeriods.opex.end', now()->subDays(6)->toDateString())
            ->assertDontSee('Beli Tisu Periode Uji');
    }

    public function test_commission_tab_lists_commission_per_service_for_selected_period(): void
    {
        [$owner, $service] = $this->salonOwnerWithCommissionService();
        $this->recordCompletedServiceSale($owner, $service, qty: 2, daysAgo: 3);

        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'finance')
            ->set('financeDateFilter', 'all')->call('setFinanceSubTab', 'commission')
            ->call('setFinancePeriodMode', 'commission', 'all')
            ->assertSee('Komisi dihitung per layanan, belum per pegawai')
            ->assertSee('Rincian Komisi Pegawai per Layanan')
            ->assertSee('Layanan Komisi Uji')
            ->assertSee('Rp 20.000');
    }

    public function test_commission_period_excludes_sales_outside_selected_day(): void
    {
        [$owner, $service] = $this->salonOwnerWithCommissionService();
        $this->recordCompletedServiceSale($owner, $service, qty: 1, daysAgo: 3);

        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'finance')
            ->set('financeDateFilter', 'all')->call('setFinanceSubTab', 'commission')
            ->call('setFinancePeriodMode', 'commission', 'day')
            ->set('financePeriods.commission.date', now()->toDateString())
            ->assertDontSee('Layanan Komisi Uji');
    }

    public function test_unknown_period_mode_is_ignored(): void
    {
        [$owner] = $this->salonOwnerWithCommissionService();

        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'finance')
            ->call('setFinancePeriodMode', 'products', 'tahunan')
            ->assertSet('financePeriods.products.mode', 'all');
    }

    /**
     * @return array{0: User, 1: Product}
     */
    private function salonOwnerWithCommissionService(): array
    {
        $owner = User::where('email', 'arsihapis@putribeautycare.site')->firstOrFail();
        $tenant = Tenant::findOrFail($owner->tenant_id);
        $category = Category::where('tenant_id', $tenant->id)->firstOrFail();

        $service = Product::create([
            'tenant_id' => $tenant->id,
            'category_id' => $category->id,
            'type' => 'service',
            'name' => 'Layanan Komisi Uji',
            'slug' => 'layanan-komisi-uji',
            'sku' => 'SRV-KOMISIUJI',
            'base_price' => 60000,
            'cost_price' => 0,
            'duration_minutes' => 30,
            'commission_type' => 'fixed',
            'commission_value' => 10000,
            'is_active' => true,
            'is_available' => true,
        ]);

        return [$owner, $service];
    }

    private function recordCompletedServiceSale(User $owner, Product $service, int $qty, int $daysAgo): void
    {
        $order = Order::create([
            'tenant_id' => $owner->tenant_id,
            'outlet_id' => Outlet::where('tenant_id', $owner->tenant_id)->value('id'),
            'user_id' => $owner->id,
            'order_number' => 'ORD-PERIOD-'.$daysAgo.'-'.$qty,
            'order_type' => 'salon_order',
            'subtotal' => 60000 * $qty,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'service_amount' => 0,
            'total_amount' => 60000 * $qty,
            'status' => 'completed',
            'payment_status' => 'paid',
            'paid_at' => now()->subDays($daysAgo),
            'created_at' => now()->subDays($daysAgo),
        ]);

        OrderItem::create([
            'tenant_id' => $owner->tenant_id,
            'order_id' => $order->id,
            'product_id' => $service->id,
            'product_name' => $service->name,
            'qty' => $qty,
            'unit_price' => 60000,
            'subtotal' => 60000 * $qty,
        ]);
    }
}
