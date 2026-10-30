<?php

namespace Tests\Feature;

use App\Livewire\Pos\Terminal;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_pos_terminal_renders_successfully()
    {
        $this->get('/pos')
            ->assertStatus(200)
            ->assertSee('POS Terminal')
            ->assertSee('Katalog')
            ->assertSee('Semua Menu')
            ->assertSee('Kopi Ruang Pagi')
            ->assertSee('Dina Putri')
            ->assertSee('Kasir')
            ->assertSee('Pesanan Baru')
            ->assertSee('Dine In')
            ->assertSee('Meja 08')
            ->assertSee('Es Kopi Susu Aren')
            ->assertSee('Cafe Latte')
            ->assertSee('Croissant Butter')
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
        Livewire::test(Terminal::class)
            ->assertCount('cart', 3)
            ->call('incrementQty', 0)
            // Initial qty was 2, now 3 -> subtotal was 44.000, now 66.000
            ->assertSee('Rp66.000')
            ->call('decrementQty', 0)
            ->assertSee('Rp44.000')
            ->call('removeFromCart', 2)
            ->assertCount('cart', 2);
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
        Livewire::test(Terminal::class)
            ->set('shiftStartingCash', 200000)
            ->call('startShift')
            ->call('openPaymentModal')
            ->assertSet('showPaymentModal', true)
            ->set('paymentMethod', 'cash')
            ->set('cashTendered', 100000)
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
        Livewire::test(Terminal::class)
            ->assertCount('cart', 3)
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
}

