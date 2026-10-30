<div class="min-h-screen bg-slate-50 flex flex-col antialiased text-slate-800" x-data="{ sidebarOpen: true }">
    <!-- TOAST NOTIFICATION -->
    @if ($toastMessage)
        <div class="fixed top-5 right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg border text-sm transition-all animate-bounce {{ $toastType === 'error' ? 'bg-rose-50 border-rose-200 text-rose-800' : ($toastType === 'info' ? 'bg-sky-50 border-sky-200 text-sky-800' : 'bg-emerald-50 border-emerald-200 text-emerald-800') }}">
            <i class="fa-solid {{ $toastType === 'error' ? 'fa-circle-xmark text-rose-500' : ($toastType === 'info' ? 'fa-circle-info text-sky-500' : 'fa-circle-check text-emerald-500') }}"></i>
            <span class="font-medium">{{ $toastMessage }}</span>
            <button wire:click="closeToast" class="ml-2 text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <div class="flex flex-1">
        <!-- SIDEBAR -->
        <aside class="w-64 bg-white border-r border-slate-200/80 flex flex-col justify-between shrink-0 transition-all duration-200">
            <div>
                <!-- Brand / Outlet Header -->
                <div class="h-16 flex items-center justify-between px-5 border-b border-slate-100">
                    <a href="{{ route('tenant.dashboard') }}" class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-orange-500 flex items-center justify-center text-white font-bold text-lg shadow-sm shadow-orange-500/20">
                            <i class="fa-solid fa-shapes"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-slate-900 tracking-tight text-base leading-none">Lokara</span>
                                <span class="text-[10px] font-bold bg-orange-100 text-orange-600 px-1.5 py-0.5 rounded tracking-wide uppercase">ADMIN</span>
                            </div>
                            <span class="text-[11px] text-slate-400 font-medium leading-none block mt-0.5 truncate max-w-[140px]">{{ $tenant->name ?? 'Kopi Ruang Pagi' }}</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Menus -->
                <nav class="p-3 space-y-1">
                    <div class="px-3 pt-2 pb-1.5 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Operasional</div>

                    <!-- Dashboard -->
                    <button wire:click="switchTab('dashboard')"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ $activeTab === 'dashboard' ? 'bg-orange-50 text-orange-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-chart-pie w-5 text-center {{ $activeTab === 'dashboard' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                            <span>Dashboard</span>
                        </div>
                    </button>

                    <!-- Master Layanan (Khusus Modul Salon & Barbershop) -->
                    @if ($tenant?->isSalon() || $tenant?->hasFeature('has_services'))
                        <button wire:click="switchTab('services')"
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ $activeTab === 'services' ? 'bg-orange-50 text-orange-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-scissors w-5 text-center {{ $activeTab === 'services' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>Master Layanan</span>
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $activeTab === 'services' ? 'bg-orange-200/60 text-orange-700' : 'bg-slate-100 text-slate-500' }}">{{ $services->count() }}</span>
                        </button>
                    @endif

                    <!-- Master Produk (Barang & Retail) -->
                    <button wire:click="switchTab('products')"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ $activeTab === 'products' ? 'bg-orange-50 text-orange-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-boxes-stacked w-5 text-center {{ $activeTab === 'products' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                            <span>Master Produk</span>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $activeTab === 'products' ? 'bg-orange-200/60 text-orange-700' : 'bg-slate-100 text-slate-500' }}">{{ $products->count() }}</span>
                    </button>

                    <!-- Bahan Baku & Resep BOM (Hanya untuk F&B atau yang memiliki fitur resep) -->
                    @if (!$tenant?->isSalon() || $tenant?->hasFeature('has_recipes'))
                        <button wire:click="switchTab('recipes')"
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ $activeTab === 'recipes' ? 'bg-orange-50 text-orange-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-mortar-pestle w-5 text-center {{ $activeTab === 'recipes' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>Bahan Baku & HPP</span>
                            </div>
                            @if($lowStockIngredients->count() > 0)
                                <span class="text-[10px] bg-rose-100 text-rose-600 px-1.5 py-0.5 rounded-full font-bold">!</span>
                            @endif
                        </button>
                    @endif

                    <!-- Stok & Gudang -->
                    <button wire:click="switchTab('stock')"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ $activeTab === 'stock' ? 'bg-orange-50 text-orange-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-warehouse w-5 text-center {{ $activeTab === 'stock' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                            <span>Stok & Opname</span>
                        </div>
                    </button>

                    <div class="px-3 pt-3 pb-1.5 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Transaksi & Finansial</div>

                    <!-- Pembelian & Supplier -->
                    <button wire:click="switchTab('purchases')"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ $activeTab === 'purchases' ? 'bg-orange-50 text-orange-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-truck-field w-5 text-center {{ $activeTab === 'purchases' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                            <span>Pembelian (PO)</span>
                        </div>
                    </button>

                    <!-- Riwayat Transaksi -->
                    <button wire:click="switchTab('transactions')"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ $activeTab === 'transactions' ? 'bg-orange-50 text-orange-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-receipt w-5 text-center {{ $activeTab === 'transactions' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                            <span>Riwayat Transaksi</span>
                        </div>
                    </button>

                    <!-- Diskon & Promo -->
                    <button wire:click="switchTab('discounts')"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ $activeTab === 'discounts' ? 'bg-orange-50 text-orange-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-tags w-5 text-center {{ $activeTab === 'discounts' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                            <span>Diskon & Promo</span>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $activeTab === 'discounts' ? 'bg-orange-200/60 text-orange-700' : 'bg-slate-100 text-slate-500' }}">{{ $discounts->count() }}</span>
                    </button>

                    <!-- Laba Rugi & Finansial -->
                    <button wire:click="switchTab('finance')"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ $activeTab === 'finance' ? 'bg-orange-50 text-orange-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-file-invoice-dollar w-5 text-center {{ $activeTab === 'finance' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                            <span>Laba Rugi (P&L)</span>
                        </div>
                    </button>

                    <div class="px-3 pt-3 pb-1.5 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Manajemen</div>

                    <!-- CRM & Pelanggan -->
                    <button wire:click="switchTab('crm')"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ $activeTab === 'crm' ? 'bg-orange-50 text-orange-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-users w-5 text-center {{ $activeTab === 'crm' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                            <span>Pelanggan & CRM</span>
                        </div>
                    </button>

                    <!-- Pengaturan Outlet -->
                    <button wire:click="switchTab('settings')"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ $activeTab === 'settings' ? 'bg-orange-50 text-orange-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-sliders w-5 text-center {{ $activeTab === 'settings' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                            <span>Outlet & Staf Kasir</span>
                        </div>
                    </button>
                </nav>
            </div>

            <!-- Bottom User & Fast POS Action -->
            <div class="p-3 border-t border-slate-100 space-y-2">
                <!-- Buka POS Button -->
                <a href="{{ route('pos.terminal') }}"
                    class="w-full flex items-center justify-center gap-2 py-2.5 px-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-sm rounded-xl shadow-sm transition-all hover:shadow">
                    <i class="fa-solid fa-cash-register"></i>
                    <span>Buka POS Kasir</span>
                </a>

                <!-- User profile card -->
                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-200/60">
                    <div class="flex items-center gap-2.5 truncate">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&h=100&q=80"
                            class="w-8 h-8 rounded-full object-cover border border-slate-200">
                        <div class="truncate">
                            <div class="text-xs font-semibold text-slate-800 truncate">{{ auth()->user()->name ?? 'Rian Adiputra' }}</div>
                            <div class="text-[10px] text-slate-500 capitalize">{{ auth()->user()->role ?? 'Owner Toko' }}</div>
                        </div>
                    </div>
                    <a href="{{ route('logout') }}" title="Keluar" class="text-slate-400 hover:text-rose-600 p-1">
                        <i class="fa-solid fa-right-from-bracket text-xs"></i>
                    </a>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- TOP NAVBAR -->
            <header class="h-16 bg-white border-b border-slate-200/80 px-6 flex items-center justify-between shrink-0">
                <!-- Breadcrumbs -->
                <div class="flex items-center gap-2 text-sm text-slate-500">
                    <span class="font-medium text-slate-700">{{ $tenant->name ?? 'Kopi Ruang Pagi' }}</span>
                    <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
                    <span class="capitalize font-semibold text-orange-600">{{ str_replace('_', ' ', $activeTab) }}</span>
                </div>

                <!-- Top Right Controls -->
                <div class="flex items-center gap-3">
                    <!-- Tenant Switcher Dropdown (Demo Multi-Tenant & Salon Selector) -->
                    @if(isset($allTenants) && $allTenants->count() > 1)
                        <div class="flex items-center gap-1.5">
                            <span class="text-[11px] text-slate-400 font-medium hidden lg:inline">Bisnis:</span>
                            <select wire:change="switchTenant($event.target.value)"
                                class="bg-orange-50 border border-orange-200 text-xs font-bold text-orange-700 py-1.5 px-2.5 rounded-lg focus:outline-none focus:ring-1 focus:ring-orange-500 cursor-pointer">
                                @foreach($allTenants as $t)
                                    <option value="{{ $t->id }}" {{ $tenantId == $t->id ? 'selected' : '' }}>
                                        🏢 {{ $t->name }} ({{ $t->businessType?->name ?? 'Bisnis' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Outlet Switcher -->
                    @if($outlets->count() > 1)
                        <div class="relative">
                            <select wire:change="switchOutlet($event.target.value)"
                                class="bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 py-1.5 px-3 rounded-lg focus:outline-none focus:ring-1 focus:ring-orange-500 cursor-pointer">
                                @foreach($outlets as $out)
                                    <option value="{{ $out->id }}" {{ $selectedOutletId == $out->id ? 'selected' : '' }}>
                                        📍 {{ $out->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="text-xs font-medium text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-orange-500"></i>
                            <span>{{ $selectedOutlet->name ?? 'Outlet Utama' }}</span>
                        </div>
                    @endif

                    <!-- Switch to POS Kasir Quick Button -->
                    <a href="{{ route('pos.terminal') }}"
                        class="hidden sm:flex items-center gap-2 text-xs font-semibold px-3 py-1.5 bg-orange-50 text-orange-600 border border-orange-200 rounded-lg hover:bg-orange-100 transition">
                        <i class="fa-solid fa-desktop"></i>
                        <span>Layar POS Kasir</span>
                    </a>

                    <!-- Switch to Super Admin (if user is superadmin) -->
                    @if(auth()->check() && auth()->user()->role === 'super_admin')
                        <a href="{{ route('superadmin.dashboard') }}"
                            class="text-xs font-medium px-2.5 py-1.5 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition">
                            <i class="fa-solid fa-user-shield mr-1"></i> Super Admin
                        </a>
                    @endif

                    <!-- Notification -->
                    <button class="relative p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-50">
                        <i class="fa-regular fa-bell"></i>
                        @if($lowStockIngredients->count() > 0)
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full animate-pulse"></span>
                        @endif
                    </button>
                </div>
            </header>

            <!-- MAIN TAB BODY -->
            <main class="flex-1 p-6 overflow-y-auto">
                <!-- ==================== TAB 1: DASHBOARD ==================== -->
                @if ($activeTab === 'dashboard')
                    <div class="space-y-6">
                        <!-- Welcome & Quick Action Bar -->
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div>
                                <h1 class="text-xl font-bold text-slate-900">Halo, {{ auth()->user()->name ?? 'Rian Adiputra' }} 👋</h1>
                                <p class="text-sm text-slate-500 mt-0.5">Berikut adalah ringkasan performa penjualan dan operasional outlet Anda hari ini.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($tenant?->isSalon() || $tenant?->hasFeature('has_services'))
                                    <button wire:click="openNewProductModal" class="px-3.5 py-2 text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl transition flex items-center gap-1.5">
                                        <i class="fa-solid fa-boxes-stacked text-slate-500"></i>
                                        <span>Tambah Produk</span>
                                    </button>
                                    <button wire:click="openNewServiceModal" class="px-3.5 py-2 text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-xl transition flex items-center gap-1.5">
                                        <i class="fa-solid fa-scissors text-indigo-500"></i>
                                        <span>Tambah Layanan</span>
                                    </button>
                                @else
                                    <button wire:click="openNewProductModal" class="px-3.5 py-2 text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl transition flex items-center gap-1.5">
                                        <i class="fa-solid fa-plus text-slate-500"></i>
                                        <span>Tambah Menu</span>
                                    </button>
                                @endif
                                <button wire:click="openExpenseModal" class="px-3.5 py-2 text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-wallet text-slate-500"></i>
                                    <span>Catat Biaya</span>
                                </button>
                                <a href="{{ route('pos.terminal') }}" class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-cash-register"></i>
                                    <span>Buka POS Kasir</span>
                                </a>
                            </div>
                        </div>

                        <!-- Low Stock Warning Alert if any -->
                        @if($lowStockIngredients->count() > 0)
                            <div class="bg-amber-50 border border-amber-200/80 rounded-2xl p-4 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold text-lg">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-amber-900">Peringatan: {{ $lowStockIngredients->count() }} Bahan Baku Menipis / Kritis!</h4>
                                        <p class="text-xs text-amber-700">
                                            @foreach($lowStockIngredients as $low)
                                                <span class="font-medium underline">{{ $low->name }}</span> (Sisa: {{ (int)$low->current_stock }} {{ $low->usage_unit }})@if(!$loop->last), @endif
                                            @endforeach
                                        </p>
                                    </div>
                                </div>
                                <button wire:click="openPoModal" class="px-3 py-1.5 text-xs font-semibold bg-amber-600 hover:bg-amber-700 text-white rounded-lg shadow-sm whitespace-nowrap">
                                    <i class="fa-solid fa-cart-plus mr-1"></i> Buat PO Restock
                                </button>
                            </div>
                        @endif

                        <!-- 4 Stat Summary Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Card 1: Penjualan Hari Ini -->
                            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                                <div class="flex items-center justify-between text-slate-500 text-xs font-medium">
                                    <span>Penjualan Hari Ini</span>
                                    <span class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center text-sm">
                                        <i class="fa-solid fa-money-bill-wave"></i>
                                    </span>
                                </div>
                                <div class="mt-2 text-2xl font-bold text-slate-900">Rp {{ number_format($todaySales, 0, ',', '.') }}</div>
                                <div class="mt-1 flex items-center gap-1.5 text-xs text-emerald-600 font-medium">
                                    <i class="fa-solid fa-arrow-trend-up"></i>
                                    <span>{{ $todayTxCount }} transaksi berhasil</span>
                                </div>
                            </div>

                            <!-- Card 2: Rata-Rata Nilai Struk -->
                            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                                <div class="flex items-center justify-between text-slate-500 text-xs font-medium">
                                    <span>Average Ticket Size</span>
                                    <span class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center text-sm">
                                        <i class="fa-solid fa-receipt"></i>
                                    </span>
                                </div>
                                <div class="mt-2 text-2xl font-bold text-slate-900">Rp {{ number_format($avgTicket, 0, ',', '.') }}</div>
                                <div class="mt-1 text-xs text-slate-500">Nilai rata-rata per transaksi</div>
                            </div>

                            <!-- Card 3: Estimasi Laba Kotor Bulan Ini -->
                            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                                <div class="flex items-center justify-between text-slate-500 text-xs font-medium">
                                    <span>Laba Kotor (Bulan Ini)</span>
                                    <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                                        <i class="fa-solid fa-chart-line"></i>
                                    </span>
                                </div>
                                <div class="mt-2 text-2xl font-bold text-emerald-600">Rp {{ number_format($grossProfit, 0, ',', '.') }}</div>
                                <div class="mt-1 flex items-center gap-1.5 text-xs text-slate-500">
                                    <span class="font-semibold text-slate-700">Margin: {{ $grossMarginPct }}%</span>
                                    <span>(setelah HPP)</span>
                                </div>
                            </div>

                            <!-- Card 4: Estimasi Laba Bersih (Net Profit) -->
                            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                                <div class="flex items-center justify-between text-slate-500 text-xs font-medium">
                                    <span>Laba Bersih (Setelah OPEX)</span>
                                    <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
                                        <i class="fa-solid fa-scale-balanced"></i>
                                    </span>
                                </div>
                                <div class="mt-2 text-2xl font-bold {{ $netProfit >= 0 ? 'text-indigo-600' : 'text-rose-600' }}">
                                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                                </div>
                                <div class="mt-1 text-xs text-slate-500">Beban OPEX: Rp {{ number_format($totalOpex, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        <!-- Middle Grid: Peak Hours Chart Simulation & Top Selling Menu -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Left: Trend & Jam Ramai (2 cols) -->
                            <div class="lg:col-span-2 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-base">Trend Jam Ramai (Peak Hours)</h3>
                                        <p class="text-xs text-slate-500">Distribusi pesanan hari ini berdasarkan jam operasional</p>
                                    </div>
                                    <span class="text-xs bg-slate-100 text-slate-600 font-medium px-2.5 py-1 rounded-lg">Hari Ini</span>
                                </div>

                                <!-- Bar Chart Mockup (Pure Tailwind, Shared Hosting Friendly) -->
                                <div class="h-44 flex items-end justify-between gap-2 pt-6 pb-2 px-4 border-b border-slate-100">
                                    <div class="flex-1 flex flex-col items-center gap-2">
                                        <div class="w-full bg-orange-100 rounded-t-lg h-10 transition-all hover:bg-orange-200 relative group">
                                            <span class="hidden group-hover:block absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] px-1.5 py-0.5 rounded whitespace-nowrap">4 Order</span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-medium">08:00</span>
                                    </div>
                                    <div class="flex-1 flex flex-col items-center gap-2">
                                        <div class="w-full bg-orange-200 rounded-t-lg h-20 transition-all hover:bg-orange-300 relative group">
                                            <span class="hidden group-hover:block absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] px-1.5 py-0.5 rounded whitespace-nowrap">12 Order</span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-medium">10:00</span>
                                    </div>
                                    <div class="flex-1 flex flex-col items-center gap-2">
                                        <div class="w-full bg-orange-500 rounded-t-lg h-36 transition-all hover:bg-orange-600 relative group">
                                            <span class="hidden group-hover:block absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] px-1.5 py-0.5 rounded whitespace-nowrap">28 Order (Puncak Siang)</span>
                                        </div>
                                        <span class="text-[10px] text-orange-600 font-bold">12:00</span>
                                    </div>
                                    <div class="flex-1 flex flex-col items-center gap-2">
                                        <div class="w-full bg-orange-300 rounded-t-lg h-24 transition-all hover:bg-orange-400 relative group">
                                            <span class="hidden group-hover:block absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] px-1.5 py-0.5 rounded whitespace-nowrap">16 Order</span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-medium">14:00</span>
                                    </div>
                                    <div class="flex-1 flex flex-col items-center gap-2">
                                        <div class="w-full bg-orange-400 rounded-t-lg h-32 transition-all hover:bg-orange-500 relative group">
                                            <span class="hidden group-hover:block absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] px-1.5 py-0.5 rounded whitespace-nowrap">24 Order (Nongkrong Sore)</span>
                                        </div>
                                        <span class="text-[10px] text-slate-600 font-medium">16:00</span>
                                    </div>
                                    <div class="flex-1 flex flex-col items-center gap-2">
                                        <div class="w-full bg-orange-500 rounded-t-lg h-40 transition-all hover:bg-orange-600 relative group">
                                            <span class="hidden group-hover:block absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] px-1.5 py-0.5 rounded whitespace-nowrap">32 Order (Puncak Malam)</span>
                                        </div>
                                        <span class="text-[10px] text-orange-600 font-bold">19:00</span>
                                    </div>
                                    <div class="flex-1 flex flex-col items-center gap-2">
                                        <div class="w-full bg-orange-200 rounded-t-lg h-16 transition-all hover:bg-orange-300 relative group">
                                            <span class="hidden group-hover:block absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] px-1.5 py-0.5 rounded whitespace-nowrap">8 Order</span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-medium">21:00</span>
                                    </div>
                                </div>

                                <div class="mt-4 flex items-center justify-between text-xs text-slate-500">
                                    <div class="flex items-center gap-4">
                                        <span class="flex items-center gap-1.5">
                                            <span class="w-2.5 h-2.5 rounded-sm bg-orange-500"></span> Jam Ramai Utama (12:00 & 19:00)
                                        </span>
                                    </div>
                                    <span class="text-slate-400">Rekomendasi: Tambah 1 barista pada pukul 18.00</span>
                                </div>
                            </div>

                            <!-- Right: Top 5 Best Sellers (1 col) -->
                            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="font-bold text-slate-900 text-base">Top 5 Best Seller</h3>
                                        <button wire:click="switchTab('products')" class="text-xs text-orange-600 hover:underline font-medium">Lihat Semua</button>
                                    </div>
                                    <p class="text-xs text-slate-500 mb-4">Produk dengan perputaran penjualan tertinggi</p>

                                    <div class="space-y-3">
                                        @foreach($products->take(5) as $idx => $p)
                                            <div class="flex items-center justify-between gap-3 text-sm">
                                                <div class="flex items-center gap-2.5 min-w-0">
                                                    <span class="w-5 h-5 rounded-md bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold shrink-0">
                                                        {{ $idx + 1 }}
                                                    </span>
                                                    <div class="truncate">
                                                        <div class="font-medium text-slate-800 truncate">{{ $p->name }}</div>
                                                        <div class="text-[11px] text-slate-400">{{ $p->category_tag }}</div>
                                                    </div>
                                                </div>
                                                <div class="text-right shrink-0">
                                                    <div class="font-semibold text-slate-900">Rp {{ number_format($p->base_price, 0, ',', '.') }}</div>
                                                    <div class="text-[10px] text-emerald-600 font-medium">
                                                        @php
                                                            $margin = $p->base_price > 0 ? round((($p->base_price - $p->cost_price) / $p->base_price) * 100) : 0;
                                                        @endphp
                                                        Margin: {{ $margin }}%
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="pt-4 mt-4 border-t border-slate-100">
                                    <div class="flex items-center justify-between text-xs text-slate-500">
                                        <span>Total Menu Aktif:</span>
                                        <span class="font-bold text-slate-700">{{ $products->count() }} produk</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Section: Kasir Aktif & Status Kas Laci -->
                        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="font-bold text-slate-900 text-base">Status Shift Kasir & Laci Kas</h3>
                                    <p class="text-xs text-slate-500">Monitoring kas fisik dan staf kasir yang sedang bertugas</p>
                                </div>
                                <span class="px-2.5 py-1 text-xs font-semibold bg-emerald-50 text-emerald-600 rounded-lg flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span> Shift Aktif
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold">
                                        <i class="fa-solid fa-user-check"></i>
                                    </div>
                                    <div>
                                        <div class="text-xs text-slate-400">Kasir Bertugas</div>
                                        <div class="text-sm font-bold text-slate-800">Dina Putri</div>
                                        <div class="text-[11px] text-slate-500">Buka Shift: 08:00 WIB</div>
                                    </div>
                                </div>

                                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                                        <i class="fa-solid fa-coins"></i>
                                    </div>
                                    <div>
                                        <div class="text-xs text-slate-400">Modal Kas Awal</div>
                                        <div class="text-sm font-bold text-slate-800">Rp 200.000</div>
                                        <div class="text-[11px] text-slate-500">Uang kembalian laci</div>
                                    </div>
                                </div>

                                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center font-bold">
                                        <i class="fa-solid fa-cash-register"></i>
                                    </div>
                                    <div>
                                        <div class="text-xs text-slate-400">Estimasi Uang di Laci</div>
                                        <div class="text-sm font-bold text-slate-900">Rp {{ number_format(200000 + $todaySales, 0, ',', '.') }}</div>
                                        <div class="text-[11px] text-slate-500">Termasuk penerimaan tunai</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- ==================== TAB 2: MASTER PRODUK & MENU ==================== -->
                @if ($activeTab === 'products')
                    <div class="space-y-5">
                        <!-- Top Header & Filter Controls -->
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h1 class="text-xl font-bold text-slate-900">Katalog & Master Produk</h1>
                                    @if($tenant?->isSalon() || $tenant?->hasFeature('has_services'))
                                        <span class="text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-full uppercase tracking-wider">
                                            Produk Fisik & Retail
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $tenant?->isSalon() ? 'Kelola produk retail perawatan, pomade, shampoo, harga jual, dan stok barang.' : 'Kelola menu, harga jual cabang, HPP modal, dan ketersediaan stok.' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2.5">
                                @if($tenant?->isSalon() || $tenant?->hasFeature('has_services'))
                                    <button wire:click="switchTab('services')"
                                        class="px-3.5 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-semibold text-xs rounded-xl shadow-xs flex items-center gap-2 transition">
                                        <i class="fa-solid fa-scissors"></i>
                                        <span>Ke Master Layanan ({{ $services->count() }})</span>
                                    </button>
                                @endif
                                <button wire:click="openNewProductModal"
                                    class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>Tambah Produk Baru</span>
                                </button>
                            </div>
                        </div>

                        <!-- Search & Category Filters -->
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div class="flex items-center gap-2 overflow-x-auto w-full lg:w-auto pb-2 lg:pb-0">
                                <!-- Filter Semua Kategori -->
                                <button wire:click="$set('selectedCategoryFilter', 'all')"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition shrink-0 {{ $selectedCategoryFilter === 'all' ? 'bg-orange-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                    Semua Kategori ({{ $totalProductsCount ?? $products->count() }})
                                </button>

                                <!-- Pill Kategori + Tombol Edit Langsung -->
                                @foreach($categories as $cat)
                                    <div class="inline-flex items-center rounded-lg border transition shrink-0 {{ $selectedCategoryFilter == $cat->id ? 'bg-orange-500 border-orange-500 text-white shadow-sm' : 'bg-slate-100 border-transparent text-slate-600 hover:bg-slate-200' }}">
                                        <button wire:click="$set('selectedCategoryFilter', {{ $cat->id }})"
                                            class="px-3 py-1.5 text-xs font-semibold whitespace-nowrap flex items-center gap-1.5">
                                            @if($cat->icon)
                                                <i class="fa-solid {{ $cat->icon }} text-[10px] {{ $selectedCategoryFilter == $cat->id ? 'text-white' : 'text-slate-400' }}"></i>
                                            @endif
                                            <span>{{ $cat->name }}</span>
                                        </button>
                                        <button wire:click="openEditCategoryModal({{ $cat->id }})"
                                            title="Edit Kategori: {{ $cat->name }}"
                                            class="pr-2 pl-0.5 py-1.5 text-[10px] {{ $selectedCategoryFilter == $cat->id ? 'text-orange-200 hover:text-white' : 'text-slate-400 hover:text-slate-700' }} transition">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    </div>
                                @endforeach

                                <!-- Tombol Tambah Kategori Langsung di Samping Kategori -->
                                <button wire:click="openNewCategoryModal"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-dashed border-orange-400 bg-orange-50 text-orange-600 hover:bg-orange-100 hover:border-orange-500 transition flex items-center gap-1.5 whitespace-nowrap shrink-0 shadow-xs"
                                    title="Tambah Kategori Menu Baru">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                    <span>Tambah Kategori</span>
                                </button>

                                <!-- Tombol Kelola Semua Kategori -->
                                <button wire:click="openManageCategoriesModal"
                                    class="px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-800 transition flex items-center gap-1.5 whitespace-nowrap shrink-0"
                                    title="Kelola & Atur Urutan Kategori">
                                    <i class="fa-solid fa-sliders text-[11px] text-slate-500"></i>
                                    <span class="hidden sm:inline">Kelola Kategori</span>
                                </button>
                            </div>

                            <div class="relative w-full lg:w-64 shrink-0">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                                <input type="text" wire:model.live.debounce.300ms="searchProduct"
                                    placeholder="Cari nama menu..."
                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-8 pr-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
                            </div>
                        </div>

                        <!-- Product Table -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 border-b border-slate-200/80 text-xs text-slate-500 uppercase tracking-wider font-semibold">
                                        <tr>
                                            <th class="py-3 px-4">Menu Produk</th>
                                            <th class="py-3 px-4">Kategori</th>
                                            <th class="py-3 px-4 text-right">Harga Jual</th>
                                            <th class="py-3 px-4 text-right">HPP (Modal)</th>
                                            <th class="py-3 px-4 text-center">Margin %</th>
                                            <th class="py-3 px-4 text-center">Status</th>
                                            <th class="py-3 px-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($products as $p)
                                            <tr class="hover:bg-slate-50/60 transition">
                                                <td class="py-3.5 px-4">
                                                    <div class="flex items-center gap-3">
                                                        <img src="{{ $p->image_url ?: 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=150&h=150&q=80' }}"
                                                            class="w-10 h-10 rounded-xl object-cover border border-slate-200 shrink-0">
                                                        <div>
                                                            <div class="font-bold text-slate-900">{{ $p->name }}</div>
                                                            <div class="text-[11px] text-slate-400">{{ $p->sku ?? 'PRD-00' . $p->id }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3.5 px-4 text-xs text-slate-600">
                                                    @if($p->category)
                                                        <button wire:click="openEditCategoryModal({{ $p->category->id }})"
                                                            title="Edit kategori {{ $p->category->name }}"
                                                            class="bg-slate-100 hover:bg-orange-50 hover:text-orange-600 px-2.5 py-1 rounded-md font-medium text-slate-700 inline-flex items-center gap-1.5 transition">
                                                            @if($p->category->icon)
                                                                <i class="fa-solid {{ $p->category->icon }} text-[10px] text-slate-400"></i>
                                                            @endif
                                                            <span>{{ $p->category->name }}</span>
                                                            <i class="fa-solid fa-pen text-[9px] text-slate-400 ml-0.5"></i>
                                                        </button>
                                                    @else
                                                        <span class="bg-slate-100 px-2.5 py-1 rounded-md font-medium text-slate-400">
                                                            Tanpa Kategori
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                                                    Rp {{ number_format($p->base_price, 0, ',', '.') }}
                                                </td>
                                                <td class="py-3.5 px-4 text-right text-xs text-slate-500">
                                                    Rp {{ number_format($p->cost_price, 0, ',', '.') }}
                                                </td>
                                                <td class="py-3.5 px-4 text-center">
                                                    @php
                                                        $margin = $p->base_price > 0 ? round((($p->base_price - $p->cost_price) / $p->base_price) * 100) : 0;
                                                    @endphp
                                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $margin >= 60 ? 'bg-emerald-50 text-emerald-600' : ($margin >= 40 ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600') }}">
                                                        {{ $margin }}%
                                                    </span>
                                                </td>
                                                <td class="py-3.5 px-4 text-center">
                                                    <button wire:click="toggleProductAvailability({{ $p->id }})"
                                                        class="text-xs font-semibold px-2.5 py-1 rounded-lg transition {{ $p->is_available ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-rose-50 text-rose-600 border border-rose-200' }}">
                                                        {{ $p->is_available ? 'Tersedia' : 'Habis' }}
                                                    </button>
                                                </td>
                                                <td class="py-3.5 px-4 text-right space-x-1">
                                                    <button wire:click="openRecipeModal({{ $p->id }})"
                                                        title="Atur Resep BOM & HPP"
                                                        class="p-1.5 text-xs text-slate-500 hover:text-orange-600 hover:bg-orange-50 rounded-lg">
                                                        <i class="fa-solid fa-mortar-pestle"></i>
                                                    </button>
                                                    <button wire:click="editProduct({{ $p->id }})"
                                                        title="Edit Produk"
                                                        class="p-1.5 text-xs text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                                    Tidak ada menu yang sesuai filter.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- ==================== TAB: MASTER LAYANAN (KHUSUS SALON & BARBERSHOP) ==================== -->
                @if ($activeTab === 'services')
                    <div class="space-y-5">
                        <!-- Top Header & Action Controls -->
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h1 class="text-xl font-bold text-slate-900">Katalog & Master Layanan</h1>
                                    <span class="text-[10px] font-bold bg-indigo-50 text-indigo-600 border border-indigo-200 px-2 py-0.5 rounded-full uppercase tracking-wider">
                                        Modul Salon & Barbershop
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">Kelola tarif potong rambut, perawatan spa, durasi waktu pengerjaan, dan skema komisi kapster/stylist.</p>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <button wire:click="switchTab('products')"
                                    class="px-3.5 py-2.5 bg-orange-50 hover:bg-orange-100 text-orange-700 border border-orange-200 font-semibold text-xs rounded-xl shadow-xs flex items-center gap-2 transition">
                                    <i class="fa-solid fa-boxes-stacked"></i>
                                    <span>Ke Master Produk ({{ $products->count() }})</span>
                                </button>
                                <button wire:click="openNewServiceModal"
                                    class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition">
                                    <i class="fa-solid fa-scissors"></i>
                                    <span>Tambah Layanan Baru</span>
                                </button>
                            </div>
                        </div>

                        <!-- 4 Stat Summary Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                                <div class="text-slate-500 text-xs font-medium">Total Layanan Aktif</div>
                                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $services->where('is_available', true)->count() }} Layanan</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $services->count() }} total terdaftar</div>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                                <div class="text-slate-500 text-xs font-medium">Rata-rata Durasi Layanan</div>
                                <div class="text-2xl font-bold text-indigo-600 mt-1">
                                    {{ $services->count() > 0 ? round($services->avg('duration_minutes')) : 40 }} Menit
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Waktu pengerjaan per sesi</div>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                                <div class="text-slate-500 text-xs font-medium">Rata-rata Tarif Layanan</div>
                                <div class="text-2xl font-bold text-emerald-600 mt-1">
                                    Rp {{ number_format($services->count() > 0 ? $services->avg('base_price') : 0, 0, ',', '.') }}
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Harga per transaksi jasa</div>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                                <div class="text-slate-500 text-xs font-medium">Rata-rata Komisi Kapster</div>
                                <div class="text-2xl font-bold text-orange-600 mt-1">
                                    @php
                                        $avgCom = 0;
                                        if ($services->count() > 0) {
                                            $totalCom = 0;
                                            foreach($services as $s) {
                                                $totalCom += $s->estimated_commission;
                                            }
                                            $avgCom = $totalCom / $services->count();
                                        }
                                    @endphp
                                    Rp {{ number_format($avgCom, 0, ',', '.') }}
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Estimasi hak kapster / staf</div>
                            </div>
                        </div>

                        <!-- Search & Category Filters -->
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-2 sm:pb-0">
                                <button wire:click="$set('selectedServiceCategoryFilter', 'all')"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $selectedServiceCategoryFilter === 'all' ? 'bg-orange-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                    Semua Kategori ({{ $services->count() }})
                                </button>
                                @foreach($serviceCategories ?? $categories as $cat)
                                    <button wire:click="$set('selectedServiceCategoryFilter', {{ $cat->id }})"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-lg transition whitespace-nowrap {{ $selectedServiceCategoryFilter == $cat->id ? 'bg-orange-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                        {{ $cat->name }} ({{ $cat->products_count }})
                                    </button>
                                @endforeach
                            </div>

                            <div class="relative w-full sm:w-64">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                                <input type="text" wire:model.live.debounce.300ms="searchService"
                                    placeholder="Cari layanan jasa..."
                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-8 pr-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
                            </div>
                        </div>

                        <!-- Service Table -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 border-b border-slate-200/80 text-xs text-slate-500 uppercase tracking-wider font-semibold">
                                        <tr>
                                            <th class="py-3 px-4">Nama Layanan</th>
                                            <th class="py-3 px-4">Kategori</th>
                                            <th class="py-3 px-4 text-center">Durasi</th>
                                            <th class="py-3 px-4 text-right">Tarif Jasa</th>
                                            <th class="py-3 px-4 text-right">Komisi Kapster</th>
                                            <th class="py-3 px-4 text-right">Modal Bahan</th>
                                            <th class="py-3 px-4 text-center">Margin Bersih</th>
                                            <th class="py-3 px-4 text-center">Status</th>
                                            <th class="py-3 px-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($services as $srv)
                                            <tr class="hover:bg-slate-50/60 transition">
                                                <td class="py-3.5 px-4">
                                                    <div class="flex items-center gap-3">
                                                        <img src="{{ $srv->image_url ?: 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?auto=format&fit=crop&w=150&h=150&q=80' }}"
                                                            class="w-10 h-10 rounded-xl object-cover border border-slate-200 shrink-0">
                                                        <div>
                                                            <div class="font-bold text-slate-900">{{ $srv->name }}</div>
                                                            <div class="text-[11px] text-slate-400">{{ $srv->sku ?? 'SRV-00' . $srv->id }} • {{ $srv->category_tag ?? 'Treatment' }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3.5 px-4 text-xs">
                                                    <span class="bg-slate-100 px-2.5 py-1 rounded-md font-medium text-slate-700">
                                                        {{ $srv->category->name ?? 'Layanan' }}
                                                    </span>
                                                </td>
                                                <td class="py-3.5 px-4 text-center">
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                                                        <i class="fa-regular fa-clock text-[10px]"></i>
                                                        {{ $srv->duration_minutes ?? 30 }} Menit
                                                    </span>
                                                </td>
                                                <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                                                    Rp {{ number_format($srv->base_price, 0, ',', '.') }}
                                                </td>
                                                <td class="py-3.5 px-4 text-right text-xs">
                                                    <span class="font-bold text-indigo-600 block">
                                                        Rp {{ number_format($srv->estimated_commission, 0, ',', '.') }}
                                                    </span>
                                                    <span class="text-[10px] text-slate-400">
                                                        {{ $srv->commission_type === 'percentage' ? '(' . (int)$srv->commission_value . '% Tarif)' : '(Nominal Tetap)' }}
                                                    </span>
                                                </td>
                                                <td class="py-3.5 px-4 text-right text-xs text-slate-500">
                                                    Rp {{ number_format($srv->cost_price, 0, ',', '.') }}
                                                </td>
                                                <td class="py-3.5 px-4 text-center">
                                                    @php
                                                        $netRevenue = $srv->base_price - $srv->estimated_commission - $srv->cost_price;
                                                        $marginPct = $srv->base_price > 0 ? round(($netRevenue / $srv->base_price) * 100) : 0;
                                                    @endphp
                                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $marginPct >= 50 ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                                        Rp {{ number_format($netRevenue, 0, ',', '.') }} ({{ $marginPct }}%)
                                                    </span>
                                                </td>
                                                <td class="py-3.5 px-4 text-center">
                                                    <button wire:click="toggleServiceAvailability({{ $srv->id }})"
                                                        class="text-xs font-semibold px-2.5 py-1 rounded-lg transition {{ $srv->is_available ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-rose-50 text-rose-600 border border-rose-200' }}">
                                                        {{ $srv->is_available ? 'Tersedia' : 'Libur' }}
                                                    </button>
                                                </td>
                                                <td class="py-3.5 px-4 text-right space-x-1">
                                                    <button wire:click="editService({{ $srv->id }})"
                                                        title="Edit Layanan"
                                                        class="p-1.5 text-xs text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>
                                                    <button wire:click="deleteService({{ $srv->id }})"
                                                        wire:confirm="Yakin ingin menghapus layanan '{{ $srv->name }}'?"
                                                        title="Hapus Layanan"
                                                        class="p-1.5 text-xs text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="py-12 text-center text-slate-400 text-sm">
                                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-xl">
                                                        <i class="fa-solid fa-scissors"></i>
                                                    </div>
                                                    <div>Belum ada layanan salon/barbershop yang ditambahkan.</div>
                                                    <button wire:click="openNewServiceModal" class="mt-2 text-xs text-orange-600 font-semibold hover:underline">
                                                        + Tambah Layanan Pertama
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- ==================== TAB 3: BAHAN BAKU & RESEP BOM ==================== -->
                @if ($activeTab === 'recipes')
                    <div class="space-y-6">
                        <!-- Top Header -->
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div>
                                <h1 class="text-xl font-bold text-slate-900">Bahan Baku & Resep BOM (Bill of Materials)</h1>
                                <p class="text-xs text-slate-500 mt-0.5">Konfigurasi takaran bahan per porsi, auto-deduct saat kasir menjual, dan kalkulasi HPP otomatis.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="openNewIngredientModal"
                                    class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>Tambah Bahan Baku</span>
                                </button>
                            </div>
                        </div>

                        <!-- 2 Columns: Left = Raw Materials Table, Right = Recipe Cost Calculator -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Raw Materials Table (2 cols) -->
                            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                                    <h3 class="font-bold text-slate-900 text-sm">Daftar Bahan Baku Mentah</h3>
                                    <span class="text-xs text-slate-400">{{ $ingredients->count() }} item terdaftar</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-sm">
                                        <thead class="bg-slate-50 border-b border-slate-200/80 text-xs text-slate-500 uppercase font-semibold">
                                            <tr>
                                                <th class="py-3 px-4">Bahan Baku</th>
                                                <th class="py-3 px-4">Satuan Beli & Pakai</th>
                                                <th class="py-3 px-4 text-right">Biaya Satuan Pakai</th>
                                                <th class="py-3 px-4 text-right">Stok Saat Ini</th>
                                                <th class="py-3 px-4 text-center">Status</th>
                                                <th class="py-3 px-4 text-right">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            @foreach($ingredients as $ing)
                                                <tr class="hover:bg-slate-50/60 transition">
                                                    <td class="py-3 px-4">
                                                        <div class="font-bold text-slate-900">{{ $ing->name }}</div>
                                                        <div class="text-[11px] text-slate-400">{{ $ing->code }} • {{ $ing->storage_location }}</div>
                                                    </td>
                                                    <td class="py-3 px-4 text-xs text-slate-600">
                                                        1 {{ $ing->purchase_unit }} = {{ (int)$ing->conversion_factor }} {{ $ing->usage_unit }}
                                                    </td>
                                                    <td class="py-3 px-4 text-right text-xs font-semibold text-slate-800">
                                                        Rp {{ number_format($ing->cost_per_usage_unit, 0, ',', '.') }} / {{ $ing->usage_unit }}
                                                    </td>
                                                    <td class="py-3 px-4 text-right font-bold {{ $ing->current_stock <= $ing->minimum_stock ? 'text-rose-600' : 'text-slate-900' }}">
                                                        {{ number_format($ing->current_stock, 0, ',', '.') }} {{ $ing->usage_unit }}
                                                    </td>
                                                    <td class="py-3 px-4 text-center">
                                                        @if($ing->current_stock <= $ing->minimum_stock)
                                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-50 text-rose-600 border border-rose-200 animate-pulse">
                                                                Menipis!
                                                            </span>
                                                        @else
                                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200">
                                                                Aman
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3 px-4 text-right">
                                                        <button wire:click="openOpnameModal({{ $ing->id }})"
                                                            title="Sesuaikan Stok (Opname)"
                                                            class="text-xs font-semibold px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg">
                                                            Opname
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Right: Recipe Matrix Sample (1 col) -->
                            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                                <h3 class="font-bold text-slate-900 text-base">Contoh Simulasi HPP Resep</h3>
                                <p class="text-xs text-slate-500">Klik tombol di bawah untuk melihat komposisi resep dan rekalkulasi HPP otomatis.</p>

                                <div class="space-y-3">
                                    @foreach($products->take(4) as $p)
                                        <div class="p-3 rounded-xl border border-slate-200/80 bg-slate-50 flex items-center justify-between gap-3">
                                            <div>
                                                <div class="text-sm font-bold text-slate-800">{{ $p->name }}</div>
                                                <div class="text-xs text-slate-500">
                                                    HPP: <span class="font-semibold text-orange-600">Rp {{ number_format($p->cost_price, 0, ',', '.') }}</span>
                                                    • Jual: Rp {{ number_format($p->base_price, 0, ',', '.') }}
                                                </div>
                                            </div>
                                            <button wire:click="openRecipeModal({{ $p->id }})"
                                                class="px-2.5 py-1.5 text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:border-orange-500 hover:text-orange-600 rounded-lg shadow-2xs">
                                                Resep BOM
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- ==================== TAB 4: STOK & GUDANG ==================== -->
                @if ($activeTab === 'stock')
                    <div class="space-y-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div>
                                <h1 class="text-xl font-bold text-slate-900">Stok & Inventori Multi-Lokasi</h1>
                                <p class="text-xs text-slate-500 mt-0.5">Saldo persediaan bahan, kartu mutasi stok real-time, dan audit stok opname fisik.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="openOpnameModal({{ $ingredients->first()->id ?? 1 }})"
                                    class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition">
                                    <i class="fa-solid fa-clipboard-check"></i>
                                    <span>Mulai Stok Opname</span>
                                </button>
                            </div>
                        </div>

                        <!-- 3 Multi-Storage Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                                <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                                    <span class="font-semibold text-slate-700">📍 Bar Depan & Display</span>
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 font-bold">Aktif Kasir</span>
                                </div>
                                <div class="text-2xl font-bold text-slate-900">4 Bahan Utama</div>
                                <div class="text-xs text-slate-500 mt-1">Biji kopi, gula aren, cup takeaway</div>
                            </div>

                            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                                <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                                    <span class="font-semibold text-slate-700">❄️ Freezer & Chiller Dapur</span>
                                    <span class="px-2 py-0.5 rounded-full bg-sky-50 text-sky-600 font-bold">Suhu Dingin</span>
                                </div>
                                <div class="text-2xl font-bold text-slate-900">3 Bahan Segar</div>
                                <div class="text-xs text-slate-500 mt-1">Susu Fresh Milk, Ayam, Kentang Beku</div>
                            </div>

                            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                                <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                                    <span class="font-semibold text-slate-700">📦 Gudang Belakang / Logistik</span>
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold">Stok Cadangan</span>
                                </div>
                                <div class="text-2xl font-bold text-slate-900">Stok Buffer</div>
                                <div class="text-xs text-slate-500 mt-1">Kemasan dus kardus & sirup cadangan</div>
                            </div>
                        </div>

                        <!-- Stock Movement Ledger Table -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <div class="p-4 border-b border-slate-100">
                                <h3 class="font-bold text-slate-900 text-sm">Kartu Mutasi Stok Terakhir (Audit Trail)</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 border-b border-slate-200/80 text-xs text-slate-500 uppercase font-semibold">
                                        <tr>
                                            <th class="py-3 px-4">Waktu</th>
                                            <th class="py-3 px-4">Bahan Baku</th>
                                            <th class="py-3 px-4 text-center">Jenis Mutasi</th>
                                            <th class="py-3 px-4 text-right">Perubahan Qty</th>
                                            <th class="py-3 px-4 text-right">Saldo Akhir</th>
                                            <th class="py-3 px-4">Keterangan / Referensi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($stockMovements as $mv)
                                            <tr class="hover:bg-slate-50/60 transition">
                                                <td class="py-3 px-4 text-xs text-slate-500">{{ $mv->created_at->format('d/m/Y H:i') }}</td>
                                                <td class="py-3 px-4 font-bold text-slate-900">{{ $mv->ingredient->name ?? '-' }}</td>
                                                <td class="py-3 px-4 text-center">
                                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md uppercase {{ $mv->type === 'purchase' ? 'bg-emerald-50 text-emerald-600' : ($mv->type === 'sale_pos' ? 'bg-sky-50 text-sky-600' : 'bg-amber-50 text-amber-600') }}">
                                                        {{ $mv->type }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-right font-bold {{ $mv->qty >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                                    {{ $mv->qty >= 0 ? '+' : '' }}{{ number_format($mv->qty, 0, ',', '.') }} {{ $mv->ingredient->usage_unit ?? '' }}
                                                </td>
                                                <td class="py-3 px-4 text-right font-semibold text-slate-800">
                                                    {{ number_format($mv->balance_after, 0, ',', '.') }} {{ $mv->ingredient->usage_unit ?? '' }}
                                                </td>
                                                <td class="py-3 px-4 text-xs text-slate-500">{{ $mv->reference_note ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-6 text-center text-slate-400 text-sm">Belum ada mutasi stok tercatat.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- ==================== TAB 5: PEMBELIAN & SUPPLIER ==================== -->
                @if ($activeTab === 'purchases')
                    <div class="space-y-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div>
                                <h1 class="text-xl font-bold text-slate-900">Pembelian & Supplier (Procurement)</h1>
                                <p class="text-xs text-slate-500 mt-0.5">Kelola Purchase Order (PO), penerimaan barang (GRN), dan daftar kontak pemasok.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="openPoModal"
                                    class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition">
                                    <i class="fa-solid fa-cart-plus"></i>
                                    <span>Buat Purchase Order (PO)</span>
                                </button>
                            </div>
                        </div>

                        <!-- PO Table -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                                <h3 class="font-bold text-slate-900 text-sm">Daftar Purchase Order</h3>
                                <span class="text-xs text-slate-500">{{ $purchaseOrders->count() }} PO terdaftar</span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 border-b border-slate-200/80 text-xs text-slate-500 uppercase font-semibold">
                                        <tr>
                                            <th class="py-3 px-4">No. PO</th>
                                            <th class="py-3 px-4">Supplier</th>
                                            <th class="py-3 px-4">Tanggal Order</th>
                                            <th class="py-3 px-4 text-right">Total Tagihan</th>
                                            <th class="py-3 px-4 text-center">Status</th>
                                            <th class="py-3 px-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($purchaseOrders as $po)
                                            <tr class="hover:bg-slate-50/60 transition">
                                                <td class="py-3 px-4 font-bold text-slate-900">{{ $po->po_number }}</td>
                                                <td class="py-3 px-4">
                                                    <div class="font-semibold text-slate-800">{{ $po->supplier->name ?? '-' }}</div>
                                                    <div class="text-[11px] text-slate-400">{{ $po->supplier->contact_person ?? '' }} ({{ $po->supplier->phone ?? '' }})</div>
                                                </td>
                                                <td class="py-3 px-4 text-xs text-slate-600">{{ $po->order_date->format('d M Y') }}</td>
                                                <td class="py-3 px-4 text-right font-bold text-slate-900">
                                                    Rp {{ number_format($po->total_amount, 0, ',', '.') }}
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $po->status === 'received' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-amber-50 text-amber-600 border border-amber-200' }}">
                                                        {{ $po->status === 'received' ? 'Diterima (GRN)' : 'Dipesan' }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-right">
                                                    @if($po->status !== 'received')
                                                        <button wire:click="markPoReceived({{ $po->id }})"
                                                            class="text-xs font-semibold px-2.5 py-1 bg-emerald-50 text-emerald-600 border border-emerald-200 hover:bg-emerald-100 rounded-lg">
                                                            Terima Barang (GRN)
                                                        </button>
                                                    @else
                                                        <span class="text-xs text-slate-400"><i class="fa-solid fa-check text-emerald-500 mr-1"></i> Selesai</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-6 text-center text-slate-400 text-sm">Belum ada purchase order.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Suppliers Master Cards -->
                        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                            <h3 class="font-bold text-slate-900 text-sm">Master Rekanan Supplier</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach($suppliers as $sup)
                                    <div class="p-4 rounded-xl border border-slate-200/80 bg-slate-50">
                                        <div class="font-bold text-slate-900 text-sm">{{ $sup->name }}</div>
                                        <div class="text-xs text-slate-500 mt-1">PIC: {{ $sup->contact_person }}</div>
                                        <div class="text-xs text-slate-500">Telp: {{ $sup->phone }}</div>
                                        <div class="mt-2 text-[11px] font-semibold text-slate-600 bg-white px-2 py-1 rounded inline-block border border-slate-200/60">
                                            Tempo: {{ $sup->terms_days == 0 ? 'Cash on Delivery (COD)' : $sup->terms_days . ' Hari' }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- ==================== TAB 6: RIWAYAT TRANSAKSI ==================== -->
                @if ($activeTab === 'transactions')
                    <div class="space-y-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div>
                                <h1 class="text-xl font-bold text-slate-900">Riwayat Transaksi Penjualan</h1>
                                <p class="text-xs text-slate-500 mt-0.5">Audit log lengkap struk belanja, status order, metode pembayaran, dan otorisasi void.</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <select wire:model.live="transactionStatusFilter"
                                    class="bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 py-1.5 px-3 rounded-lg focus:outline-none focus:ring-1 focus:ring-orange-500">
                                    <option value="all">Semua Status</option>
                                    <option value="completed">Selesai</option>
                                    <option value="hold">Disimpan (Hold)</option>
                                    <option value="voided">Dibatalkan (Void)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Transactions Table -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 border-b border-slate-200/80 text-xs text-slate-500 uppercase font-semibold">
                                        <tr>
                                            <th class="py-3 px-4">No. Order</th>
                                            <th class="py-3 px-4">Waktu</th>
                                            <th class="py-3 px-4">Tipe & Meja</th>
                                            <th class="py-3 px-4 text-right">Total Transaksi</th>
                                            <th class="py-3 px-4 text-center">Status</th>
                                            <th class="py-3 px-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($transactions as $tx)
                                            <tr class="hover:bg-slate-50/60 transition">
                                                <td class="py-3 px-4 font-bold text-slate-900">
                                                    {{ $tx->order_number }}
                                                    <div class="text-[11px] font-normal text-slate-400">
                                                        {{ $tx->items->count() }} item • Kasir: Dina
                                                    </div>
                                                </td>
                                                <td class="py-3 px-4 text-xs text-slate-600">
                                                    {{ $tx->created_at->format('d M Y, H:i') }}
                                                </td>
                                                <td class="py-3 px-4 text-xs text-slate-600">
                                                    <span class="capitalize font-medium">{{ str_replace('_', ' ', $tx->order_type) }}</span>
                                                    @if($tx->table_number)
                                                        <span class="text-orange-600 font-semibold">• {{ $tx->table_number }}</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4 text-right font-bold text-slate-900">
                                                    Rp {{ number_format($tx->total_amount, 0, ',', '.') }}
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $tx->status === 'completed' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : ($tx->status === 'voided' ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-amber-50 text-amber-600 border border-amber-200') }}">
                                                        {{ $tx->status === 'completed' ? 'Lunas' : ($tx->status === 'voided' ? 'Void' : 'Hold') }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-right">
                                                    @if($tx->status === 'completed')
                                                        <button wire:click="openVoidModal({{ $tx->id }})"
                                                            title="Otorisasi Void Pesanan"
                                                            class="text-xs font-semibold px-2.5 py-1 text-rose-600 hover:bg-rose-50 border border-rose-200 rounded-lg">
                                                            Void Order
                                                        </button>
                                                    @else
                                                        <span class="text-xs text-slate-400">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                                                    Belum ada transaksi pada kriteria filter ini.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- ==================== TAB: DISKON & PROMOSI ==================== -->
                @if ($activeTab === 'discounts')
                    <div class="space-y-5">
                        <!-- Top Header & Action Controls -->
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div>
                                <h1 class="text-xl font-bold text-slate-900">Manajemen Diskon & Promosi</h1>
                                <p class="text-xs text-slate-500 mt-0.5">Atur skema diskon persentase (%) atau potongan nominal langsung (Rp) untuk layanan dan produk.</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <button wire:click="openNewDiscountModal"
                                    class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition">
                                    <i class="fa-solid fa-tags"></i>
                                    <span>Tambah Diskon Baru</span>
                                </button>
                            </div>
                        </div>

                        <!-- 4 Stat Summary Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                                <div class="text-slate-500 text-xs font-medium">Promo Aktif</div>
                                <div class="text-2xl font-bold text-emerald-600 mt-1">
                                    {{ $discounts->where('is_active', true)->count() }} Promo
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Siap digunakan di POS kasir</div>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                                <div class="text-slate-500 text-xs font-medium">Diskon Persentase (%)</div>
                                <div class="text-2xl font-bold text-indigo-600 mt-1">
                                    {{ $discounts->where('type', 'percentage')->count() }} Kupon
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Potongan berbasis rasio %</div>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                                <div class="text-slate-500 text-xs font-medium">Diskon Nominal Uang (Rp)</div>
                                <div class="text-2xl font-bold text-purple-600 mt-1">
                                    {{ $discounts->where('type', 'fixed')->count() }} Kupon
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Potongan rupiah langsung</div>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                                <div class="text-slate-500 text-xs font-medium">Total Penggunaan Promo</div>
                                <div class="text-2xl font-bold text-slate-900 mt-1">
                                    {{ $discounts->sum('usage_count') }} Kali
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Digunakan oleh pelanggan</div>
                            </div>
                        </div>

                        <!-- Filter & Search Controls -->
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div class="flex items-center gap-2">
                                <button wire:click="$set('discountStatusFilter', 'all')"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $discountStatusFilter === 'all' ? 'bg-orange-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                    Semua Promo ({{ $discounts->count() }})
                                </button>
                                <button wire:click="$set('discountStatusFilter', 'active')"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $discountStatusFilter === 'active' ? 'bg-orange-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                    Sedang Aktif
                                </button>
                                <button wire:click="$set('discountStatusFilter', 'inactive')"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $discountStatusFilter === 'inactive' ? 'bg-orange-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                    Nonaktif
                                </button>
                            </div>

                            <div class="relative w-full sm:w-64">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                                <input type="text" wire:model.live.debounce.300ms="searchDiscount"
                                    placeholder="Cari nama atau kode promo..."
                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-8 pr-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
                            </div>
                        </div>

                        <!-- Discounts Table -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 border-b border-slate-200/80 text-xs text-slate-500 uppercase tracking-wider font-semibold">
                                        <tr>
                                            <th class="py-3 px-4">Nama Diskon & Kupon</th>
                                            <th class="py-3 px-4 text-center">Tipe & Nilai Potongan</th>
                                            <th class="py-3 px-4 text-right">Syarat Min. Belanja</th>
                                            <th class="py-3 px-4 text-center">Berlaku Untuk</th>
                                            <th class="py-3 px-4 text-center">Periode Berlaku</th>
                                            <th class="py-3 px-4 text-center">Penggunaan</th>
                                            <th class="py-3 px-4 text-center">Status</th>
                                            <th class="py-3 px-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($discounts as $disc)
                                            <tr class="hover:bg-slate-50/60 transition">
                                                <td class="py-3.5 px-4">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $disc->type === 'percentage' ? 'bg-emerald-50 text-emerald-600' : 'bg-purple-50 text-purple-600' }}">
                                                            <i class="fa-solid {{ $disc->type === 'percentage' ? 'fa-percent' : 'fa-money-bill-wave' }}"></i>
                                                        </div>
                                                        <div>
                                                            <div class="font-bold text-slate-900">{{ $disc->name }}</div>
                                                            <div class="flex items-center gap-2 mt-0.5">
                                                                @if($disc->code)
                                                                    <span class="font-mono text-[10px] bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded border border-slate-200 font-semibold">
                                                                        {{ $disc->code }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-[10px] text-slate-400">Otomatis / Tanpa Kode</span>
                                                                @endif
                                                                @if($disc->notes)
                                                                    <span class="text-[11px] text-slate-400 truncate max-w-xs">• {{ $disc->notes }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3.5 px-4 text-center">
                                                    @if($disc->type === 'percentage')
                                                        <span class="inline-flex flex-col items-center">
                                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                                Diskon {{ (int)$disc->value }}%
                                                            </span>
                                                            @if($disc->max_discount)
                                                                <span class="text-[10px] text-slate-400 mt-0.5">Maks. Rp {{ number_format($disc->max_discount, 0, ',', '.') }}</span>
                                                            @endif
                                                        </span>
                                                    @else
                                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                            Potongan Rp {{ number_format($disc->value, 0, ',', '.') }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-3.5 px-4 text-right font-medium text-slate-700">
                                                    {{ $disc->min_purchase > 0 ? 'Rp ' . number_format($disc->min_purchase, 0, ',', '.') : 'Tanpa Minimum' }}
                                                </td>
                                                <td class="py-3.5 px-4 text-center">
                                                    <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">
                                                        @if($disc->applicable_to === 'services')
                                                            Khusus Layanan Jasa
                                                        @elseif($disc->applicable_to === 'products')
                                                            Khusus Produk Retail
                                                        @else
                                                            Semua Menu & Layanan
                                                        @endif
                                                    </span>
                                                </td>
                                                <td class="py-3.5 px-4 text-center text-xs text-slate-500">
                                                    @if($disc->start_date && $disc->end_date)
                                                        <div>{{ $disc->start_date->format('d M') }} - {{ $disc->end_date->format('d M Y') }}</div>
                                                    @elseif($disc->end_date)
                                                        <div>s/d {{ $disc->end_date->format('d M Y') }}</div>
                                                    @else
                                                        <span class="text-emerald-600 font-medium">Berlaku Selamanya</span>
                                                    @endif
                                                </td>
                                                <td class="py-3.5 px-4 text-center text-xs">
                                                    <span class="font-bold text-slate-700">{{ $disc->usage_count }}</span>
                                                    <span class="text-slate-400">/ {{ $disc->max_usage ?: '∞' }} kali</span>
                                                </td>
                                                <td class="py-3.5 px-4 text-center">
                                                    <button wire:click="toggleDiscountStatus({{ $disc->id }})"
                                                        class="text-xs font-semibold px-2.5 py-1 rounded-lg transition {{ $disc->is_active ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                                        {{ $disc->is_active ? 'Aktif' : 'Nonaktif' }}
                                                    </button>
                                                </td>
                                                <td class="py-3.5 px-4 text-right space-x-1">
                                                    <button wire:click="editDiscount({{ $disc->id }})"
                                                        title="Edit Promo"
                                                        class="p-1.5 text-xs text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>
                                                    <button wire:click="deleteDiscount({{ $disc->id }})"
                                                        wire:confirm="Yakin ingin menghapus diskon '{{ $disc->name }}'?"
                                                        title="Hapus Promo"
                                                        class="p-1.5 text-xs text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="py-12 text-center text-slate-400 text-sm">
                                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-xl">
                                                        <i class="fa-solid fa-tags"></i>
                                                    </div>
                                                    <div>Belum ada program diskon atau promosi aktif.</div>
                                                    <button wire:click="openNewDiscountModal" class="mt-2 text-xs text-orange-600 font-semibold hover:underline">
                                                        + Buat Promo Pertama
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- ==================== TAB 7: LABA RUGI & FINANSIAL ==================== -->
                @if ($activeTab === 'finance')
                    <div class="space-y-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div>
                                <h1 class="text-xl font-bold text-slate-900">Laporan Laba Rugi (P&L) & Arus Kas</h1>
                                <p class="text-xs text-slate-500 mt-0.5">Analisis keuntungan bersih: Omzet Kotor dikurangi Biaya HPP dan Biaya Operasional (OPEX).</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="openExpenseModal"
                                    class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>Catat Pengeluaran Toko</span>
                                </button>
                            </div>
                        </div>

                        <!-- P&L Financial Waterfall Card -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                            <h3 class="font-bold text-slate-900 text-base">Laporan Laba Rugi Bulan Ini (Oktober 2026)</h3>

                            <div class="space-y-2.5 text-sm">
                                <div class="flex justify-between py-2 border-b border-slate-100 font-semibold text-slate-800">
                                    <span>(+) Pendapatan Penjualan Kotor (Gross Sales)</span>
                                    <span>Rp {{ number_format($monthGrossSales, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-slate-100 text-rose-600 font-medium pl-4">
                                    <span>(-) Harga Pokok Penjualan (HPP / Biaya Bahan)</span>
                                    <span>- Rp {{ number_format($estimatedCogs, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between py-2.5 bg-emerald-50/70 px-3 rounded-xl font-bold text-emerald-800">
                                    <span>(=) Laba Kotor (Gross Profit) [{{ $grossMarginPct }}%]</span>
                                    <span>Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-slate-100 text-rose-600 font-medium pl-4">
                                    <span>(-) Total Biaya Operasional Toko (OPEX)</span>
                                    <span>- Rp {{ number_format($totalOpex, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between py-3.5 bg-slate-900 text-white px-4 rounded-xl font-bold text-base shadow-sm">
                                    <span>(=) Laba Bersih Toko (Net Profit)</span>
                                    <span class="{{ $netProfit >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                        Rp {{ number_format($netProfit, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Expenses (OPEX) Table -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                                <h3 class="font-bold text-slate-900 text-sm">Rincian Beban Biaya Operasional (OPEX)</h3>
                                <span class="text-xs text-slate-500">Total: Rp {{ number_format($totalOpex, 0, ',', '.') }}</span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 border-b border-slate-200/80 text-xs text-slate-500 uppercase font-semibold">
                                        <tr>
                                            <th class="py-3 px-4">Tanggal</th>
                                            <th class="py-3 px-4">Kategori Beban</th>
                                            <th class="py-3 px-4">Deskripsi Biaya</th>
                                            <th class="py-3 px-4">Metode Bayar</th>
                                            <th class="py-3 px-4 text-right">Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($expenses as $exp)
                                            <tr class="hover:bg-slate-50/60 transition">
                                                <td class="py-3 px-4 text-xs text-slate-500">{{ $exp->expense_date->format('d M Y') }}</td>
                                                <td class="py-3 px-4">
                                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">
                                                        {{ $exp->category }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 font-semibold text-slate-900">
                                                    {{ $exp->title }}
                                                    <div class="text-[11px] font-normal text-slate-400">{{ $exp->notes }}</div>
                                                </td>
                                                <td class="py-3 px-4 text-xs text-slate-600">{{ $exp->payment_method }}</td>
                                                <td class="py-3 px-4 text-right font-bold text-rose-600">
                                                    Rp {{ number_format($exp->amount, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- ==================== TAB 8: CRM & PELANGGAN ==================== -->
                @if ($activeTab === 'crm')
                    <div class="space-y-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div>
                                <h1 class="text-xl font-bold text-slate-900">Pelanggan & CRM Loyalti</h1>
                                <p class="text-xs text-slate-500 mt-0.5">Database member toko, poin belanja yang dapat ditukarkan, dan riwayat kunjungan.</p>
                            </div>
                        </div>

                        <!-- Customer Table -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 border-b border-slate-200/80 text-xs text-slate-500 uppercase font-semibold">
                                        <tr>
                                            <th class="py-3 px-4">Nama Pelanggan</th>
                                            <th class="py-3 px-4">No. WhatsApp</th>
                                            <th class="py-3 px-4 text-center">Tier Member</th>
                                            <th class="py-3 px-4 text-right">Poin Loyalti</th>
                                            <th class="py-3 px-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($customers as $c)
                                            <tr class="hover:bg-slate-50/60 transition">
                                                <td class="py-3 px-4">
                                                    <div class="font-bold text-slate-900">{{ $c->name }}</div>
                                                    <div class="text-[11px] text-slate-400">{{ $c->email ?? '-' }}</div>
                                                </td>
                                                <td class="py-3 px-4 text-xs font-medium text-slate-600">{{ $c->phone ?? '-' }}</td>
                                                <td class="py-3 px-4 text-center">
                                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $c->points >= 500 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}">
                                                        {{ $c->points >= 500 ? '👑 Gold VIP' : 'Silver Member' }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-right font-bold text-orange-600">
                                                    {{ number_format($c->points, 0, ',', '.') }} Pts
                                                </td>
                                                <td class="py-3 px-4 text-right">
                                                    <button class="text-xs text-slate-500 hover:text-orange-600 font-medium">Lihat Riwayat</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="py-8 text-center text-slate-400 text-sm">Belum ada pelanggan terdaftar.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- ==================== TAB 9: PENGATURAN OUTLET & STAF KASIR ==================== -->
                @if ($activeTab === 'settings')
                    <div class="space-y-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div>
                                <h1 class="text-xl font-bold text-slate-900">Pengaturan Outlet & Staf Kasir</h1>
                                <p class="text-xs text-slate-500 mt-0.5">Konfigurasi struk belanja thermal, persentase pajak PPn 11%, dan manajemen PIN kasir.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Left: Outlet Settings Form -->
                            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                                <h3 class="font-bold text-slate-900 text-base">Profil & Konfigurasi Cabang</h3>

                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Outlet</label>
                                        <input type="text" value="{{ $selectedOutlet->name ?? 'Outlet Utama' }}"
                                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 font-medium">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
                                        <textarea rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 font-medium">{{ $selectedOutlet->address ?? 'Jl. Panglima Polim No. 12, Jakarta Selatan' }}</textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tarif Pajak PPn (%)</label>
                                            <input type="number" value="{{ (int)$selectedOutlet->tax_rate }}"
                                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 font-medium">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Service Charge (%)</label>
                                            <input type="number" value="0"
                                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 font-medium">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pesan Footer Struk Kasir Thermal</label>
                                        <input type="text" value="Terima kasih atas kunjungan Anda di Kopi Ruang Pagi!"
                                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 font-medium">
                                    </div>
                                    <div class="pt-2">
                                        <button wire:click="$dispatch('toast', {message: 'Pengaturan outlet berhasil disimpan!'})"
                                            class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-sm">
                                            Simpan Perubahan
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Cashier Staff & PIN Management -->
                            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                                <h3 class="font-bold text-slate-900 text-base">Manajemen Staf & PIN Kasir POS</h3>
                                <p class="text-xs text-slate-500">Staf menggunakan 6 digit PIN untuk login cepat dan otorisasi transaksi pada layar sentuh POS kasir.</p>

                                <div class="space-y-3">
                                    @foreach($cashiers as $staff)
                                        <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50 flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-orange-100 text-orange-600 font-bold flex items-center justify-center text-xs">
                                                    {{ substr($staff->name, 0, 2) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-900 text-sm">{{ $staff->name }}</div>
                                                    <div class="text-[11px] text-slate-500 capitalize">{{ $staff->role }} • {{ $staff->email }}</div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-xs font-mono font-bold text-slate-700 bg-white px-2 py-1 rounded border border-slate-200">
                                                    PIN: {{ $staff->pin_code ?? '123456' }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </main>
        </div>
    </div>

    <!-- ==================== MODALS ==================== -->

    <!-- 1. Modal Tambah / Edit Produk -->
    @if ($showProductModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-base">{{ $editingProductId ? 'Edit Produk Menu' : 'Tambah Menu Produk Baru' }}</h3>
                    <button wire:click="$set('showProductModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto text-sm">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Menu</label>
                        <input type="text" wire:model="productName" placeholder="Contoh: Es Kopi Susu Pandan"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        @error('productName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-semibold text-slate-700">Kategori</label>
                                <button type="button" wire:click="openNewCategoryModal"
                                    class="text-[11px] font-semibold text-orange-600 hover:text-orange-700 hover:underline flex items-center gap-1">
                                    <i class="fa-solid fa-plus text-[9px]"></i> Kategori Baru
                                </button>
                            </div>
                            <select wire:model="productCategoryId"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tag Sub-kategori</label>
                            <input type="text" wire:model="productCategoryTag" placeholder="e.g. Kopi • Ice"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Harga Jual (Rp)</label>
                            <input type="number" wire:model="productBasePrice"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 font-bold">
                            @error('productBasePrice') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">HPP / Biaya Modal (Rp)</label>
                            <input type="number" wire:model="productCostPrice"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">URL Foto Produk</label>
                        <input type="text" wire:model="productImageUrl" placeholder="https://images.unsplash.com/..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
                    </div>

                    <div class="flex items-center gap-4 pt-2">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                            <input type="checkbox" wire:model="productIsAvailable" class="rounded text-orange-500 focus:ring-orange-500">
                            <span>Tersedia untuk dijual</span>
                        </label>
                    </div>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button wire:click="$set('showProductModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Batal</button>
                    <button wire:click="saveProduct"
                        class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm">
                        Simpan Produk
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- 2. Modal Tambah Bahan Baku -->
    @if ($showIngredientModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-base">Tambah Bahan Baku Mentah</h3>
                    <button wire:click="$set('showIngredientModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto text-sm">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Bahan Baku</label>
                        <input type="text" wire:model="ingredientName" placeholder="Contoh: Biji Kopi Arabika Gayo"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Satuan Beli (Pembelian)</label>
                            <input type="text" wire:model="ingredientPurchaseUnit" placeholder="e.g. Kg, Dus, Liter"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Satuan Pakai (Resep)</label>
                            <input type="text" wire:model="ingredientUsageUnit" placeholder="e.g. Gram, ml, Pcs"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Faktor Konversi (1 Beli = ... Pakai)</label>
                            <input type="number" wire:model="ingredientConversionFactor" placeholder="1000"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 font-semibold">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Biaya per Satuan Pakai (Rp)</label>
                            <input type="number" wire:model="ingredientCostPerUsage" placeholder="180"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 font-semibold">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Stok Saldo Awal</label>
                            <input type="number" wire:model="ingredientCurrentStock"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Batas Minimal Peringatan</label>
                            <input type="number" wire:model="ingredientMinStock"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button wire:click="$set('showIngredientModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Batal</button>
                    <button wire:click="saveIngredient"
                        class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm">
                        Simpan Bahan Baku
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- 3. Modal Resep BOM Builder -->
    @if ($showRecipeModal && $selectedRecipeProduct)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-xl w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Resep BOM: {{ $selectedRecipeProduct->name }}</h3>
                        <p class="text-xs text-slate-500">Komposisi bahan per 1 porsi saji & rekalkulasi HPP otomatis</p>
                    </div>
                    <button wire:click="$set('showRecipeModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto text-sm">
                    <!-- Current Ingredients in Recipe -->
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-slate-700">Komposisi Bahan Saat Ini</label>
                        <div class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden bg-slate-50">
                            @forelse($selectedRecipeProduct->recipes as $rc)
                                <div class="p-3 flex items-center justify-between gap-3 text-xs">
                                    <div>
                                        <div class="font-bold text-slate-800">{{ $rc->ingredient->name ?? '-' }}</div>
                                        <div class="text-slate-400">
                                            {{ (int)$rc->quantity }} {{ $rc->ingredient->usage_unit ?? '' }} x Rp {{ number_format($rc->ingredient->cost_per_usage_unit ?? 0, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="font-bold text-slate-900">
                                            Rp {{ number_format($rc->quantity * ($rc->ingredient->cost_per_usage_unit ?? 0), 0, ',', '.') }}
                                        </span>
                                        <button wire:click="removeRecipeItem({{ $rc->id }})" class="text-slate-400 hover:text-rose-600">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-slate-400 text-xs">Belum ada bahan dalam resep ini.</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Add new ingredient row -->
                    <div class="p-3 bg-orange-50/50 rounded-xl border border-orange-100 space-y-3">
                        <label class="block text-xs font-semibold text-orange-900">+ Tambah Bahan ke Resep</label>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="col-span-2">
                                <select wire:model="selectedRecipeIngredientId"
                                    class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-orange-500">
                                    @foreach($ingredients as $ing)
                                        <option value="{{ $ing->id }}">{{ $ing->name }} ({{ $ing->usage_unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <input type="number" wire:model="recipeQuantity" placeholder="Jumlah"
                                    class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-orange-500 font-bold">
                            </div>
                        </div>
                        <button wire:click="addRecipeItem"
                            class="w-full py-1.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-lg shadow-2xs">
                            Tambahkan Bahan
                        </button>
                    </div>

                    <!-- Auto HPP Calculation Result -->
                    <div class="p-3 bg-slate-900 text-white rounded-xl flex items-center justify-between text-xs">
                        <span>Estimasi Total HPP Modal:</span>
                        <span class="text-sm font-bold text-orange-400">Rp {{ number_format($selectedRecipeProduct->cost_price, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                    <button wire:click="$set('showRecipeModal', false)"
                        class="px-4 py-2 text-xs font-semibold bg-slate-800 text-white rounded-xl">Selesai</button>
                </div>
            </div>
        </div>
    @endif

    <!-- 4. Modal Stok Opname -->
    @if ($showOpnameModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-base">Input Stok Opname Fisik</h3>
                    <button wire:click="$set('showOpnameModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="p-5 space-y-4 text-sm">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Bahan Baku</label>
                        <select wire:model.live="opnameIngredientId"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-1 focus:ring-orange-500">
                            @foreach($ingredients as $ing)
                                <option value="{{ $ing->id }}">{{ $ing->name }} (Sistem: {{ (int)$ing->current_stock }} {{ $ing->usage_unit }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Fisik Dihitung Nyata</label>
                        <input type="number" wire:model="opnamePhysicalQty"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-1 focus:ring-orange-500 font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan / Alasan Penyesuaian</label>
                        <input type="text" wire:model="opnameReason"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:ring-1 focus:ring-orange-500">
                    </div>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button wire:click="$set('showOpnameModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Batal</button>
                    <button wire:click="executeOpname"
                        class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm">
                        Sinkronkan Saldo Stok
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- 5. Modal Catat Pengeluaran OPEX -->
    @if ($showExpenseModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-base">Catat Beban Operasional (OPEX)</h3>
                    <button wire:click="$set('showExpenseModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="p-5 space-y-4 text-sm">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori Biaya</label>
                        <select wire:model="expenseCategory"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-1 focus:ring-orange-500">
                            <option value="Listrik & Air">Listrik & Air</option>
                            <option value="Gaji Karyawan">Gaji Karyawan</option>
                            <option value="Sewa Tempat">Sewa Tempat</option>
                            <option value="Marketing">Marketing & Iklan</option>
                            <option value="Pemeliharaan">Pemeliharaan & Servis Alat</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Judul / Deskripsi Pengeluaran</label>
                        <input type="text" wire:model="expenseTitle" placeholder="Contoh: Beli Token Listrik PLN"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-1 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal (Rp)</label>
                        <input type="number" wire:model="expenseAmount"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-1 focus:ring-orange-500 font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Sumber Kas</label>
                        <select wire:model="expensePaymentMethod"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-1 focus:ring-orange-500">
                            <option value="Kas Toko / Tunai">Kas Toko / Tunai</option>
                            <option value="Transfer Bank">Transfer Bank Rekening</option>
                        </select>
                    </div>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button wire:click="$set('showExpenseModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Batal</button>
                    <button wire:click="saveExpense"
                        class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm">
                        Simpan Beban
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- 6. Modal Buat Purchase Order (PO) -->
    @if ($showPoModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-base">Buat Purchase Order (PO) Baru</h3>
                    <button wire:click="$set('showPoModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="p-5 space-y-4 text-sm">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Supplier</label>
                        <select wire:model="poSupplierId"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-1 focus:ring-orange-500">
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Total Nilai Pesanan (Rp)</label>
                        <input type="number" wire:model="poTotalAmount"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-1 focus:ring-orange-500 font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Pengadaan</label>
                        <input type="text" wire:model="poNotes" placeholder="e.g. Restock 10 Kg Espresso Blend"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:ring-1 focus:ring-orange-500">
                    </div>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button wire:click="$set('showPoModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Batal</button>
                    <button wire:click="savePurchaseOrder"
                        class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm">
                        Terbitkan PO
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- 7. Modal Otorisasi Void Pesanan dengan PIN -->
    @if ($showVoidModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-sm w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-rose-600">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <h3 class="font-bold text-slate-900 text-base">Otorisasi Void Order</h3>
                    </div>
                    <button wire:click="$set('showVoidModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="p-5 space-y-4 text-sm">
                    <p class="text-xs text-slate-500">Masukkan 6-digit PIN Manajer / Owner untuk membatalkan transaksi ini.</p>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">6-Digit PIN Manajer</label>
                        <input type="password" maxlength="6" wire:model="voidPin" placeholder="••••••"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-center text-lg tracking-widest font-mono focus:ring-1 focus:ring-rose-500">
                        <div class="text-[10px] text-slate-400 text-center mt-1">Hint Demo PIN: 888888 atau 123456</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan Pembatalan</label>
                        <input type="text" wire:model="voidReason"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:ring-1 focus:ring-rose-500">
                    </div>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button wire:click="$set('showVoidModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Batal</button>
                    <button wire:click="executeVoidOrder"
                        class="px-4 py-2 text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow-sm">
                        Konfirmasi Void
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- 8. Modal Tambah / Edit Kategori Menu -->
    @if ($showCategoryModal)
        <div class="fixed inset-0 z-[60] bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">
                            {{ $editingCategoryId ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Kategori tersimpan langsung ke database & tampil di POS Kasir</p>
                    </div>
                    <button wire:click="$set('showCategoryModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto text-sm">
                    <!-- Nama Kategori -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Nama Kategori <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" wire:model="categoryName" placeholder="Contoh: Signature Coffee, Haircut, Treatment"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        @error('categoryName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Pilih Ikon -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Ikon Cepat</label>
                        <div class="grid grid-cols-6 gap-2 mb-2">
                            @php
                                $presetIcons = [
                                    ['icon' => 'fa-mug-hot', 'label' => 'Kopi'],
                                    ['icon' => 'fa-scissors', 'label' => 'Cukur'],
                                    ['icon' => 'fa-spa', 'label' => 'Spa'],
                                    ['icon' => 'fa-paintbrush', 'label' => 'Coloring'],
                                    ['icon' => 'fa-utensils', 'label' => 'Makanan'],
                                    ['icon' => 'fa-cookie-bite', 'label' => 'Snack'],
                                    ['icon' => 'fa-burger', 'label' => 'Burger'],
                                    ['icon' => 'fa-glass-water', 'label' => 'Minuman'],
                                    ['icon' => 'fa-bottle-water', 'label' => 'Botol'],
                                    ['icon' => 'fa-tag', 'label' => 'Tag Umum'],
                                    ['icon' => 'fa-box', 'label' => 'Paket'],
                                    ['icon' => 'fa-star', 'label' => 'Favorit'],
                                ];
                            @endphp
                            @foreach($presetIcons as $item)
                                <button type="button" wire:click="$set('categoryIcon', '{{ $item['icon'] }}')"
                                    title="{{ $item['label'] }}"
                                    class="h-10 rounded-xl border flex items-center justify-center text-sm transition {{ $categoryIcon === $item['icon'] ? 'bg-orange-50 border-orange-500 text-orange-600 font-bold shadow-xs' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                                    <i class="fa-solid {{ $item['icon'] }}"></i>
                                </button>
                            @endforeach
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-500">FontAwesome:</span>
                            <div class="relative flex-1">
                                <i class="fa-solid {{ $categoryIcon ?: 'fa-tag' }} absolute left-3 top-2.5 text-xs text-orange-500"></i>
                                <input type="text" wire:model="categoryIcon" placeholder="fa-scissors"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-8 pr-3 py-1.5 text-xs font-mono focus:outline-none focus:ring-1 focus:ring-orange-500">
                            </div>
                        </div>
                    </div>

                    <!-- Urutan Tampil & Status -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Urutan Tampil (Sort)</label>
                            <input type="number" wire:model="categorySortOrder" min="0"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 font-medium">
                            @error('categorySortOrder') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Status Kategori</label>
                            <label class="flex items-center gap-2 mt-2.5 cursor-pointer text-xs font-medium text-slate-700">
                                <input type="checkbox" wire:model="categoryIsActive" class="rounded text-orange-500 focus:ring-orange-500">
                                <span>Aktif di POS & Menu</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2">
                    @if ($editingCategoryId)
                        <button type="button"
                            wire:click="deleteCategory({{ $editingCategoryId }})"
                            wire:confirm="Yakin ingin menghapus kategori ini? Produk dalam kategori ini tidak akan terhapus, hanya status kategorinya menjadi kosong."
                            class="px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition flex items-center gap-1.5">
                            <i class="fa-solid fa-trash-can"></i>
                            <span>Hapus</span>
                        </button>
                    @else
                        <div></div>
                    @endif

                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="$set('showCategoryModal', false)"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">
                            Batal
                        </button>
                        <button type="button" wire:click="saveCategory"
                            class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm flex items-center gap-1.5">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ $editingCategoryId ? 'Simpan Perubahan' : 'Tambah Kategori' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- 9. Modal Kelola Semua Kategori Menu -->
    @if ($showManageCategoriesModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-2xl w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Kelola Semua Kategori Menu</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar kategori produk tenant Anda. Kategori baru akan langsung tampil di Master Produk & POS Kasir.</p>
                    </div>
                    <button wire:click="$set('showManageCategoriesModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 max-h-[70vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-xs font-semibold text-slate-500">Total: {{ $categories->count() }} Kategori</div>
                        <button type="button" wire:click="openNewCategoryModal"
                            class="px-3 py-1.5 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm flex items-center gap-1.5">
                            <i class="fa-solid fa-plus text-[10px]"></i>
                            <span>Tambah Kategori Baru</span>
                        </button>
                    </div>

                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="py-2.5 px-3">Urutan</th>
                                    <th class="py-2.5 px-3">Kategori</th>
                                    <th class="py-2.5 px-3">Slug</th>
                                    <th class="py-2.5 px-3 text-center">Jumlah Menu</th>
                                    <th class="py-2.5 px-3 text-center">Status</th>
                                    <th class="py-2.5 px-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($categories as $cat)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-3 font-mono text-slate-400">#{{ $cat->sort_order }}</td>
                                        <td class="py-3 px-3">
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center shrink-0">
                                                    <i class="fa-solid {{ $cat->icon ?: 'fa-tag' }}"></i>
                                                </div>
                                                <span class="font-bold text-slate-900">{{ $cat->name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-3 font-mono text-slate-400 text-[11px]">{{ $cat->slug }}</td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="px-2 py-0.5 rounded-full bg-slate-100 font-semibold text-slate-700">
                                                {{ $cat->products_count ?? 0 }} Produk
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="px-2 py-0.5 rounded-full font-semibold {{ $cat->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                                                {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-right space-x-1">
                                            <button type="button" wire:click="openEditCategoryModal({{ $cat->id }})"
                                                class="px-2.5 py-1 text-slate-600 hover:text-orange-600 hover:bg-orange-50 rounded-lg font-semibold transition"
                                                title="Edit Kategori">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </button>
                                            <button type="button" wire:click="deleteCategory({{ $cat->id }})"
                                                wire:confirm="Hapus kategori '{{ $cat->name }}'? Produk di dalamnya tidak akan terhapus."
                                                class="px-2.5 py-1 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg font-semibold transition"
                                                title="Hapus Kategori">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-6 text-center text-slate-400">
                                            Belum ada kategori yang dibuat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                    <button type="button" wire:click="$set('showManageCategoriesModal', false)"
                        class="px-4 py-2 text-xs font-semibold bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- 10. Modal Tambah / Edit Layanan Salon & Barbershop -->
    @if ($showServiceModal)
        <div class="fixed inset-0 z-[60] bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">
                            {{ $editingServiceId ? 'Edit Layanan Jasa' : 'Tambah Layanan Jasa Baru' }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Konfigurasi tarif layanan, durasi waktu sesi, dan komisi kapster / stylist</p>
                    </div>
                    <button wire:click="$set('showServiceModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto text-sm">
                    <!-- Nama Layanan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Layanan Jasa <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="serviceName" placeholder="Contoh: Gentleman Haircut, Creambath Spa, Beard Trim"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        @error('serviceName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Kategori & Tag Sub -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-semibold text-slate-700">Kategori Layanan</label>
                                <button type="button" wire:click="openNewCategoryModal"
                                    class="text-[11px] font-semibold text-orange-600 hover:text-orange-700 hover:underline flex items-center gap-1">
                                    <i class="fa-solid fa-plus text-[9px]"></i> Kategori Baru
                                </button>
                            </div>
                            <select wire:model="serviceCategoryId"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tag Sub-kategori</label>
                            <input type="text" wire:model="serviceCategoryTag" placeholder="e.g. Haircut • Pria"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                    </div>

                    <!-- Tarif Jasa & Durasi -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tarif Layanan (Rp) <span class="text-rose-500">*</span></label>
                            <input type="number" wire:model="servicePrice"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm font-bold text-slate-900 focus:outline-none focus:ring-1 focus:ring-orange-500">
                            @error('servicePrice') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Estimasi Durasi (Menit) <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input type="number" wire:model="serviceDuration" min="5" step="5" placeholder="30"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-3.5 pr-12 py-2 text-sm font-semibold focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-medium">Menit</span>
                            </div>
                            @error('serviceDuration') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Skema Komisi Kapster -->
                    <div class="p-3.5 bg-indigo-50/60 border border-indigo-100 rounded-xl space-y-2.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-indigo-900 flex items-center gap-1.5">
                                <i class="fa-solid fa-hand-holding-dollar text-indigo-600"></i>
                                <span>Skema Komisi Kapster / Stylist</span>
                            </label>
                            <div class="flex items-center gap-3 text-xs">
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" value="fixed" wire:model.live="serviceCommissionType" class="text-indigo-600 focus:ring-indigo-500">
                                    <span class="font-medium text-slate-700">Nominal Tetap (Rp)</span>
                                </label>
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" value="percentage" wire:model.live="serviceCommissionType" class="text-indigo-600 focus:ring-indigo-500">
                                    <span class="font-medium text-slate-700">Persentase (%)</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <div>
                                <label class="block text-[11px] font-semibold text-indigo-900 mb-1">
                                    {{ $serviceCommissionType === 'percentage' ? 'Persentase Komisi (%)' : 'Nilai Komisi per Sesi (Rp)' }}
                                </label>
                                <input type="number" wire:model.live="serviceCommissionValue"
                                    class="w-full bg-white border border-indigo-200 rounded-lg px-3 py-1.5 text-xs font-bold text-indigo-700 focus:ring-1 focus:ring-indigo-500">
                                @error('serviceCommissionValue') <span class="text-[11px] text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-indigo-900 mb-1">Estimasi Komisi Diterima Staf</label>
                                <div class="bg-white border border-indigo-200 rounded-lg px-3 py-1.5 text-xs font-bold text-emerald-600">
                                    @php
                                        $estCom = $serviceCommissionType === 'percentage' 
                                            ? round(($servicePrice * $serviceCommissionValue) / 100) 
                                            : $serviceCommissionValue;
                                    @endphp
                                    Rp {{ number_format(max(0, $estCom), 0, ',', '.') }} / sesi
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Bahan / HPP & URL Foto -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Modal Bahan Produk (Rp)</label>
                            <input type="number" wire:model="serviceCostPrice" placeholder="e.g. 5000 (shampoo/oil)"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <span class="text-[10px] text-slate-400">Pemakaian shampoo, pomade, cream</span>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">URL Foto Layanan</label>
                            <input type="text" wire:model="serviceImageUrl" placeholder="https://images.unsplash..."
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                            <input type="checkbox" wire:model="serviceIsAvailable" class="rounded text-orange-500 focus:ring-orange-500">
                            <span>Layanan ini tersedia & aktif di POS Kasir</span>
                        </label>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2">
                    @if ($editingServiceId)
                        <button type="button"
                            wire:click="deleteService({{ $editingServiceId }})"
                            wire:confirm="Yakin ingin menghapus layanan ini?"
                            class="px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition flex items-center gap-1.5">
                            <i class="fa-solid fa-trash-can"></i>
                            <span>Hapus</span>
                        </button>
                    @else
                        <div></div>
                    @endif

                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="$set('showServiceModal', false)"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Batal</button>
                        <button type="button" wire:click="saveService"
                            class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm flex items-center gap-1.5">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ $editingServiceId ? 'Simpan Perubahan' : 'Tambah Layanan' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- 11. Modal Tambah / Edit Diskon Lengkap (% dan Rp) -->
    @if ($showDiscountModal)
        <div class="fixed inset-0 z-[60] bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">
                            {{ $editingDiscountId ? 'Edit Program Diskon / Promo' : 'Tambah Program Diskon Baru' }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Diskon dapat berupa persentase (%) atau potongan uang langsung (Rp)</p>
                    </div>
                    <button wire:click="$set('showDiscountModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto text-sm">
                    <!-- Nama Diskon & Kode Kupon -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Promo / Diskon <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="discountName" placeholder="Contoh: Diskon Pelajar & Mahasiswa 15%, Potongan Cukur 15K"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                            @error('discountName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Kupon / Voucher (Opsional)</label>
                            <input type="text" wire:model="discountCode" placeholder="Contoh: PELAJAR15, CUKUR15K"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm font-mono uppercase focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <span class="text-[10px] text-slate-400">Kosongkan jika promo otomatis tanpa kode</span>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Berlaku Untuk</label>
                            <select wire:model="discountApplicableTo"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <option value="all">Semua Menu & Layanan</option>
                                <option value="services">Khusus Layanan Jasa</option>
                                <option value="products">Khusus Produk Retail</option>
                            </select>
                        </div>
                    </div>

                    <!-- Pilihan Tipe Diskon: Persentase (%) vs Nominal Uang (Rp) -->
                    <div class="p-4 rounded-xl border border-orange-200 bg-orange-50/50 space-y-3">
                        <label class="block text-xs font-bold text-slate-800">Skema & Tipe Potongan Harga <span class="text-rose-500">*</span></label>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer transition {{ $discountType === 'percentage' ? 'bg-white border-orange-500 shadow-xs' : 'bg-slate-50 border-slate-200' }}">
                                <input type="radio" value="percentage" wire:model.live="discountType" class="text-orange-500 focus:ring-orange-500">
                                <div>
                                    <div class="text-xs font-bold text-slate-900">Persentase (%)</div>
                                    <div class="text-[10px] text-slate-500">Misal: 10%, 20%, 50%</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer transition {{ $discountType === 'fixed' ? 'bg-white border-orange-500 shadow-xs' : 'bg-slate-50 border-slate-200' }}">
                                <input type="radio" value="fixed" wire:model.live="discountType" class="text-orange-500 focus:ring-orange-500">
                                <div>
                                    <div class="text-xs font-bold text-slate-900">Nominal Langsung (Rp)</div>
                                    <div class="text-[10px] text-slate-500">Misal: Potongan Rp 15.000</div>
                                </div>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    {{ $discountType === 'percentage' ? 'Besar Diskon (%)' : 'Besar Potongan (Rp)' }} <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" wire:model="discountValue" min="1"
                                        class="w-full bg-white border border-slate-300 rounded-xl pl-3.5 pr-10 py-2 text-sm font-bold text-orange-600 focus:ring-1 focus:ring-orange-500">
                                    <span class="absolute right-3 top-2.5 text-xs font-bold text-slate-400">
                                        {{ $discountType === 'percentage' ? '%' : 'Rp' }}
                                    </span>
                                </div>
                                @error('discountValue') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            @if($discountType === 'percentage')
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Maksimal Potongan (Rp)</label>
                                    <input type="number" wire:model="discountMaxAmount" placeholder="e.g. 25000"
                                        class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2 text-sm font-semibold focus:ring-1 focus:ring-orange-500">
                                    <span class="text-[10px] text-slate-400">Batas maksimal nominal diskon</span>
                                </div>
                            @else
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan Nilai</label>
                                    <div class="py-2 text-xs text-slate-500">
                                        Total belanja dipotong langsung Rp {{ number_format($discountValue ?: 0, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Syarat & Kuota -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Syarat Min. Belanja (Rp)</label>
                            <input type="number" wire:model="discountMinPurchase" placeholder="0"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <span class="text-[10px] text-slate-400">Isi 0 jika tanpa syarat minimum</span>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Batas Kuota Penggunaan</label>
                            <input type="number" wire:model="discountMaxUsage" placeholder="e.g. 100"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <span class="text-[10px] text-slate-400">Kosongkan jika kuota tak terbatas</span>
                        </div>
                    </div>

                    <!-- Periode Promo -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai Berlaku</label>
                            <input type="date" wire:model="discountStartDate"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Berakhir</label>
                            <input type="date" wire:model="discountEndDate"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                    </div>

                    <!-- Catatan / S&K -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan / Syarat & Ketentuan</label>
                        <input type="text" wire:model="discountNotes" placeholder="Contoh: Khusus hari Senin - Kamis, berlaku untuk member"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                    </div>

                    <div class="flex items-center gap-4 pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                            <input type="checkbox" wire:model="discountIsActive" class="rounded text-orange-500 focus:ring-orange-500">
                            <span>Promo ini aktif dan siap digunakan saat transaksi kasir</span>
                        </label>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2">
                    @if ($editingDiscountId)
                        <button type="button"
                            wire:click="deleteDiscount({{ $editingDiscountId }})"
                            wire:confirm="Yakin ingin menghapus diskon ini?"
                            class="px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition flex items-center gap-1.5">
                            <i class="fa-solid fa-trash-can"></i>
                            <span>Hapus</span>
                        </button>
                    @else
                        <div></div>
                    @endif

                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="$set('showDiscountModal', false)"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Batal</button>
                        <button type="button" wire:click="saveDiscount"
                            class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm flex items-center gap-1.5">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ $editingDiscountId ? 'Simpan Perubahan' : 'Buat Diskon' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

