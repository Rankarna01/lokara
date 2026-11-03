<?php

namespace App\Livewire\Pos;

use App\Livewire\Pos\Concerns\HasCartAndCheckout;
use App\Livewire\Pos\Concerns\HasCustomerAndBarcode;
use App\Livewire\Pos\Concerns\HasHoldAndRecall;
use App\Livewire\Pos\Concerns\HasPrinterSettings;
use App\Livewire\Pos\Concerns\HasReceiptPrinting;
use App\Livewire\Pos\Concerns\HasShiftAndCash;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Terminal extends Component
{
    use HasCartAndCheckout;
    use HasCustomerAndBarcode;
    use HasHoldAndRecall;
    use HasPrinterSettings;
    use HasReceiptPrinting;
    use HasShiftAndCash;

    // Active navigation tab on POS sidebar
    public string $activeNav = 'katalog';

    // Selected Outlet
    public ?int $selectedOutletId = null;

    // Filter & Search
    public string $search = '';

    public string $selectedCategory = 'all';

    // Toast
    public string $toastMessage = '';

    public function getCurrentTenant(): ?Tenant
    {
        $user = Auth::user();
        if ($user && $user->tenant_id) {
            return Tenant::find($user->tenant_id);
        }

        return Tenant::where('slug', 'kopiruangpagi')->first() ?? Tenant::first();
    }

    public function isSalonTenant(): bool
    {
        $tenant = $this->getCurrentTenant();

        return $tenant ? $tenant->isSalon() : false;
    }

    public function mount()
    {
        $user = Auth::user();
        if ($user && $user->tenant_id) {
            $this->selectedOutletId = $user->outlet_id;
            if (! $this->selectedOutletId) {
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
        if (! $this->isSalonTenant()) {
            $this->checkActiveShift($tenantId);
        } else {
            $this->activeShiftId = null;
            $this->showOpenShiftModal = false;
        }

        // Start with clean empty cart
        $this->cart = [];
        if ($this->isSalonTenant()) {
            $this->orderType = 'salon_order';
            $this->selectedTable = '';
        }

        // Seed initial petty cash movement
        $this->pettyCashMovements = [
            ['type' => 'out', 'amount' => 25000, 'reason' => 'Beli Es Batu Kristal 1 Pack', 'time' => '10:15'],
            ['type' => 'out', 'amount' => 15000, 'reason' => 'Beli Galon Air Mineral', 'time' => '11:40'],
        ];

        $this->calculateTotals();
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
        if (! $outlet) {
            $this->notify('Cabang outlet tidak ditemukan.', 'error');

            return;
        }

        $this->selectedOutletId = $outlet->id;
        $this->checkActiveShift($tenantId);
        $this->notify("Beralih ke cabang '{$outlet->name}'.");
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
        if (! $this->selectedOutletId || ! $outlets->contains('id', $this->selectedOutletId)) {
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

        if (! empty($this->orderSearch)) {
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
        $customersList = Customer::where('tenant_id', $tenant?->id)
            ->when($this->customerSearch !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->customerSearch.'%')
                        ->orWhere('phone', 'like', '%'.$this->customerSearch.'%');
                });
            })
            ->orderBy('name')
            ->take(50)
            ->get()
            ->map(fn (Customer $customer) => [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone ?? '-',
                'points' => (int) $customer->points,
                'tier' => ucfirst($customer->membership_tier ?? 'silver').' Member',
            ])
            ->all();

        // Cash Calculation for Shift
        $cashSalesTotal = OrderPayment::where('payment_method', 'cash')->sum('amount');
        $pettyOutTotal = array_sum(array_column(array_filter($this->pettyCashMovements, fn ($p) => $p['type'] === 'out'), 'amount'));
        $pettyInTotal = array_sum(array_column(array_filter($this->pettyCashMovements, fn ($p) => $p['type'] === 'in'), 'amount'));
        $expectedCashDrawer = $this->shiftStartingCash + $cashSalesTotal + $pettyInTotal - $pettyOutTotal;

        $totals = $this->calculateTotals();

        return view('livewire.pos.terminal', [
            'tenant' => $tenant,
            'isSalon' => $this->isSalonTenant(),
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
        ])->layout('layouts.app', ['title' => 'POS Terminal - '.($tenant?->name ?? 'Lokara POS')]);
    }
}
