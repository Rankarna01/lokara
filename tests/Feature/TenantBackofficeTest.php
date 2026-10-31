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
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_can_upload_product_image_file_and_remove_image()
    {
        Storage::fake('public');

        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $tenant = Tenant::first();
        $cat = Category::where('tenant_id', $tenant->id)->first();

        $fakeProductImage = UploadedFile::fake()->image('iced_latte.jpg', 400, 400);

        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openNewProductModal')
            ->assertSet('showProductModal', true)
            ->set('productName', 'Iced Spanish Latte')
            ->set('productCategoryId', $cat->id)
            ->set('productBasePrice', 32000)
            ->set('productCostPrice', 11000)
            ->set('productImageFile', $fakeProductImage)
            ->call('saveProduct')
            ->assertSet('showProductModal', false);

        $product = Product::where('name', 'Iced Spanish Latte')->first();
        $this->assertNotNull($product);
        $this->assertNotNull($product->image_url);
        $this->assertStringStartsWith('/storage/products/', $product->image_url);

        // Verify stored in public disk
        $relativeDiskPath = str_replace('/storage/', '', $product->image_url);
        Storage::disk('public')->assertExists($relativeDiskPath);

        // Test removeProductImage method
        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('editProduct', $product->id)
            ->assertSet('productImageUrl', $product->image_url)
            ->call('removeProductImage')
            ->assertSet('productImageFile', null)
            ->assertSet('productImageUrl', '')
            ->call('saveProduct');

        $this->assertNull($product->fresh()->image_url);
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

    public function test_super_admin_can_switch_tenant_in_backoffice()
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $fnbTenant = Tenant::where('slug', 'kopiruangpagi')->first();
        $salonTenant = Tenant::where('slug', 'studiorona')->first();

        Livewire::actingAs($superAdmin, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTenant', $salonTenant->id)
            ->assertSet('tenantId', $salonTenant->id)
            ->assertSee('Studio Rona');
    }

    public function test_tenant_owner_cannot_switch_to_another_business()
    {
        $fnbOwner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $fnbTenant = Tenant::where('slug', 'kopiruangpagi')->first();
        $salonTenant = Tenant::where('slug', 'studiorona')->first();

        Livewire::actingAs($fnbOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->assertSet('tenantId', $fnbTenant->id)
            ->assertDontSee('Super Admin View:')
            ->call('switchTenant', $salonTenant->id)
            ->assertSet('tenantId', $fnbTenant->id) // Tetap terkunci di tenant pemiliknya
            ->assertDontSee('Studio Rona (Salon & Barbershop)');
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

    public function test_can_upload_service_image_file_and_remove_image()
    {
        Storage::fake('public');

        $salonOwner = User::where('email', 'contact@studiorona.id')->first();
        $salonTenant = Tenant::where('slug', 'studiorona')->first();
        $cat = Category::where('tenant_id', $salonTenant->id)->first();

        $fakeImage = UploadedFile::fake()->image('treatment.jpg', 400, 400);

        $component = Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openNewServiceModal')
            ->assertSet('showServiceModal', true)
            ->set('serviceName', 'Facial Glow Treatment')
            ->set('serviceCategoryId', $cat->id)
            ->set('servicePrice', 95000)
            ->set('serviceDuration', 50)
            ->set('serviceCommissionType', 'fixed')
            ->set('serviceCommissionValue', 25000)
            ->set('serviceImageFile', $fakeImage)
            ->call('saveService')
            ->assertSet('showServiceModal', false);

        $service = Product::where('tenant_id', $salonTenant->id)
            ->where('name', 'Facial Glow Treatment')
            ->first();

        $this->assertNotNull($service);
        $this->assertNotNull($service->image_url);
        $this->assertStringStartsWith('/storage/services/', $service->image_url);

        // Verify file stored in public disk
        $relativeDiskPath = str_replace('/storage/', '', $service->image_url);
        Storage::disk('public')->assertExists($relativeDiskPath);

        // Test removeServiceImage method
        Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('editService', $service->id)
            ->assertSet('serviceImageUrl', $service->image_url)
            ->call('removeServiceImage')
            ->assertSet('serviceImageFile', null)
            ->assertSet('serviceImageUrl', '')
            ->call('saveService');

        $this->assertNull($service->fresh()->image_url);
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

    public function test_can_create_and_edit_outlet_branch()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $tenant = Tenant::first();

        // 1. Create New Outlet Branch
        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openNewOutletModal')
            ->assertSet('showOutletModal', true)
            ->set('outletName', 'Cabang Bandung Dago')
            ->set('outletAddress', 'Jl. Ir. H. Juanda No. 102, Dago, Bandung')
            ->set('outletPhone', '081223344556')
            ->set('outletTaxRate', 10.0)
            ->set('outletServiceRate', 5.0)
            ->set('outletReceiptFooter', 'Hatur nuhun tos sumping ka Kopi Ruang Pagi Dago!')
            ->call('saveOutlet')
            ->assertSet('showOutletModal', false);

        $outlet = Outlet::where('tenant_id', $tenant->id)
            ->where('name', 'Cabang Bandung Dago')
            ->first();

        $this->assertNotNull($outlet);
        $this->assertEquals('Jl. Ir. H. Juanda No. 102, Dago, Bandung', $outlet->address);
        $this->assertEquals(10.0, (float) $outlet->tax_rate);
        $this->assertEquals(5.0, (float) $outlet->service_rate);
        $this->assertTrue($outlet->is_active);

        // 2. Edit Outlet Branch
        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('editOutlet', $outlet->id)
            ->assertSet('showOutletModal', true)
            ->assertSet('outletName', 'Cabang Bandung Dago')
            ->set('outletPhone', '081299998888')
            ->call('saveOutlet');

        $this->assertEquals('081299998888', $outlet->fresh()->phone);

        // 3. Toggle Outlet Status
        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('toggleOutletStatus', $outlet->id);

        $this->assertFalse($outlet->fresh()->is_active);
    }

    public function test_can_create_and_manage_staff_cashier_accounts()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $tenant = Tenant::first();
        $outlet = Outlet::where('tenant_id', $tenant->id)->first();

        // 1. Create New Cashier Account
        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openNewStaffModal')
            ->assertSet('showStaffModal', true)
            ->set('staffName', 'Siti Rahmawati')
            ->set('staffEmail', 'siti.kasir@kopiruangpagi.id')
            ->set('staffPassword', 'secret123')
            ->set('staffRole', 'cashier')
            ->set('staffOutletId', $outlet->id)
            ->set('staffPinCode', '345678')
            ->set('staffPhone', '081288887777')
            ->call('saveStaff')
            ->assertSet('showStaffModal', false);

        $staff = User::where('email', 'siti.kasir@kopiruangpagi.id')->first();
        $this->assertNotNull($staff);
        $this->assertEquals('Siti Rahmawati', $staff->name);
        $this->assertEquals('cashier', $staff->role);
        $this->assertEquals($outlet->id, $staff->outlet_id);
        $this->assertEquals('345678', $staff->pin_code);
        $this->assertTrue($staff->is_active);

        // 2. Edit Staff Account
        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('editStaff', $staff->id)
            ->assertSet('showStaffModal', true)
            ->set('staffRole', 'manager')
            ->set('staffPinCode', '987654')
            ->call('saveStaff');

        $staff->refresh();
        $this->assertEquals('manager', $staff->role);
        $this->assertEquals('987654', $staff->pin_code);

        // 3. Toggle Staff Status
        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('toggleStaffStatus', $staff->id);

        $this->assertFalse($staff->fresh()->is_active);

        // 4. Owner cannot be deactivated or deleted
        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('toggleStaffStatus', $owner->id)
            ->assertSee('Akun Pemilik (Owner) tidak dapat dinonaktifkan')
            ->call('deleteStaff', $owner->id)
            ->assertSee('Akun Pemilik (Owner) tidak dapat dihapus');

        $this->assertTrue($owner->fresh()->is_active);

        // 5. Delete Staff
        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('deleteStaff', $staff->id);

        $this->assertNull(User::find($staff->id));
    }

    public function test_cashier_role_cannot_access_backoffice_redirects_to_pos()
    {
        $cashier = User::where('role', 'cashier')->first();
        $this->assertNotNull($cashier);

        Livewire::actingAs($cashier, 'web')
            ->test(BackofficeDashboard::class)
            ->assertRedirect(route('pos.terminal'));
    }

    public function test_owner_can_manage_master_shift_templates_and_policies()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $tenant = Tenant::where('slug', 'kopiruangpagi')->first();

        // 1. View Master Shift in Settings Tab
        $component = Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'settings')
            ->assertSee('Master Shift')
            ->assertSee('Kebijakan Kasir')
            ->assertSee('Shift 1 (Pagi)')
            ->assertSee('Kebijakan Operasional Kasir');

        // 2. Add New Shift Template
        $component->call('openNewShiftTemplateModal')
            ->assertSet('showShiftTemplateModal', true)
            ->set('shiftTemplateName', 'Shift Malam (Midnight)')
            ->set('shiftTemplateStartTime', '23:00')
            ->set('shiftTemplateEndTime', '07:00')
            ->set('shiftTemplateDefaultCash', 150000)
            ->set('shiftTemplateIsActive', true)
            ->call('saveShiftTemplate')
            ->assertSet('showShiftTemplateModal', false);

        $tenant->refresh();
        $templates = $tenant->settings['shift_templates'] ?? [];
        $this->assertTrue(collect($templates)->contains('name', 'Shift Malam (Midnight)'));

        // 3. Save Shift Policies
        $component->set('policyDefaultCash', 250000)
            ->set('policyEnforceLock', true)
            ->set('policyRequireReconciliation', true)
            ->call('saveShiftPolicies');

        $tenant->refresh();
        $this->assertEquals(250000, $tenant->settings['shift_policies']['default_cash']);
        $this->assertTrue($tenant->settings['shift_policies']['enforce_lock']);

        // 4. Toggle Shift Template Status
        $createdTemplate = collect($tenant->settings['shift_templates'])->firstWhere('name', 'Shift Malam (Midnight)');
        $this->assertNotNull($createdTemplate);

        $component->call('toggleShiftTemplateStatus', $createdTemplate['id']);
        $tenant->refresh();
        $updated = collect($tenant->settings['shift_templates'])->firstWhere('id', $createdTemplate['id']);
        $this->assertFalse($updated['is_active']);

        // 5. Delete Shift Template
        $component->call('deleteShiftTemplate', $createdTemplate['id']);
        $tenant->refresh();
        $this->assertFalse(collect($tenant->settings['shift_templates'])->contains('id', $createdTemplate['id']));
    }

    public function test_can_manage_supplier_crud()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $tenant = Tenant::first();

        // 1. Create Supplier
        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openNewSupplierModal')
            ->assertSet('showSupplierModal', true)
            ->set('supplierName', 'PT Aneka Bahan Nusantara')
            ->set('supplierContactPerson', 'Rahmat Hidayat')
            ->set('supplierPhone', '081299775533')
            ->set('supplierEmail', 'sales@anekabahan.co.id')
            ->set('supplierAddress', 'Kawasan Industri Cikarang Blok A-5')
            ->set('supplierTermsDays', 14)
            ->set('supplierIsActive', true)
            ->call('saveSupplier')
            ->assertSet('showSupplierModal', false);

        $supplier = Supplier::where('tenant_id', $tenant->id)
            ->where('name', 'PT Aneka Bahan Nusantara')
            ->first();

        $this->assertNotNull($supplier);
        $this->assertEquals('Rahmat Hidayat', $supplier->contact_person);
        $this->assertEquals(14, $supplier->terms_days);
        $this->assertTrue($supplier->is_active);

        // 2. Edit Supplier
        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('editSupplier', $supplier->id)
            ->assertSet('showSupplierModal', true)
            ->assertSet('supplierName', 'PT Aneka Bahan Nusantara')
            ->set('supplierTermsDays', 30)
            ->call('saveSupplier')
            ->assertSet('showSupplierModal', false);

        $this->assertEquals(30, $supplier->fresh()->terms_days);

        // 3. Toggle Supplier Status
        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('toggleSupplierStatus', $supplier->id);

        $this->assertFalse($supplier->fresh()->is_active);

        // 4. Delete Supplier
        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('deleteSupplier', $supplier->id);

        $this->assertNull(Supplier::find($supplier->id));
    }

    public function test_can_filter_and_manage_purchase_orders()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $tenant = Tenant::first();
        $supplier = Supplier::where('tenant_id', $tenant->id)->first();

        // Create a PO
        $component = Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openPoModal', $supplier->id)
            ->assertSet('showPoModal', true)
            ->assertSet('poSupplierId', $supplier->id)
            ->set('poTotalAmount', 2500000)
            ->set('poNotes', 'Pengadaan Biji Kopi Arabika 50kg')
            ->call('savePurchaseOrder')
            ->assertSet('showPoModal', false);

        $po = PurchaseOrder::where('tenant_id', $tenant->id)
            ->where('notes', 'Pengadaan Biji Kopi Arabika 50kg')
            ->first();

        $this->assertNotNull($po);
        $this->assertEquals('ordered', $po->status);

        // Test view detail PO
        $component->call('switchTab', 'purchases')
            ->call('closeToast')
            ->call('viewPoDetail', $po->id)
            ->assertSet('showPoDetailModal', true)
            ->assertSet('viewingPoId', $po->id)
            ->set('showPoDetailModal', false);

        // Test Filter by status
        $component->set('poStatusFilter', 'ordered')
            ->assertSee($po->po_number)
            ->set('poStatusFilter', 'received')
            ->assertDontSee($po->po_number);

        // Test Search PO
        $component->set('poStatusFilter', 'all')
            ->set('searchPo', 'Arabika 50kg')
            ->assertSee($po->po_number)
            ->set('searchPo', 'NonExistentString12345')
            ->assertDontSee($po->po_number);

        // Test Date Filter
        $component->set('searchPo', '')
            ->set('poDateFilter', 'today')
            ->assertSee($po->po_number);

        // Test Mark PO Received (GRN)
        $component->call('markPoReceived', $po->id);
        $this->assertEquals('received', $po->fresh()->status);

        // Test Reset Filters
        $component->call('resetPoFilters')
            ->assertSet('searchPo', '')
            ->assertSet('poStatusFilter', 'all')
            ->assertSet('poDateFilter', 'all');
    }

    public function test_salon_stock_opname_manages_direct_products_with_timestamp_and_audit()
    {
        $salonTenant = Tenant::where('slug', 'studiorona')->first();
        $salonOwner = User::where('tenant_id', $salonTenant->id)->where('role', 'owner')->first();

        $product = Product::where('tenant_id', $salonTenant->id)
            ->where('type', 'item')
            ->first();
        $this->assertNotNull($product, 'Salon retail product should exist');

        $auditTime = '2026-10-31T14:30';

        Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openOpnameModal', $product->id)
            ->assertSet('showOpnameModal', true)
            ->assertSet('opnameItemType', 'product')
            ->assertSet('opnameProductId', $product->id)
            ->set('opnamePhysicalQty', 22)
            ->set('opnameRecordedAt', $auditTime)
            ->set('opnameReasonCategory', 'Pemeriksaan Rutin')
            ->set('opnameReason', 'Audit fisik rutin rak display styling')
            ->call('executeOpname')
            ->assertSet('showOpnameModal', false);

        $product->refresh();
        $this->assertEquals(22, (float) $product->stock_quantity);

        // Verify StockMovement recorded accurately
        $movement = StockMovement::where('tenant_id', $salonTenant->id)
            ->where('product_id', $product->id)
            ->where('type', 'opname')
            ->latest('id')
            ->first();

        $this->assertNotNull($movement);
        $this->assertEquals(22, (float) $movement->balance_after);
        $this->assertEquals($salonOwner->id, $movement->user_id);
        $this->assertStringContainsString('Stok Opname Produk', $movement->reference_note);
        $this->assertStringContainsString('Audit fisik rutin rak display styling', $movement->reference_note);
        $this->assertEquals('2026-10-31 14:30:00', $movement->created_at->format('Y-m-d H:i:s'));
    }

    public function test_stock_movement_filters_by_search_type_item_type_and_date_time_range()
    {
        $salonTenant = Tenant::where('slug', 'studiorona')->first();
        $salonOwner = User::where('tenant_id', $salonTenant->id)->where('role', 'owner')->first();
        $outlet = Outlet::where('tenant_id', $salonTenant->id)->first();

        $product = Product::where('tenant_id', $salonTenant->id)->where('type', 'item')->first();

        // Create explicit stock movements for testing filters
        $mv1 = StockMovement::create([
            'tenant_id' => $salonTenant->id,
            'outlet_id' => $outlet->id,
            'product_id' => $product->id,
            'user_id' => $salonOwner->id,
            'type' => 'opname',
            'qty' => 5,
            'balance_after' => 25,
            'reference_note' => 'Opname UnikXYZ Test Marker',
            'created_at' => Carbon::parse('2026-10-25 10:15:00'),
            'updated_at' => Carbon::parse('2026-10-25 10:15:00'),
        ]);

        $mv2 = StockMovement::create([
            'tenant_id' => $salonTenant->id,
            'outlet_id' => $outlet->id,
            'product_id' => $product->id,
            'user_id' => $salonOwner->id,
            'type' => 'sale_pos',
            'qty' => -3,
            'balance_after' => 22,
            'reference_note' => 'Penjualan POS Kasir Reguler',
            'created_at' => Carbon::parse('2026-10-30 16:30:00'),
            'updated_at' => Carbon::parse('2026-10-30 16:30:00'),
        ]);

        $component = Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->set('activeTab', 'stock')
            ->set('stockActiveSubTab', 'ledger');

        // 1. Search Filter
        $component->set('stockSearch', 'UnikXYZ')
            ->assertSee('Opname UnikXYZ Test Marker')
            ->assertDontSee('Penjualan POS Kasir Reguler');

        // 2. Mutation Type Filter
        $component->set('stockSearch', '')
            ->set('stockTypeFilter', 'sale_pos')
            ->assertSee('Penjualan POS Kasir Reguler')
            ->assertDontSee('Opname UnikXYZ Test Marker');

        // 3. Item Type Filter
        $component->set('stockTypeFilter', 'all')
            ->set('stockItemTypeFilter', 'product')
            ->assertSee($product->name);

        // 4. Custom Date and Time Filter
        $component->set('stockDateFilter', 'custom')
            ->set('stockStartDate', '2026-10-24')
            ->set('stockStartTime', '00:00')
            ->set('stockEndDate', '2026-10-26')
            ->set('stockEndTime', '23:59')
            ->assertSee('Opname UnikXYZ Test Marker')
            ->assertDontSee('Penjualan POS Kasir Reguler');

        // 5. Reset Filters
        $component->call('resetStockFilters')
            ->assertSet('stockSearch', '')
            ->assertSet('stockTypeFilter', 'all')
            ->assertSet('stockItemTypeFilter', 'all')
            ->assertSet('stockDateFilter', 'all')
            ->assertSet('stockStartDate', null)
            ->assertSet('stockEndDate', null);
    }

    public function test_page_refresh_preserves_active_tab_and_does_not_reset_to_dashboard()
    {
        $salonOwner = User::where('tenant_id', 3)->where('role', 'owner')->first();

        // 1. Test via query string parameter ?tab=stock
        $this->actingAs($salonOwner, 'web')
            ->get('/app?tab=stock')
            ->assertOk()
            ->assertSee('Stok & Opname Produk Ritel Salon');

        // 2. Test switching tab updates session and Livewire keeps activeTab
        Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'stock')
            ->assertSet('activeTab', 'stock');

        $this->assertEquals('stock', session('tenant_active_tab'));

        // 3. Test mounting without query string falls back to session
        Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->assertSet('activeTab', 'stock');
    }
}
