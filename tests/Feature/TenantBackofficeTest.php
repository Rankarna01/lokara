<?php

namespace Tests\Feature;

use App\Livewire\Tenant\BackofficeDashboard;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Expense;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
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
use Illuminate\Support\Facades\Hash;
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

    public function test_putrybeautycare_salon_owner_sees_master_layanan_from_seeder()
    {
        $owner = User::where('email', 'arsihapis@putribeautycare.site')->first();
        $this->assertNotNull($owner);
        $this->assertEquals('owner', $owner->role);
        $this->assertTrue(Hash::check('password123', $owner->password));

        $tenant = Tenant::where('slug', 'putrybeautycare')->first();
        $this->assertEquals($tenant->id, $owner->tenant_id);
        $this->assertTrue($tenant->isSalon());

        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->assertSee('Master Layanan')
            ->call('switchTab', 'services')
            ->assertSee('Facial Glow Brightening')
            ->assertSee('Makeup Natural Harian');
    }

    public function test_salon_transaction_history_exports_excel_and_pdf()
    {
        $owner = User::where('email', 'arsihapis@putribeautycare.site')->first();

        $component = Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'transactions')
            ->assertSee('ORD-PBC-001');

        $component->call('exportTransactionsExcel')
            ->assertFileDownloaded(null, null, 'application/vnd.ms-excel; charset=UTF-8');

        $component->call('exportTransactionsPdf')
            ->assertDispatched('open-transactions-pdf');

        $this->actingAs($owner)
            ->get('/app/transactions/pdf/kunci-tidak-valid')
            ->assertNotFound();
    }

    public function test_salon_hides_order_type_filter_but_fnb_keeps_it()
    {
        $salonOwner = User::where('email', 'arsihapis@putribeautycare.site')->first();
        $fnbOwner = User::where('email', 'rian@kopiruangpagi.id')->first();

        Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'transactions')
            ->assertDontSee('Semua Tipe Order');

        Livewire::actingAs($fnbOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'transactions')
            ->assertSee('Semua Tipe Order');
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

    public function test_empty_cost_price_is_saved_as_zero_for_service_and_product()
    {
        $salonOwner = User::where('email', 'contact@studiorona.id')->first();
        $salonTenant = Tenant::where('slug', 'studiorona')->first();
        $cat = Category::where('tenant_id', $salonTenant->id)->first();

        Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openNewServiceModal')
            ->set('serviceName', 'Layanan Modal Kosong')
            ->set('serviceCategoryId', $cat->id)
            ->set('servicePrice', 50000)
            ->set('serviceDuration', 30)
            ->set('serviceCostPrice', '')
            ->call('saveService')
            ->assertHasNoErrors()
            ->assertSet('showServiceModal', false);

        $this->assertEquals(0, (float) Product::where('name', 'Layanan Modal Kosong')->first()->cost_price);

        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $tenant = Tenant::where('slug', 'kopiruangpagi')->first();
        $productCat = Category::where('tenant_id', $tenant->id)->first();

        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openNewProductModal')
            ->set('productName', 'Produk Modal Kosong')
            ->set('productCategoryId', $productCat->id)
            ->set('productBasePrice', 20000)
            ->set('productCostPrice', null)
            ->call('saveProduct')
            ->assertHasNoErrors()
            ->assertSet('showProductModal', false);

        $this->assertEquals(0, (float) Product::where('name', 'Produk Modal Kosong')->first()->cost_price);
    }

    public function test_service_and_product_tables_paginate_ten_per_page_and_reset_on_search()
    {
        $salonOwner = User::where('email', 'contact@studiorona.id')->first();
        $salonTenant = Tenant::where('slug', 'studiorona')->first();
        $salonCategory = Category::where('tenant_id', $salonTenant->id)->first();

        foreach (range(1, 12) as $number) {
            $label = str_pad((string) $number, 2, '0', STR_PAD_LEFT);
            Product::create([
                'tenant_id' => $salonTenant->id,
                'category_id' => $salonCategory->id,
                'type' => 'service',
                'name' => "Layanan Halaman {$label}",
                'slug' => "layanan-halaman-{$label}",
                'sku' => "SRV-PAGE{$label}",
                'base_price' => 50000,
                'duration_minutes' => 30,
                'commission_type' => 'fixed',
                'commission_value' => 0,
                'is_active' => true,
                'is_available' => true,
            ]);
        }

        Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'services')
            ->assertSee('Layanan Halaman 12')
            ->assertDontSee('Layanan Halaman 01')
            ->call('gotoPage', 2, 'servicesPage')
            ->assertSee('Layanan Halaman 01')
            ->set('searchService', 'Halaman 1')
            ->assertSee('Layanan Halaman 10');

        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $tenant = Tenant::where('slug', 'kopiruangpagi')->first();
        $productCategory = Category::where('tenant_id', $tenant->id)->first();

        foreach (range(1, 12) as $number) {
            $label = str_pad((string) $number, 2, '0', STR_PAD_LEFT);
            Product::create([
                'tenant_id' => $tenant->id,
                'category_id' => $productCategory->id,
                'type' => 'item',
                'name' => "Produk Halaman {$label}",
                'slug' => "produk-halaman-{$label}",
                'sku' => "PRD-PAGE{$label}",
                'base_price' => 20000,
                'is_active' => true,
                'is_available' => true,
            ]);
        }

        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'products')
            ->assertSee('Produk Halaman 12')
            ->assertDontSee('Produk Halaman 01')
            ->call('gotoPage', 2, 'productsPage')
            ->assertSee('Produk Halaman 01')
            ->set('searchProduct', 'Halaman 1')
            ->assertSee('Produk Halaman 10');
    }

    public function test_salon_finance_hides_commission_scheme_and_uses_entered_material_cost()
    {
        $salonOwner = User::where('email', 'arsihapis@putribeautycare.site')->first();
        $salonTenant = Tenant::where('slug', 'putrybeautycare')->first();
        $cat = Category::where('tenant_id', $salonTenant->id)->first();

        Product::create([
            'tenant_id' => $salonTenant->id,
            'category_id' => $cat->id,
            'type' => 'service',
            'name' => 'Layanan Modal Nol',
            'slug' => 'layanan-modal-nol',
            'sku' => 'SRV-MODALNOL',
            'base_price' => 60000,
            'cost_price' => 0,
            'duration_minutes' => 30,
            'commission_type' => 'fixed',
            'commission_value' => 10000,
            'is_active' => true,
            'is_available' => true,
        ]);

        Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'finance')
            ->call('setFinanceSubTab', 'services')
            ->assertSee('Layanan Modal Nol')
            ->assertDontSee('Skema Komisi')
            ->assertDontSee('Rp 5.000');

        $barberTenant = Tenant::where('slug', 'gentlemanbarbershop')->first();
        $barberOwner = User::where('tenant_id', $barberTenant->id)->where('role', 'owner')->first();

        Livewire::actingAs($barberOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'finance')
            ->call('setFinanceSubTab', 'services')
            ->assertSee('Skema Komisi');
    }

    public function test_opex_with_past_date_follows_profit_loss_period_filter()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $pastDate = now()->subMonths(2)->toDateString();

        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openExpenseModal')
            ->set('expenseCategory', 'Sewa Tempat')
            ->set('expenseDate', $pastDate)
            ->set('expenseTitle', 'Sewa Ruko Bulan Lalu')
            ->set('expenseAmount', 777000)
            ->call('saveExpense')
            ->assertHasNoErrors()
            ->assertSet('showExpenseModal', false);

        $this->assertTrue(
            Expense::where('tenant_id', $owner->tenant_id)
                ->where('title', 'Sewa Ruko Bulan Lalu')
                ->whereDate('expense_date', $pastDate)
                ->exists()
        );

        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'finance')
            ->set('financeDateFilter', 'this_month')
            ->assertDontSee('Sewa Ruko Bulan Lalu')
            ->set('financeDateFilter', 'all')
            ->assertSee('Sewa Ruko Bulan Lalu');
    }

    public function test_opex_rejects_future_date_and_adds_new_category_in_modal()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();

        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openExpenseModal')
            ->set('expenseDate', now()->addDay()->toDateString())
            ->set('expenseTitle', 'Pengeluaran Masa Depan')
            ->set('expenseAmount', 50000)
            ->call('saveExpense')
            ->assertHasErrors(['expenseDate']);

        Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openExpenseModal')
            ->set('showNewExpenseCategory', true)
            ->set('expenseNewCategory', 'Perlengkapan Uji')
            ->call('addExpenseCategory')
            ->assertSet('expenseCategory', 'Perlengkapan Uji')
            ->assertSet('showNewExpenseCategory', false);

        $this->assertDatabaseHas('expense_categories', [
            'tenant_id' => $owner->tenant_id,
            'name' => 'Perlengkapan Uji',
        ]);
    }

    public function test_service_fee_column_is_shown_and_editable()
    {
        $salonOwner = User::where('email', 'contact@studiorona.id')->first();
        $salonTenant = Tenant::where('slug', 'studiorona')->first();
        $cat = Category::where('tenant_id', $salonTenant->id)->first();

        $service = Product::create([
            'tenant_id' => $salonTenant->id,
            'category_id' => $cat->id,
            'type' => 'service',
            'name' => 'Layanan Uji Fee',
            'slug' => 'layanan-uji-fee',
            'sku' => 'SRV-UJIFEE',
            'base_price' => 100000,
            'cost_price' => 0,
            'duration_minutes' => 30,
            'commission_type' => 'percentage',
            'commission_value' => 20,
            'is_active' => true,
            'is_available' => true,
        ]);

        Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'services')
            ->assertSee('Fee Kapster')
            ->assertSee('Rp 20.000')
            ->assertSee('20% dari tarif')
            ->call('editService', $service->id)
            ->assertSet('serviceCommissionType', 'percentage')
            ->set('serviceCommissionType', 'fixed')
            ->set('serviceCommissionValue', 15000)
            ->call('saveService')
            ->assertHasNoErrors();

        $service->refresh();
        $this->assertSame('fixed', $service->commission_type);
        $this->assertEquals(15000, (float) $service->commission_value);
    }

    public function test_new_service_defaults_fee_and_material_cost_to_zero()
    {
        $salonOwner = User::where('email', 'arsihapis@putribeautycare.site')->first();
        $salonTenant = Tenant::where('slug', 'putrybeautycare')->first();
        $cat = Category::where('tenant_id', $salonTenant->id)->first();

        Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('openNewServiceModal')
            ->set('serviceName', 'Layanan Default Nol')
            ->set('serviceCategoryId', $cat->id)
            ->set('servicePrice', 80000)
            ->set('serviceDuration', 45)
            ->call('saveService')
            ->assertHasNoErrors();

        $service = Product::where('tenant_id', $salonTenant->id)->where('name', 'Layanan Default Nol')->first();
        $this->assertEquals(0, (float) $service->commission_value);
        $this->assertEquals(0, (float) $service->cost_price);
    }

    public function test_salon_master_layanan_hides_material_cost_but_barbershop_keeps_it()
    {
        $salonOwner = User::where('email', 'arsihapis@putribeautycare.site')->first();

        Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'services')
            ->assertDontSee('Modal Bahan')
            ->call('openNewServiceModal')
            ->assertDontSee('Modal Bahan Produk');

        $barberTenant = Tenant::where('slug', 'gentlemanbarbershop')->first();
        $barberOwner = User::where('tenant_id', $barberTenant->id)->where('role', 'owner')->first();

        Livewire::actingAs($barberOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'services')
            ->assertSee('Modal Bahan')
            ->call('openNewServiceModal')
            ->assertSee('Modal Bahan Produk');
    }

    public function test_service_with_order_history_cannot_be_deleted()
    {
        $salonOwner = User::where('email', 'contact@studiorona.id')->first();
        $salonTenant = Tenant::where('slug', 'studiorona')->first();
        $service = Product::where('tenant_id', $salonTenant->id)
            ->where('name', 'like', '%Gentleman Haircut%')
            ->first();

        Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('deleteService', $service->id)
            ->assertSet('toastType', 'error')
            ->assertSee('tidak bisa dihapus');

        $this->assertNotNull($service->fresh());
    }

    public function test_service_without_order_history_can_be_deleted()
    {
        $salonOwner = User::where('email', 'contact@studiorona.id')->first();
        $salonTenant = Tenant::where('slug', 'studiorona')->first();
        $cat = Category::where('tenant_id', $salonTenant->id)->first();

        $service = Product::create([
            'tenant_id' => $salonTenant->id,
            'category_id' => $cat->id,
            'type' => 'service',
            'name' => 'Layanan Uji Hapus',
            'slug' => 'layanan-uji-hapus',
            'sku' => 'SRV-TESTDEL',
            'base_price' => 50000,
            'duration_minutes' => 30,
            'commission_type' => 'fixed',
            'commission_value' => 0,
            'is_active' => true,
            'is_available' => true,
        ]);

        Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('deleteService', $service->id)
            ->assertSet('toastType', 'success');

        $this->assertNull(Product::find($service->id));
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

    public function test_salon_laba_rugi_has_two_tabs_for_products_and_services_with_filtering_and_searching()
    {
        $salonOwner = User::where('email', 'contact@studiorona.id')->first();
        if (! $salonOwner) {
            $salonTenant = Tenant::where('slug', 'studiorona')->first();
            $salonOwner = User::where('tenant_id', $salonTenant->id)->first();
        }

        $component = Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'finance')
            ->assertSet('activeTab', 'finance')
            ->assertSet('financeSubTab', 'products')
            ->assertSee('Ringkasan Laba Rugi Salon', false)
            ->assertSee('1. Penjualan Produk Master')
            ->assertSee('2. Penjualan Master Layanan')
            ->assertSee('Pomade Strong Matte Clay 100g');

        // 1. Test Switch to Tab 2: Penjualan Master Layanan
        $component->call('setFinanceSubTab', 'services')
            ->assertSet('financeSubTab', 'services')
            ->assertSee('Rincian Laba Rugi Master Layanan Jasa')
            ->assertSee('Gentleman Haircut & Styling')
            ->assertSee('Modal Bahan')
            ->assertSee('Komisi Kapster')
            ->assertSee('Laba Bersih Layanan');

        // 2. Test Searching in Master Layanan
        $component->set('financeSearch', 'Creambath')
            ->assertSee('Creambath Spa & Head Massage')
            ->assertDontSee('Gentleman Haircut & Styling');

        // 3. Test Switch back to Tab 1: Penjualan Produk Master & Search
        $component->call('setFinanceSubTab', 'products')
            ->assertSet('financeSubTab', 'products')
            ->set('financeSearch', 'Shampoo')
            ->assertSee('Shampoo Anti-Dandruff Professional 500ml')
            ->assertDontSee('Pomade Strong Matte Clay 100g');

        // 4. Test Sorting & Date Filtering
        $component->set('financeSortBy', 'qty_desc')
            ->set('financeDateFilter', 'all')
            ->assertSet('financeSortBy', 'qty_desc')
            ->assertSet('financeDateFilter', 'all');

        // 5. Test Reset Filters
        $component->call('resetFinanceFilters')
            ->assertSet('financeSearch', '')
            ->assertSet('financeCategoryFilter', 'all')
            ->assertSet('financeDateFilter', 'this_month')
            ->assertSet('financeSortBy', 'profit_desc');
    }

    public function test_transaction_history_tab_renders_with_relations_chart_and_metrics()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $tenant = Tenant::find($owner->tenant_id);
        $outlet = Outlet::where('tenant_id', $tenant->id)->first();
        $product = Product::where('tenant_id', $tenant->id)->first();

        // Ensure order has complete relations
        $customer = Customer::create([
            'tenant_id' => $tenant->id,
            'name' => 'Budi Santoso',
            'phone' => '081299887766',
        ]);

        $order = Order::create([
            'tenant_id' => $tenant->id,
            'outlet_id' => $outlet->id,
            'customer_id' => $customer->id,
            'user_id' => $owner->id,
            'order_number' => 'ORD-TEST-001',
            'order_type' => 'dine_in',
            'table_number' => '05',
            'subtotal' => 50000,
            'discount_amount' => 5000,
            'tax_amount' => 4500,
            'service_amount' => 0,
            'total_amount' => 49500,
            'status' => 'completed',
            'payment_status' => 'paid',
            'paid_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);

        OrderItem::create([
            'tenant_id' => $tenant->id,
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'qty' => 2,
            'unit_price' => 25000,
            'subtotal' => 50000,
        ]);

        OrderPayment::create([
            'tenant_id' => $tenant->id,
            'order_id' => $order->id,
            'payment_method' => 'cash',
            'amount' => 49500,
            'cash_tendered' => 50000,
            'change_due' => 500,
            'reference_number' => 'REF-TEST-001',
        ]);

        $component = Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'transactions')
            ->assertSet('activeTab', 'transactions')
            ->assertSee('Riwayat Transaksi Penjualan')
            ->assertSee('Audit log struk belanja')
            ->assertSee('Total Omset')
            ->assertSee('Transaksi Lunas')
            ->assertSee('Rata-rata Struk (AOV)')
            ->assertSee('Pembayaran Terfavorit')
            ->assertSee('Tren Penjualan & Frekuensi Transaksi POS', false)
            ->assertSee('Sebaran Metode Bayar')
            ->assertSee('Distribusi Tipe Pesanan')
            ->assertSee('ORD-TEST-001')
            ->assertSee('Budi Santoso');

        // Test metric switch in chart
        $component->call('setTransactionTrendMetric', 'count')
            ->assertSet('transactionTrendMetric', 'count')
            ->call('setTransactionTrendMetric', 'revenue')
            ->assertSet('transactionTrendMetric', 'revenue');

        // Test period switch in chart
        $component->call('setTransactionTrendPeriod', '14_days')
            ->assertSet('transactionTrendPeriod', '14_days')
            ->call('setTransactionTrendPeriod', '7_days')
            ->assertSet('transactionTrendPeriod', '7_days');
    }

    public function test_transaction_filters_by_search_status_payment_method_and_date()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $tenant = Tenant::find($owner->tenant_id);
        $outlet = Outlet::where('tenant_id', $tenant->id)->first();
        $product = Product::where('tenant_id', $tenant->id)->first();

        $order = Order::create([
            'tenant_id' => $tenant->id,
            'outlet_id' => $outlet->id,
            'user_id' => $owner->id,
            'order_number' => 'ORD-FILTER-002',
            'order_type' => 'dine_in',
            'table_number' => '02',
            'subtotal' => 60000,
            'discount_amount' => 0,
            'tax_amount' => 6000,
            'service_amount' => 0,
            'total_amount' => 66000,
            'status' => 'completed',
            'payment_status' => 'paid',
            'paid_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);

        OrderItem::create([
            'tenant_id' => $tenant->id,
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'qty' => 1,
            'unit_price' => 60000,
            'subtotal' => 60000,
        ]);

        OrderPayment::create([
            'tenant_id' => $tenant->id,
            'order_id' => $order->id,
            'payment_method' => 'cash',
            'amount' => 66000,
            'cash_tendered' => 70000,
            'change_due' => 4000,
            'reference_number' => 'REF-FILTER-002',
        ]);

        $component = Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'transactions')
            ->set('searchTransaction', 'ORD-FILTER-002')
            ->assertSee('ORD-FILTER-002')
            ->set('searchTransaction', 'NON_EXISTENT_ORDER_9999')
            ->assertSee('Tidak Ada Transaksi Ditemukan')
            ->call('resetTransactionFilters')
            ->assertSet('searchTransaction', '')
            ->assertSet('transactionStatusFilter', 'all')
            ->assertSet('transactionPaymentMethodFilter', 'all')
            ->assertSet('transactionDateFilter', '30_days');

        // Test Status Filter
        $component->set('transactionStatusFilter', 'completed')
            ->assertSet('transactionStatusFilter', 'completed');

        // Test Payment Method Filter
        $component->set('transactionPaymentMethodFilter', 'cash')
            ->assertSet('transactionPaymentMethodFilter', 'cash');

        // Test Custom Date Filter
        $component->call('setTransactionDateFilter', 'custom')
            ->assertSet('transactionDateFilter', 'custom')
            ->set('transactionStartDate', '2026-10-01')
            ->set('transactionEndDate', '2026-10-31')
            ->assertSet('transactionStartDate', '2026-10-01');
    }

    public function test_transaction_detail_modal_and_print_receipt_modal()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $tenant = Tenant::find($owner->tenant_id);
        $outlet = Outlet::where('tenant_id', $tenant->id)->first();
        $product = Product::where('tenant_id', $tenant->id)->first();

        $order = Order::create([
            'tenant_id' => $tenant->id,
            'outlet_id' => $outlet->id,
            'user_id' => $owner->id,
            'order_number' => 'ORD-MODAL-003',
            'order_type' => 'dine_in',
            'table_number' => '03',
            'subtotal' => 40000,
            'discount_amount' => 0,
            'tax_amount' => 4000,
            'service_amount' => 0,
            'total_amount' => 44000,
            'status' => 'completed',
            'payment_status' => 'paid',
            'paid_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);

        OrderItem::create([
            'tenant_id' => $tenant->id,
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'qty' => 1,
            'unit_price' => 40000,
            'subtotal' => 40000,
        ]);

        OrderPayment::create([
            'tenant_id' => $tenant->id,
            'order_id' => $order->id,
            'payment_method' => 'cash',
            'amount' => 44000,
            'cash_tendered' => 50000,
            'change_due' => 6000,
            'reference_number' => 'REF-MODAL-003',
        ]);

        $component = Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'transactions')
            ->call('viewTransactionDetail', $order->id)
            ->assertSet('showTransactionDetailModal', true)
            ->assertSet('selectedTransactionId', $order->id)
            ->assertSee($order->order_number)
            ->assertSee('Rincian Item Pesanan')
            ->call('closeTransactionDetailModal')
            ->assertSet('showTransactionDetailModal', false);

        // Test Print Receipt Modal
        $component->call('openPrintReceiptModal', $order->id)
            ->assertSet('showPrintReceiptModal', true)
            ->assertSet('selectedTransactionId', $order->id)
            ->assertSee('Preview Struk Kasir')
            ->assertSee('Cetak Sekarang')
            ->call('closePrintReceiptModal')
            ->assertSet('showPrintReceiptModal', false);
    }

    public function test_salon_transaction_history_has_separate_tabs_for_services_and_products()
    {
        $salonOwner = User::where('email', 'contact@studiorona.id')->first();
        if (! $salonOwner) {
            $salonTenant = Tenant::where('slug', 'studiorona')->first();
            $salonOwner = User::where('tenant_id', $salonTenant->id)->first();
        }
        $this->assertNotNull($salonOwner, 'Salon owner should exist in seeders.');

        $component = Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'transactions')
            ->assertSee('Riwayat Transaksi Penjualan')
            ->assertSee('Modul Salon & Barbershop', false)
            ->assertSee('Penjualan Layanan (Jasa)')
            ->assertSee('Penjualan Produk (Retail)')
            ->assertSee('Semua Transaksi');

        // Test filtering to services
        $component->call('setTransactionTypeTab', 'services')
            ->assertSet('transactionTypeTab', 'services');

        // Test filtering to retail products
        $component->call('setTransactionTypeTab', 'products')
            ->assertSet('transactionTypeTab', 'products');

        // Test filtering back to all
        $component->call('setTransactionTypeTab', 'all')
            ->assertSet('transactionTypeTab', 'all');

        // Test reset transaction filters resets transactionTypeTab
        $component->call('setTransactionTypeTab', 'services')
            ->call('resetTransactionFilters')
            ->assertSet('transactionTypeTab', 'all');
    }

    public function test_crm_customers_crud_and_detail_view_with_order_history()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $tenant = Tenant::find($owner->tenant_id);

        $component = Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'crm')
            ->assertSet('activeTab', 'crm')
            ->assertSee('Pelanggan & CRM Loyalti', false)
            ->assertSee('Total Pelanggan')
            ->assertSee('Total Poin Loyalti');

        // 1. Open New Customer Modal & Create
        $component->call('openNewCustomerModal')
            ->assertSet('showCustomerModal', true)
            ->set('customerName', 'Dewi Lestari')
            ->set('customerPhone', '081234567899')
            ->set('customerEmail', 'dewi@example.com')
            ->set('customerGender', 'female')
            ->set('customerBirthDate', '1995-08-17')
            ->set('customerAddress', 'Jl. Sudirman No. 10, Jakarta')
            ->set('customerMembershipTier', 'gold')
            ->set('customerPoints', 750)
            ->set('customerNotes', 'Preferensi oat milk dan tanpa gula')
            ->call('saveCustomer')
            ->assertSet('showCustomerModal', false);

        $this->assertDatabaseHas('customers', [
            'tenant_id' => $tenant->id,
            'name' => 'Dewi Lestari',
            'phone' => '081234567899',
            'membership_tier' => 'gold',
            'points' => 750,
        ]);

        $customer = Customer::where('tenant_id', $tenant->id)->where('name', 'Dewi Lestari')->first();

        // 2. Search & Filter
        $component->set('searchCustomer', 'Dewi')
            ->assertSee('Dewi Lestari')
            ->assertSee('750 Pts')
            ->set('searchCustomer', 'NON_EXISTENT_NAME_XYZ')
            ->assertSee('Tidak Ada Pelanggan Ditemukan')
            ->call('resetCustomerFilters')
            ->assertSet('searchCustomer', '');

        // 3. View Customer Detail Modal
        $component->call('viewCustomerDetail', $customer->id)
            ->assertSet('showCustomerDetailModal', true)
            ->assertSet('selectedCustomerId', $customer->id)
            ->assertSee('Dewi Lestari')
            ->assertSee('Preferensi oat milk')
            ->call('closeCustomerDetailModal')
            ->assertSet('showCustomerDetailModal', false);

        // 4. Edit Customer
        $component->call('editCustomer', $customer->id)
            ->assertSet('showCustomerModal', true)
            ->assertSet('editingCustomerId', $customer->id)
            ->set('customerPoints', 900)
            ->call('saveCustomer')
            ->assertSet('showCustomerModal', false);

        $this->assertEquals(900, $customer->fresh()->points);

        // 5. Delete Customer
        $component->call('deleteCustomer', $customer->id);
        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id,
        ]);
    }

    public function test_salon_settings_hides_shift_section_by_default_and_allows_toggling()
    {
        $salonOwner = User::where('tenant_id', 3)->where('role', 'owner')->first();
        if (! $salonOwner) {
            $salonTenant = Tenant::where('slug', 'studiorona')->first();
            $salonOwner = User::where('tenant_id', $salonTenant->id)->first();
        }

        $component = Livewire::actingAs($salonOwner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'settings')
            ->assertSet('activeTab', 'settings')
            ->assertSet('showSalonShiftSection', false)
            ->assertSee('Modul Salon & Barbershop (Operasional Fleksibel)', false)
            ->assertSee('Tampilkan Master Shift')
            ->assertDontSee('Kebijakan Operasional Kasir');

        // Toggle to reveal master shift section
        $component->call('toggleSalonShiftSection')
            ->assertSet('showSalonShiftSection', true)
            ->assertSee('Master Shift & Kebijakan Kasir', false)
            ->assertSee('Sembunyikan Shift')
            ->call('toggleSalonShiftSection')
            ->assertSet('showSalonShiftSection', false);

        // Clicking openNewShiftTemplateModal should also reveal shift and open modal
        $component->call('openNewShiftTemplateModal')
            ->assertSet('showSalonShiftSection', true)
            ->assertSet('showShiftTemplateModal', true);
    }

    public function test_settings_sub_tabs_and_receipt_customization()
    {
        Storage::fake('public');
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $tenant = $owner->tenant;

        $component = Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'settings')
            ->assertSet('settingsSubTab', 'general')
            ->call('switchSettingsSubTab', 'receipt')
            ->assertSet('settingsSubTab', 'receipt')
            ->assertSee('Kustomisasi Struk Thermal')
            ->set('receiptStoreName', 'Kopi Ruang Pagi Signature')
            ->set('receiptTagline', 'Artisan Coffee & Eatery')
            ->set('receiptPaperSize', '80mm')
            ->set('receiptHeaderNotes', 'Selamat Menikmati Racikan Kami')
            ->set('receiptFooterNotes', 'Terima Kasih Banyak, Ditunggu Kembali!')
            ->set('receiptWifiInfo', 'RuangPagi_VIP / 12345678')
            ->set('receiptSocialMedia', '@kopiruangpagi')
            ->set('storeLogoFile', UploadedFile::fake()->image('store_logo.png'))
            ->call('saveReceiptSettings');

        $tenant->refresh();
        $this->assertEquals('Kopi Ruang Pagi Signature', $tenant->settings['receipt_store_name']);
        $this->assertEquals('Artisan Coffee & Eatery', $tenant->settings['receipt_tagline']);
        $this->assertEquals('80mm', $tenant->settings['receipt_paper_size']);
        $this->assertEquals('RuangPagi_VIP / 12345678', $tenant->settings['receipt_wifi_info']);
        $this->assertNotEmpty($tenant->settings['receipt_logo_url']);

        // Test remove logo
        $component->call('removeStoreLogo');
        $tenant->refresh();
        $this->assertNull($tenant->settings['receipt_logo_url'] ?? null);
    }

    public function test_settings_payment_methods_and_qris_upload()
    {
        Storage::fake('public');
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $tenant = $owner->tenant;

        $component = Livewire::actingAs($owner, 'web')
            ->test(BackofficeDashboard::class)
            ->call('switchTab', 'settings')
            ->call('switchSettingsSubTab', 'payments')
            ->assertSet('settingsSubTab', 'payments')
            ->assertSee('3. Metode Bayar & Upload QRIS', false)
            ->set('paymentQrisMerchantName', 'Kopi Ruang Pagi Official')
            ->set('paymentQrisNmid', 'ID9988776655')
            ->set('paymentBankName', 'BCA')
            ->set('paymentBankAccountNumber', '7700-1122-3344')
            ->set('paymentBankAccountHolder', 'PT Kopi Ruang Pagi')
            ->set('paymentTransferActive', true)
            ->set('qrisImageFile', UploadedFile::fake()->image('qris_barcode.png'))
            ->call('savePaymentSettings');

        $tenant->refresh();
        $this->assertEquals('Kopi Ruang Pagi Official', $tenant->settings['payment_qris_merchant_name']);
        $this->assertEquals('ID9988776655', $tenant->settings['payment_qris_nmid']);
        $this->assertEquals('7700-1122-3344', $tenant->settings['payment_bank_account_number']);
        $this->assertNotEmpty($tenant->settings['payment_qris_image_url']);

        // Test remove QRIS image
        $component->call('removeQrisImage');
        $tenant->refresh();
        $this->assertNull($tenant->settings['payment_qris_image_url'] ?? null);
    }

    public function test_pwa_manifest_and_service_worker_available()
    {
        $response = $this->get('/manifest.json');
        $response->assertStatus(200);
        $this->assertFileExists(public_path('manifest.json'));
        $manifest = json_decode(file_get_contents(public_path('manifest.json')), true);
        $this->assertEquals('Lokara POS & Backoffice', $manifest['name']);
        $this->assertEquals('Lokara POS', $manifest['short_name']);

        $swResponse = $this->get('/sw.js');
        $swResponse->assertStatus(200);
        $this->assertFileExists(public_path('sw.js'));
    }

    public function test_tenant_header_has_pwa_install_button_and_sidebar_toggler()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();

        $this->actingAs($owner)
            ->get('/app')
            ->assertStatus(200)
            ->assertSee('toggleSidebar()', false)
            ->assertSee('pwaInstallManager()', false)
            ->assertSee('Install App');
    }

    public function test_layout_renders_favicon_and_opengraph_meta_tags()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();

        $this->actingAs($owner)
            ->get('/app')
            ->assertStatus(200)
            ->assertSee('images/logo-app.png')
            ->assertSee('images/opengraph.png')
            ->assertSee('property="og:image"', false)
            ->assertSee('property="og:title"', false)
            ->assertSee('name="twitter:card"', false);
    }
}
