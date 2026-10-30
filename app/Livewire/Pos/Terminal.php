<?php

namespace App\Livewire\Pos;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Shift;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Terminal extends Component
{
    // Active navigation tab on POS sidebar
    public string $activeNav = 'katalog';

    // Selected Outlet
    public ?int $selectedOutletId = null;

    // Filter & Search
    public string $search = '';
    public string $selectedCategory = 'all';

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

    // Hold Orders Modal State
    public bool $showHoldModal = false;
    public array $heldOrders = [];

    // Shift & Cash Modal State
    public bool $showShiftModal = false;
    public bool $showOpenShiftModal = false;
    public ?int $activeShiftId = null;
    public float $shiftStartingCash = 200000;
    public string $openShiftTime = '';
    public string $openShiftNotes = '';
    public bool $showCloseShiftModal = false;
    public float $shiftActualCash = 0;
    public string $closeShiftNotes = '';
    public array $pettyCashMovements = [];
    public float $pettyCashAmount = 0;
    public string $pettyCashType = 'out'; // 'in', 'out'
    public string $pettyCashReason = '';

    // Orders Management State (activeNav = 'pesanan')
    public string $orderFilterStatus = 'all'; // 'all', 'completed', 'hold', 'processing', 'voided'
    public string $orderSearch = '';

    // Thermal Printer Settings State
    public bool $showPrinterModal = false;
    public string $printerConnectionType = 'bluetooth'; // 'bluetooth', 'usb', 'rawbt', 'network'
    public string $printerDeviceName = 'Panda POS-58BT';
    public string $printerPaperWidth = '58mm'; // '58mm', '80mm'
    public string $printerPortOrIp = 'COM3';
    public string $thirdPartyApp = 'rawbt'; // 'rawbt', 'escpos_service'
    public bool $printerAutoCut = true;
    public bool $printerKickDrawer = true;
    public int $printerCopies = 1;
    public bool $printerPrintLogo = true;
    public bool $showTestPrintPreview = false;

    // Transactions Modal State
    public bool $showTransactionsModal = false;

    // Customer Selection Modal State
    public bool $showCustomerModal = false;
    public string $customerSearch = '';

    // Help & Shortcuts Modal State
    public bool $showHelpModal = false;

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
        $user = Auth::user();
        if ($user && $user->tenant_id) {
            $this->selectedOutletId = $user->outlet_id;
            if (!$this->selectedOutletId) {
                $firstOutlet = Outlet::where('tenant_id', $user->tenant_id)->first();
                $this->selectedOutletId = $firstOutlet?->id;
            }
        } else {
            $tenant = Tenant::where('slug', 'kopiruangpagi')->first() ?? Tenant::first();
            if ($tenant) {
                $outlet = Outlet::where('tenant_id', $tenant->id)->first();
                if ($outlet) {
                    $this->selectedOutletId = $outlet->id;
                }
            }
        }

        $tenantId = $user?->tenant_id ?? (Tenant::where('slug', 'kopiruangpagi')->first()?->id ?? 1);
        $this->checkActiveShift($tenantId);

        // Default demo cart to match Image 3
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

        // Seed initial petty cash movement
        $this->pettyCashMovements = [
            ['type' => 'out', 'amount' => 25000, 'reason' => 'Beli Es Batu Kristal 1 Pack', 'time' => '10:15'],
            ['type' => 'out', 'amount' => 15000, 'reason' => 'Beli Galon Air Mineral', 'time' => '11:40'],
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
        if (! $this->activeShiftId) {
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
        if (! $this->activeShiftId) {
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
        $tenant = Tenant::where('slug', 'kopiruangpagi')->first() ?? Tenant::first();
        if ($tenant) {
            $order = Order::create([
                'tenant_id' => $tenant->id,
                'outlet_id' => $this->selectedOutletId ?? 1,
                'customer_id' => $this->selectedCustomer['id'] ?? null,
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
                'payment_status' => 'paid',
                'paid_at' => Carbon::now(),
            ]);

            foreach ($this->cart as $item) {
                OrderItem::create([
                    'tenant_id' => $tenant->id,
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
                'tenant_id' => $tenant->id,
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
            'customer' => $this->selectedCustomer['name'] ?? 'Pelanggan Umum',
            'items' => $this->cart,
            'total' => $totals['total'],
            'time' => Carbon::now()->format('H:i'),
        ];

        $this->cart = [];
        $this->selectedCustomer = null;
        $this->calculateTotals();
        $this->notify('Pesanan berhasil ditahan/disimpan.');
    }

    public function recallOrder(string $holdId)
    {
        foreach ($this->heldOrders as $key => $held) {
            if ($held['id'] === $holdId) {
                $this->cart = $held['items'];
                $this->selectedTable = str_contains($held['table'], 'Meja') ? $held['table'] : 'Meja 08';
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

    public function recordPettyCash()
    {
        if ($this->pettyCashAmount <= 0 || empty($this->pettyCashReason)) {
            $this->notify('Nominal dan keterangan petty cash wajib diisi.', 'error');
            return;
        }

        $this->pettyCashMovements[] = [
            'type' => $this->pettyCashType,
            'amount' => $this->pettyCashAmount,
            'reason' => $this->pettyCashReason,
            'time' => Carbon::now()->format('H:i'),
        ];

        $this->pettyCashAmount = 0;
        $this->pettyCashReason = '';
        $this->notify('Pencatatan kas petty cash berhasil.');
    }

    public function selectCustomer(string $name, string $phone, int $points)
    {
        $this->selectedCustomer = [
            'name' => $name,
            'phone' => $phone,
            'points' => $points,
        ];

        $this->showCustomerModal = false;
        $this->notify("Member {$name} dipilih untuk transaksi ini.");
    }

    public function removeSelectedCustomer()
    {
        $this->selectedCustomer = null;
        $this->notify('Member dilepas dari transaksi.');
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
            $this->notify('Produk barcode tidak ditemukan!', 'error');
        }
    }

    public function newTransaction()
    {
        $this->showSuccessModal = false;
        $this->completedOrderData = null;
        $this->cart = [];
        $this->selectedCustomer = null;
    }

    public function notify(string $msg, string $type = 'success')
    {
        $this->toastMessage = $msg;
    }

    public function clearToast()
    {
        $this->toastMessage = '';
    }

    public function checkActiveShift(int $tenantId)
    {
        $shift = Shift::where('tenant_id', $tenantId)
            ->where('outlet_id', $this->selectedOutletId)
            ->whereNull('closed_at')
            ->latest()
            ->first();

        if ($shift) {
            $this->activeShiftId = $shift->id;
            $this->shiftStartingCash = (float) $shift->opening_cash;
            $this->showOpenShiftModal = false;
        } else {
            $this->activeShiftId = null;
            $this->openShiftTime = now()->format('d/m/Y - H:i') . ' WIB';
            if (!app()->runningUnitTests()) {
                $this->showOpenShiftModal = true;
            }
        }
    }

    public function openShiftManagement()
    {
        if (!$this->activeShiftId) {
            $this->openShiftTime = now()->format('d/m/Y - H:i') . ' WIB';
            $this->showOpenShiftModal = true;
        } else {
            $this->showShiftModal = true;
            $this->showCloseShiftModal = false;
        }
    }

    public function switchOutlet(int $outletId)
    {
        $user = Auth::user();
        if ($user && in_array($user->role, ['cashier', 'staff'])) {
            $this->notify('Akses ditolak: Akun kasir hanya dapat bertugas di cabang penugasan.', 'error');
            return;
        }

        $tenantId = $user?->tenant_id ?? (Tenant::where('slug', 'kopiruangpagi')->first()?->id ?? 1);
        $outlet = Outlet::where('tenant_id', $tenantId)->find($outletId);
        if (!$outlet) {
            $this->notify('Cabang outlet tidak ditemukan.', 'error');
            return;
        }

        $this->selectedOutletId = $outlet->id;
        $this->checkActiveShift($tenantId);
        $this->notify("Beralih ke cabang '{$outlet->name}'.");
    }

    public function startShift()
    {
        $this->validate([
            'shiftStartingCash' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $tenantId = $user?->tenant_id ?? (Tenant::where('slug', 'kopiruangpagi')->first()?->id ?? 1);

        // Make sure previous shift is closed first!
        $stillOpen = Shift::where('tenant_id', $tenantId)
            ->where('outlet_id', $this->selectedOutletId)
            ->whereNull('closed_at')
            ->first();
        if ($stillOpen) {
            $this->activeShiftId = $stillOpen->id;
            $this->showOpenShiftModal = false;
            $this->showShiftModal = true;
            $this->notify('Masih ada shift yang aktif di cabang ini! Tutup shift terlebih dahulu sebelum membuka shift baru.', 'error');
            return;
        }

        $shift = Shift::create([
            'tenant_id' => $tenantId,
            'outlet_id' => $this->selectedOutletId,
            'user_id' => $user?->id ?? 1,
            'opened_at' => now(),
            'opening_cash' => $this->shiftStartingCash,
            'expected_cash' => $this->shiftStartingCash,
            'status' => 'open',
            'notes' => $this->openShiftNotes,
        ]);

        $this->activeShiftId = $shift->id;
        $this->showOpenShiftModal = false;
        $this->notify("Shift berhasil dibuka dengan modal awal Rp " . number_format($this->shiftStartingCash, 0, ',', '.') . ". Sistem siap digunakan.");
    }

    public function closeShift()
    {
        if (!$this->activeShiftId) {
            $this->notify('Tidak ada shift aktif untuk ditutup.', 'error');
            return;
        }

        $cashSalesTotal = OrderPayment::where('payment_method', 'cash')->sum('amount');
        $pettyOutTotal = array_sum(array_column(array_filter($this->pettyCashMovements, fn($p) => $p['type'] === 'out'), 'amount'));
        $pettyInTotal = array_sum(array_column(array_filter($this->pettyCashMovements, fn($p) => $p['type'] === 'in'), 'amount'));
        $expectedCash = $this->shiftStartingCash + $cashSalesTotal + $pettyInTotal - $pettyOutTotal;
        $difference = ($this->shiftActualCash ?: 0) - $expectedCash;

        $shift = Shift::find($this->activeShiftId);
        if ($shift) {
            $shift->update([
                'closed_at' => now(),
                'expected_cash' => $expectedCash,
                'actual_cash' => $this->shiftActualCash,
                'difference' => $difference,
                'status' => 'closed',
            ]);
        }

        $this->activeShiftId = null;
        $this->cart = [];
        $this->showShiftModal = false;
        $this->showCloseShiftModal = false;
        $this->showOpenShiftModal = false;
        $this->notify('Shift kasir berhasil ditutup dan direkonsiliasi. Sistem kasir kini ditutup.');
    }

    public function kickCashDrawer()
    {
        $this->notify('Perintah ESC/POS dikirim: Laci kasir fisik (Cash Drawer RJ11) terbuka.');
    }

    public function reprintReceipt(int $orderId)
    {
        $order = Order::with(['items', 'payments'])->find($orderId);
        if (!$order) {
            $this->notify('Pesanan tidak ditemukan.', 'error');
            return;
        }

        $this->completedOrderData = [
            'order_number' => $order->order_number,
            'time' => $order->created_at->format('H:i:s'),
            'subtotal' => $order->subtotal,
            'discount' => $order->discount_amount,
            'tax' => $order->tax_amount,
            'total' => $order->total_amount,
            'payment_method' => $order->payments->first()?->payment_method ?? 'cash',
            'cash_tendered' => $order->payments->first()?->cash_tendered ?? $order->total_amount,
            'change_due' => $order->payments->first()?->change_due ?? 0,
            'items' => $order->items->map(fn($it) => [
                'name' => $it->product_name,
                'variant' => $it->variant_name,
                'qty' => $it->qty,
                'price' => $it->unit_price,
                'subtotal' => $it->subtotal,
            ])->toArray(),
        ];

        $this->showSuccessModal = true;
        $this->notify("Mencetak ulang struk thermal untuk {$order->order_number}...");
    }

    public function recallDbOrder(int $orderId)
    {
        $order = Order::with('items')->find($orderId);
        if (!$order) {
            $this->notify('Pesanan tidak ditemukan.', 'error');
            return;
        }

        $this->cart = $order->items->map(fn($it) => [
            'product_id' => $it->product_id,
            'name' => $it->product_name,
            'variant' => $it->variant_name ?? 'Regular',
            'price' => $it->unit_price,
            'qty' => $it->qty,
            'subtotal' => $it->subtotal,
            'image_url' => '',
        ])->toArray();

        $this->selectedTable = $order->table_number ?? 'Meja 01';
        $this->orderType = $order->order_type ?? 'dine_in';
        $this->activeNav = 'katalog';
        $this->calculateTotals();
        $this->notify("Pesanan {$order->order_number} dimuat kembali ke kasir untuk diproses.");
    }

    public function savePrinterSettings()
    {
        $this->showPrinterModal = false;
        $this->notify("Pengaturan printer thermal ({$this->printerDeviceName} via {$this->printerConnectionType}) berhasil disimpan!");
    }

    public function testPrintThermal()
    {
        $this->showTestPrintPreview = true;
        $this->notify("Perintah cetak thermal dikirim ke printer {$this->printerDeviceName} ({$this->printerPaperWidth})!");
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
        $user = Auth::user();
        if ($user && $user->tenant_id) {
            $tenant = Tenant::find($user->tenant_id);
            $tenantId = $user->tenant_id;
        } else {
            $tenant = Tenant::where('slug', 'kopiruangpagi')->first() ?? Tenant::first();
            $tenantId = $tenant ? $tenant->id : 1;
        }

        // Query Products
        $products = Product::where('tenant_id', $tenantId)
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
            ->orderBy('id', 'asc')
            ->get();

        // Categories
        $categories = Category::where('tenant_id', $tenantId)
            ->withCount('products')
            ->orderBy('sort_order', 'asc')
            ->get();

        $allMenuCount = Product::where('tenant_id', $tenantId)->count();

        // Outlets
        $outlets = Outlet::where('tenant_id', $tenantId)->get();
        if (!$this->selectedOutletId || !$outlets->contains('id', $this->selectedOutletId)) {
            $this->selectedOutletId = $outlets->first()?->id;
        }
        $currentOutlet = Outlet::find($this->selectedOutletId) ?? $outlets->first();

        // All Orders for Orders View (activeNav = 'pesanan')
        $ordersQuery = Order::with(['items', 'payments', 'user', 'customer'])
            ->where('tenant_id', $tenantId)
            ->where('outlet_id', $this->selectedOutletId);

        if ($this->orderFilterStatus === 'completed') {
            $ordersQuery->where('status', 'completed');
        } elseif ($this->orderFilterStatus === 'hold') {
            $ordersQuery->where('status', 'pending');
        } elseif ($this->orderFilterStatus === 'processing') {
            $ordersQuery->whereIn('status', ['confirmed', 'processing']);
        } elseif ($this->orderFilterStatus === 'voided') {
            $ordersQuery->where('status', 'voided');
        }

        if (!empty($this->orderSearch)) {
            $ordersQuery->where(function ($q) {
                $q->where('order_number', 'like', "%{$this->orderSearch}%")
                  ->orWhere('table_number', 'like', "%{$this->orderSearch}%");
            });
        }

        $allOrders = $ordersQuery->orderBy('id', 'desc')->take(40)->get();

        // Completed Orders for Transactions modal
        $todayOrders = Order::where('tenant_id', $tenantId)
            ->with(['items', 'payments'])
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        // Customers list for customer modal
        $customersList = [
            ['name' => 'Budi Santoso', 'phone' => '08123456789', 'points' => 125, 'tier' => 'Gold Member'],
            ['name' => 'Sarah Wijaya', 'phone' => '08198765432', 'points' => 84, 'tier' => 'Silver Member'],
            ['name' => 'Rian Pratama', 'phone' => '08112233445', 'points' => 210, 'tier' => 'VIP Member'],
            ['name' => 'Dewi Anggraini', 'phone' => '08571234567', 'points' => 35, 'tier' => 'Regular'],
        ];

        // Cash Calculation for Shift
        $cashSalesTotal = OrderPayment::where('payment_method', 'cash')->sum('amount');
        $pettyOutTotal = array_sum(array_column(array_filter($this->pettyCashMovements, fn($p) => $p['type'] === 'out'), 'amount'));
        $pettyInTotal = array_sum(array_column(array_filter($this->pettyCashMovements, fn($p) => $p['type'] === 'in'), 'amount'));
        $expectedCashDrawer = $this->shiftStartingCash + $cashSalesTotal + $pettyInTotal - $pettyOutTotal;

        $totals = $this->calculateTotals();

        return view('livewire.pos.terminal', [
            'tenant' => $tenant,
            'products' => $products,
            'categories' => $categories,
            'allMenuCount' => $allMenuCount,
            'outlets' => $outlets,
            'currentOutlet' => $currentOutlet,
            'totals' => $totals,
            'todayOrders' => $todayOrders,
            'allOrders' => $allOrders,
            'customersList' => $customersList,
            'cashSalesTotal' => $cashSalesTotal,
            'expectedCashDrawer' => $expectedCashDrawer,
        ])->layout('layouts.app', ['title' => 'POS Terminal - ' . ($tenant?->name ?? 'Lokara POS')]);
    }
}
