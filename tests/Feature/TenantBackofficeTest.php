<?php

namespace Tests\Feature;

use App\Livewire\Tenant\BackofficeDashboard;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TenantBackofficeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_tenant_backoffice_renders_successfully()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();

        $this->actingAs($owner)
            ->get('/app')
            ->assertStatus(200)
            ->assertSee('Kopi Ruang Pagi')
            ->assertSee('Dashboard')
            ->assertSee('Buka POS Kasir');
    }

    public function test_can_switch_tabs_in_backoffice()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();

        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'products')
            ->assertSet('activeTab', 'products')
            ->assertSee('Master Produk')
            ->call('switchTab', 'recipes')
            ->assertSet('activeTab', 'recipes')
            ->assertSee('Bahan Baku')
            ->call('switchTab', 'stock')
            ->assertSet('activeTab', 'stock')
            ->assertSee('Inventori')
            ->call('switchTab', 'finance')
            ->assertSet('activeTab', 'finance')
            ->assertSee('Laba Rugi')
            ->call('switchTab', 'crm')
            ->assertSet('activeTab', 'crm')
            ->assertSee('Pelanggan');
    }

    public function test_can_add_new_product_and_toggle_availability()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $tenant = Tenant::first();
        $cat = Category::where('tenant_id', $tenant->id)->first();

        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openNewProductModal')
            ->set('productName', 'Kopi Susu Pandan Premium')
            ->set('productCategoryId', $cat->id)
            ->set('productBasePrice', 28000)
            ->set('productCostPrice', 9000)
            ->call('saveProduct')
            ->assertSet('showProductModal', false);

        $product = Product::where('name', 'Kopi Susu Pandan Premium')->first();
        $this->assertNotNull($product);
        $this->assertEquals(28000, $product->base_price);
        $this->assertEquals(9000, $product->cost_price);
        $this->assertTrue($product->is_available);

        // Toggle availability
        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('toggleProductAvailability', $product->id);

        $this->assertFalse($product->fresh()->is_available);
    }

    public function test_can_add_raw_material_and_recipe_recalculates_hpp()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $tenant = Tenant::first();
        $product = Product::where('tenant_id', $tenant->id)->first();

        // 1. Add raw ingredient
        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->set('ingredientName', 'Sirup Karamel Monin')
            ->set('ingredientCode', 'RAW-TEST-01')
            ->set('ingredientCategory', 'Sirup & Pemanis')
            ->set('ingredientPurchaseUnit', 'Botol')
            ->set('ingredientUsageUnit', 'ml')
            ->set('ingredientConversionFactor', 750)
            ->set('ingredientCostPerUsage', 200) // Rp 200 per ml
            ->set('ingredientCurrentStock', 1500)
            ->set('ingredientMinStock', 500)
            ->call('saveIngredient');

        $ingredient = Ingredient::where('name', 'Sirup Karamel Monin')->first();
        $this->assertNotNull($ingredient);

        // 2. Add ingredient to product recipe
        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openRecipeModal', $product->id)
            ->set('selectedRecipeIngredientId', $ingredient->id)
            ->set('recipeQuantity', 30) // 30 ml x 200 = 6000
            ->call('addRecipeItem');

        // Check product HPP was recalculated
        $product->refresh();
        $this->assertGreaterThanOrEqual(6000, (float) $product->cost_price);
    }

    public function test_can_perform_stock_opname()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $ingredient = Ingredient::first();

        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openOpnameModal', $ingredient->id)
            ->set('opnamePhysicalQty', 7500)
            ->set('opnameReason', 'Audit fisik mingguan')
            ->call('executeOpname');

        $ingredient->refresh();
        $this->assertEquals(7500, (float) $ingredient->current_stock);
    }

    public function test_can_create_purchase_order_and_record_expense()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $supplier = Supplier::first();

        // Test PO
        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openPoModal')
            ->set('poSupplierId', $supplier->id)
            ->set('poTotalAmount', 1850000)
            ->set('poNotes', 'Pengadaan Darurat Akhir Pekan')
            ->call('savePurchaseOrder');

        $po = PurchaseOrder::where('notes', 'Pengadaan Darurat Akhir Pekan')->first();
        $this->assertNotNull($po);
        $this->assertEquals(1850000, (float) $po->total_amount);

        // Test Expense
        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openExpenseModal')
            ->set('expenseCategory', 'Listrik & Air')
            ->set('expenseTitle', 'Pembelian Galon Air Dapur')
            ->set('expenseAmount', 120000)
            ->set('expensePaymentMethod', 'Kas Toko / Tunai')
            ->call('saveExpense');

        $this->assertDatabaseHas('expenses', [
            'title' => 'Pembelian Galon Air Dapur',
            'amount' => 120000,
        ]);
    }

    public function test_void_order_with_manager_pin()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $tenant = Tenant::first();
        $outlet = Outlet::first();
        $order = Order::create([
            'tenant_id' => $tenant->id,
            'outlet_id' => $outlet->id,
            'order_number' => 'ORD-TEST-99',
            'order_type' => 'dine_in',
            'subtotal' => 25000,
            'total_amount' => 27750,
            'status' => 'completed',
        ]);

        // Test with invalid PIN
        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openVoidModal', $order->id)
            ->set('voidPin', '000000')
            ->call('executeVoidOrder');

        $this->assertEquals('completed', $order->fresh()->status);

        // Test with valid manager PIN
        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openVoidModal', $order->id)
            ->set('voidPin', '888888')
            ->call('executeVoidOrder');

        $this->assertEquals('voided', $order->fresh()->status);
    }
}
