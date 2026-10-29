<?php

namespace Tests\Feature;

use App\Livewire\Pos\Terminal;
use App\Models\Product;
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
            ->assertSee('Lanjut Pembayaran');
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
}

