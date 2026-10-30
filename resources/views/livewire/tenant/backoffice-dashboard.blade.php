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
                            <span>Cabang, Staf & Shift</span>
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
                    <!-- Super Admin Only: Switcher to inspect any tenant -->
                    @if(auth()->check() && auth()->user()->isSuperAdmin() && isset($allTenants) && $allTenants->count() > 1)
                        <div class="flex items-center gap-1.5 bg-amber-50 border border-amber-300 rounded-xl px-2.5 py-1 text-xs shadow-2xs" title="Mode Super Admin: Simulasi Tampilan Tenant">
                            <i class="fa-solid fa-user-shield text-amber-600 text-xs"></i>
                            <span class="text-[10px] uppercase font-bold text-amber-800 tracking-wider hidden md:inline">Super Admin View:</span>
                            <select wire:change="switchTenant($event.target.value)"
                                class="bg-transparent text-xs font-bold text-amber-900 focus:outline-none cursor-pointer">
                                @foreach($allTenants as $t)
                                    <option value="{{ $t->id }}" {{ $tenantId == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }} ({{ $t->businessType?->name ?? 'Bisnis' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <!-- Owner Tenant: Jenis Usaha Badge (Static Pill, Bukan Dropdown) -->
                        <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-50 text-slate-700 border border-slate-200 shadow-2xs">
                            <i class="fa-solid {{ $tenant?->businessType?->icon ?: 'fa-store' }} text-orange-500"></i>
                            <span>{{ $tenant?->businessType?->name ?? 'Bisnis' }}</span>
                        </div>
                    @endif

                    <!-- Outlet Switcher (Cabang Toko Milik Tenant) -->
                    @if($outlets->count() > 1)
                        <div class="relative">
                            <select wire:change="switchOutlet($event.target.value)"
                                class="bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 py-1.5 px-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500 cursor-pointer shadow-2xs">
                                @foreach($outlets as $out)
                                    <option value="{{ $out->id }}" {{ $selectedOutletId == $out->id ? 'selected' : '' }}>
                                        📍 {{ $out->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="text-xs font-medium text-slate-700 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl flex items-center gap-1.5 shadow-2xs">
                            <i class="fa-solid fa-location-dot text-orange-500"></i>
                            <span>{{ $selectedOutlet->name ?? 'Outlet Utama' }}</span>
                        </div>
                    @endif

                    <!-- Switch to POS Kasir Quick Button -->
                    <a href="{{ route('pos.terminal') }}"
                        class="hidden sm:flex items-center gap-2 text-xs font-semibold px-3 py-1.5 bg-orange-50 text-orange-600 border border-orange-200 rounded-xl hover:bg-orange-100 transition shadow-2xs">
                        <i class="fa-solid fa-desktop"></i>
                        <span>Layar POS Kasir</span>
                    </a>

                    <!-- Switch to Super Admin (if user is superadmin) -->
                    @if(auth()->check() && auth()->user()->isSuperAdmin())
                        <a href="{{ route('superadmin.dashboard') }}"
                            class="text-xs font-semibold px-3 py-1.5 bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition shadow-2xs">
                            <i class="fa-solid fa-arrow-left mr-1"></i> Super Admin
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
                                <!-- Filter Semua Kategori -->
                                <button wire:click="$set('selectedServiceCategoryFilter', 'all')"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition shrink-0 {{ $selectedServiceCategoryFilter === 'all' ? 'bg-orange-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                    Semua Kategori ({{ $services->count() }})
                                </button>

                                <!-- Pill Kategori + Tombol Edit Langsung -->
                                @foreach($serviceCategories ?? $categories as $cat)
                                    <div class="inline-flex items-center rounded-lg border transition shrink-0 {{ $selectedServiceCategoryFilter == $cat->id ? 'bg-orange-500 border-orange-500 text-white shadow-sm' : 'bg-slate-100 border-transparent text-slate-600 hover:bg-slate-200' }}">
                                        <button wire:click="$set('selectedServiceCategoryFilter', {{ $cat->id }})"
                                            class="px-3 py-1.5 text-xs font-semibold whitespace-nowrap flex items-center gap-1.5">
                                            @if($cat->icon)
                                                <i class="fa-solid {{ $cat->icon }} text-[10px] {{ $selectedServiceCategoryFilter == $cat->id ? 'text-white' : 'text-slate-400' }}"></i>
                                            @endif
                                            <span>{{ $cat->name }} ({{ $cat->products_count }})</span>
                                        </button>
                                        <button wire:click="openEditCategoryModal({{ $cat->id }})"
                                            title="Edit Kategori: {{ $cat->name }}"
                                            class="pr-2 pl-0.5 py-1.5 text-[10px] {{ $selectedServiceCategoryFilter == $cat->id ? 'text-orange-200 hover:text-white' : 'text-slate-400 hover:text-slate-700' }} transition">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    </div>
                                @endforeach

                                <!-- Tombol Tambah Kategori Langsung di Samping Kategori -->
                                <button wire:click="openNewCategoryModal"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-dashed border-orange-400 bg-orange-50 text-orange-600 hover:bg-orange-100 hover:border-orange-500 transition flex items-center gap-1.5 whitespace-nowrap shrink-0 shadow-xs cursor-pointer"
                                    title="Tambah Kategori Layanan Baru">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                    <span>Tambah Kategori</span>
                                </button>

                                <!-- Tombol Kelola Semua Kategori -->
                                <button wire:click="openManageCategoriesModal"
                                    class="px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-800 transition flex items-center gap-1.5 whitespace-nowrap shrink-0 cursor-pointer"
                                    title="Kelola & Atur Urutan Kategori">
                                    <i class="fa-solid fa-sliders text-[11px] text-slate-500"></i>
                                    <span class="hidden sm:inline">Kelola Kategori</span>
                                </button>
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
                        <!-- Top Header & Primary Action Controls -->
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div>
                                <div class="flex items-center gap-2.5">
                                    <h1 class="text-xl font-bold text-slate-900">Pembelian & Supplier (Procurement)</h1>
                                    <span class="text-[10px] font-bold bg-orange-50 text-orange-600 border border-orange-200 px-2 py-0.5 rounded-full uppercase tracking-wider">
                                        Rantai Pasok & PO
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">Kelola Purchase Order (PO), penerimaan barang (GRN), syarat tempo pembayaran, dan kontak rekanan pemasok.</p>
                            </div>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <button type="button" wire:click="openNewSupplierModal"
                                    class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center gap-2 cursor-pointer shadow-xs">
                                    <i class="fa-solid fa-user-plus text-slate-500"></i>
                                    <span>Tambah Supplier</span>
                                </button>
                                <button type="button" wire:click="openPoModal()"
                                    class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition cursor-pointer">
                                    <i class="fa-solid fa-cart-plus"></i>
                                    <span>Buat Purchase Order (PO)</span>
                                </button>
                            </div>
                        </div>

                        <!-- 4 Stat Summary Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 text-xs font-medium">Total Pengadaan (PO)</span>
                                    <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-file-invoice-dollar"></i>
                                    </span>
                                </div>
                                <div class="text-2xl font-bold text-slate-900 mt-2">
                                    Rp {{ number_format($totalPoAmount, 0, ',', '.') }}
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $totalPoCount }} PO terdaftar di sistem</div>
                            </div>

                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 text-xs font-medium">Menunggu Pengiriman</span>
                                    <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-truck-fast"></i>
                                    </span>
                                </div>
                                <div class="text-2xl font-bold text-amber-600 mt-2">{{ $orderedPoCount }} PO</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Sedang diproses pemasok</div>
                            </div>

                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 text-xs font-medium">Selesai Diterima (GRN)</span>
                                    <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-boxes-packing"></i>
                                    </span>
                                </div>
                                <div class="text-2xl font-bold text-emerald-600 mt-2">{{ $receivedPoCount }} PO</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Stok telah masuk gudang</div>
                            </div>

                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 text-xs font-medium">Rekanan Supplier</span>
                                    <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-handshake"></i>
                                    </span>
                                </div>
                                <div class="text-2xl font-bold text-indigo-600 mt-2">{{ $suppliers->count() }} Pemasok</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $suppliers->where('is_active', true)->count() }} rekanan aktif</div>
                            </div>
                        </div>

                        <!-- ================= SECTION 1: DAFTAR PURCHASE ORDER ================= -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <!-- Card Header & Filter Bar -->
                            <div class="p-5 border-b border-slate-100 space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                                            <i class="fa-solid fa-clipboard-list text-orange-500"></i>
                                            <span>Daftar Purchase Order</span>
                                        </h3>
                                        <p class="text-xs text-slate-500 mt-0.5">Pantau status pemesanan ke pemasok dan konfirmasi penerimaan barang (GRN)</p>
                                    </div>
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 self-start sm:self-auto">
                                        {{ $purchaseOrders->count() }} PO ditemukan
                                    </span>
                                </div>

                                <!-- Filter Controls Grid -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-1">
                                    <!-- 1. Search Bar -->
                                    <div class="relative">
                                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                                        <input type="text" wire:model.live.debounce.300ms="searchPo"
                                            placeholder="Cari No. PO, supplier, catatan..."
                                            class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
                                    </div>

                                    <!-- 2. Status Filter -->
                                    <div>
                                        <select wire:model.live="poStatusFilter"
                                            class="w-full bg-slate-50 border border-slate-200 text-xs font-medium text-slate-700 py-1.5 px-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500 cursor-pointer">
                                            <option value="all">Semua Status PO</option>
                                            <option value="ordered">Dipesan (Dalam Pengiriman)</option>
                                            <option value="received">Diterima (GRN Selesai)</option>
                                            <option value="cancelled">Dibatalkan</option>
                                        </select>
                                    </div>

                                    <!-- 3. Supplier Filter -->
                                    <div>
                                        <select wire:model.live="poSupplierFilter"
                                            class="w-full bg-slate-50 border border-slate-200 text-xs font-medium text-slate-700 py-1.5 px-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500 cursor-pointer">
                                            <option value="all">Semua Supplier Rekanan</option>
                                            @foreach($allSuppliersList as $sOption)
                                                <option value="{{ $sOption->id }}">{{ $sOption->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- 4. Date Range Filter -->
                                    <div>
                                        <select wire:model.live="poDateFilter"
                                            class="w-full bg-slate-50 border border-slate-200 text-xs font-medium text-slate-700 py-1.5 px-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500 cursor-pointer">
                                            <option value="all">Semua Waktu Order</option>
                                            <option value="today">Hari Ini</option>
                                            <option value="7_days">7 Hari Terakhir</option>
                                            <option value="30_days">30 Hari Terakhir</option>
                                            <option value="this_month">Bulan Ini</option>
                                            <option value="custom">Rentang Tanggal Khusus</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Custom Date Range Inputs (Shown when poDateFilter === 'custom') -->
                                @if($poDateFilter === 'custom')
                                    <div class="flex items-center gap-3 pt-2 bg-slate-50/80 p-3 rounded-xl border border-slate-200/70">
                                        <span class="text-xs font-semibold text-slate-600">Dari:</span>
                                        <input type="date" wire:model.live="poStartDate"
                                            class="bg-white border border-slate-200 rounded-lg px-2.5 py-1 text-xs focus:ring-1 focus:ring-orange-500">
                                        <span class="text-xs font-semibold text-slate-600">Sampai:</span>
                                        <input type="date" wire:model.live="poEndDate"
                                            class="bg-white border border-slate-200 rounded-lg px-2.5 py-1 text-xs focus:ring-1 focus:ring-orange-500">
                                        <button type="button" wire:click="resetPoFilters"
                                            class="text-xs text-orange-600 hover:text-orange-700 font-semibold ml-auto hover:underline cursor-pointer">
                                            Reset Filter
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <!-- PO Table Content -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 border-b border-slate-200/80 text-xs text-slate-500 uppercase font-semibold">
                                        <tr>
                                            <th class="py-3 px-4">No. PO</th>
                                            <th class="py-3 px-4">Supplier Pemasok</th>
                                            <th class="py-3 px-4">Tanggal Order</th>
                                            <th class="py-3 px-4">Estimasi Tiba</th>
                                            <th class="py-3 px-4 text-right">Total Tagihan</th>
                                            <th class="py-3 px-4 text-center">Status PO</th>
                                            <th class="py-3 px-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($purchaseOrders as $po)
                                            <tr class="hover:bg-slate-50/60 transition">
                                                <td class="py-3 px-4">
                                                    <button type="button" wire:click="viewPoDetail({{ $po->id }})"
                                                        class="font-mono font-bold text-orange-600 hover:text-orange-700 hover:underline cursor-pointer flex items-center gap-1.5">
                                                        <i class="fa-solid fa-file-invoice text-slate-400 text-xs"></i>
                                                        <span>{{ $po->po_number }}</span>
                                                    </button>
                                                    @if($po->notes)
                                                        <div class="text-[11px] text-slate-400 mt-0.5 truncate max-w-[200px]" title="{{ $po->notes }}">
                                                            {{ $po->notes }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4">
                                                    <div class="font-semibold text-slate-900">{{ $po->supplier->name ?? 'Pemasok Terhapus' }}</div>
                                                    <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                                        @if($po->supplier?->contact_person)
                                                            <span><i class="fa-regular fa-user mr-1 text-slate-400"></i>{{ $po->supplier->contact_person }}</span>
                                                        @endif
                                                        @if($po->supplier?->phone)
                                                            <span><i class="fa-solid fa-phone mr-1 text-slate-400"></i>{{ $po->supplier->phone }}</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="py-3 px-4 text-xs text-slate-600 whitespace-nowrap">
                                                    <div class="font-medium text-slate-800">{{ $po->order_date ? $po->order_date->format('d M Y') : '-' }}</div>
                                                    <div class="text-[10px] text-slate-400">{{ $po->order_date ? $po->order_date->diffForHumans() : '' }}</div>
                                                </td>
                                                <td class="py-3 px-4 text-xs text-slate-600 whitespace-nowrap">
                                                    @if($po->expected_date)
                                                        <span class="font-medium text-slate-700">{{ $po->expected_date->format('d M Y') }}</span>
                                                    @else
                                                        <span class="text-slate-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4 text-right font-bold text-slate-900 whitespace-nowrap">
                                                    Rp {{ number_format($po->total_amount, 0, ',', '.') }}
                                                </td>
                                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                                    @if($po->status === 'received')
                                                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Diterima (GRN)
                                                        </span>
                                                    @elseif($po->status === 'cancelled')
                                                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Dibatalkan
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Dipesan
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                                    <div class="flex items-center justify-end gap-1.5">
                                                        <button type="button" wire:click="viewPoDetail({{ $po->id }})"
                                                            title="Lihat Detail Purchase Order"
                                                            class="px-2.5 py-1 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition cursor-pointer">
                                                            Detail
                                                        </button>

                                                        @if($po->status === 'ordered')
                                                            <button type="button" wire:click="markPoReceived({{ $po->id }})"
                                                                title="Konfirmasi Barang Diterima (GRN)"
                                                                class="px-2.5 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 rounded-lg transition cursor-pointer flex items-center gap-1">
                                                                <i class="fa-solid fa-box-open text-[10px]"></i>
                                                                <span>Terima (GRN)</span>
                                                            </button>

                                                            <button type="button" wire:click="cancelPo({{ $po->id }})"
                                                                wire:confirm="Yakin ingin membatalkan Purchase Order ini?"
                                                                title="Batalkan PO"
                                                                class="p-1 text-xs text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition cursor-pointer">
                                                                <i class="fa-solid fa-ban"></i>
                                                            </button>
                                                        @elseif($po->status === 'received')
                                                            <span class="text-xs text-emerald-600 font-medium px-2 py-1 flex items-center gap-1">
                                                                <i class="fa-solid fa-circle-check"></i> Selesai
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                                        <i class="fa-solid fa-receipt text-lg"></i>
                                                    </div>
                                                    <p class="font-semibold text-slate-700">Belum ada purchase order ditemukan.</p>
                                                    <p class="text-xs text-slate-400 mt-0.5">Sesuaikan filter pencarian atau buat PO baru untuk memesan ke supplier.</p>
                                                    <button type="button" wire:click="openPoModal()"
                                                        class="mt-3.5 px-3.5 py-1.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-xs inline-flex items-center gap-1.5 transition cursor-pointer">
                                                        <i class="fa-solid fa-plus text-[10px]"></i>
                                                        <span>Buat Purchase Order Baru</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ================= SECTION 2: MASTER REKANAN SUPPLIER ================= -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                                <div>
                                    <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                                        <i class="fa-solid fa-handshake text-indigo-500"></i>
                                        <span>Master Rekanan Supplier</span>
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-0.5">Kelola daftar kontak pemasok, syarat tempo pembayaran, dan riwayat pesanan.</p>
                                </div>
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    @if($suppliers->isEmpty())
                                        <button type="button" wire:click="seedDefaultSuppliers"
                                            class="px-3.5 py-2 text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 rounded-xl transition flex items-center gap-1.5 cursor-pointer shadow-xs">
                                            <i class="fa-solid fa-wand-magic-sparkles text-indigo-500"></i>
                                            <span>Gunakan Rekomendasi Pemasok</span>
                                        </button>
                                    @endif
                                    <button type="button" wire:click="openNewSupplierModal"
                                        class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition cursor-pointer">
                                        <i class="fa-solid fa-plus"></i>
                                        <span>Tambah Supplier Baru</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Search & Filter Controls for Suppliers -->
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                                <div class="relative w-full sm:w-72">
                                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                                    <input type="text" wire:model.live.debounce.300ms="searchSupplier"
                                        placeholder="Cari nama supplier, PIC, telepon..."
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
                                </div>
                                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                                    <select wire:model.live="supplierStatusFilter"
                                        class="bg-slate-50 border border-slate-200 text-xs font-medium text-slate-700 py-1.5 px-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500 cursor-pointer">
                                        <option value="all">Semua Status ({{ $suppliers->count() }})</option>
                                        <option value="active">Aktif Bekerjasama</option>
                                        <option value="inactive">Nonaktif</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Suppliers Cards Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @forelse($suppliers as $sup)
                                    <div class="p-5 rounded-2xl border {{ $sup->is_active ? 'border-slate-200/80 bg-white' : 'border-slate-200 bg-slate-50/60 opacity-80' }} hover:shadow-md transition flex flex-col justify-between">
                                        <div>
                                            <div class="flex items-start justify-between gap-2">
                                                <div>
                                                    <h4 class="font-bold text-slate-900 text-sm leading-snug">{{ $sup->name }}</h4>
                                                    <span class="text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                                        <i class="fa-solid fa-receipt text-[10px]"></i>
                                                        <span>{{ $sup->purchase_orders_count ?? 0 }} PO Terbit</span>
                                                    </span>
                                                </div>

                                                <!-- Toggle Active Status -->
                                                <button type="button" wire:click="toggleSupplierStatus({{ $sup->id }})"
                                                    title="Klik untuk ubah status aktif/nonaktif"
                                                    class="cursor-pointer shrink-0">
                                                    @if($sup->is_active)
                                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center gap-1 hover:bg-emerald-100">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-500 border border-slate-200 flex items-center gap-1 hover:bg-slate-200">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Nonaktif
                                                        </span>
                                                    @endif
                                                </button>
                                            </div>

                                            <div class="mt-3.5 space-y-2 text-xs text-slate-600">
                                                @if($sup->contact_person)
                                                    <div class="flex items-center gap-2">
                                                        <i class="fa-solid fa-user-tie text-slate-400 w-4 text-center"></i>
                                                        <span class="font-medium text-slate-800">{{ $sup->contact_person }}</span>
                                                    </div>
                                                @endif

                                                @if($sup->phone)
                                                    <div class="flex items-center gap-2">
                                                        <i class="fa-solid fa-phone text-slate-400 w-4 text-center"></i>
                                                        <a href="tel:{{ $sup->phone }}" class="text-slate-700 hover:text-orange-600 hover:underline">{{ $sup->phone }}</a>
                                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sup->phone) }}" target="_blank"
                                                            class="text-emerald-600 hover:text-emerald-700 ml-1 text-[11px]" title="Chat via WhatsApp">
                                                            <i class="fa-brands fa-whatsapp"></i>
                                                        </a>
                                                    </div>
                                                @endif

                                                @if($sup->email)
                                                    <div class="flex items-center gap-2">
                                                        <i class="fa-solid fa-envelope text-slate-400 w-4 text-center"></i>
                                                        <a href="mailto:{{ $sup->email }}" class="text-slate-700 hover:text-orange-600 hover:underline truncate">{{ $sup->email }}</a>
                                                    </div>
                                                @endif

                                                @if($sup->address)
                                                    <div class="flex items-start gap-2 pt-0.5">
                                                        <i class="fa-solid fa-location-dot text-slate-400 w-4 text-center mt-0.5 shrink-0"></i>
                                                        <span class="text-slate-500 text-[11px] leading-relaxed line-clamp-2" title="{{ $sup->address }}">{{ $sup->address }}</span>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="mt-3.5 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                                                <span class="text-[11px] text-slate-400">Tempo Pembayaran:</span>
                                                <span class="px-2 py-0.5 rounded-lg text-[11px] font-semibold {{ $sup->terms_days == 0 ? 'bg-amber-50 text-amber-700 border border-amber-200/60' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                                    <i class="fa-regular fa-clock mr-1 text-[10px]"></i>
                                                    {{ $sup->terms_days == 0 ? 'COD / Tunai' : 'Tempo ' . $sup->terms_days . ' Hari' }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Card Action Buttons -->
                                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                                            <button type="button" wire:click="openPoModal({{ $sup->id }})"
                                                class="px-2.5 py-1.5 text-xs font-semibold bg-orange-50 text-orange-600 hover:bg-orange-100 border border-orange-200 rounded-lg transition flex items-center gap-1.5 cursor-pointer shadow-2xs">
                                                <i class="fa-solid fa-cart-plus text-[10px]"></i>
                                                <span>Buat PO</span>
                                            </button>

                                            <div class="flex items-center gap-1">
                                                <button type="button" wire:click="editSupplier({{ $sup->id }})"
                                                    title="Edit Data Supplier"
                                                    class="p-1.5 text-xs text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition cursor-pointer">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button type="button" wire:click="deleteSupplier({{ $sup->id }})"
                                                    wire:confirm="Yakin ingin menghapus supplier ini?"
                                                    title="Hapus Supplier"
                                                    class="p-1.5 text-xs text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition cursor-pointer">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-1 md:col-span-3 py-10 text-center text-slate-400 text-sm bg-slate-50/80 rounded-2xl border border-dashed border-slate-200">
                                        <div class="w-12 h-12 rounded-full bg-white text-slate-400 flex items-center justify-center mx-auto mb-3 shadow-xs">
                                            <i class="fa-solid fa-truck-ramp-box text-lg text-slate-400"></i>
                                        </div>
                                        <h4 class="font-bold text-slate-800 text-sm">Belum Ada Rekanan Supplier</h4>
                                        <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">Daftarkan supplier bahan baku, produk retail, atau gunakan preset standar untuk mulai membuat Purchase Order.</p>
                                        <div class="mt-4 flex items-center justify-center gap-3">
                                            <button type="button" wire:click="openNewSupplierModal"
                                                class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-xs transition cursor-pointer inline-flex items-center gap-1.5">
                                                <i class="fa-solid fa-plus text-[10px]"></i>
                                                <span>Tambah Supplier Baru</span>
                                            </button>
                                            <button type="button" wire:click="seedDefaultSuppliers"
                                                class="px-3.5 py-2 text-xs font-semibold bg-white text-slate-700 hover:bg-slate-100 border border-slate-200 rounded-xl transition cursor-pointer inline-flex items-center gap-1.5">
                                                <i class="fa-solid fa-wand-magic-sparkles text-indigo-500"></i>
                                                <span>Rekomendasi Pemasok Standar</span>
                                            </button>
                                        </div>
                                    </div>
                                @endforelse
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

                <!-- ==================== TAB 9: PENGATURAN CABANG OUTLET & MANAJEMEN PEGAWAI ==================== -->
                @if ($activeTab === 'settings')
                    <div class="space-y-6">
                        <!-- Top Header & Actions -->
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div>
                                <h1 class="text-xl font-bold text-slate-900">Cabang Lokasi & Manajemen Staf Pegawai</h1>
                                <p class="text-xs text-slate-500 mt-0.5">Kelola cabang lokasi toko, akun login kasir masing-masing cabang, serta hak otorisasi PIN POS.</p>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <button wire:click="openNewOutletModal"
                                    class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center gap-2">
                                    <i class="fa-solid fa-store text-orange-500"></i>
                                    <span>+ Tambah Cabang Baru</span>
                                </button>
                                <button wire:click="openNewStaffModal"
                                    class="px-3.5 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-sm transition flex items-center gap-2">
                                    <i class="fa-solid fa-user-plus"></i>
                                    <span>+ Tambah Akun Pegawai</span>
                                </button>
                            </div>
                        </div>

                        <!-- Stats Row -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center font-bold">
                                    <i class="fa-solid fa-store"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-slate-400 font-medium">Total Cabang Lokasi</div>
                                    <div class="text-lg font-bold text-slate-900">{{ $outlets->count() }} Cabang</div>
                                </div>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-slate-400 font-medium">Total Akun Pegawai & Kasir</div>
                                    <div class="text-lg font-bold text-slate-900">{{ $cashiers->count() }} Staf</div>
                                </div>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-slate-400 font-medium">Cabang Aktif Back Office</div>
                                    <div class="text-lg font-bold text-slate-900 truncate max-w-[180px]">{{ $selectedOutlet->name ?? 'Outlet Utama' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 1: Daftar Cabang Lokasi (Outlets) -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-store text-orange-500 text-sm"></i>
                                    <h3 class="font-bold text-slate-900 text-sm">Daftar Cabang Lokasi Toko</h3>
                                </div>
                                <span class="text-xs text-slate-400">{{ $outlets->count() }} Cabang Terdaftar</span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                                            <th class="py-3 px-4">Nama Cabang & Alamat</th>
                                            <th class="py-3 px-4">Kontak Cabang</th>
                                            <th class="py-3 px-4 text-center">Pajak & Service</th>
                                            <th class="py-3 px-4 text-center">Staf Ditugaskan</th>
                                            <th class="py-3 px-4 text-center">Status</th>
                                            <th class="py-3 px-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-xs">
                                        @forelse($outlets as $out)
                                            <tr class="hover:bg-slate-50/50 transition">
                                                <td class="py-3 px-4">
                                                    <div class="flex items-center gap-2.5">
                                                        <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-xs shrink-0">
                                                            <i class="fa-solid fa-shop"></i>
                                                        </div>
                                                        <div>
                                                            <div class="font-bold text-slate-900 flex items-center gap-2">
                                                                <span>{{ $out->name }}</span>
                                                                @if($selectedOutletId === $out->id)
                                                                    <span class="text-[10px] font-bold bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">Aktif Dipilih</span>
                                                                @endif
                                                            </div>
                                                            <div class="text-[11px] text-slate-400 truncate max-w-xs">{{ $out->address ?? 'Alamat belum diisi' }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-4 text-slate-600 font-medium">
                                                    {{ $out->phone ?? '-' }}
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    <span class="text-[11px] font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">
                                                        PPn {{ (int)$out->tax_rate }}% | Srv {{ (int)$out->service_rate }}%
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-center font-bold text-slate-700">
                                                    {{ $out->users_count ?? $out->users()->count() }} Staf
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    <button wire:click="toggleOutletStatus({{ $out->id }})"
                                                        class="text-[11px] font-semibold px-2 py-0.5 rounded-full transition {{ $out->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400' }}">
                                                        {{ $out->is_active ? '● Buka / Aktif' : '○ Tutup' }}
                                                    </button>
                                                </td>
                                                <td class="py-3 px-4 text-right space-x-1.5">
                                                    @if($selectedOutletId !== $out->id)
                                                        <button wire:click="switchOutlet({{ $out->id }})"
                                                            class="px-2.5 py-1 text-[11px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                                                            Pilih Cabang
                                                        </button>
                                                    @endif
                                                    <button wire:click="editOutlet({{ $out->id }})"
                                                        class="p-1.5 text-slate-400 hover:text-orange-600 transition" title="Edit Cabang">
                                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-8 text-center text-slate-400">Belum ada cabang outlet terdaftar.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Section 2: Daftar Akun Staf & Kasir Masing-Masing Cabang -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-users-gear text-orange-500 text-sm"></i>
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-sm">Daftar Akun Pegawai, Kasir & Hak Akses</h3>
                                        <p class="text-[11px] text-slate-400">Setiap pegawai memiliki akun login sendiri dengan batasan wewenang sesuai role dan cabang penugasan.</p>
                                    </div>
                                </div>
                                <span class="text-xs text-slate-400">{{ $cashiers->count() }} Pengguna</span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                                            <th class="py-3 px-4">Nama Pegawai & Email Login</th>
                                            <th class="py-3 px-4">Peran (Role)</th>
                                            <th class="py-3 px-4">Cabang Penugasan</th>
                                            <th class="py-3 px-4 text-center">PIN Kasir POS</th>
                                            <th class="py-3 px-4 text-center">Status</th>
                                            <th class="py-3 px-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-xs">
                                        @forelse($cashiers as $staff)
                                            <tr class="hover:bg-slate-50/50 transition">
                                                <td class="py-3 px-4">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-xs">
                                                            {{ substr($staff->name, 0, 2) }}
                                                        </div>
                                                        <div>
                                                            <div class="font-bold text-slate-900">{{ $staff->name }}</div>
                                                            <div class="text-[11px] text-slate-400 flex items-center gap-2">
                                                                <span><i class="fa-regular fa-envelope text-[10px]"></i> {{ $staff->email }}</span>
                                                                @if($staff->phone)
                                                                    <span>• <i class="fa-solid fa-phone text-[10px]"></i> {{ $staff->phone }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-4">
                                                    @if($staff->role === 'owner')
                                                        <span class="text-[11px] font-bold bg-amber-100 text-amber-800 px-2.5 py-0.5 rounded-full">👑 Owner (Pemilik)</span>
                                                    @elseif($staff->role === 'manager')
                                                        <span class="text-[11px] font-bold bg-blue-100 text-blue-800 px-2.5 py-0.5 rounded-full">👔 Manajer Cabang</span>
                                                    @elseif($staff->role === 'cashier')
                                                        <span class="text-[11px] font-bold bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-full">🖥️ Kasir POS</span>
                                                    @else
                                                        <span class="text-[11px] font-bold bg-purple-100 text-purple-800 px-2.5 py-0.5 rounded-full">✂️ Pegawai / Staf</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4">
                                                    <span class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                                                        <i class="fa-solid fa-location-dot text-slate-400 text-xs"></i>
                                                        {{ $staff->outlet->name ?? 'Semua Cabang' }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    <span class="text-xs font-mono font-bold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">
                                                        {{ $staff->pin_code ?? '------' }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    <button wire:click="toggleStaffStatus({{ $staff->id }})"
                                                        class="text-[11px] font-semibold px-2 py-0.5 rounded-full transition {{ $staff->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400' }}">
                                                        {{ $staff->is_active ? '● Aktif' : '○ Non-Aktif' }}
                                                    </button>
                                                </td>
                                                <td class="py-3 px-4 text-right space-x-1.5">
                                                    <button wire:click="editStaff({{ $staff->id }})"
                                                        class="p-1.5 text-slate-400 hover:text-orange-600 transition" title="Edit Staf">
                                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                                    </button>
                                                    @if($staff->role !== 'owner')
                                                        <button wire:click="deleteStaff({{ $staff->id }})"
                                                            wire:confirm="Yakin ingin menghapus akun staf {{ $staff->name }}?"
                                                            class="p-1.5 text-slate-400 hover:text-rose-600 transition" title="Hapus Staf">
                                                            <i class="fa-regular fa-trash-can text-xs"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-8 text-center text-slate-400">Belum ada akun pegawai terdaftar.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Section 3: Master Shift, Jadwal & Rekonsiliasi Kas Laci -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm">
                                        <i class="fa-solid fa-business-time"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-sm">Master Shift & Kebijakan Kasir</h3>
                                        <p class="text-[11px] text-slate-400">Atur template jadwal shift kerja, kebijakan buka/tutup kasir, serta modal kas awal laci.</p>
                                    </div>
                                </div>
                                <button wire:click="openNewShiftTemplateModal"
                                    class="px-3.5 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-xs transition flex items-center gap-2 shrink-0">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                    <span>+ Tambah Template Shift</span>
                                </button>
                            </div>

                            <div class="p-5 space-y-6">
                                <!-- Kebijakan & Aturan Kasir -->
                                <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl space-y-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-shield-halved text-orange-500 text-xs"></i>
                                            <span class="font-bold text-slate-900 text-xs">Kebijakan Operasional Kasir</span>
                                        </div>
                                        <button wire:click="saveShiftPolicies"
                                                class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-[11px] rounded-lg transition shadow-2xs">
                                            Simpan Kebijakan
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                                        <div class="bg-white p-3 rounded-xl border border-slate-200 space-y-1">
                                            <label class="block font-semibold text-slate-700 text-[11px]">Modal Kas Awal Default (Rp)</label>
                                            <div class="relative">
                                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs">Rp</span>
                                                <input type="number" wire:model="policyDefaultCash"
                                                       class="w-full pl-8 pr-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                                            </div>
                                            <p class="text-[10px] text-slate-400">Nominal rekomendasi saat kasir membuka shift baru.</p>
                                        </div>
                                        <div class="bg-white p-3 rounded-xl border border-slate-200 flex items-center justify-between">
                                            <div>
                                                <div class="font-semibold text-slate-800 text-[11px]">Wajib Buka Shift</div>
                                                <p class="text-[10px] text-slate-400">Kunci kasir hingga shift resmi dibuka</p>
                                            </div>
                                            <input type="checkbox" wire:model="policyEnforceLock" class="w-4 h-4 text-orange-500 rounded focus:ring-orange-500 cursor-pointer" />
                                        </div>
                                        <div class="bg-white p-3 rounded-xl border border-slate-200 flex items-center justify-between">
                                            <div>
                                                <div class="font-semibold text-slate-800 text-[11px]">Wajib Rekonsiliasi Kas</div>
                                                <p class="text-[10px] text-slate-400">Kasir wajib hitung fisik uang di laci</p>
                                            </div>
                                            <input type="checkbox" wire:model="policyRequireReconciliation" class="w-4 h-4 text-orange-500 rounded focus:ring-orange-500 cursor-pointer" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabel Template Shift -->
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-bold text-slate-800 text-xs flex items-center gap-2">
                                            <i class="fa-solid fa-clock text-slate-400"></i>
                                            <span>Master Jadwal Jam Shift</span>
                                        </h4>
                                        <span class="text-[11px] text-slate-400">{{ count($shiftTemplates) }} Template Tersedia</span>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        @foreach($shiftTemplates as $tmpl)
                                            <div class="p-4 rounded-2xl border transition {{ ($tmpl['is_active'] ?? true) ? 'bg-white border-slate-200 shadow-2xs' : 'bg-slate-50 border-slate-200/60 opacity-60' }}">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-bold text-slate-900 text-xs">{{ $tmpl['name'] }}</span>
                                                    <button wire:click="toggleShiftTemplateStatus({{ $tmpl['id'] }})"
                                                            class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ ($tmpl['is_active'] ?? true) ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">
                                                        {{ ($tmpl['is_active'] ?? true) ? 'Aktif' : 'Non-Aktif' }}
                                                    </button>
                                                </div>
                                                <div class="mt-2 space-y-1 text-xs">
                                                    <div class="flex items-center justify-between text-slate-500 text-[11px]">
                                                        <span>Jam Operasional:</span>
                                                        <span class="font-bold text-slate-800 font-mono">{{ $tmpl['start_time'] }} - {{ $tmpl['end_time'] }}</span>
                                                    </div>
                                                    <div class="flex items-center justify-between text-slate-500 text-[11px]">
                                                        <span>Modal Kas Laci:</span>
                                                        <span class="font-bold text-orange-600">Rp{{ number_format($tmpl['default_cash'] ?? 200000, 0, ',', '.') }}</span>
                                                    </div>
                                                </div>
                                                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-end gap-1.5">
                                                    <button wire:click="editShiftTemplate({{ $tmpl['id'] }})" class="p-1.5 text-slate-400 hover:text-orange-600 transition" title="Edit Template">
                                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                                    </button>
                                                    <button wire:click="deleteShiftTemplate({{ $tmpl['id'] }})" class="p-1.5 text-slate-400 hover:text-rose-600 transition" title="Hapus Template">
                                                        <i class="fa-regular fa-trash-can text-xs"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Riwayat Shift Kasir Terkini (Monitoring Log) -->
                                <div class="space-y-3 pt-2">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-bold text-slate-800 text-xs flex items-center gap-2">
                                            <i class="fa-solid fa-list-check text-slate-400"></i>
                                            <span>Monitoring Riwayat Shift Kasir Semua Cabang</span>
                                        </h4>
                                        <span class="text-[11px] text-slate-400">{{ $shiftHistory->count() }} Riwayat Terakhir</span>
                                    </div>
                                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                                        <table class="w-full text-left border-collapse text-xs">
                                            <thead>
                                                <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                                                    <th class="py-2.5 px-3">Kasir & Cabang</th>
                                                    <th class="py-2.5 px-3">Waktu Buka / Tutup</th>
                                                    <th class="py-2.5 px-3 text-right">Modal Awal</th>
                                                    <th class="py-2.5 px-3 text-right">Kas Aktual</th>
                                                    <th class="py-2.5 px-3 text-right">Selisih Kas</th>
                                                    <th class="py-2.5 px-3 text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                @forelse($shiftHistory as $sh)
                                                    <tr class="hover:bg-slate-50/50 transition">
                                                        <td class="py-2.5 px-3">
                                                            <div class="font-bold text-slate-900">{{ $sh->user->name ?? 'Kasir' }}</div>
                                                            <div class="text-[10px] text-slate-400 flex items-center gap-1">
                                                                <i class="fa-solid fa-store text-[9px]"></i>
                                                                <span>{{ $sh->outlet->name ?? 'Cabang' }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="py-2.5 px-3 text-[11px] text-slate-600">
                                                            <div>{{ $sh->opened_at ? $sh->opened_at->format('d/m/y H:i') : '-' }}</div>
                                                            <div class="text-[10px] text-slate-400">{{ $sh->closed_at ? 'Tutup: ' . $sh->closed_at->format('d/m/y H:i') : 'Masih berjalan' }}</div>
                                                        </td>
                                                        <td class="py-2.5 px-3 text-right font-semibold text-slate-800">
                                                            Rp{{ number_format($sh->opening_cash, 0, ',', '.') }}
                                                        </td>
                                                        <td class="py-2.5 px-3 text-right font-semibold text-slate-800">
                                                            {{ $sh->actual_cash !== null ? 'Rp' . number_format($sh->actual_cash, 0, ',', '.') : '-' }}
                                                        </td>
                                                        <td class="py-2.5 px-3 text-right font-bold {{ ($sh->difference ?? 0) < 0 ? 'text-rose-600' : (($sh->difference ?? 0) > 0 ? 'text-emerald-600' : 'text-slate-600') }}">
                                                            {{ $sh->difference !== null ? (($sh->difference > 0 ? '+' : '') . 'Rp' . number_format($sh->difference, 0, ',', '.')) : '-' }}
                                                        </td>
                                                        <td class="py-2.5 px-3 text-center">
                                                            @if($sh->closed_at)
                                                                <span class="text-[10px] font-bold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">Selesai</span>
                                                            @else
                                                                <span class="text-[10px] font-bold bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full animate-pulse">● Berjalan</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="py-6 text-center text-slate-400">Belum ada riwayat shift kasir tercatat.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
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
        <div class="fixed inset-0 z-[70] bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                            <i class="fa-solid fa-cart-plus text-orange-500"></i>
                            <span>Buat Purchase Order (PO) Baru</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Terbitkan pesanan resmi ke rekanan pemasok.</p>
                    </div>
                    <button type="button" wire:click="$set('showPoModal', false)" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="p-5 space-y-4 text-sm max-h-[75vh] overflow-y-auto">
                    <!-- Supplier Selection with Quick Add -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-slate-700">Pilih Supplier Rekanan <span class="text-rose-500">*</span></label>
                            <button type="button" wire:click="openNewSupplierModal"
                                class="text-[11px] font-semibold text-orange-600 hover:text-orange-700 hover:underline flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-plus text-[9px]"></i> Tambah Supplier Baru
                            </button>
                        </div>
                        <select wire:model="poSupplierId"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-1 focus:ring-orange-500 cursor-pointer">
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($allSuppliersList as $sup)
                                <option value="{{ $sup->id }}">
                                    {{ $sup->name }} ({{ $sup->terms_days == 0 ? 'COD / Tunai' : 'Tempo ' . $sup->terms_days . ' Hari' }})
                                </option>
                            @endforeach
                        </select>
                        @error('poSupplierId') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tanggal Estimasi & Outlet -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Estimasi Tiba / Pengiriman</label>
                            <input type="date" wire:model="poExpectedDate"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-1 focus:ring-orange-500">
                            @error('poExpectedDate') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Outlet Penerima</label>
                            <div class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-700 truncate">
                                {{ $selectedOutlet->name ?? 'Outlet Utama' }}
                            </div>
                        </div>
                    </div>

                    <!-- Total Nilai Tagihan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Total Nilai Tagihan Pesanan (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" wire:model="poTotalAmount" placeholder="1500000" min="1000"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3.5 py-2 text-sm focus:ring-1 focus:ring-orange-500 font-bold text-slate-900">
                        </div>
                        @error('poTotalAmount') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Catatan Pengadaan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Pengadaan / Rincian Barang</label>
                        <textarea wire:model="poNotes" rows="3" placeholder="Contoh: Restock Shampoo Anti-Dandruff 20 btl, Pomade 30 pcs, dan Handuk Microfiber..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:ring-1 focus:ring-orange-500"></textarea>
                        @error('poNotes') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" wire:click="$set('showPoModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl cursor-pointer">Batal</button>
                    <button type="button" wire:click="savePurchaseOrder"
                        class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-check"></i>
                        <span>Terbitkan PO Resmi</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Tambah / Edit Rekanan Supplier -->
    @if ($showSupplierModal)
        <div class="fixed inset-0 z-[80] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                            <i class="fa-solid fa-truck-field text-indigo-500"></i>
                            <span>{{ $editingSupplierId ? 'Edit Data Rekanan Supplier' : 'Tambah Rekanan Supplier Baru' }}</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftarkan profil pemasok untuk mempermudah penerbitan Purchase Order (PO).</p>
                    </div>
                    <button type="button" wire:click="$set('showSupplierModal', false)" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 space-y-4 text-sm max-h-[75vh] overflow-y-auto">
                    <!-- Nama Supplier -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Perusahaan / Supplier <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="supplierName" placeholder="Contoh: PT L'Oreal Indonesia, Toko Kemasan Mandiri"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-1 focus:ring-orange-500">
                        @error('supplierName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- PIC & No. Telepon -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama PIC (Kontak)</label>
                            <input type="text" wire:model="supplierContactPerson" placeholder="Contoh: Ibu Maya / Pak Hendro"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:ring-1 focus:ring-orange-500">
                            @error('supplierContactPerson') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">No. Telepon / WhatsApp</label>
                            <input type="text" wire:model="supplierPhone" placeholder="Contoh: 081233445566"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:ring-1 focus:ring-orange-500">
                            @error('supplierPhone') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Email Pemasok</label>
                        <input type="email" wire:model="supplierEmail" placeholder="Contoh: order@supplier.co.id"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:ring-1 focus:ring-orange-500">
                        @error('supplierEmail') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Alamat Lengkap -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Kantor / Gudang</label>
                        <textarea wire:model="supplierAddress" rows="2" placeholder="Alamat pengiriman / lokasi pemasok..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:ring-1 focus:ring-orange-500"></textarea>
                        @error('supplierAddress') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Syarat Tempo Pembayaran -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Syarat Pembayaran (Tempo Hari)</label>
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <button type="button" wire:click="$set('supplierTermsDays', 0)"
                                class="px-2.5 py-1 text-xs font-medium rounded-lg border transition {{ $supplierTermsDays === 0 ? 'bg-orange-500 text-white border-orange-500' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200' }}">
                                COD / Tunai (0 Hari)
                            </button>
                            <button type="button" wire:click="$set('supplierTermsDays', 7)"
                                class="px-2.5 py-1 text-xs font-medium rounded-lg border transition {{ $supplierTermsDays === 7 ? 'bg-orange-500 text-white border-orange-500' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200' }}">
                                Tempo 7 Hari
                            </button>
                            <button type="button" wire:click="$set('supplierTermsDays', 14)"
                                class="px-2.5 py-1 text-xs font-medium rounded-lg border transition {{ $supplierTermsDays === 14 ? 'bg-orange-500 text-white border-orange-500' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200' }}">
                                Tempo 14 Hari
                            </button>
                            <button type="button" wire:click="$set('supplierTermsDays', 30)"
                                class="px-2.5 py-1 text-xs font-medium rounded-lg border transition {{ $supplierTermsDays === 30 ? 'bg-orange-500 text-white border-orange-500' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200' }}">
                                Tempo 30 Hari
                            </button>
                        </div>
                        <div class="relative w-44">
                            <input type="number" min="0" max="180" wire:model="supplierTermsDays"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-semibold text-slate-900 focus:ring-1 focus:ring-orange-500">
                            <span class="absolute right-3 top-2 text-xs text-slate-400">Hari</span>
                        </div>
                        @error('supplierTermsDays') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Status Aktif -->
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer select-none text-xs font-medium text-slate-700">
                            <input type="checkbox" wire:model="supplierIsActive" class="rounded text-orange-500 focus:ring-orange-500">
                            <span>Supplier Aktif Bekerjasama</span>
                        </label>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" wire:click="$set('showSupplierModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl cursor-pointer">Batal</button>
                    <button type="button" wire:click="saveSupplier"
                        class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-check"></i>
                        <span>{{ $editingSupplierId ? 'Simpan Perubahan' : 'Tambah Supplier' }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Detail Purchase Order (PO) -->
    @if ($showPoDetailModal && $viewingPo)
        <div class="fixed inset-0 z-[75] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-slate-900 text-base">{{ $viewingPo->po_number }}</span>
                            @if($viewingPo->status === 'received')
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Diterima (GRN)</span>
                            @elseif($viewingPo->status === 'cancelled')
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">Dibatalkan</span>
                            @else
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">Dipesan</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Detail faktur pemesanan pengadaan barang</p>
                    </div>
                    <button type="button" wire:click="$set('showPoDetailModal', false)" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 space-y-4 text-xs max-h-[75vh] overflow-y-auto">
                    <!-- Supplier Info Box -->
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/70 space-y-2">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Rekanan Pemasok</div>
                        <div class="font-bold text-slate-900 text-sm">{{ $viewingPo->supplier->name ?? 'Pemasok Terhapus' }}</div>
                        <div class="grid grid-cols-2 gap-2 text-slate-600 pt-1">
                            <div><i class="fa-solid fa-user-tie text-slate-400 mr-1.5"></i>PIC: {{ $viewingPo->supplier->contact_person ?? '-' }}</div>
                            <div><i class="fa-solid fa-phone text-slate-400 mr-1.5"></i>{{ $viewingPo->supplier->phone ?? '-' }}</div>
                            <div class="col-span-2"><i class="fa-solid fa-location-dot text-slate-400 mr-1.5"></i>{{ $viewingPo->supplier->address ?? '-' }}</div>
                        </div>
                    </div>

                    <!-- PO Dates & Outlet -->
                    <div class="grid grid-cols-2 gap-3 p-3 bg-slate-50/70 rounded-xl border border-slate-200/60">
                        <div>
                            <div class="text-slate-400 text-[10px]">Tanggal Terbit Order:</div>
                            <div class="font-bold text-slate-800 mt-0.5">{{ $viewingPo->order_date ? $viewingPo->order_date->format('d M Y') : '-' }}</div>
                        </div>
                        <div>
                            <div class="text-slate-400 text-[10px]">Estimasi Kedatangan:</div>
                            <div class="font-bold text-slate-800 mt-0.5">{{ $viewingPo->expected_date ? $viewingPo->expected_date->format('d M Y') : '-' }}</div>
                        </div>
                        <div class="col-span-2 pt-1 border-t border-slate-200/50">
                            <div class="text-slate-400 text-[10px]">Outlet Tujuan:</div>
                            <div class="font-semibold text-slate-800 mt-0.5">{{ $viewingPo->outlet->name ?? 'Outlet Utama' }}</div>
                        </div>
                    </div>

                    <!-- Catatan Pengadaan -->
                    @if($viewingPo->notes)
                        <div class="p-3 bg-amber-50/60 border border-amber-200/60 rounded-xl">
                            <div class="font-semibold text-amber-900 text-[11px]">Catatan / Instruksi Pengadaan:</div>
                            <div class="text-amber-800 mt-0.5 leading-relaxed">{{ $viewingPo->notes }}</div>
                        </div>
                    @endif

                    <!-- Total Tagihan -->
                    <div class="p-4 bg-orange-50/80 rounded-xl border border-orange-200 flex items-center justify-between">
                        <div>
                            <div class="text-orange-700 text-[11px] font-semibold uppercase tracking-wider">Total Nilai Tagihan PO</div>
                            <div class="text-[10px] text-orange-600">Syarat: {{ $viewingPo->supplier?->terms_days == 0 ? 'Cash on Delivery (COD)' : 'Tempo ' . $viewingPo->supplier?->terms_days . ' Hari' }}</div>
                        </div>
                        <div class="text-xl font-bold text-slate-900 font-mono">
                            Rp {{ number_format($viewingPo->total_amount, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2">
                    @if($viewingPo->status === 'ordered')
                        <button type="button" wire:click="cancelPo({{ $viewingPo->id }})"
                            wire:confirm="Yakin ingin membatalkan Purchase Order ini?"
                            class="px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition cursor-pointer">
                            Batalkan PO
                        </button>

                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="$set('showPoDetailModal', false)"
                                class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl cursor-pointer">Tutup</button>
                            <button type="button" wire:click="markPoReceived({{ $viewingPo->id }})"
                                class="px-4 py-2 text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                                <i class="fa-solid fa-box-open"></i>
                                <span>Konfirmasi Terima Barang (GRN)</span>
                            </button>
                        </div>
                    @else
                        <div></div>
                        <button type="button" wire:click="$set('showPoDetailModal', false)"
                            class="px-4 py-2 text-xs font-semibold bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl cursor-pointer">Tutup</button>
                    @endif
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
        <div class="fixed inset-0 z-[80] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
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
        <div class="fixed inset-0 z-[75] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
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

                    <!-- Modal Bahan / HPP Produk -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Modal Bahan Produk (HPP per Sesi)</label>
                        <input type="number" wire:model="serviceCostPrice" placeholder="e.g. 5000 (shampoo, pomade, cream)"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        <span class="text-[10px] text-slate-400">Pemakaian shampoo, pomade, obat cream, handuk, dll</span>
                    </div>

                    <!-- Upload Foto Layanan -->
                    <div class="border border-slate-200 rounded-2xl p-3.5 bg-slate-50/70 space-y-3" x-data="{ showUrlInput: {{ !empty($serviceImageUrl) ? 'true' : 'false' }} }">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-semibold text-slate-800">
                                <i class="fa-solid fa-camera text-orange-500 mr-1"></i> Foto Layanan Jasa
                            </label>
                            <button type="button" @click="showUrlInput = !showUrlInput"
                                class="text-[11px] font-medium text-slate-500 hover:text-orange-600 underline transition">
                                <span x-text="showUrlInput ? 'Sembunyikan Opsi URL' : 'Gunakan URL Foto Web'"></span>
                            </button>
                        </div>

                        <div class="flex items-center gap-3.5">
                            <!-- Image Preview Thumbnail -->
                            <div class="relative shrink-0 w-20 h-20 rounded-xl overflow-hidden bg-white border border-slate-200 shadow-xs flex items-center justify-center">
                                @if ($serviceImageFile)
                                    <img src="{{ $serviceImageFile->temporaryUrl() }}" class="w-full h-full object-cover">
                                    <span class="absolute bottom-0 inset-x-0 bg-emerald-600/95 text-white text-[9px] font-bold text-center py-0.5">Preview Baru</span>
                                @elseif ($serviceImageUrl)
                                    <img src="{{ $serviceImageUrl }}" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1503951914875-452162b0f3f1?auto=format&fit=crop&w=150&h=150&q=80'">
                                    <span class="absolute bottom-0 inset-x-0 bg-slate-800/80 text-white text-[9px] font-semibold text-center py-0.5">Tersimpan</span>
                                @else
                                    <div class="text-center p-2 text-slate-400">
                                        <i class="fa-regular fa-image text-2xl text-slate-300"></i>
                                        <div class="text-[9px] mt-0.5 font-medium text-slate-400">Tanpa Foto</div>
                                    </div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="flex-1 min-w-0 space-y-1.5">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-semibold cursor-pointer transition shadow-xs">
                                        <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                                        <span>Pilih / Unggah Gambar</span>
                                        <input type="file" wire:model="serviceImageFile" accept="image/png,image/jpeg,image/webp,image/jpg" class="hidden">
                                    </label>

                                    @if ($serviceImageFile || $serviceImageUrl)
                                        <button type="button" wire:click="removeServiceImage"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-600 rounded-xl text-xs font-semibold transition">
                                            <i class="fa-solid fa-trash-can text-[11px]"></i>
                                            <span>Hapus</span>
                                        </button>
                                    @endif
                                </div>

                                <div wire:loading wire:target="serviceImageFile" class="text-xs text-orange-600 font-semibold flex items-center gap-1.5">
                                    <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                                    <span>Mengunggah file foto...</span>
                                </div>

                                <p class="text-[10px] text-slate-400 leading-tight">
                                    Format: JPG, PNG, atau WEBP. Ukuran maksimal 3MB.
                                </p>

                                @error('serviceImageFile')
                                    <span class="text-[11px] text-rose-500 font-medium block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- URL Input Option -->
                        <div x-show="showUrlInput" class="pt-2.5 border-t border-slate-200/80 space-y-1">
                            <label class="block text-[11px] font-semibold text-slate-600">URL Gambar Eksternal (Opsional):</label>
                            <input type="text" wire:model="serviceImageUrl" placeholder="https://images.unsplash.com/..."
                                class="w-full bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
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

    <!-- 12. Modal Tambah / Edit Cabang Outlet -->
    @if ($showOutletModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">{{ $editingOutletId ? 'Edit Konfigurasi Cabang' : 'Tambah Cabang Lokasi Baru' }}</h3>
                    </div>
                    <button wire:click="$set('showOutletModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto text-sm">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Cabang / Outlet</label>
                        <input type="text" wire:model="outletName" placeholder="Contoh: Cabang Kemang / Cabang Dago"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        @error('outletName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
                        <textarea rows="2" wire:model="outletAddress" placeholder="Jl. Kemang Raya No. 45, Jakarta Selatan"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500"></textarea>
                        @error('outletAddress') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Telepon Cabang</label>
                        <input type="text" wire:model="outletPhone" placeholder="081298765432"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tarif Pajak PPn (%)</label>
                            <input type="number" wire:model="outletTaxRate"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 font-semibold">
                            @error('outletTaxRate') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Service Charge (%)</label>
                            <input type="number" wire:model="outletServiceRate"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 font-semibold">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pesan Footer Struk Kasir</label>
                        <input type="text" wire:model="outletReceiptFooter" placeholder="Terima kasih atas kunjungan Anda!"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                    </div>

                    <div class="flex items-center gap-4 pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                            <input type="checkbox" wire:model="outletIsActive" class="rounded text-orange-500 focus:ring-orange-500">
                            <span>Cabang ini aktif beroperasi dan dapat dipilih oleh kasir</span>
                        </label>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" wire:click="$set('showOutletModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Batal</button>
                    <button type="button" wire:click="saveOutlet"
                        class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i>
                        <span>{{ $editingOutletId ? 'Simpan Perubahan' : 'Buat Cabang' }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- 13. Modal Tambah / Edit Akun Pegawai & Kasir -->
    @if ($showStaffModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">{{ $editingStaffId ? 'Edit Akun Pegawai / Kasir' : 'Tambah Akun Pegawai Baru' }}</h3>
                    </div>
                    <button wire:click="$set('showStaffModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto text-sm">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Pegawai</label>
                        <input type="text" wire:model="staffName" placeholder="Contoh: Siti Rahmawati"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        @error('staffName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email Login</label>
                            <input type="email" wire:model="staffEmail" placeholder="siti@kopiruangpagi.id"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                            @error('staffEmail') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">No. Handphone / WhatsApp</label>
                            <input type="text" wire:model="staffPhone" placeholder="08123456789"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Peran (Role Akses)</label>
                            <select wire:model="staffRole"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 font-medium">
                                <option value="cashier">Kasir POS (Hanya Layar Kasir)</option>
                                <option value="manager">Manajer Cabang (Kasir + Back Office)</option>
                                <option value="staff">Staf Layanan / Kitchen / Kapster</option>
                            </select>
                            @error('staffRole') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Cabang Penugasan</label>
                            <select wire:model="staffOutletId"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 font-medium">
                                @foreach($outlets as $out)
                                    <option value="{{ $out->id }}">{{ $out->name }}</option>
                                @endforeach
                            </select>
                            @error('staffOutletId') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">6-Digit PIN Kasir POS</label>
                            <input type="text" wire:model="staffPinCode" maxlength="6" placeholder="123456"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm font-mono font-bold tracking-widest text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <span class="text-[10px] text-slate-400">Untuk otorisasi cepat di layar POS</span>
                            @error('staffPinCode') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi (Password)</label>
                            <input type="password" wire:model="staffPassword" placeholder="{{ $editingStaffId ? 'Kosongkan jika tak diubah' : 'Default: password' }}"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <span class="text-[10px] text-slate-400">Digunakan untuk login ke sistem</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                            <input type="checkbox" wire:model="staffIsActive" class="rounded text-orange-500 focus:ring-orange-500">
                            <span>Akun aktif dan diizinkan masuk ke sistem Lokara POS</span>
                        </label>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" wire:click="$set('showStaffModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Batal</button>
                    <button type="button" wire:click="saveStaff"
                        class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i>
                        <span>{{ $editingStaffId ? 'Simpan Perubahan' : 'Buat Akun Pegawai' }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- 7. Modal Tambah / Edit Template Shift -->
    @if ($showShiftTemplateModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">{{ $editingShiftTemplateId ? 'Edit Template Shift' : 'Tambah Template Shift Baru' }}</h3>
                    </div>
                    <button wire:click="$set('showShiftTemplateModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Shift</label>
                        <input type="text" wire:model="shiftTemplateName" placeholder="Contoh: Shift 1 (Pagi)"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500">
                        @error('shiftTemplateName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Jam Mulai</label>
                            <input type="time" wire:model="shiftTemplateStartTime"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500">
                            @error('shiftTemplateStartTime') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Jam Selesai</label>
                            <input type="time" wire:model="shiftTemplateEndTime"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500">
                            @error('shiftTemplateEndTime') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Rekomendasi Modal Kas Awal (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold">Rp</span>
                            <input type="number" wire:model="shiftTemplateDefaultCash" step="1000" placeholder="200000"
                                class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        @error('shiftTemplateDefaultCash') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                            <input type="checkbox" wire:model="shiftTemplateIsActive" class="rounded text-orange-500 focus:ring-orange-500">
                            <span>Template shift aktif dan dapat dipilih di kasir</span>
                        </label>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" wire:click="$set('showShiftTemplateModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Batal</button>
                    <button type="button" wire:click="saveShiftTemplate"
                        class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i>
                        <span>{{ $editingShiftTemplateId ? 'Simpan Perubahan' : 'Buat Template Shift' }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

