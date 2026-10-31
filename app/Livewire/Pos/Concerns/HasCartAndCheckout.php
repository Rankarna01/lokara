<?php

namespace App\Livewire\Pos\Concerns;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait HasCartAndCheckout
{
    // Cart State
    public array $cart = [];
    public string $orderType = 'dine_in'; // 'dine_in', 'take_away'
    public string $selectedTable = 'Meja 08';
    public ?array $selectedCustomer = null;

    // Payment Modal State
    public bool $showPaymentModal = false;
    public string $paymentMethod = 'cash';
    public float $cashTendered = 100000;
    public float $changeDue = 5650;

    // Payment Success Modal State
    public bool $showSuccessModal = false;
    public ?array $completedOrderData = null;

    // Variant Selection Modal State
    public bool $showVariantModal = false;
    public ?Product $selectedProductForVariant = null;
    public string $selectedVariantName = '';
    public float $selectedVariantPrice = 0;
    public string $itemNotes = '';

    public function setCategory(string $slug)
    {
        $this->selectedCategory = $slug;
    }

    public function setOrderType(string $type)
    {
        $this->orderType = $type;
    }

    public function addToCart(int $productId)
    {
        $isSalon = method_exists($this, 'isSalonTenant') && $this->isSalonTenant();

        if (! $this->activeShiftId && ! $isSalon) {
            $this->showOpenShiftModal = true;
            $this->notify('Sistem kasir sedang ditutup! Anda wajib membuka shift terlebih dahulu sebelum melayani pesanan.', 'error');
            return;
        }

        $product = Product::with('variants')->find($productId);
        if (! $product) {
            return;
        }

        if ($product->variants->count() > 0) {
            $this->selectedProductForVariant = $product;
            $this->selectedVariantName = $product->variants->first()->name;
            $this->selectedVariantPrice = (float) $product->variants->first()->final_price;
            $this->itemNotes = '';
            $this->showVariantModal = true;
            return;
        }

        $this->insertItemToCart($product, $product->name, (float) $product->base_price, 'Standar');
    }

    public function confirmVariantSelection()
    {
        if ($this->selectedProductForVariant) {
            $variantText = $this->selectedVariantName . ($this->itemNotes ? ' • ' . $this->itemNotes : '');
            $this->insertItemToCart(
                $this->selectedProductForVariant,
                $this->selectedProductForVariant->name,
                $this->selectedVariantPrice,
                $variantText
            );
        }

        $this->showVariantModal = false;
        $this->selectedProductForVariant = null;
    }

    private function insertItemToCart(Product $product, string $name, float $price, string $variant)
    {
        $found = false;
        foreach ($this->cart as $key => $item) {
            if ($item['product_id'] === $product->id && $item['variant'] === $variant) {
                $this->cart[$key]['qty']++;
                $this->cart[$key]['subtotal'] = $this->cart[$key]['qty'] * $this->cart[$key]['price'];
                $found = true;
                break;
            }
        }

        if (! $found) {
            $this->cart[] = [
                'product_id' => $product->id,
                'name' => $name,
                'variant' => $variant,
                'price' => $price,
                'qty' => 1,
                'subtotal' => $price,
                'image_url' => $product->image_url,
            ];
        }

        $this->calculateTotals();
        $this->notify("{$name} ditambahkan ke pesanan.");
    }

    public function incrementQty(int $index)
    {
        if (isset($this->cart[$index])) {
            $this->cart[$index]['qty']++;
            $this->cart[$index]['subtotal'] = $this->cart[$index]['qty'] * $this->cart[$index]['price'];
            $this->calculateTotals();
        }
    }

    public function decrementQty(int $index)
    {
        if (isset($this->cart[$index])) {
            if ($this->cart[$index]['qty'] > 1) {
                $this->cart[$index]['qty']--;
                $this->cart[$index]['subtotal'] = $this->cart[$index]['qty'] * $this->cart[$index]['price'];
            } else {
                unset($this->cart[$index]);
                $this->cart = array_values($this->cart);
            }
            $this->calculateTotals();
        }
    }

    public function removeFromCart(int $index)
    {
        if (isset($this->cart[$index])) {
            unset($this->cart[$index]);
            $this->cart = array_values($this->cart);
            $this->calculateTotals();
        }
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->selectedCustomer = null;
        $this->calculateTotals();
        $this->notify('Keranjang pesanan telah dikosongkan.');
    }

    public function calculateTotals(): array
    {
        $subtotal = 0;
        foreach ($this->cart as $item) {
            $subtotal += $item['subtotal'];
        }

        $discount = 0;
        $tax = $subtotal * 0.11; // 11% PPN
        $total = $subtotal - $discount + $tax;

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $total,
        ];
    }

    public function openPaymentModal()
    {
        if (empty($this->cart)) {
            $this->notify('Keranjang masih kosong!', 'error');
            return;
        }

        $totals = $this->calculateTotals();
        $this->cashTendered = ceil($totals['total'] / 10000) * 10000;
        $this->changeDue = max(0, $this->cashTendered - $totals['total']);
        $this->showPaymentModal = true;
    }

    public function setQuickCash(float $amount)
    {
        $this->cashTendered = $amount;
        $totals = $this->calculateTotals();
        $this->changeDue = max(0, $this->cashTendered - $totals['total']);
    }

    public function processPayment()
    {
        $isSalon = method_exists($this, 'isSalonTenant') && $this->isSalonTenant();

        if (! $this->activeShiftId && ! $isSalon) {
            $this->showPaymentModal = false;
            $this->showOpenShiftModal = true;
            $this->notify('Sistem kasir sedang ditutup! Anda wajib membuka shift terlebih dahulu.', 'error');
            return;
        }

        $totals = $this->calculateTotals();
        $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4));

        $this->completedOrderData = [
            'order_number' => $orderNumber,
            'items' => $this->cart,
            'subtotal' => $totals['subtotal'],
            'tax' => $totals['tax'],
            'total' => $totals['total'],
            'payment_method' => strtoupper($this->paymentMethod),
            'cash_tendered' => $this->paymentMethod === 'cash' ? $this->cashTendered : $totals['total'],
            'change_due' => $this->paymentMethod === 'cash' ? $this->changeDue : 0,
            'order_type' => $this->orderType === 'dine_in' ? 'Dine In (' . $this->selectedTable . ')' : 'Take Away',
            'customer_name' => $this->selectedCustomer['name'] ?? 'Pelanggan Reguler',
            'created_at' => Carbon::now()->translatedFormat('d M Y, H:i'),
        ];

        // Save order to MySQL DB
        $user = Auth::user();
        $tenantId = $user?->tenant_id ?? (Tenant::where('slug', 'kopiruangpagi')->first()?->id ?? Tenant::first()?->id);
        $outletId = $this->selectedOutletId;
        if (! $outletId && $tenantId) {
            $outletId = $user?->outlet_id ?? Outlet::where('tenant_id', $tenantId)->first()?->id;
        }

        if ($tenantId) {
            $order = Order::create([
                'tenant_id' => $tenantId,
                'outlet_id' => $outletId ?? 1,
                'customer_id' => $this->selectedCustomer['id'] ?? null,
                'shift_id' => $this->activeShiftId,
                'user_id' => $user?->id ?? 1,
                'order_number' => $orderNumber,
                'order_type' => $this->orderType,
                'table_number' => $this->orderType === 'dine_in' ? $this->selectedTable : null,
                'subtotal' => $totals['subtotal'],
                'discount_amount' => 0,
                'tax_amount' => $totals['tax'],
                'service_amount' => 0,
                'total_amount' => $totals['total'],
                'status' => 'completed',
                'payment_status' => 'paid',
                'paid_at' => Carbon::now(),
            ]);

            foreach ($this->cart as $item) {
                OrderItem::create([
                    'tenant_id' => $tenantId,
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'variant_name' => $item['variant'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Decrement stock for retail items that track stock
                $product = Product::where('id', $item['product_id'])->where('tenant_id', $tenantId)->first();
                if ($product && $product->track_stock) {
                    $product->decrement('stock_quantity', (float) $item['qty']);
                    \App\Models\StockMovement::create([
                        'tenant_id' => $tenantId,
                        'outlet_id' => $this->selectedOutletId ?? 1,
                        'product_id' => $product->id,
                        'ingredient_id' => null,
                        'user_id' => $user?->id ?? 1,
                        'type' => 'sale_pos',
                        'qty' => -((float) $item['qty']),
                        'balance_after' => (float) $product->fresh()->stock_quantity,
                        'reference_note' => "Penjualan POS #{$order->order_number}",
                        'created_at' => Carbon::now(),
                    ]);
                }
            }

            OrderPayment::create([
                'tenant_id' => $tenantId,
                'order_id' => $order->id,
                'payment_method' => $this->paymentMethod,
                'amount' => $totals['total'],
                'cash_tendered' => $this->paymentMethod === 'cash' ? $this->cashTendered : $totals['total'],
                'change_due' => $this->paymentMethod === 'cash' ? $this->changeDue : 0,
                'reference_number' => 'REF-' . time(),
            ]);
        }

        $this->showPaymentModal = false;
        $this->showSuccessModal = true;
        $this->cart = [];
        $this->selectedCustomer = null;
    }

    public function newTransaction()
    {
        $this->showSuccessModal = false;
        $this->completedOrderData = null;
        $this->cart = [];
        $this->selectedCustomer = null;
    }
}

