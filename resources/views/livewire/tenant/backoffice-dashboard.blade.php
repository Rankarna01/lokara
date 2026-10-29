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

                    <!-- Master Produk -->
                    <button wire:click="switchTab('products')"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ $activeTab === 'products' ? 'bg-orange-50 text-orange-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-boxes-stacked w-5 text-center {{ $activeTab === 'products' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                            <span>Master Produk</span>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $activeTab === 'products' ? 'bg-orange-200/60 text-orange-700' : 'bg-slate-100 text-slate-500' }}">{{ $products->count() }}</span>
                    </button>

                    <!-- Bahan Baku & Resep BOM -->
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
                                <button wire:click="openNewProductModal" class="px-3.5 py-2 text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-plus text-slate-500"></i>
                                    <span>Tambah Menu</span>
                                </button>
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
                                <h1 class="text-xl font-bold text-slate-900">Katalog & Master Produk</h1>
                                <p class="text-xs text-slate-500 mt-0.5">Kelola menu, harga jual cabang, HPP modal, dan ketersediaan stok.</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <button wire:click="openNewProductModal"
                                    class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>Tambah Produk Baru</span>
                                </button>
                            </div>
                        </div>

                        <!-- Search & Category Filters -->
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-2 sm:pb-0">
                                <button wire:click="$set('selectedCategoryFilter', 'all')"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $selectedCategoryFilter === 'all' ? 'bg-orange-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                    Semua Kategori ({{ $products->count() }})
                                </button>
                                @foreach($categories as $cat)
                                    <button wire:click="$set('selectedCategoryFilter', {{ $cat->id }})"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-lg transition whitespace-nowrap {{ $selectedCategoryFilter == $cat->id ? 'bg-orange-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                        {{ $cat->name }}
                                    </button>
                                @endforeach
                            </div>

                            <div class="relative w-full sm:w-64">
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
                                                    <span class="bg-slate-100 px-2.5 py-1 rounded-md font-medium text-slate-700">
                                                        {{ $p->category->name ?? 'Katalog' }}
                                                    </span>
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
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori</label>
                            <select wire:model="productCategoryId"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
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
</div>

