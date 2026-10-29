<div class="min-h-screen bg-surface flex flex-col antialiased text-dark" x-data="{ userMenuOpen: false, dateDropdownOpen: false }">

    <!-- Toast Notification -->
    @if ($toastMessage)
        <div class="fixed top-5 right-5 z-50 flex items-center gap-3 bg-white border border-slate-200 px-4 py-3 rounded-xl shadow-lg animate-bounce"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false; $wire.clearToast() }, 4000)">
            <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-check text-sm"></i>
            </div>
            <div class="text-sm font-medium text-slate-800">{{ $toastMessage }}</div>
            <button wire:click="clearToast" class="text-slate-400 hover:text-slate-600 ml-2">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    @endif

    <div class="flex-1 flex overflow-hidden">

        <!-- Mobile Sidebar Backdrop -->
        <div class="fixed inset-0 bg-slate-900/40 z-40 lg:hidden transition-opacity"
             x-show="$wire.sidebarOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="$wire.toggleSidebar()">
        </div>

        <!-- ================= SIDEBAR (Matching Image 1 & 2) ================= -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200/80 flex flex-col justify-between transition-transform duration-200 lg:static lg:translate-x-0"
               :class="$wire.sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

            <div>
                <!-- Brand Logo (Orange Icon + Lokara POS badge) -->
                <div class="h-16 flex items-center px-6 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-orange-500 flex items-center justify-center text-white shadow-sm shadow-orange-500/20">
                            <!-- Custom Hexagon / Layers Icon -->
                            <i class="fa-solid fa-cubes text-sm"></i>
                        </div>
                        <div class="flex items-center">
                            <span class="text-xl font-bold tracking-tight text-slate-900">Lokara</span>
                            <span class="ml-1.5 px-1.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-orange-500 text-white tracking-wider">POS</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation List -->
                <div class="px-3 py-4 space-y-6">
                    <div>
                        <div class="px-3 pb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            Platform
                        </div>
                        <nav class="space-y-1">
                            <!-- Dashboard -->
                            <button wire:click="setTab('dashboard')"
                                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'dashboard' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <i class="fa-solid fa-house text-base w-5 text-center {{ $activeTab === 'dashboard' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>Dashboard</span>
                            </button>

                            <!-- Tenant -->
                            <button wire:click="setTab('tenants')"
                                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'tenants' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <i class="fa-solid fa-store text-base w-5 text-center {{ $activeTab === 'tenants' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>Tenant</span>
                            </button>

                            <!-- Akun Tenant -->
                            <button wire:click="setTab('tenant-users')"
                                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'tenant-users' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <i class="fa-solid fa-users text-base w-5 text-center {{ $activeTab === 'tenant-users' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>Akun Tenant</span>
                            </button>

                            <!-- Paket & Langganan -->
                            <button wire:click="setTab('plans')"
                                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'plans' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <i class="fa-solid fa-credit-card text-base w-5 text-center {{ $activeTab === 'plans' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>Paket & Langganan</span>
                            </button>

                            <!-- Billing & Invoice -->
                            <button wire:click="setTab('invoices')"
                                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'invoices' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <i class="fa-solid fa-file-invoice-dollar text-base w-5 text-center {{ $activeTab === 'invoices' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>Billing & Invoice</span>
                            </button>

                            <!-- Template Usaha -->
                            <button wire:click="setTab('business-types')"
                                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'business-types' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <i class="fa-solid fa-shapes text-base w-5 text-center {{ $activeTab === 'business-types' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>Template Usaha</span>
                            </button>

                            <!-- Pengaturan Global -->
                            <button wire:click="setTab('settings')"
                                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'settings' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <i class="fa-solid fa-gear text-base w-5 text-center {{ $activeTab === 'settings' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>Pengaturan Global</span>
                            </button>

                            <!-- Support & Pengumuman -->
                            <button wire:click="setTab('support')"
                                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'support' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <i class="fa-solid fa-bullhorn text-base w-5 text-center {{ $activeTab === 'support' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>Support & Pengumuman</span>
                            </button>

                            <!-- Monitoring & Audit -->
                            <button wire:click="setTab('monitoring')"
                                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'monitoring' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <i class="fa-solid fa-shield-halved text-base w-5 text-center {{ $activeTab === 'monitoring' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>Monitoring & Audit</span>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Bottom User Profile Card (Matching Image 1) -->
            <div class="p-3 border-t border-slate-100" x-data="{ bottomMenuOpen: false }">
                <div @click="bottomMenuOpen = !bottomMenuOpen"
                     class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-semibold text-sm">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div class="text-left">
                            <div class="text-xs font-bold text-slate-900">{{ auth()->user()->name ?? 'Super Admin' }}</div>
                            <div class="text-[11px] text-slate-400">{{ auth()->user()->role ?? 'Platform Owner' }}</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs text-slate-400" :class="bottomMenuOpen ? 'rotate-90' : ''"></i>
                </div>

                <!-- Bottom Popup Menu -->
                <div x-show="bottomMenuOpen"
                     @click.outside="bottomMenuOpen = false"
                     x-transition
                     class="mt-2 p-1.5 bg-white border border-slate-200 rounded-xl shadow-lg space-y-1 text-xs">
                    <button wire:click="setTab('settings')" @click="bottomMenuOpen = false" class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-slate-50 rounded-lg text-left">
                        <i class="fa-solid fa-gear text-slate-400"></i>
                        <span>Pengaturan Akun</span>
                    </button>
                    <button wire:click="logout" class="w-full flex items-center gap-2.5 px-3 py-2 text-rose-600 hover:bg-rose-50 rounded-lg text-left font-medium">
                        <i class="fa-solid fa-arrow-right-from-bracket text-rose-500"></i>
                        <span>Keluar (Logout)</span>
                    </button>
                </div>
            </div>
        </aside>

        <!-- ================= MAIN CONTENT WRAPPER ================= -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

            <!-- Top Header Navbar (Matching Image 1) -->
            <header class="h-16 bg-white border-b border-slate-200/80 px-4 lg:px-8 flex items-center justify-between sticky top-0 z-30">
                <div class="flex items-center gap-4">
                    <!-- Mobile Hamburger -->
                    <button wire:click="toggleSidebar" class="lg:hidden text-slate-500 hover:text-slate-800 p-1.5 rounded-lg hover:bg-slate-100">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>

                    <!-- Breadcrumbs -->
                    <div class="flex items-center gap-2 text-sm text-slate-500 font-normal">
                        <span>Platform</span>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-900 font-medium capitalize">{{ str_replace('-', ' ', $activeTab) }}</span>
                    </div>
                </div>

                <!-- Right Header Controls -->
                <div class="flex items-center gap-4">
                    <!-- Search Input -->
                    <div class="relative hidden sm:block w-64 lg:w-72">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text"
                               wire:model.live.debounce.300ms="searchQuery"
                               placeholder="Cari tenant, invoice..."
                               class="w-full bg-slate-50/80 border border-slate-200/90 rounded-lg pl-9 pr-3.5 py-1.5 text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition" />
                    </div>

                    <!-- Notification Bell -->
                    <button class="relative p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-50 transition">
                        <i class="fa-regular fa-bell text-base"></i>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-orange-500"></span>
                    </button>

                    <!-- User Profile Dropdown -->
                    <div class="relative" x-data="{ userMenuOpen: false }">
                        <button @click="userMenuOpen = !userMenuOpen"
                                class="flex items-center gap-2.5 pl-2 border-l border-slate-200 focus:outline-none">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=80&h=80&q=80"
                                 alt="Admin"
                                 class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200" />
                            <span class="hidden md:inline-block text-xs font-semibold text-slate-800">{{ auth()->user()->name ?? 'Admin Lokara' }}</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                        </button>

                        <div x-show="userMenuOpen"
                             @click.outside="userMenuOpen = false"
                             x-transition
                             class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-xl shadow-xl py-1 z-50 text-xs">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <div class="font-bold text-slate-900">{{ auth()->user()->name ?? 'Admin Lokara' }}</div>
                                <div class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email ?? 'admin@lokarapos.id' }}</div>
                            </div>
                            <button wire:click="setTab('settings')" @click="userMenuOpen = false" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-slate-700 flex items-center gap-2">
                                <i class="fa-solid fa-gear text-slate-400"></i>
                                <span>Pengaturan Platform</span>
                            </button>
                            <button wire:click="logout" class="w-full text-left px-4 py-2 hover:bg-rose-50 text-rose-600 flex items-center gap-2 font-medium">
                                <i class="fa-solid fa-arrow-right-from-bracket text-rose-500"></i>
                                <span>Keluar (Logout)</span>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Body Content -->
            <main class="flex-1 p-4 lg:p-8 space-y-6">

                <!-- ################# TAB 1: DASHBOARD PLATFORM (IMAGE 1) ################# -->
                @if ($activeTab === 'dashboard')
                    <!-- Title & Date Filter Bar -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Dashboard Platform</h1>
                            <p class="text-sm text-slate-500 mt-0.5">Ringkasan operasional Lokara POS</p>
                        </div>

                        <!-- Date Filter Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition">
                                <i class="fa-regular fa-calendar text-slate-400"></i>
                                <span>
                                    @if ($dateFilter === '7_days') 7 hari terakhir
                                    @elseif ($dateFilter === '30_days') 30 hari terakhir
                                    @elseif ($dateFilter === '90_days') 90 hari terakhir
                                    @else Tahun 2026
                                    @endif
                                </span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-1"></i>
                            </button>

                            <div x-show="open"
                                 @click.outside="open = false"
                                 class="absolute right-0 mt-1 w-44 bg-white border border-slate-200 rounded-xl shadow-lg py-1 z-30 text-xs">
                                <button wire:click="setDateFilter('7_days')" @click="open = false" class="w-full text-left px-4 py-2 hover:bg-slate-50 {{ $dateFilter === '7_days' ? 'text-orange-600 font-semibold' : 'text-slate-700' }}">7 hari terakhir</button>
                                <button wire:click="setDateFilter('30_days')" @click="open = false" class="w-full text-left px-4 py-2 hover:bg-slate-50 {{ $dateFilter === '30_days' ? 'text-orange-600 font-semibold' : 'text-slate-700' }}">30 hari terakhir</button>
                                <button wire:click="setDateFilter('90_days')" @click="open = false" class="w-full text-left px-4 py-2 hover:bg-slate-50 {{ $dateFilter === '90_days' ? 'text-orange-600 font-semibold' : 'text-slate-700' }}">90 hari terakhir</button>
                                <button wire:click="setDateFilter('this_year')" @click="open = false" class="w-full text-left px-4 py-2 hover:bg-slate-50 {{ $dateFilter === 'this_year' ? 'text-orange-600 font-semibold' : 'text-slate-700' }}">Tahun 2026</button>
                            </div>
                        </div>
                    </div>

                    <!-- 4 Top Stat Cards (Matching Image 1) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Stat 1: Total Tenant -->
                        <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-sm hover:border-slate-300 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                                    <i class="fa-solid fa-store text-sm"></i>
                                </div>
                                <span class="text-xs font-medium text-slate-500">Total Tenant</span>
                            </div>
                            <div class="mt-4">
                                <div class="text-2xl lg:text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalTenantCount, 0, ',', '.') }}</div>
                                <div class="text-xs text-slate-400 mt-1">{{ number_format($activeTenantCount, 0, ',', '.') }} aktif</div>
                            </div>
                        </div>

                        <!-- Stat 2: Pendapatan Bulanan -->
                        <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-sm hover:border-slate-300 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                                    <i class="fa-solid fa-wallet text-sm"></i>
                                </div>
                                <span class="text-xs font-medium text-slate-500">Pendapatan Bulanan</span>
                            </div>
                            <div class="mt-4">
                                <div class="text-2xl lg:text-3xl font-bold tracking-tight text-slate-900">{{ $mrrValue }}</div>
                                <div class="text-xs text-slate-400 mt-1">MRR saat ini</div>
                            </div>
                        </div>

                        <!-- Stat 3: Transaksi Platform -->
                        <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-sm hover:border-slate-300 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                                    <i class="fa-solid fa-right-left text-sm"></i>
                                </div>
                                <span class="text-xs font-medium text-slate-500">Transaksi Platform</span>
                            </div>
                            <div class="mt-4">
                                <div class="text-2xl lg:text-3xl font-bold tracking-tight text-slate-900">{{ $totalTransactions }}</div>
                                <div class="text-xs text-slate-400 mt-1">30 hari terakhir</div>
                            </div>
                        </div>

                        <!-- Stat 4: Perlu Perhatian -->
                        <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-sm hover:border-slate-300 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                                    <i class="fa-regular fa-clock text-sm"></i>
                                </div>
                                <span class="text-xs font-medium text-slate-500">Perlu Perhatian</span>
                            </div>
                            <div class="mt-4">
                                <div class="text-2xl lg:text-3xl font-bold tracking-tight text-slate-900">{{ $attentionCount }}</div>
                                <div class="text-xs text-slate-400 mt-1">Tenant segera berakhir</div>
                            </div>
                        </div>
                    </div>

                    <!-- Middle Grid: Charts (Line Chart ~65% + Donut Chart ~35%) -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

                        <!-- Left Chart: Pertumbuhan Tenant -->
                        <div class="lg:col-span-8 bg-white border border-slate-200/80 rounded-xl p-6 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-bold text-slate-900">Pertumbuhan Tenant</h3>
                            </div>
                            <div class="h-64 relative">
                                <canvas id="growthChart"></canvas>
                            </div>
                        </div>

                        <!-- Right Chart: Status Langganan -->
                        <div class="lg:col-span-4 bg-white border border-slate-200/80 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                            <div class="mb-2">
                                <h3 class="text-sm font-bold text-slate-900">Status Langganan</h3>
                            </div>

                            <div class="flex items-center justify-center relative py-4">
                                <div class="w-44 h-44 relative flex items-center justify-center">
                                    <canvas id="subscriptionDonutChart"></canvas>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                        <span class="text-xl font-bold text-slate-900">1.284</span>
                                        <span class="text-[11px] text-slate-400">Total Tenant</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Legend List (Matching Image 1) -->
                            <div class="space-y-2 pt-2 border-t border-slate-100 text-xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                                        <span class="text-slate-600">Active</span>
                                    </div>
                                    <span class="font-semibold text-slate-900">1.102</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-sky-300"></span>
                                        <span class="text-slate-600">Trial</span>
                                    </div>
                                    <span class="font-semibold text-slate-900">96</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                                        <span class="text-slate-600">Suspended</span>
                                    </div>
                                    <span class="font-semibold text-slate-900">28</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-slate-800"></span>
                                        <span class="text-slate-600">Expired</span>
                                    </div>
                                    <span class="font-semibold text-slate-900">58</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Grid: Tenant Terbaru (Left) & Aktivitas Terbaru (Right) -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

                        <!-- Left: Tenant Terbaru Table (Matching Image 1) -->
                        <div class="lg:col-span-8 bg-white border border-slate-200/80 rounded-xl p-6 shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-bold text-slate-900">Tenant Terbaru</h3>
                                <button wire:click="setTab('tenants')" class="text-xs text-orange-600 hover:text-orange-700 font-medium">Lihat Semua</button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead>
                                        <tr class="text-slate-400 border-b border-slate-100 pb-2">
                                            <th class="py-2.5 font-medium">Nama Bisnis</th>
                                            <th class="py-2.5 font-medium">Jenis Usaha</th>
                                            <th class="py-2.5 font-medium">Paket</th>
                                            <th class="py-2.5 font-medium">Bergabung</th>
                                            <th class="py-2.5 font-medium text-right">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                        @forelse ($recentTenants as $tenant)
                                            <tr class="hover:bg-slate-50/60 transition">
                                                <td class="py-3 flex items-center gap-2.5 font-semibold text-slate-900">
                                                    <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600 flex-shrink-0">
                                                        <i class="fa-solid {{ $tenant->icon ?? 'fa-store' }} text-xs"></i>
                                                    </div>
                                                    <span>{{ $tenant->name }}</span>
                                                </td>
                                                <td class="py-3 text-slate-600">
                                                    {{ $tenant->businessType ? explode(' ', $tenant->businessType->name)[0] : 'Umum' }}
                                                </td>
                                                <td class="py-3 text-slate-600 font-medium">
                                                    {{ $tenant->plan ? $tenant->plan->name : 'Basic' }}
                                                </td>
                                                <td class="py-3 text-slate-500">
                                                    {{ $tenant->created_at ? $tenant->created_at->translatedFormat('d M Y') : '28 Okt 2026' }}
                                                </td>
                                                <td class="py-3 text-right">
                                                    @if ($tenant->status === 'active')
                                                        <span class="inline-flex items-center gap-1.5 text-slate-700 font-medium">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                                        </span>
                                                    @elseif ($tenant->status === 'trial')
                                                        <span class="inline-flex items-center gap-1.5 text-slate-700 font-medium">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Trial
                                                        </span>
                                                    @elseif ($tenant->status === 'suspended')
                                                        <span class="inline-flex items-center gap-1.5 text-slate-700 font-medium">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Suspended
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 text-slate-700 font-medium">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Expired
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="py-4 text-center text-slate-400">Belum ada tenant baru</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Right: Aktivitas Terbaru (Matching Image 1) -->
                        <div class="lg:col-span-4 bg-white border border-slate-200/80 rounded-xl p-6 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-bold text-slate-900">Aktivitas Terbaru</h3>
                                <button wire:click="setTab('monitoring')" class="text-xs text-orange-600 hover:text-orange-700 font-medium">Log Lengkap</button>
                            </div>

                            <div class="space-y-4">
                                @forelse ($activityLogs as $log)
                                    <div class="flex items-start justify-between gap-3 text-xs">
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-lg {{ $log->icon_color }} flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <i class="fa-solid {{ $log->icon }} text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-slate-800 leading-snug">{{ $log->action }}</p>
                                                <p class="text-slate-400 text-[11px] mt-0.5">{{ $log->description }}</p>
                                            </div>
                                        </div>
                                        <span class="text-[11px] text-slate-400 whitespace-nowrap">{{ $log->badge_text }}</span>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400">Belum ada aktivitas tercatat.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endif

                <!-- ################# TAB 2: TENANT MANAGEMENT ################# -->
                @if ($activeTab === 'tenants')
                    <div class="space-y-5">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Manajemen Tenant</h1>
                                <p class="text-sm text-slate-500 mt-0.5">Kelola seluruh tenant bisnis yang terdaftar di platform Lokara POS</p>
                            </div>
                            <button wire:click="$set('showCreateTenantModal', true)"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold shadow-sm transition">
                                <i class="fa-solid fa-plus text-xs"></i>
                                <span>Tambah Tenant Baru</span>
                            </button>
                        </div>

                        <!-- Filter Status Pills -->
                        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                            <button wire:click="$set('tenantStatusFilter', 'all')" class="px-3.5 py-1.5 rounded-lg font-medium transition {{ $tenantStatusFilter === 'all' ? 'bg-orange-500 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Semua ({{ $totalTenantCount }})</button>
                            <button wire:click="$set('tenantStatusFilter', 'active')" class="px-3.5 py-1.5 rounded-lg font-medium transition {{ $tenantStatusFilter === 'active' ? 'bg-orange-500 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Aktif ({{ $activeTenantCount }})</button>
                            <button wire:click="$set('tenantStatusFilter', 'trial')" class="px-3.5 py-1.5 rounded-lg font-medium transition {{ $tenantStatusFilter === 'trial' ? 'bg-orange-500 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Trial ({{ $trialTenantCount }})</button>
                            <button wire:click="$set('tenantStatusFilter', 'suspended')" class="px-3.5 py-1.5 rounded-lg font-medium transition {{ $tenantStatusFilter === 'suspended' ? 'bg-orange-500 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Suspended ({{ $suspendedTenantCount }})</button>
                            <button wire:click="$set('tenantStatusFilter', 'expired')" class="px-3.5 py-1.5 rounded-lg font-medium transition {{ $tenantStatusFilter === 'expired' ? 'bg-orange-500 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Expired ({{ $expiredTenantCount }})</button>
                        </div>

                        <!-- Tenant Table Card -->
                        <div class="bg-white border border-slate-200/80 rounded-xl shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-slate-500">
                                        <tr>
                                            <th class="py-3 px-4 font-semibold">Bisnis & Subdomain</th>
                                            <th class="py-3 px-4 font-semibold">Jenis Usaha</th>
                                            <th class="py-3 px-4 font-semibold">Owner & Kontak</th>
                                            <th class="py-3 px-4 font-semibold">Paket</th>
                                            <th class="py-3 px-4 font-semibold">Status</th>
                                            <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse ($allTenants as $t)
                                            <tr class="hover:bg-slate-50/50 transition">
                                                <td class="py-3 px-4">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center font-bold text-xs">
                                                            <i class="fa-solid {{ $t->icon ?? 'fa-store' }}"></i>
                                                        </div>
                                                        <div>
                                                            <div class="font-bold text-slate-900 text-sm">{{ $t->name }}</div>
                                                            <div class="text-[11px] text-slate-400">{{ $t->slug }}.lokara.id</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-4 text-slate-600">
                                                    <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-700 px-2.5 py-1 rounded-md text-[11px]">
                                                        {{ $t->businessType ? $t->businessType->name : 'F&B' }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4">
                                                    <div class="font-medium text-slate-800">{{ $t->owner_name ?? '-' }}</div>
                                                    <div class="text-[11px] text-slate-400">{{ $t->owner_phone ?? $t->owner_email }}</div>
                                                </td>
                                                <td class="py-3 px-4">
                                                    <span class="font-bold text-slate-800">{{ $t->plan ? $t->plan->name : 'Pro' }}</span>
                                                </td>
                                                <td class="py-3 px-4">
                                                    @if ($t->status === 'active')
                                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-600">Aktif</span>
                                                    @elseif ($t->status === 'trial')
                                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-600">Trial</span>
                                                    @elseif ($t->status === 'suspended')
                                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-600">Suspended</span>
                                                    @else
                                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600">Expired</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4 text-right space-x-2">
                                                    <button wire:click="impersonateTenant({{ $t->id }})" title="Login sebagai Owner" class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 transition">
                                                        <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
                                                    </button>
                                                    <button wire:click="toggleTenantStatus({{ $t->id }})" title="{{ $t->status === 'active' ? 'Suspend Tenant' : 'Aktifkan Tenant' }}" class="p-1.5 rounded-lg border border-slate-200 {{ $t->status === 'active' ? 'text-rose-500 hover:bg-rose-50' : 'text-emerald-500 hover:bg-emerald-50' }} transition">
                                                        <i class="fa-solid {{ $t->status === 'active' ? 'fa-ban' : 'fa-check' }} text-xs"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-8 text-center text-slate-400">Tidak ada tenant yang cocok dengan pencarian.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3 border-t border-slate-100">
                                {{ $allTenants->links() }}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- ################# TAB 3: AKUN TENANT ################# -->
                @if ($activeTab === 'tenant-users')
                    <div class="space-y-5">
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Akun Pengguna Tenant</h1>
                            <p class="text-sm text-slate-500 mt-0.5">Daftar pemilik dan staf lintas seluruh bisnis yang aktif di platform</p>
                        </div>

                        <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-sm">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="p-4 rounded-lg bg-slate-50 border border-slate-200/60">
                                    <div class="text-xs font-semibold text-slate-400 uppercase">Total User Terdaftar</div>
                                    <div class="text-2xl font-bold text-slate-900 mt-1">3.412</div>
                                </div>
                                <div class="p-4 rounded-lg bg-slate-50 border border-slate-200/60">
                                    <div class="text-xs font-semibold text-slate-400 uppercase">User Aktif Hari Ini</div>
                                    <div class="text-2xl font-bold text-emerald-600 mt-1">1.840</div>
                                </div>
                                <div class="p-4 rounded-lg bg-slate-50 border border-slate-200/60">
                                    <div class="text-xs font-semibold text-slate-400 uppercase">Role Terbanyak</div>
                                    <div class="text-2xl font-bold text-orange-500 mt-1">Kasir (64%)</div>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-slate-500">
                                        <tr>
                                            <th class="py-3 px-4 font-semibold">Nama Akun</th>
                                            <th class="py-3 px-4 font-semibold">Tenant Asal</th>
                                            <th class="py-3 px-4 font-semibold">Peran (Role)</th>
                                            <th class="py-3 px-4 font-semibold">Login Terakhir</th>
                                            <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach ($allTenants as $t)
                                            <tr class="hover:bg-slate-50/50 transition">
                                                <td class="py-3 px-4 font-semibold text-slate-900">
                                                    {{ $t->owner_name }}
                                                    <span class="block text-[11px] font-normal text-slate-400">{{ $t->owner_email }}</span>
                                                </td>
                                                <td class="py-3 px-4 text-slate-700 font-medium">{{ $t->name }}</td>
                                                <td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold text-[10px]">Owner</span></td>
                                                <td class="py-3 px-4 text-slate-500">Hari ini, 14:20</td>
                                                <td class="py-3 px-4 text-right">
                                                    <button wire:click="notify('Link reset password dikirim ke {{ $t->owner_email }}')" class="px-2.5 py-1 text-[11px] rounded border border-slate-200 text-slate-700 hover:bg-slate-50 font-medium">Reset Password</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- ################# TAB 4: PAKET & LANGGANAN ################# -->
                @if ($activeTab === 'plans')
                    <div class="space-y-6">
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Paket & Langganan</h1>
                            <p class="text-sm text-slate-500 mt-0.5">Konfigurasi batasan fitur, kuota outlet, dan tarif langganan SaaS</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                            @foreach ($plans as $plan)
                                <div class="bg-white border {{ $plan->is_featured ? 'border-orange-500 ring-1 ring-orange-500' : 'border-slate-200/80' }} rounded-xl p-6 shadow-sm flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-lg font-bold text-slate-900">{{ $plan->name }}</h3>
                                            @if ($plan->badge)
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $plan->is_featured ? 'bg-orange-500 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $plan->badge }}</span>
                                            @endif
                                        </div>

                                        <div class="mt-4">
                                            <span class="text-2xl font-extrabold text-slate-900">Rp {{ number_format($plan->price_monthly, 0, ',', '.') }}</span>
                                            <span class="text-xs text-slate-400">/ bulan</span>
                                        </div>

                                        <div class="mt-6 space-y-2.5 border-t border-slate-100 pt-4 text-xs">
                                            <div class="flex items-center gap-2 text-slate-700">
                                                <i class="fa-solid fa-check text-emerald-500 text-xs"></i>
                                                <span>Max <strong>{{ $plan->limits['max_outlets'] ?? 1 }}</strong> Outlet Cabang</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-slate-700">
                                                <i class="fa-solid fa-check text-emerald-500 text-xs"></i>
                                                <span>Max <strong>{{ $plan->limits['max_users'] ?? 2 }}</strong> Akun Karyawan</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-slate-700">
                                                <i class="fa-solid fa-check text-emerald-500 text-xs"></i>
                                                <span>Max <strong>{{ $plan->limits['max_products'] ?? 50 }}</strong> Master Produk</span>
                                            </div>

                                            @if (is_array($plan->features))
                                                @foreach ($plan->features as $feat)
                                                    <div class="flex items-center gap-2 text-slate-600">
                                                        <i class="fa-solid fa-check text-orange-500 text-xs"></i>
                                                        <span>{{ $feat }}</span>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                                        <span class="text-slate-400">{{ $plan->tenants_count ?? 0 }} Tenant aktif</span>
                                        <button wire:click="notify('Edit paket {{ $plan->name }} siap disesuaikan')" class="text-orange-600 hover:text-orange-700 font-semibold">Ubah Tarif</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- ################# TAB 5: BILLING & INVOICE ################# -->
                @if ($activeTab === 'invoices')
                    <div class="space-y-5">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Tagihan & Invoice Platform</h1>
                                <p class="text-sm text-slate-500 mt-0.5">Riwayat penagihan biaya langganan bulanan dan tahunan seluruh tenant</p>
                            </div>
                            <button wire:click="notify('Proses penerbitan invoice H-7 otomatis berjalan via scheduler')" class="px-3.5 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                                <i class="fa-solid fa-arrows-rotate mr-1 text-slate-400"></i> Sinkronisasi Tagihan
                            </button>
                        </div>

                        <div class="bg-white border border-slate-200/80 rounded-xl shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-slate-500">
                                        <tr>
                                            <th class="py-3 px-4 font-semibold">Nomor Invoice</th>
                                            <th class="py-3 px-4 font-semibold">Nama Tenant</th>
                                            <th class="py-3 px-4 font-semibold">Total Tagihan</th>
                                            <th class="py-3 px-4 font-semibold">Jatuh Tempo</th>
                                            <th class="py-3 px-4 font-semibold">Metode Bayar</th>
                                            <th class="py-3 px-4 font-semibold">Status</th>
                                            <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse ($invoices as $inv)
                                            <tr class="hover:bg-slate-50/50 transition">
                                                <td class="py-3 px-4 font-bold text-slate-900">{{ $inv->invoice_number }}</td>
                                                <td class="py-3 px-4 font-medium text-slate-800">{{ $inv->tenant ? $inv->tenant->name : '-' }}</td>
                                                <td class="py-3 px-4 font-bold text-slate-900">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                                                <td class="py-3 px-4 text-slate-500">{{ $inv->due_date ? $inv->due_date->translatedFormat('d M Y') : '-' }}</td>
                                                <td class="py-3 px-4 text-slate-600">{{ $inv->payment_method ?? 'Menunggu Pembayaran' }}</td>
                                                <td class="py-3 px-4">
                                                    @if ($inv->status === 'paid')
                                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-600">Lunas</span>
                                                    @elseif ($inv->status === 'overdue')
                                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-600">Terlambat</span>
                                                    @else
                                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-600">Belum Bayar</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4 text-right">
                                                    @if ($inv->status !== 'paid')
                                                        <button wire:click="markInvoicePaid({{ $inv->id }})" class="px-2.5 py-1 text-[11px] font-medium rounded bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition">Tandai Lunas</button>
                                                    @else
                                                        <span class="text-slate-400 text-[11px]">Terverifikasi</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="py-6 text-center text-slate-400">Belum ada data invoice.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3 border-t border-slate-100">
                                {{ $invoices->links() }}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- ################# TAB 6: TEMPLATE USAHA ################# -->
                @if ($activeTab === 'business-types')
                    <div class="space-y-6">
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Template Jenis Usaha</h1>
                            <p class="text-sm text-slate-500 mt-0.5">Konfigurasi modul dan preset standar saat tenant baru mendaftar</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach ($businessTypes as $bt)
                                <div class="bg-white border border-slate-200/80 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-lg">
                                                <i class="fa-solid {{ $bt->icon }}"></i>
                                            </div>
                                            <div>
                                                <h3 class="font-bold text-slate-900">{{ $bt->name }}</h3>
                                                <span class="text-[11px] text-slate-400">{{ $bt->tenants_count ?? 0 }} Bisnis Menggunakan</span>
                                            </div>
                                        </div>

                                        <p class="text-xs text-slate-500 mt-4 leading-relaxed">{{ $bt->description }}</p>

                                        <div class="mt-4 p-3 rounded-lg bg-slate-50 border border-slate-100 text-[11px] text-slate-600 space-y-1">
                                            <div class="font-semibold text-slate-700 mb-1">Preset Modul Aktif:</div>
                                            @if ($bt->slug === 'fnb')
                                                <div>• Resep & Bill of Materials (BOM) Gram/ml</div>
                                                <div>• Manajemen Meja & Cetak Dapur/Bar</div>
                                                <div>• Peringatan Bahan Kedaluwarsa</div>
                                            @elseif ($bt->slug === 'retail')
                                                <div>• Barcode Scanning Cepat (EAN-13)</div>
                                                <div>• Varian Ukuran & Warna</div>
                                                <div>• Multi-Gudang & Grosir</div>
                                            @elseif ($bt->slug === 'salon')
                                                <div>• Komisi Pegawai / Kapster</div>
                                                <div>• Layanan Jasa & Perawatan</div>
                                            @elseif ($bt->slug === 'laundry')
                                                <div>• Layanan Kiloan & Satuan</div>
                                                <div>• Pelacakan Status Cuci-Kering-Setrika</div>
                                            @else
                                                <div>• Harga Bertingkat Grosir</div>
                                                <div>• Piutang Penjualan Tempo</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                        <span class="text-emerald-600 font-semibold flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>
                                        <button wire:click="notify('Template {{ $bt->name }} siap dikonfigurasi')" class="text-orange-600 hover:text-orange-700 font-medium">Ubah Preset</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- ################# TAB 7: PENGATURAN GLOBAL ################# -->
                @if ($activeTab === 'settings')
                    <div class="space-y-6">
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pengaturan Global Platform</h1>
                            <p class="text-sm text-slate-500 mt-0.5">Konfigurasi merek, payment gateway, notifikasi WhatsApp, dan nilai default</p>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Card 1: Identitas Platform -->
                            <div class="bg-white border border-slate-200/80 rounded-xl p-6 shadow-sm space-y-4">
                                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">Identitas & Brand Aplikasi</h3>
                                <div class="space-y-3 text-xs">
                                    <div>
                                        <label class="block font-semibold text-slate-700 mb-1">Nama Aplikasi / Platform</label>
                                        <input type="text" value="Lokara POS" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-slate-700 mb-1">Domain Platform Utama</label>
                                        <input type="text" value="lokara.id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-slate-700 mb-1">Mata Uang Utama</label>
                                        <input type="text" value="IDR (Rupiah Indonesia)" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                                    </div>
                                </div>
                                <div class="pt-2 text-right">
                                    <button wire:click="notify('Pengaturan identitas berhasil disimpan')" class="px-4 py-2 bg-orange-500 text-white rounded-lg text-xs font-semibold hover:bg-orange-600 transition">Simpan Perubahan</button>
                                </div>
                            </div>

                            <!-- Card 2: Gateway & Notifikasi -->
                            <div class="bg-white border border-slate-200/80 rounded-xl p-6 shadow-sm space-y-4">
                                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">Integrasi Payment & WhatsApp</h3>
                                <div class="space-y-3 text-xs">
                                    <div>
                                        <label class="block font-semibold text-slate-700 mb-1">Payment Gateway Langganan</label>
                                        <select class="w-full border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                            <option selected>Midtrans Snap API (Production)</option>
                                            <option>Xendit Invoice API</option>
                                            <option>Duitku Payment Gateway</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-slate-700 mb-1">WhatsApp Gateway Provider</label>
                                        <select class="w-full border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                            <option selected>Fonnte API Gateway</option>
                                            <option>Wablas Enterprise</option>
                                            <option>Meta WhatsApp Cloud API Official</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-slate-700 mb-1">Nomor WhatsApp Resmi Dukungan</label>
                                        <input type="text" value="081234567890" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                                    </div>
                                </div>
                                <div class="pt-2 text-right">
                                    <button wire:click="notify('Konfigurasi gateway berhasil diperbarui')" class="px-4 py-2 bg-orange-500 text-white rounded-lg text-xs font-semibold hover:bg-orange-600 transition">Simpan Integrasi</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- ################# TAB 8: SUPPORT & PENGUMUMAN ################# -->
                @if ($activeTab === 'support')
                    <div class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Support & Pengumuman</h1>
                                <p class="text-sm text-slate-500 mt-0.5">Siarkan pesan banner update fitur atau pengumuman pemeliharaan ke seluruh tenant</p>
                            </div>
                            <button wire:click="notify('Form siaran broadcast baru dibuka')" class="px-4 py-2 bg-orange-500 text-white rounded-lg text-xs font-semibold hover:bg-orange-600 transition">
                                <i class="fa-solid fa-bullhorn mr-1.5"></i> Buat Siaran Baru
                            </button>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                            @foreach ($announcements as $ann)
                                <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-sm space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $ann->type === 'update' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                            {{ $ann->type }}
                                        </span>
                                        <span class="text-xs text-slate-400">{{ $ann->created_at ? $ann->created_at->diffForHumans() : 'Hari ini' }}</span>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-900">{{ $ann->title }}</h3>
                                    <p class="text-xs text-slate-600 leading-relaxed">{{ $ann->content }}</p>
                                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                                        <span class="text-emerald-600 font-medium">Tampil di Backoffice Tenant</span>
                                        <button wire:click="notify('Siaran dinonaktifkan')" class="text-rose-500 hover:text-rose-600 font-medium">Hentikan</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- ################# TAB 9: MONITORING & AUDIT ################# -->
                @if ($activeTab === 'monitoring')
                    <div class="space-y-6">
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Monitoring & Audit Log</h1>
                            <p class="text-sm text-slate-500 mt-0.5">Pantau kesehatan antrean sistem, pemakaian database, dan audit jejak operasi platform</p>
                        </div>

                        <!-- System Health Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="p-4 bg-white border border-slate-200/80 rounded-xl shadow-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-slate-500">Database Engine</span>
                                    <i class="fa-solid fa-database text-emerald-500"></i>
                                </div>
                                <div class="text-lg font-bold text-slate-900 mt-2">Normal & Sehat</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Shared Hosting Compatible</div>
                            </div>
                            <div class="p-4 bg-white border border-slate-200/80 rounded-xl shadow-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-slate-500">Queue & Jobs</span>
                                    <i class="fa-solid fa-bolt text-emerald-500"></i>
                                </div>
                                <div class="text-lg font-bold text-slate-900 mt-2">0 Antrean Tertunda</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Driver: Database / Sync</div>
                            </div>
                            <div class="p-4 bg-white border border-slate-200/80 rounded-xl shadow-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-slate-500">Penyimpanan Cache</span>
                                    <i class="fa-solid fa-server text-emerald-500"></i>
                                </div>
                                <div class="text-lg font-bold text-slate-900 mt-2">File / DB Cache</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Responsif < 20ms</div>
                            </div>
                            <div class="p-4 bg-white border border-slate-200/80 rounded-xl shadow-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-slate-500">Backup Terakhir</span>
                                    <i class="fa-solid fa-shield-check text-emerald-500"></i>
                                </div>
                                <div class="text-lg font-bold text-slate-900 mt-2">Otomatis Hari Ini</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">02:00 WIB Sukses</div>
                            </div>
                        </div>

                        <!-- Audit Logs List -->
                        <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-sm">
                            <h3 class="text-sm font-bold text-slate-900 mb-4">Catatan Aktivitas Operasional (Audit Trail)</h3>
                            <div class="space-y-3">
                                @foreach ($allLogs as $log)
                                    <div class="flex items-center justify-between p-3 rounded-lg hover:bg-slate-50 border border-slate-100 text-xs transition">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg {{ $log->icon_color }} flex items-center justify-center flex-shrink-0">
                                                <i class="fa-solid {{ $log->icon }} text-xs"></i>
                                            </div>
                                            <div>
                                                <span class="font-bold text-slate-800">{{ $log->action }}</span>
                                                <span class="text-slate-400 ml-2">({{ $log->description }})</span>
                                            </div>
                                        </div>
                                        <span class="text-slate-400 text-[11px]">{{ $log->created_at ? $log->created_at->diffForHumans() : $log->badge_text }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

            </main>
        </div>
    </div>

    <!-- ================= MODAL TAMBAH TENANT BARU ================= -->
    @if ($showCreateTenantModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 animate-scale-up space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-slate-900">Daftarkan Tenant Baru</h3>
                    <button wire:click="$set('showCreateTenantModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <form wire:submit.prevent="createTenant" class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Bisnis / Toko</label>
                        <input type="text" wire:model.live="newTenantName" placeholder="Contoh: Kopi Ruang Pagi" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                        @error('newTenantName') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Subdomain Slug</label>
                            <div class="flex items-center border border-slate-200 rounded-lg overflow-hidden">
                                <input type="text" wire:model="newTenantSlug" class="w-full px-3 py-2 text-slate-800 focus:outline-none" />
                                <span class="bg-slate-100 px-2.5 py-2 text-slate-400 text-[11px]">.lokara.id</span>
                            </div>
                            @error('newTenantSlug') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kota Asal</label>
                            <input type="text" wire:model="newTenantCity" placeholder="Jakarta Selatan" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Jenis Usaha</label>
                            <select wire:model="newTenantBusinessType" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none">
                                @foreach ($businessTypes as $bt)
                                    <option value="{{ $bt->id }}">{{ $bt->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pilihan Paket</label>
                            <select wire:model="newTenantPlan" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none">
                                @foreach ($plans as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} (Rp {{ number_format($p->price_monthly, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 space-y-2">
                        <div class="font-semibold text-slate-700">Kontak Pemilik (Owner PIC)</div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <input type="text" wire:model="newTenantOwnerName" placeholder="Nama Pemilik" class="border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none" />
                            <input type="email" wire:model="newTenantOwnerEmail" placeholder="Email" class="border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none" />
                            <input type="text" wire:model="newTenantOwnerPhone" placeholder="No. WhatsApp" class="border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none" />
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" wire:click="$set('showCreateTenantModal', false)" class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-semibold transition">Daftarkan Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>

<!-- Chart.js Scripts Initialization -->
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        initCharts();
    });

    document.addEventListener('livewire:navigated', function () {
        initCharts();
    });

    function initCharts() {
        // 1. Line Chart: Pertumbuhan Tenant (Matching Image 1)
        const growthCanvas = document.getElementById('growthChart');
        if (growthCanvas) {
            const ctxGrowth = growthCanvas.getContext('2d');
            
            // Subtle Orange Gradient
            const gradientOrange = ctxGrowth.createLinearGradient(0, 0, 0, 240);
            gradientOrange.addColorStop(0, 'rgba(249, 115, 22, 0.16)');
            gradientOrange.addColorStop(1, 'rgba(249, 115, 22, 0.00)');

            new Chart(ctxGrowth, {
                type: 'line',
                data: {
                    labels: ['Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep'],
                    datasets: [{
                        label: 'Tenant Terdaftar',
                        data: [800, 950, 1050, 1200, 1350, 1450],
                        borderColor: '#F97316',
                        backgroundColor: gradientOrange,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#F97316',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2,
                        pointRadius: 4.5,
                        pointHoverRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#111827',
                            titleFont: { family: 'Inter', size: 12 },
                            bodyFont: { family: 'Inter', size: 12 },
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false,
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { family: 'Inter', size: 11 },
                                color: '#94A3B8'
                            }
                        },
                        y: {
                            min: 0,
                            max: 1600,
                            ticks: {
                                stepSize: 400,
                                font: { family: 'Inter', size: 11 },
                                color: '#94A3B8',
                                callback: function(value) {
                                    return value === 0 ? '0' : (value / 1000 >= 1 ? (value / 1000).toFixed(3).replace('.', '.') : value);
                                }
                            },
                            grid: {
                                color: '#F1F5F9',
                                drawBorder: false
                            }
                        }
                    }
                }
            });
        }

        // 2. Donut Chart: Status Langganan (Matching Image 1)
        const donutCanvas = document.getElementById('subscriptionDonutChart');
        if (donutCanvas) {
            const ctxDonut = donutCanvas.getContext('2d');
            new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: ['Active', 'Trial', 'Suspended', 'Expired'],
                    datasets: [{
                        data: [1102, 96, 28, 58],
                        backgroundColor: [
                            '#F97316', // Active Orange
                            '#BAE6FD', // Trial Soft Sky Blue
                            '#64748B', // Suspended Slate
                            '#1E293B'  // Expired Dark Slate
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '76%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#111827',
                            padding: 8,
                            cornerRadius: 6,
                            bodyFont: { family: 'Inter', size: 11 }
                        }
                    }
                }
            });
        }
    }
</script>
@endpush

