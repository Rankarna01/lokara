<?php

namespace App\Livewire\Pos;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Terminal extends Component
{
    // Active navigation tab on POS sidebar
    public string $activeNav = 'katalog'; // 'katalog', 'pesanan', 'shift', 'transaksi', 'pelanggan', 'bantuan'

    // Selected Outlet
    public ?int $selectedOutletId = null;

    // Filter & Search
    public string $search = '';
    public string $selectedCategory = 'all'; // 'all', 'kopi', 'non-kopi', 'makanan', 'snack'

    // Cart State
    public array $cart = [];
    public string $orderType = 'dine_in'; // 'dine_in', 'take_away'
    public string $selectedTable = 'Meja 08';

    // Payment Modal State
    public bool $showPaymentModal = false;
    public string $paymentMethod = 'cash'; // 'cash', 'qris', 'debit'
    public float $cashTendered = 100000;
    public float $changeDue = 5650;

    // Payment Success Modal State
    public bool $showSuccessModal = false;
    public ?array $completedOrderData = null;

    // Hold Orders Modal State
    public bool $showHoldModal = false;
    public array $heldOrders = [];

    // Barcode Scan Modal State
    public bool $showBarcodeModal = false;
    public string $barcodeInput = '';

    // Variant Selection Modal State
    public bool $showVariantModal = false;
    public ?Product $selectedProductForVariant = null;
    public string $selectedVariantName = '';
    public float $selectedVariantPrice = 0;
    public string $itemNotes = '';

    // Toast
    public string $toastMessage = '';

    public function mount()
    {
        $tenant = Tenant::where('slug', 'kopiruangpagi')->first() ?? Tenant::first();
        if ($tenant) {
            $outlet = Outlet::where('tenant_id', $tenant->id)->first();
            if ($outlet) {
                $this->selectedOutletId = $outlet->id;
            }
        }

        // Initialize default demo cart to match Image 3 exactly!
        $this->cart = [
            [
                'product_id' => 1,
                'name' => 'Es Kopi Susu Aren',
                'variant' => 'Regular • Tanpa Gula',
                'price' => 22000,
                'qty' => 2,
                'subtotal' => 44000,
                'image_url' => 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=300&h=300&q=80',
            ],
            [
                'product_id' => 3,
                'name' => 'Cafe Latte',
                'variant' => 'Hot • Large',
                'price' => 25000,
                'qty' => 1,
                'subtotal' => 25000,
                'image_url' => 'https://images.unsplash.com/photo-1534778101976-62847782c213?auto=format&fit=crop&w=300&h=300&q=80',
            ],
            [
                'product_id' => 5,
                'name' => 'Croissant Butter',
                'variant' => 'Original',
                'price' => 16000,
                'qty' => 1,
                'subtotal' => 16000,
                'image_url' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&w=300&h=300&q=80',
            ],
        ];

        $this->calculateTotals();
    }

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
        $product = Product::with('variants')->find($productId);
        if (! $product) {
            return;
        }

        // If product has variants, open modal selection
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
        // Check if item with same variant already in cart
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
            'created_at' => Carbon::now()->translatedFormat('d M Y, H:i'),
        ];

        // Save order to database
        $tenant = Tenant::where('slug', 'kopiruangpagi')->first() ?? Tenant::first();
        if ($tenant) {
            $order = Order::create([
                'tenant_id' => $tenant->id,
                'outlet_id' => $this->selectedOutletId ?? 1,
                'user_id' => Auth::id() ?? 1,
                'order_number' => $orderNumber,
                'order_type' => $this->orderType,
                'table_number' => $this->orderType === 'dine_in' ? $this->selectedTable : null,
                'subtotal' => $totals['subtotal'],
                'discount_amount' => 0,
                'tax_amount' => $totals['tax'],
                'service_amount' => 0,
                'total_amount' => $totals['total'],
                'status' => 'completed',
                'paid_at' => Carbon::now(),
            ]);

            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'variant_name' => $item['variant'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            OrderPayment::create([
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
    }

    public function holdOrder()
    {
        if (empty($this->cart)) {
            $this->notify('Keranjang kosong untuk disimpan.', 'error');
            return;
        }

        $totals = $this->calculateTotals();
        $this->heldOrders[] = [
            'id' => uniqid('HOLD-'),
            'table' => $this->orderType === 'dine_in' ? $this->selectedTable : 'Take Away',
            'items' => $this->cart,
            'total' => $totals['total'],
            'time' => Carbon::now()->format('H:i'),
        ];

        $this->cart = [];
        $this->calculateTotals();
        $this->notify('Pesanan berhasil ditahan/disimpan.');
    }

    public function recallOrder(string $holdId)
    {
        foreach ($this->heldOrders as $key => $held) {
            if ($held['id'] === $holdId) {
                $this->cart = $held['items'];
                $this->selectedTable = str_contains($held['table'], 'Meja') ? $held['table'] : 'Meja 01';
                $this->orderType = str_contains($held['table'], 'Meja') ? 'dine_in' : 'take_away';
                unset($this->heldOrders[$key]);
                $this->heldOrders = array_values($this->heldOrders);
                break;
            }
        }

        $this->showHoldModal = false;
        $this->calculateTotals();
        $this->notify('Pesanan berhasil dimuat kembali ke keranjang.');
    }

    public function scanBarcode()
    {
        $product = Product::where('barcode', $this->barcodeInput)
            ->orWhere('sku', $this->barcodeInput)
            ->first();

        if ($product) {
            $this->addToCart($product->id);
            $this->showBarcodeModal = false;
            $this->barcodeInput = '';
            $this->notify("Barcode terdeteksi: {$product->name}");
        } else {
            $this->notify('Produk dengan barcode tersebut tidak ditemukan!', 'error');
        }
    }

    public function newTransaction()
    {
        $this->showSuccessModal = false;
        $this->completedOrderData = null;
        $this->cart = [];
    }

    public function notify(string $msg, string $type = 'success')
    {
        $this->toastMessage = $msg;
    }

    public function clearToast()
    {
        $this->toastMessage = '';
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    }

    public function render()
    {
        $tenant = Tenant::where('slug', 'kopiruangpagi')->first() ?? Tenant::first();

        // Query Products
        $productsQuery = Product::where('tenant_id', $tenant ? $tenant->id : 1)
            ->where('is_active', true)
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('barcode', 'like', "%{$this->search}%");
            })
            ->when($this->selectedCategory !== 'all', function ($q) {
                $q->whereHas('category', function ($catQuery) {
                    $catQuery->where('slug', $this->selectedCategory);
                });
            })
            ->orderBy('id', 'asc');

        $products = $productsQuery->get();

        // Categories
        $categories = Category::where('tenant_id', $tenant ? $tenant->id : 1)
            ->withCount('products')
            ->orderBy('sort_order', 'asc')
            ->get();

        $allMenuCount = Product::where('tenant_id', $tenant ? $tenant->id : 1)->count();

        // Outlets
        $outlets = Outlet::where('tenant_id', $tenant ? $tenant->id : 1)->get();
        $currentOutlet = Outlet::find($this->selectedOutletId) ?? $outlets->first();

        $totals = $this->calculateTotals();

        return view('livewire.pos.terminal', [
            'products' => $products,
            'categories' => $categories,
            'allMenuCount' => $allMenuCount,
            'outlets' => $outlets,
            'currentOutlet' => $currentOutlet,
            'totals' => $totals,
        ])->layout('layouts.app', ['title' => 'POS Terminal - Lokara POS']);
    }
}

