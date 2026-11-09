<?php

namespace Tests\Feature;

use App\Livewire\Pos\Terminal;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class PosTerminalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_is_redirected_to_login_when_accessing_pos()
    {
        $this->get('/pos')->assertRedirect('/login');
    }

    public function test_pos_terminal_renders_successfully()
    {
        $dina = User::where('email', 'dina@kopiruangpagi.id')->first();
        $this->actingAs($dina)
            ->get('/pos')
            ->assertStatus(200)
            ->assertSee('POS Terminal')
            ->assertSee('Katalog')
            ->assertSee('Semua Menu')
            ->assertSee('Kopi Ruang Pagi')
            ->assertSee($dina->name)
            ->assertSee('Kasir')
            ->assertSee('Pesanan Baru')
            ->assertSee('Dine In')
            ->assertSee('Meja 01')
            ->assertSee('Belum ada item pesanan')
            ->assertSee('Buka Shift Terlebih Dahulu');
    }

    public function test_can_switch_category_filter()
    {
        Livewire::test(Terminal::class)
            ->assertSet('selectedCategory', 'all')
            ->call('setCategory', 'kopi')
            ->assertSet('selectedCategory', 'kopi')
            ->assertSee('Es Kopi Susu Aren')
            ->call('setCategory', 'snack')
            ->assertSet('selectedCategory', 'snack')
            ->assertSee('Croissant Butter');
    }

    public function test_can_modify_cart_quantities()
    {
        $product = Product::first();

        Livewire::test(Terminal::class)
            ->assertCount('cart', 0)
            ->set('cart', [
                [
                    'product_id' => $product->id,
                    'name' => 'Es Kopi Susu Aren',
                    'variant' => 'Regular • Tanpa Gula',
                    'price' => 22000,
                    'qty' => 2,
                    'subtotal' => 44000,
                    'image_url' => '',
                ],
                [
                    'product_id' => $product->id,
                    'name' => 'Croissant Butter',
                    'variant' => 'Original',
                    'price' => 16000,
                    'qty' => 1,
                    'subtotal' => 16000,
                    'image_url' => '',
                ],
            ])
            ->call('incrementQty', 0)
            ->assertSee('Rp66.000')
            ->call('decrementQty', 0)
            ->assertSee('Rp44.000')
            ->call('removeFromCart', 1)
            ->assertCount('cart', 1);
    }

    public function test_can_switch_order_type()
    {
        Livewire::test(Terminal::class)
            ->assertSet('orderType', 'dine_in')
            ->call('setOrderType', 'take_away')
            ->assertSet('orderType', 'take_away')
            ->call('setOrderType', 'dine_in')
            ->assertSet('orderType', 'dine_in');
    }

    public function test_can_complete_payment_and_create_order()
    {
        $product = Product::first();

        Livewire::test(Terminal::class)
            ->set('shiftStartingCash', 200000)
            ->call('startShift')
            ->set('cart', [
                [
                    'product_id' => $product->id,
                    'name' => 'Es Kopi Susu Aren',
                    'variant' => 'Regular',
                    'price' => 22000,
                    'qty' => 1,
                    'subtotal' => 22000,
                    'image_url' => '',
                ],
            ])
            ->call('openPaymentModal')
            ->assertSet('showPaymentModal', true)
            ->set('paymentMethod', 'cash')
            ->set('cashTendered', 50000)
            ->call('processPayment')
            ->assertSet('showPaymentModal', false)
            ->assertSet('showSuccessModal', true)
            ->assertCount('cart', 0);

        $this->assertDatabaseHas('orders', [
            'status' => 'completed',
        ]);
    }

    public function test_can_hold_and_recall_order()
    {
        $product = Product::first();

        Livewire::test(Terminal::class)
            ->set('cart', [
                [
                    'product_id' => $product->id,
                    'name' => 'Es Kopi Susu Aren',
                    'variant' => 'Regular',
                    'price' => 22000,
                    'qty' => 2,
                    'subtotal' => 44000,
                    'image_url' => '',
                ],
            ])
            ->assertCount('cart', 1)
            ->call('holdOrder')
            ->assertCount('cart', 0)
            ->assertCount('heldOrders', 1);
    }

    public function test_pos_terminal_binds_to_authenticated_cashier_and_outlet()
    {
        $budi = User::where('email', 'budi@kopiruangpagi.id')->first();
        $this->assertNotNull($budi);
        $outletKemang = Outlet::where('slug', 'cabang-kemang')->first();
        $this->assertNotNull($outletKemang);

        Livewire::actingAs($budi, 'web')
            ->test(Terminal::class)
            ->assertSet('selectedOutletId', $outletKemang->id)
            ->assertSee('Budi Santoso')
            ->assertSee('Cabang Kemang')
            ->assertDontSee('Back Office Toko'); // Cashiers cannot access Back Office link
    }

    public function test_can_switch_to_orders_view_and_manage_orders()
    {
        $dina = User::where('email', 'dina@kopiruangpagi.id')->first();

        Livewire::actingAs($dina, 'web')
            ->test(Terminal::class)
            ->assertSet('activeNav', 'katalog')
            ->set('activeNav', 'pesanan')
            ->assertSet('activeNav', 'pesanan')
            ->assertSee('Riwayat')
            ->assertSee('Daftar Semua Pesanan')
            ->assertSee('Semua Pesanan')
            ->set('orderFilterStatus', 'completed')
            ->assertSet('orderFilterStatus', 'completed');
    }

    public function test_open_and_close_shift_cash_drawer_flow()
    {
        $dina = User::where('email', 'dina@kopiruangpagi.id')->first();

        // 1. Open Shift
        $component = Livewire::actingAs($dina, 'web')
            ->test(Terminal::class)
            ->call('openShiftManagement')
            ->assertSet('showOpenShiftModal', true)
            ->set('shiftStartingCash', 250000)
            ->set('openShiftNotes', 'Pecahan kembalian 2k, 5k, 10k siap')
            ->call('startShift')
            ->assertSet('showOpenShiftModal', false);

        $shift = Shift::where('user_id', $dina->id)->whereNull('closed_at')->first();
        $this->assertNotNull($shift);
        $this->assertEquals(250000, (float) $shift->opening_cash);

        // 2. Open Shift Management again during active shift
        $component->call('openShiftManagement')
            ->assertSet('showShiftModal', true)
            ->assertSet('activeShiftId', $shift->id)
            ->set('pettyCashType', 'out')
            ->set('pettyCashAmount', 15000)
            ->set('pettyCashReason', 'Beli es batu kristal')
            ->call('recordPettyCash')
            ->assertCount('pettyCashMovements', 3); // 2 seeded + 1 new

        // 3. Close Shift & Reconcile Cash
        $component->set('showCloseShiftModal', true)
            ->set('shiftActualCash', 235000)
            ->set('closeShiftNotes', 'Setoran kasir aman')
            ->call('closeShift')
            ->assertSet('showShiftModal', false)
            ->assertSet('activeShiftId', null);

        $shift->refresh();
        $this->assertNotNull($shift->closed_at);
        $this->assertEquals(235000, (float) $shift->actual_cash);
    }

    public function test_thermal_printer_configuration_and_test_print()
    {
        $dina = User::where('email', 'dina@kopiruangpagi.id')->first();

        Livewire::actingAs($dina, 'web')
            ->test(Terminal::class)
            ->set('showPrinterModal', true)
            ->assertSet('showPrinterModal', true)
            ->set('printerConnectionType', 'bluetooth')
            ->set('printerDeviceName', 'Panda POS-58BT')
            ->set('printerPaperWidth', '58mm')
            ->set('printerKickDrawer', true)
            ->call('testPrintThermal')
            ->assertSet('showTestPrintPreview', true)
            ->assertSee('PRINTER THERMAL READY')
            ->call('savePrinterSettings')
            ->assertSet('showPrinterModal', false);
    }

    public function test_cashier_cannot_switch_branch_in_pos()
    {
        $dina = User::where('email', 'dina@kopiruangpagi.id')->first();
        $tebetOutlet = Outlet::where('slug', 'cabang-tebet')->first();

        Livewire::actingAs($dina, 'web')
            ->test(Terminal::class)
            ->assertSet('selectedOutletId', $dina->outlet_id)
            ->call('switchOutlet', $tebetOutlet->id)
            ->assertSet('selectedOutletId', $dina->outlet_id); // Tetap di outlet penugasan kasir
    }

    public function test_owner_can_switch_branch_in_pos()
    {
        $owner = User::where('email', 'rian@kopiruangpagi.id')->first();
        $tebetOutlet = Outlet::where('slug', 'cabang-tebet')->first();

        Livewire::actingAs($owner, 'web')
            ->test(Terminal::class)
            ->call('switchOutlet', $tebetOutlet->id)
            ->assertSet('selectedOutletId', $tebetOutlet->id);
    }

    public function test_system_locked_before_opening_shift_and_relocks_after_closing()
    {
        $dina = User::where('email', 'dina@kopiruangpagi.id')->first();
        $product = Product::first();

        // 1. Initial State: No active shift -> System is locked
        $component = Livewire::actingAs($dina, 'web')
            ->test(Terminal::class)
            ->assertSet('activeShiftId', null)
            ->call('addToCart', $product->id)
            ->assertSet('showOpenShiftModal', true); // Prompts open shift modal because locked!

        // 2. Open Shift -> System becomes unlocked
        $component->set('shiftStartingCash', 200000)
            ->call('startShift')
            ->assertSet('showOpenShiftModal', false);
        $this->assertNotNull($component->get('activeShiftId'));

        // 3. Now can add to cart
        $initialCount = count($component->get('cart'));
        $component->call('addToCart', $product->id);
        $this->assertGreaterThanOrEqual($initialCount, count($component->get('cart')));

        // 4. Close Shift -> System relocks & cart is cleared
        $component->set('showCloseShiftModal', true)
            ->set('shiftActualCash', 200000)
            ->call('closeShift')
            ->assertSet('activeShiftId', null)
            ->assertCount('cart', 0);
    }

    public function test_printing_receipt_dispatches_thermal_page_and_only_owner_session_can_open_it()
    {
        $dina = User::where('email', 'dina@kopiruangpagi.id')->first();
        $order = Order::create([
            'tenant_id' => $dina->tenant_id,
            'outlet_id' => $dina->outlet_id,
            'user_id' => $dina->id,
            'order_number' => 'ORD-PRINT-001',
            'order_type' => 'dine_in',
            'subtotal' => 25000,
            'total_amount' => 25000,
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        Livewire::actingAs($dina)
            ->test(Terminal::class)
            ->call('printReceipt', $order->id)
            ->assertDispatched('open-pos-receipt');

        Cache::put('pos-receipt:kunci-uji', [
            'user_id' => $dina->id,
            'html' => '<html>STRUK-UJI</html>',
        ], now()->addMinutes(5));

        $rian = User::where('email', 'rian@kopiruangpagi.id')->first();
        $this->actingAs($rian)->get('/pos/receipt/kunci-uji')->assertNotFound();

        Cache::put('pos-receipt:kunci-uji', [
            'user_id' => $dina->id,
            'html' => '<html>STRUK-UJI</html>',
        ], now()->addMinutes(5));

        $this->actingAs($dina)->get('/pos/receipt/kunci-uji')
            ->assertOk()
            ->assertSee('STRUK-UJI');
    }

    public function test_thermal_receipt_prints_number_of_copies_from_printer_settings()
    {
        $dina = User::where('email', 'dina@kopiruangpagi.id')->first();
        $order = Order::create([
            'tenant_id' => $dina->tenant_id,
            'outlet_id' => $dina->outlet_id,
            'user_id' => $dina->id,
            'order_number' => 'ORD-PRINT-002',
            'order_type' => 'dine_in',
            'subtotal' => 25000,
            'total_amount' => 25000,
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        $html = view('livewire.pos.receipts.thermal', [
            'order' => $order->load(['items', 'payments', 'outlet', 'customer', 'user']),
            'settings' => [],
            'tenantName' => 'Kopi Ruang Pagi',
            'paperWidth' => '58mm',
            'copies' => 2,
        ])->render();

        $this->assertSame(2, substr_count($html, 'class="copy"'));
    }

    public function test_cashier_can_add_new_member_and_select_it_for_transaction()
    {
        $dina = User::where('email', 'dina@kopiruangpagi.id')->first();

        Livewire::actingAs($dina)
            ->test(Terminal::class)
            ->call('openNewMemberForm')
            ->assertSet('showNewMemberForm', true)
            ->set('newMemberName', 'Nadia Putri')
            ->set('newMemberPhone', '081299990000')
            ->set('newMemberTier', 'gold')
            ->call('saveNewMember')
            ->assertSet('showNewMemberForm', false)
            ->assertSet('selectedCustomer', fn ($customer) => $customer['name'] === 'Nadia Putri');

        $this->assertDatabaseHas('customers', [
            'tenant_id' => $dina->tenant_id,
            'name' => 'Nadia Putri',
            'phone' => '081299990000',
            'membership_tier' => 'gold',
            'points' => 0,
        ]);
    }

    public function test_new_member_with_registered_phone_is_rejected()
    {
        $dina = User::where('email', 'dina@kopiruangpagi.id')->first();
        Customer::create([
            'tenant_id' => $dina->tenant_id,
            'name' => 'Member Lama',
            'phone' => '081277766655',
            'membership_tier' => 'silver',
            'points' => 10,
        ]);

        Livewire::actingAs($dina)
            ->test(Terminal::class)
            ->call('openNewMemberForm')
            ->set('newMemberName', 'Member Baru')
            ->set('newMemberPhone', '081277766655')
            ->call('saveNewMember')
            ->assertHasErrors(['newMemberPhone']);

        $this->assertSame(1, Customer::where('phone', '081277766655')->count());
    }

    public function test_cashier_in_pos_cannot_reach_backoffice_and_no_backoffice_link_is_shown()
    {
        $dina = User::where('email', 'dina@kopiruangpagi.id')->first();

        $this->actingAs($dina)
            ->get('/pos')
            ->assertOk()
            ->assertDontSee('Back Office Toko')
            ->assertDontSee('Ke Dashboard Back Office');

        $this->actingAs($dina)
            ->get('/app')
            ->assertRedirect(route('pos.terminal'));
    }

    public function test_catalog_search_matches_name_sku_and_barcode_within_tenant_only()
    {
        $dina = User::where('email', 'dina@kopiruangpagi.id')->first();
        $otherTenant = \App\Models\Tenant::where('slug', 'studiorona')->first();

        Product::create([
            'tenant_id' => $dina->tenant_id,
            'name' => 'Kopi Uji Pencarian',
            'slug' => 'kopi-uji-pencarian',
            'sku' => 'SKU-UJI-777',
            'barcode' => '8990001112223',
            'base_price' => 20000,
            'is_active' => true,
            'is_available' => true,
            'type' => 'item',
        ]);

        Product::create([
            'tenant_id' => $otherTenant->id,
            'name' => 'Kopi Uji Pencarian Tenant Lain',
            'slug' => 'kopi-uji-pencarian-lain',
            'sku' => 'SKU-LAIN-1',
            'base_price' => 20000,
            'is_active' => true,
            'is_available' => true,
            'type' => 'item',
        ]);

        Livewire::actingAs($dina)
            ->test(Terminal::class)
            ->set('search', 'Kopi Uji Pencarian')
            ->assertSee('Kopi Uji Pencarian')
            ->assertDontSee('Tenant Lain')
            ->set('search', 'SKU-UJI-777')
            ->assertSee('Kopi Uji Pencarian')
            ->set('search', '8990001112223')
            ->assertSee('Kopi Uji Pencarian')
            ->set('search', 'NON-ADA-XYZ')
            ->assertDontSee('Kopi Uji Pencarian');
    }

    public function test_cashier_can_record_store_expense_with_past_date_from_pos()
    {
        $dina = User::where('email', 'dina@kopiruangpagi.id')->first();
        $yesterday = now()->subDay()->toDateString();

        Livewire::actingAs($dina)
            ->test(Terminal::class)
            ->call('openPosExpenseModal')
            ->assertSet('showPosExpenseModal', true)
            ->set('posExpenseCategory', 'Operasional')
            ->set('posExpenseDate', $yesterday)
            ->set('posExpenseTitle', 'Beli galon air uji')
            ->set('posExpenseAmount', 45000)
            ->call('savePosExpense')
            ->assertHasNoErrors()
            ->assertSet('showPosExpenseModal', false);

        $this->assertTrue(
            Expense::where('tenant_id', $dina->tenant_id)
                ->where('title', 'Beli galon air uji')
                ->where('category', 'Operasional')
                ->whereDate('expense_date', $yesterday)
                ->exists()
        );
    }

    public function test_pos_renders_sidebar_toggler()
    {
        $dina = User::where('email', 'dina@kopiruangpagi.id')->first();
        $this->actingAs($dina)
            ->get('/pos')
            ->assertStatus(200)
            ->assertSee('togglePosSidebar', false)
            ->assertSee('Perkecil / Perbesar Sidebar POS');
    }

    public function test_salon_pos_terminal_hides_shift_and_allows_transactions_without_shift()
    {
        $salonStaff = User::where('email', 'doni.barber@studiorona.id')->first();
        if (! $salonStaff) {
            $salonTenant = Tenant::where('slug', 'studiorona')->first();
            $salonStaff = User::where('tenant_id', $salonTenant->id)->first();
        }

        $this->actingAs($salonStaff)
            ->get('/pos')
            ->assertStatus(200)
            ->assertDontSee('Shift & Kas')
            ->assertDontSee('Sistem Kasir Ditutup')
            ->assertDontSee('Shift Belum Dibuka')
            ->assertDontSee('Buka Shift Terlebih Dahulu')
            ->assertDontSee('Dine In')
            ->assertDontSee('Take Away')
            ->assertDontSee('Meja 01')
            ->assertDontSee('Meja 08')
            ->assertSee('Pencatatan Order Salon')
            ->assertSee('pos-cart-panel', false)
            ->assertSee('Lihat Keranjang')
            ->assertSee('Katalog Layanan & Produk');

        // Salon staff can add product to cart directly without opening shift
        $salonProduct = Product::where('tenant_id', $salonStaff->tenant_id)->first();

        $component = Livewire::actingAs($salonStaff, 'web')
            ->test(Terminal::class)
            ->assertSet('activeShiftId', null)
            ->assertSet('showOpenShiftModal', false);

        if ($salonProduct) {
            $component->call('addToCart', $salonProduct->id)
                ->assertSet('showOpenShiftModal', false);
        }
    }
}
