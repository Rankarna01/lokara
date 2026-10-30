<?php

namespace Tests\Feature;

use App\Livewire\Tenant\BackofficeDashboard;
use App\Models\Category;
use App\Models\Discount;
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

    public function test_can_create_new_category_in_master_product()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $tenant = Tenant::first();

        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openNewCategoryModal')
            ->assertSet('showCategoryModal', true)
            ->set('categoryName', 'Dessert & Bakery')
            ->set('categoryIcon', 'fa-cake-candles')
            ->set('categorySortOrder', 5)
            ->set('categoryIsActive', true)
            ->call('saveCategory')
            ->assertSet('showCategoryModal', false);

        $category = Category::where('tenant_id', $tenant->id)
            ->where('name', 'Dessert & Bakery')
            ->first();

        $this->assertNotNull($category);
        $this->assertEquals('dessert-bakery', $category->slug);
        $this->assertEquals('fa-cake-candles', $category->icon);
        $this->assertEquals(5, $category->sort_order);
        $this->assertTrue($category->is_active);
    }

    public function test_can_edit_existing_category_in_master_product()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $tenant = Tenant::first();
        $cat = Category::where('tenant_id', $tenant->id)->first();

        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openEditCategoryModal', $cat->id)
            ->assertSet('showCategoryModal', true)
            ->assertSet('editingCategoryId', $cat->id)
            ->assertSet('categoryName', $cat->name)
            ->set('categoryName', 'Kopi Spesialti')
            ->set('categoryIcon', 'fa-mug-saucer')
            ->call('saveCategory')
            ->assertSet('showCategoryModal', false);

        $cat->refresh();
        $this->assertEquals('Kopi Spesialti', $cat->name);
        $this->assertEquals('fa-mug-saucer', $cat->icon);
    }

    public function test_can_delete_category_and_nullify_products()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $tenant = Tenant::first();

        // Create a category with a product
        $category = Category::create([
            'tenant_id' => $tenant->id,
            'name' => 'Kategori Sementara',
            'slug' => 'kategori-sementara',
            'sort_order' => 99,
            'is_active' => true,
        ]);

        $product = Product::create([
            'tenant_id' => $tenant->id,
            'category_id' => $category->id,
            'name' => 'Kopi Khusus Demo',
            'slug' => 'kopi-khusus-demo',
            'base_price' => 25000,
            'cost_price' => 8000,
            'is_active' => true,
            'is_available' => true,
        ]);

        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('deleteCategory', $category->id);

        $this->assertNull(Category::find($category->id));
        $this->assertNull($product->fresh()->category_id);
    }

    public function test_can_open_manage_categories_modal()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();

        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openManageCategoriesModal')
            ->assertSet('showManageCategoriesModal', true);
    }

    public function test_salon_tenant_shows_master_layanan_and_discounts()
    {
        $salonOwner = User::where('email', 'contact@studiorona.id')->first();
        $this->assertNotNull($salonOwner);

        Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->assertSee('Master Layanan')
            ->assertSee('Master Produk')
            ->assertSee('Diskon')
            ->call('switchTab', 'products')
            ->assertSet('activeTab', 'products')
            ->assertSee('Pomade Strong Matte Clay 100g')
            ->call('switchTab', 'services')
            ->assertSet('activeTab', 'services')
            ->assertSee('Gentleman Haircut')
            ->call('switchTab', 'discounts')
            ->assertSet('activeTab', 'discounts')
            ->assertSee('OPENING20')
            ->assertSee('CUKUR15K');
    }

    public function test_can_switch_tenant_in_backoffice()
    {
        $fnbOwner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $fnbTenant = Tenant::where('slug', 'kopiruangpagi')->first();
        $salonTenant = Tenant::where('slug', 'studiorona')->first();

        Livewire::actingAs($fnbOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->assertSet('tenantId', $fnbTenant->id)
            ->call('switchTenant', $salonTenant->id)
            ->assertSet('tenantId', $salonTenant->id)
            ->assertSee('Studio Rona');
    }

    public function test_can_create_and_edit_service_in_salon()
    {
        $salonOwner = User::where('email', 'contact@studiorona.id')->first();
        $salonTenant = Tenant::where('slug', 'studiorona')->first();
        $cat = Category::where('tenant_id', $salonTenant->id)->first();

        Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openNewServiceModal')
            ->assertSet('showServiceModal', true)
            ->set('serviceName', 'Premium Head Spa')
            ->set('serviceCategoryId', $cat->id)
            ->set('servicePrice', 120000)
            ->set('serviceDuration', 45)
            ->set('serviceCommissionType', 'percentage')
            ->set('serviceCommissionValue', 20)
            ->call('saveService')
            ->assertSet('showServiceModal', false);

        $service = Product::where('tenant_id', $salonTenant->id)
            ->where('name', 'Premium Head Spa')
            ->first();

        $this->assertNotNull($service);
        $this->assertEquals('service', $service->type);
        $this->assertEquals(120000, $service->base_price);
        $this->assertEquals(45, $service->duration_minutes);
        $this->assertEquals('percentage', $service->commission_type);
        $this->assertEquals(20, $service->commission_value);
        $this->assertEquals(24000, $service->estimated_commission); // 20% of 120,000

        // Test editing service
        Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('editService', $service->id)
            ->assertSet('showServiceModal', true)
            ->assertSet('serviceName', 'Premium Head Spa')
            ->set('servicePrice', 135000)
            ->call('saveService');

        $this->assertEquals(135000, $service->fresh()->base_price);
    }

    public function test_can_create_new_category_from_inside_service_modal()
    {
        $salonOwner = User::where('email', 'contact@studiorona.id')->first();
        $salonTenant = Tenant::where('slug', 'studiorona')->first();

        $component = Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openNewServiceModal')
            ->assertSet('showServiceModal', true)
            ->call('openNewCategoryModal')
            ->assertSet('showCategoryModal', true)
            ->set('categoryName', 'Kuku & Nail Art')
            ->set('categoryIcon', 'fa-hand-sparkles')
            ->set('categorySortOrder', 9)
            ->call('saveCategory')
            ->assertSet('showCategoryModal', false)
            ->assertSet('showServiceModal', true);

        $newCat = Category::where('tenant_id', $salonTenant->id)->where('name', 'Kuku & Nail Art')->first();
        $this->assertNotNull($newCat);
        $component->assertSet('serviceCategoryId', $newCat->id);
    }

    public function test_can_create_and_manage_discounts_percentage_and_fixed()
    {
        $salonOwner = User::where('email', 'contact@studiorona.id')->first();
        $salonTenant = Tenant::where('slug', 'studiorona')->first();

        // 1. Percentage discount with max amount
        Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openNewDiscountModal')
            ->assertSet('showDiscountModal', true)
            ->set('discountName', 'Promo Gajian 25%')
            ->set('discountCode', 'PAYDAY25')
            ->set('discountType', 'percentage')
            ->set('discountValue', 25)
            ->set('discountMinPurchase', 100000)
            ->set('discountMaxAmount', 30000)
            ->call('saveDiscount')
            ->assertSet('showDiscountModal', false);

        $discPercent = Discount::where('tenant_id', $salonTenant->id)->where('code', 'PAYDAY25')->first();
        $this->assertNotNull($discPercent);
        $this->assertEquals('percentage', $discPercent->type);
        $this->assertEquals(25, $discPercent->value);
        // Calculation test: 25% of 80,000 is 0 (below min purchase 100k)
        $this->assertEquals(0, $discPercent->calculateAmount(80000));
        // 25% of 200,000 = 50,000 capped by max 30,000
        $this->assertEquals(30000, $discPercent->calculateAmount(200000));

        // 2. Fixed amount discount
        Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openNewDiscountModal')
            ->set('discountName', 'Potongan Kilat Rp 20.000')
            ->set('discountCode', 'KILAT20K')
            ->set('discountType', 'fixed')
            ->set('discountValue', 20000)
            ->set('discountMinPurchase', 50000)
            ->call('saveDiscount');

        $discFixed = Discount::where('tenant_id', $salonTenant->id)->where('code', 'KILAT20K')->first();
        $this->assertNotNull($discFixed);
        $this->assertEquals('fixed', $discFixed->type);
        $this->assertEquals(20000, $discFixed->value);
        $this->assertEquals(20000, $discFixed->calculateAmount(75000));

        // 3. Toggle discount status
        Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('toggleDiscountStatus', $discFixed->id);

        $this->assertFalse($discFixed->fresh()->is_active);
    }
}
