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
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Akun Pengguna Tenant</h1>
                                <p class="text-sm text-slate-500 mt-0.5">Daftar pemilik dan staf lintas seluruh bisnis yang aktif di platform</p>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <button type="button"
                                        wire:click="openCreateUserModal"
                                        class="px-3.5 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold shadow-sm shadow-orange-500/20 flex items-center gap-2 transition cursor-pointer">
                                    <i class="fa-solid fa-user-plus"></i>
                                    <span>Buat Akun Pengguna</span>
                                </button>
                            </div>
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
                                                    <button type="button"
                                                            wire:click="openResetPasswordModal('{{ $t->owner_email }}', '{{ addslashes($t->owner_name) }}', '{{ addslashes($t->name) }}', 'Owner')"
                                                            class="px-2.5 py-1 text-[11px] rounded-lg border border-slate-200 text-slate-700 hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 font-medium transition cursor-pointer">
                                                        Reset Password
                                                    </button>
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
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Paket & Langganan</h1>
                                <p class="text-sm text-slate-500 mt-0.5">Konfigurasi batasan fitur, kuota outlet, dan tarif langganan SaaS</p>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <button type="button"
                                        wire:click="openManageFeaturesModal"
                                        class="px-3.5 py-2 rounded-xl bg-white border border-slate-200/80 hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm flex items-center gap-2 transition hover:border-slate-300">
                                    <i class="fa-solid fa-list-check text-orange-500"></i>
                                    <span>Kelola Master Fitur</span>
                                    <span class="px-1.5 py-0.5 rounded-full bg-orange-100 text-orange-700 text-[10px] font-bold">{{ $allPlatformFeatures->count() }}</span>
                                </button>
                                <button type="button"
                                        wire:click="openCreatePlanModal"
                                        class="px-3.5 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold shadow-sm shadow-orange-500/20 flex items-center gap-2 transition">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>Tambah Paket Baru</span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                            @foreach ($plans as $plan)
                                <div class="bg-white border {{ $plan->is_featured ? 'border-orange-500 ring-2 ring-orange-500/20' : 'border-slate-200/80' }} rounded-xl p-6 shadow-sm flex flex-col justify-between hover:shadow-md transition">
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
                                            @if ($plan->price_yearly > 0)
                                                <div class="text-[11px] text-slate-400 mt-0.5">Rp {{ number_format($plan->price_yearly, 0, ',', '.') }} / tahun</div>
                                            @endif
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

                                            @if (is_array($plan->features) && count($plan->features) > 0)
                                                @foreach ($plan->features as $feat)
                                                    <div class="flex items-center gap-2 text-slate-600">
                                                        <i class="fa-solid fa-check text-orange-500 text-xs"></i>
                                                        <span>{{ $feat }}</span>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="text-[11px] text-slate-400 italic py-1">Belum ada fitur dicentang</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                                        <span class="text-slate-400">{{ $plan->tenants_count ?? 0 }} Tenant aktif</span>
                                        <button type="button"
                                                wire:click="openEditPlanModal({{ $plan->id }})"
                                                class="text-orange-600 hover:text-orange-700 font-semibold flex items-center gap-1.5 hover:underline">
                                            <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                            <span>Ubah Tarif</span>
                                        </button>
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
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Template Jenis Usaha</h1>
                                <p class="text-sm text-slate-500 mt-0.5">Konfigurasi modul dan preset standar saat tenant baru mendaftar</p>
                            </div>
                            <button type="button"
                                    wire:click="openCreateBusinessTypeModal"
                                    class="px-3.5 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold shadow-sm shadow-orange-500/20 flex items-center gap-2 transition self-start sm:self-auto cursor-pointer">
                                <i class="fa-solid fa-plus"></i>
                                <span>Tambah Template Baru</span>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach ($businessTypes as $bt)
                                <div class="bg-white border {{ $bt->is_active ? 'border-slate-200/80' : 'border-slate-200 opacity-80 bg-slate-50/50' }} rounded-xl p-6 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                                    <div>
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-lg shadow-sm">
                                                    <i class="fa-solid {{ $bt->icon }}"></i>
                                                </div>
                                                <div>
                                                    <h3 class="font-bold text-slate-900">{{ $bt->name }}</h3>
                                                    <span class="text-[11px] text-slate-400">{{ $bt->tenants_count ?? 0 }} Bisnis Menggunakan</span>
                                                </div>
                                            </div>
                                            <button type="button"
                                                    wire:click="toggleBusinessTypeActive({{ $bt->id }})"
                                                    title="Klik untuk ubah status aktif/nonaktif"
                                                    class="text-xs transition cursor-pointer">
                                                @if ($bt->is_active)
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center gap-1 hover:bg-emerald-100">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200 flex items-center gap-1 hover:bg-slate-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Nonaktif
                                                    </span>
                                                @endif
                                            </button>
                                        </div>

                                        <p class="text-xs text-slate-500 mt-4 leading-relaxed">{{ $bt->description }}</p>

                                        <div class="mt-4 p-3.5 rounded-xl bg-slate-50/80 border border-slate-100 text-[11px] text-slate-600 space-y-1.5">
                                            <div class="font-semibold text-slate-700 flex items-center justify-between">
                                                <span>Preset Modul Aktif:</span>
                                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-orange-100 text-orange-700 font-bold">
                                                    {{ count($bt->preset_modules) }} modul
                                                </span>
                                            </div>
                                            <div class="space-y-1 pt-0.5">
                                                @forelse ($bt->preset_modules as $mod)
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="text-orange-500 font-bold">•</span>
                                                        <span>{{ $mod }}</span>
                                                    </div>
                                                @empty
                                                    <div class="text-[11px] text-slate-400 italic">Belum ada modul preset</div>
                                                @endforelse
                                            </div>

                                            @if (!empty($bt->default_settings['default_units']))
                                                <div class="pt-2 mt-2 border-t border-slate-200/60 flex items-center gap-1 flex-wrap text-[10px]">
                                                    <span class="text-slate-400">Satuan:</span>
                                                    @foreach ($bt->default_settings['default_units'] as $unit)
                                                        <span class="px-1.5 py-0.5 rounded bg-white border border-slate-200 text-slate-600 font-mono text-[10px]">
                                                            {{ $unit }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                        <span class="text-slate-400 text-[11px]">Slug: <code class="font-mono text-slate-500">{{ $bt->slug }}</code></span>
                                        <button type="button"
                                                wire:click="openEditPresetModal({{ $bt->id }})"
                                                class="text-orange-600 hover:text-orange-700 font-semibold flex items-center gap-1.5 hover:underline cursor-pointer">
                                            <i class="fa-solid fa-sliders text-[11px]"></i>
                                            <span>Ubah Preset</span>
                                        </button>
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
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
             x-data
             @keydown.escape.window="$wire.closeCreateTenantModal()"
             @click.self="$wire.closeCreateTenantModal()">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 animate-scale-up space-y-4"
                 @click.outside="$wire.closeCreateTenantModal()">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-slate-900">Daftarkan Tenant Baru</h3>
                    <button type="button"
                            wire:click="closeCreateTenantModal"
                            @click.prevent="$wire.set('showCreateTenantModal', false)"
                            class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                @if ($errors->any())
                    <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3">
                        <div class="w-6 h-6 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 text-xs font-bold mt-0.5">
                            <i class="fa-solid fa-circle-exclamation"></i>
                        </div>
                        <div>
                            <div class="font-bold text-rose-900">Ada Kolom Wajib yang Belum Diisi!</div>
                            <div class="text-[11px] text-rose-600 mt-0.5">
                                Terdapat <strong>{{ $errors->count() }}</strong> kolom yang wajib diisi atau belum valid. Silakan periksa kolom bergaris merah di bawah.
                            </div>
                        </div>
                    </div>
                @endif

                <form wire:submit.prevent="createTenant" class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">
                            Nama Bisnis / Toko <span class="text-rose-500 font-bold">*</span>
                        </label>
                        <input type="text"
                               wire:model.live="newTenantName"
                               placeholder="Contoh: Kopi Ruang Pagi"
                               class="w-full border rounded-xl px-3 py-2 text-xs text-slate-800 transition focus:outline-none @error('newTenantName') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                        @error('newTenantName')
                            <div class="flex items-center gap-1.5 text-rose-600 font-medium text-[11px] mt-1">
                                <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Subdomain Slug <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <div class="flex items-center border rounded-xl overflow-hidden @error('newTenantSlug') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 @else border-slate-200 @enderror">
                                <input type="text"
                                       wire:model="newTenantSlug"
                                       placeholder="kopi-ruang-pagi"
                                       class="w-full px-3 py-2 text-xs text-slate-800 focus:outline-none bg-transparent" />
                                <span class="bg-slate-100 px-2.5 py-2 text-slate-400 text-[11px] font-mono">.lokara.id</span>
                            </div>
                            @error('newTenantSlug')
                                <div class="flex items-center gap-1.5 text-rose-600 font-medium text-[11px] mt-1">
                                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Kota Asal <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <input type="text"
                                   wire:model="newTenantCity"
                                   placeholder="Jakarta Selatan"
                                   class="w-full border rounded-xl px-3 py-2 text-xs text-slate-800 transition focus:outline-none @error('newTenantCity') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                            @error('newTenantCity')
                                <div class="flex items-center gap-1.5 text-rose-600 font-medium text-[11px] mt-1">
                                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Jenis Usaha <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <select wire:model="newTenantBusinessType"
                                    class="w-full border rounded-xl px-3 py-2 text-xs text-slate-800 transition focus:outline-none @error('newTenantBusinessType') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror">
                                <option value="">-- Pilih Jenis Usaha --</option>
                                @foreach ($businessTypes as $bt)
                                    <option value="{{ $bt->id }}">{{ $bt->name }}</option>
                                @endforeach
                            </select>
                            @error('newTenantBusinessType')
                                <div class="flex items-center gap-1.5 text-rose-600 font-medium text-[11px] mt-1">
                                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Pilihan Paket <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <select wire:model="newTenantPlan"
                                    class="w-full border rounded-xl px-3 py-2 text-xs text-slate-800 transition focus:outline-none @error('newTenantPlan') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror">
                                <option value="">-- Pilih Paket Langganan --</option>
                                @foreach ($plans as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} (Rp {{ number_format($p->price_monthly, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                            @error('newTenantPlan')
                                <div class="flex items-center gap-1.5 text-rose-600 font-medium text-[11px] mt-1">
                                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="font-semibold text-slate-700">
                                Kontak Pemilik (Owner PIC) <span class="text-rose-500 font-bold">*</span>
                            </div>
                            <span class="text-[10px] text-slate-400">Wajib diisi untuk akses login pemilik</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <div>
                                <input type="text"
                                       wire:model="newTenantOwnerName"
                                       placeholder="Nama Pemilik *"
                                       class="w-full border rounded-xl px-3 py-2 text-xs text-slate-800 transition focus:outline-none @error('newTenantOwnerName') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 placeholder-rose-300 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                                @error('newTenantOwnerName')
                                    <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <input type="email"
                                       wire:model="newTenantOwnerEmail"
                                       placeholder="Email *"
                                       class="w-full border rounded-xl px-3 py-2 text-xs text-slate-800 transition focus:outline-none @error('newTenantOwnerEmail') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 placeholder-rose-300 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                                @error('newTenantOwnerEmail')
                                    <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <input type="text"
                                       wire:model="newTenantOwnerPhone"
                                       placeholder="No. WhatsApp *"
                                       class="w-full border rounded-xl px-3 py-2 text-xs text-slate-800 transition focus:outline-none @error('newTenantOwnerPhone') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 placeholder-rose-300 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                                @error('newTenantOwnerPhone')
                                    <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button"
                                wire:click="closeCreateTenantModal"
                                @click.prevent="$wire.set('showCreateTenantModal', false)"
                                class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium text-xs transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs shadow-sm shadow-orange-500/20 transition flex items-center gap-2 cursor-pointer">
                            <span wire:loading.remove wire:target="createTenant">Daftarkan Sekarang</span>
                            <span wire:loading wire:target="createTenant" class="flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-notch fa-spin"></i>
                                <span>Memproses...</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ================= MODAL RESET PASSWORD PENGGUNA ================= -->
    @if ($showResetPasswordModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
             x-data
             @keydown.escape.window="$wire.closeResetPasswordModal()"
             @click.self="$wire.closeResetPasswordModal()">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full p-6 animate-scale-up space-y-4"
                 @click.outside="$wire.closeResetPasswordModal()">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Reset Password Pengguna</h3>
                            <p class="text-[11px] text-slate-400">Atur kata sandi baru untuk login akun tenant</p>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="closeResetPasswordModal"
                            @click.prevent="$wire.set('showResetPasswordModal', false)"
                            class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- Target User Profile Card -->
                <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-xl flex items-center justify-between text-xs">
                    <div class="min-w-0">
                        <div class="font-bold text-slate-900 truncate">{{ $resetUserName }}</div>
                        <div class="text-[11px] text-slate-500 truncate">{{ $resetUserEmail }}</div>
                        <div class="text-[10px] text-slate-400 mt-0.5">Tenant: <span class="font-semibold text-slate-700">{{ $resetTenantName }}</span></div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 font-semibold text-[10px] uppercase shrink-0">
                        {{ $resetUserRole }}
                    </span>
                </div>

                <!-- Error Alert if any -->
                @if ($errors->any())
                    <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-2.5">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm shrink-0 mt-0.5"></i>
                        <div class="text-[11px]">
                            @foreach ($errors->all() as $error)
                                <div>• {{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form wire:submit.prevent="updatePassword" class="space-y-3.5 text-xs">
                    <!-- Quick Random Password Generator -->
                    <div class="flex items-center justify-between bg-amber-50/60 border border-amber-200/80 p-2.5 rounded-xl">
                        <div class="text-[11px] text-amber-800">
                            <i class="fa-solid fa-wand-magic-sparkles text-amber-500 mr-1"></i>
                            <span>Butuh password cepat & aman?</span>
                        </div>
                        <button type="button"
                                wire:click="generateRandomPassword"
                                class="px-2.5 py-1 bg-white border border-amber-300 rounded-lg text-amber-800 hover:bg-amber-100 font-bold text-[11px] transition shadow-2xs cursor-pointer">
                            Generate Acak
                        </button>
                    </div>

                    <!-- Password Baru -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700">
                                Password Baru <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <button type="button"
                                    wire:click="$toggle('showPasswordText')"
                                    class="text-[10px] text-slate-500 hover:text-slate-800 cursor-pointer">
                                {{ $showPasswordText ? 'Sembunyikan' : 'Tampilkan' }}
                            </button>
                        </div>
                        <div class="relative">
                            <input type="{{ $showPasswordText ? 'text' : 'password' }}"
                                   wire:model="newPassword"
                                   placeholder="Minimal 6 karakter..."
                                   class="w-full border rounded-xl px-3.5 py-2.5 text-xs text-slate-800 transition focus:outline-none @error('newPassword') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                        </div>
                        @error('newPassword')
                            <div class="flex items-center gap-1.5 text-rose-600 font-medium text-[11px] mt-1">
                                <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">
                            Ulangi Password Baru <span class="text-rose-500 font-bold">*</span>
                        </label>
                        <input type="{{ $showPasswordText ? 'text' : 'password' }}"
                               wire:model="newPassword_confirmation"
                               placeholder="Ketik ulang password baru..."
                               class="w-full border rounded-xl px-3.5 py-2.5 text-xs text-slate-800 transition focus:outline-none @error('newPassword') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button"
                                wire:click="closeResetPasswordModal"
                                @click.prevent="$wire.set('showResetPasswordModal', false)"
                                class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium text-xs transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs shadow-sm shadow-orange-500/20 transition flex items-center gap-2 cursor-pointer">
                            <span wire:loading.remove wire:target="updatePassword">Simpan Password</span>
                            <span wire:loading wire:target="updatePassword" class="flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-notch fa-spin"></i>
                                <span>Menyimpan...</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ================= MODAL BUAT AKUN PENGGUNA BARU ================= -->
    @if ($showCreateUserModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
             x-data
             @keydown.escape.window="$wire.closeCreateUserModal()"
             @click.self="$wire.closeCreateUserModal()">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 animate-scale-up space-y-4"
                 @click.outside="$wire.closeCreateUserModal()">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Buat Akun Pengguna Baru</h3>
                            <p class="text-[11px] text-slate-400">Daftarkan akun login staf kasir, manajer, atau pemilik tenant</p>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="closeCreateUserModal"
                            @click.prevent="$wire.set('showCreateUserModal', false)"
                            class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                @if ($errors->any())
                    <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-2.5">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm shrink-0 mt-0.5"></i>
                        <div>
                            <div class="font-bold text-rose-900">Terdapat Kesalahan Pengisian:</div>
                            <div class="text-[11px] text-rose-600 mt-0.5">Silakan lengkapi atau perbaiki kolom yang bergaris merah di bawah.</div>
                        </div>
                    </div>
                @endif

                <form wire:submit.prevent="createUser" class="space-y-3.5 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Nama Lengkap <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <input type="text"
                                   wire:model="createUserName"
                                   placeholder="Contoh: Budi Santoso"
                                   class="w-full border rounded-xl px-3.5 py-2.5 text-xs text-slate-800 transition focus:outline-none @error('createUserName') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                            @error('createUserName')
                                <div class="text-rose-600 font-medium text-[11px] mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Alamat Email <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <input type="email"
                                   wire:model="createUserEmail"
                                   placeholder="budi@kopiruangpagi.id"
                                   class="w-full border rounded-xl px-3.5 py-2.5 text-xs text-slate-800 transition focus:outline-none @error('createUserEmail') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                            @error('createUserEmail')
                                <div class="text-rose-600 font-medium text-[11px] mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Tenant Asal <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <select wire:model="createUserTenantId"
                                    class="w-full border rounded-xl px-3 py-2 text-xs text-slate-800 transition focus:outline-none @error('createUserTenantId') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror">
                                <option value="">-- Pilih Tenant --</option>
                                @foreach ($allTenants as $tn)
                                    <option value="{{ $tn->id }}">{{ $tn->name }}</option>
                                @endforeach
                            </select>
                            @error('createUserTenantId')
                                <div class="text-rose-600 font-medium text-[11px] mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Peran (Role) <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <select wire:model="createUserRole"
                                    class="w-full border rounded-xl px-3 py-2 text-xs text-slate-800 transition focus:outline-none @error('createUserRole') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror">
                                <option value="cashier">Kasir (Layar POS)</option>
                                <option value="manager">Manajer Toko</option>
                                <option value="owner">Pemilik (Owner)</option>
                            </select>
                            @error('createUserRole')
                                <div class="text-rose-600 font-medium text-[11px] mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="bg-amber-50/60 border border-amber-200/80 p-2.5 rounded-xl flex items-center justify-between">
                        <span class="text-[11px] text-amber-800">
                            <i class="fa-solid fa-key text-amber-500 mr-1"></i>
                            Buat password otomatis?
                        </span>
                        <button type="button"
                                wire:click="generateCreateUserPassword"
                                class="px-2.5 py-1 bg-white border border-amber-300 rounded-lg text-amber-800 hover:bg-amber-100 font-bold text-[11px] transition shadow-2xs cursor-pointer">
                            Generate Acak
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Password Akun <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <input type="password"
                                   wire:model="createUserPassword"
                                   placeholder="Minimal 6 karakter..."
                                   class="w-full border rounded-xl px-3.5 py-2.5 text-xs text-slate-800 transition focus:outline-none @error('createUserPassword') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                            @error('createUserPassword')
                                <div class="text-rose-600 font-medium text-[11px] mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Ulangi Password <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <input type="password"
                                   wire:model="createUserPassword_confirmation"
                                   placeholder="Ulangi password..."
                                   class="w-full border rounded-xl px-3.5 py-2.5 text-xs text-slate-800 transition focus:outline-none @error('createUserPassword') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button"
                                wire:click="closeCreateUserModal"
                                @click.prevent="$wire.set('showCreateUserModal', false)"
                                class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium text-xs transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs shadow-sm shadow-orange-500/20 transition flex items-center gap-2 cursor-pointer">
                            <span wire:loading.remove wire:target="createUser">Buat Akun Sekarang</span>
                            <span wire:loading wire:target="createUser" class="flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-notch fa-spin"></i>
                                <span>Menyimpan...</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ================= MODAL UBAH TARIF DINAMIS & CHECKLIST FITUR ================= -->
    @if ($showEditPlanModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto"
             x-data
             @keydown.escape.window="$wire.closeEditPlanModal()"
             @click.self="$wire.closeEditPlanModal()">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-2xl w-full my-8 animate-scale-up flex flex-col max-h-[90vh]"
                 @click.outside="$wire.closeEditPlanModal()">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 shrink-0 bg-slate-50/50 rounded-t-2xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-base shadow-sm">
                            <i class="fa-solid fa-credit-card"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Ubah Tarif & Checklist Fitur</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Paket: <span class="font-semibold text-orange-600">{{ $editPlanName }}</span> • Sesuaikan harga dan checklist fitur</p>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="closeEditPlanModal"
                            @click.prevent="$wire.set('showEditPlanModal', false)"
                            class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <!-- Alert Validation Error -->
                @if ($errors->any())
                    <div class="mx-6 mt-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm mt-0.5 shrink-0"></i>
                        <div>
                            <div class="font-bold text-rose-900">Periksa Kembali Data Formulir</div>
                            <div class="text-[11px] text-rose-600 mt-0.5">Terdapat kolom yang belum valid. Silakan tinjau kembali input bertanda merah.</div>
                        </div>
                    </div>
                @endif

                <!-- Modal Body (Scrollable) -->
                <div class="overflow-y-auto px-6 py-5 space-y-6 flex-1 text-xs text-slate-700">
                    <!-- SECTION 1: TARIF & IDENTITAS PAKET -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                            <i class="fa-solid fa-tag text-orange-500 text-xs"></i>
                            <span class="font-bold text-slate-900 text-xs uppercase tracking-wider">Tarif & Kuota Paket</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Nama Paket -->
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">
                                    Nama Paket <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <input type="text"
                                       wire:model="editPlanName"
                                       placeholder="Contoh: Basic, Pro, Business"
                                       class="w-full border rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none @error('editPlanName') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                                @error('editPlanName')
                                    <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Badge Label -->
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">
                                    Badge Label <span class="text-slate-400 font-normal">(Opsional)</span>
                                </label>
                                <input type="text"
                                       wire:model="editPlanBadge"
                                       placeholder="Contoh: Terpopuler, Pemula, Enterprise"
                                       class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                            </div>
                        </div>

                        <!-- Tarif Dinamis Bulanan & Tahunan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-orange-50/40 p-4 rounded-xl border border-orange-100">
                            <div>
                                <label class="block font-semibold text-slate-800 mb-1">
                                    Tarif Bulanan (Rp) <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-2 text-slate-400 font-bold text-xs">Rp</span>
                                    <input type="number"
                                           step="1000"
                                           wire:model.live.debounce.300ms="editPlanPriceMonthly"
                                           placeholder="299000"
                                           class="w-full pl-10 pr-3.5 py-2 border rounded-xl text-xs font-bold text-slate-900 bg-white focus:outline-none @error('editPlanPriceMonthly') border-rose-400 ring-1 ring-rose-400 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                                </div>
                                <div class="text-[11px] text-orange-600 font-medium mt-1">
                                    Preview: <strong>Rp {{ number_format((float) str_replace(['.', ',', ' '], '', (string)($editPlanPriceMonthly ?: 0)), 0, ',', '.') }}</strong> / bulan
                                </div>
                                @error('editPlanPriceMonthly')
                                    <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-800 mb-1">
                                    Tarif Tahunan (Rp) <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-2 text-slate-400 font-bold text-xs">Rp</span>
                                    <input type="number"
                                           step="1000"
                                           wire:model.live.debounce.300ms="editPlanPriceYearly"
                                           placeholder="2990000"
                                           class="w-full pl-10 pr-3.5 py-2 border rounded-xl text-xs font-bold text-slate-900 bg-white focus:outline-none @error('editPlanPriceYearly') border-rose-400 ring-1 ring-rose-400 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                                </div>
                                <div class="text-[11px] text-slate-500 mt-1">
                                    Preview: <strong>Rp {{ number_format((float) str_replace(['.', ',', ' '], '', (string)($editPlanPriceYearly ?: 0)), 0, ',', '.') }}</strong> / tahun
                                </div>
                                @error('editPlanPriceYearly')
                                    <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Batasan Kuota (Limits) -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">
                                    Max Outlet <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <input type="number"
                                       min="1"
                                       wire:model="editPlanMaxOutlets"
                                       class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                                @error('editPlanMaxOutlets')
                                    <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">
                                    Max Karyawan <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <input type="number"
                                       min="1"
                                       wire:model="editPlanMaxUsers"
                                       class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                                @error('editPlanMaxUsers')
                                    <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">
                                    Max Produk <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <input type="number"
                                       min="1"
                                       wire:model="editPlanMaxProducts"
                                       class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                                @error('editPlanMaxProducts')
                                    <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Opsi Unggulan / Featured -->
                        <div class="flex flex-wrap items-center gap-6 pt-2">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox"
                                       wire:model="editPlanIsFeatured"
                                       class="w-4 h-4 text-orange-500 rounded border-slate-300 focus:ring-orange-400 cursor-pointer">
                                <span class="text-xs font-semibold text-slate-700">Tandai sebagai Paket Unggulan / Terpopuler</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox"
                                       wire:model="editPlanIsActive"
                                       class="w-4 h-4 text-orange-500 rounded border-slate-300 focus:ring-orange-400 cursor-pointer">
                                <span class="text-xs font-semibold text-slate-700">Paket Aktif & Tersedia</span>
                            </label>
                        </div>
                    </div>

                    <!-- SECTION 2: CHECKLIST FITUR PAKET -->
                    <div class="space-y-3 pt-3 border-t border-slate-100">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-list-check text-orange-500 text-xs"></i>
                                <span class="font-bold text-slate-900 text-xs uppercase tracking-wider">Checklist Fitur Paket</span>
                                <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 font-bold text-[11px]">
                                    {{ count($editPlanFeatures) }} dari {{ $allPlatformFeatures->count() }} dipilih
                                </span>
                            </div>
                            <div class="flex items-center gap-2 text-xs">
                                <button type="button" wire:click="selectAllPlanFeatures" class="text-orange-600 hover:text-orange-700 font-semibold hover:underline">
                                    Pilih Semua
                                </button>
                                <span class="text-slate-300">•</span>
                                <button type="button" wire:click="deselectAllPlanFeatures" class="text-slate-500 hover:text-slate-700 hover:underline">
                                    Hapus Pilihan
                                </button>
                            </div>
                        </div>

                        <!-- Quick Inline Tambah Fitur Baru Bar -->
                        <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-xl space-y-2">
                            <div class="text-[11px] font-semibold text-slate-600 flex items-center gap-1.5">
                                <i class="fa-solid fa-plus-circle text-orange-500"></i>
                                <span>Tambah Fitur Baru Langsung ke Checklist:</span>
                            </div>
                            <div class="flex flex-col sm:flex-row items-center gap-2">
                                <input type="text"
                                       wire:model="quickAddFeatureName"
                                       wire:keydown.enter.prevent="addFeatureInline"
                                       placeholder="Ketik nama fitur baru (misal: Integrasi GrabFood)..."
                                       class="w-full sm:flex-1 border border-slate-200 rounded-lg px-3 py-1.5 text-xs text-slate-800 bg-white focus:outline-none focus:ring-1 focus:ring-orange-500" />
                                <select wire:model="quickAddFeatureCategory"
                                        class="w-full sm:w-44 border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-slate-700 bg-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                    <option value="Kasir & Transaksi">Kasir & Transaksi</option>
                                    <option value="Inventori">Inventori</option>
                                    <option value="Laporan">Laporan</option>
                                    <option value="Paket Bundling">Paket Bundling</option>
                                    <option value="Platform">Platform</option>
                                    <option value="Integrasi">Integrasi</option>
                                    <option value="Layanan & Bantuan">Layanan & Bantuan</option>
                                </select>
                                <button type="button"
                                        wire:click="addFeatureInline"
                                        class="w-full sm:w-auto px-3.5 py-1.5 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs shadow-sm flex items-center justify-center gap-1.5 shrink-0 transition">
                                    <i class="fa-solid fa-check text-[10px]"></i>
                                    <span>Tambah & Centang</span>
                                </button>
                            </div>
                            @error('quickAddFeatureName')
                                <div class="text-rose-600 font-medium text-[10px]">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Checklist Items Container -->
                        <div class="border border-slate-200 rounded-xl overflow-hidden bg-white">
                            <div class="p-3 bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-semibold text-slate-500 flex items-center justify-between">
                                <span>Centang fitur yang aktif pada paket ini</span>
                                <span>Aksi Item</span>
                            </div>
                            <div class="max-h-64 overflow-y-auto divide-y divide-slate-100 p-1">
                                @forelse ($allPlatformFeatures as $feat)
                                    @php
                                        $isChecked = in_array($feat->name, $editPlanFeatures);
                                    @endphp
                                    <div class="flex items-center justify-between p-2.5 rounded-lg transition {{ $isChecked ? 'bg-orange-50/60' : 'hover:bg-slate-50' }}">
                                        <label class="flex items-center gap-3 flex-1 cursor-pointer select-none">
                                            <input type="checkbox"
                                                   wire:click="toggleFeatureInPlan('{{ $feat->name }}')"
                                                   {{ $isChecked ? 'checked' : '' }}
                                                   class="w-4 h-4 text-orange-500 rounded border-slate-300 focus:ring-orange-400 focus:ring-offset-0 cursor-pointer">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-semibold {{ $isChecked ? 'text-slate-900' : 'text-slate-600' }}">
                                                    {{ $feat->name }}
                                                </span>
                                                @if ($feat->category)
                                                    <span class="text-[10px] px-2 py-0.5 rounded-full {{ $isChecked ? 'bg-orange-200/70 text-orange-800' : 'bg-slate-100 text-slate-500' }}">
                                                        {{ $feat->category }}
                                                    </span>
                                                @endif
                                            </div>
                                        </label>

                                        <!-- Aksi Item: Edit nama fitur & Hapus fitur -->
                                        <div class="flex items-center gap-1 ml-3 shrink-0">
                                            <button type="button"
                                                    wire:click="openEditFeatureModal({{ $feat->id }})"
                                                    title="Edit nama fitur"
                                                    class="p-1.5 text-slate-400 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition">
                                                <i class="fa-solid fa-pen text-[10px]"></i>
                                            </button>
                                            <button type="button"
                                                    wire:click="deleteFeature({{ $feat->id }})"
                                                    wire:confirm="Hapus fitur '{{ $feat->name }}' dari seluruh paket dan platform?"
                                                    title="Hapus fitur dari platform"
                                                    class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                                                <i class="fa-solid fa-trash-can text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-6 text-center text-slate-400 italic">
                                        Belum ada fitur terdaftar. Silakan tambahkan fitur di atas.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 rounded-b-2xl flex items-center justify-end gap-3 shrink-0">
                    <button type="button"
                            wire:click="closeEditPlanModal"
                            @click.prevent="$wire.set('showEditPlanModal', false)"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-semibold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button"
                            wire:click="savePlan"
                            class="px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs shadow-sm shadow-orange-500/20 transition flex items-center gap-2">
                        <span wire:loading.remove wire:target="savePlan">Simpan Perubahan Tarif & Fitur</span>
                        <span wire:loading wire:target="savePlan" class="flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-notch fa-spin"></i>
                            <span>Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= MODAL KELOLA MASTER FITUR PLATFORM ================= -->
    @if ($showManageFeaturesModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto"
             x-data
             @keydown.escape.window="$wire.closeManageFeaturesModal()"
             @click.self="$wire.closeManageFeaturesModal()">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-2xl w-full my-8 animate-scale-up flex flex-col max-h-[90vh]"
                 @click.outside="$wire.closeManageFeaturesModal()">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 shrink-0 bg-slate-50/50 rounded-t-2xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-base shadow-sm">
                            <i class="fa-solid fa-list-check"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Kelola Master Fitur SaaS</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Daftar seluruh fitur yang dapat dicentang pada paket langganan</p>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="closeManageFeaturesModal"
                            @click.prevent="$wire.set('showManageFeaturesModal', false)"
                            class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <!-- Body (Scrollable) -->
                <div class="overflow-y-auto px-6 py-5 space-y-5 flex-1 text-xs">
                    <!-- Form Tambah Fitur Baru -->
                    <div class="p-4 bg-orange-50/50 border border-orange-200/70 rounded-xl space-y-3">
                        <div class="font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-plus text-orange-500"></i>
                            <span>Tambah Fitur Baru ke Master Platform</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5">
                            <div class="sm:col-span-7">
                                <input type="text"
                                       wire:model="newFeatureName"
                                       wire:keydown.enter.prevent="createMasterFeature"
                                       placeholder="Nama fitur (contoh: Multi-Currency, QRIS Dinamis)..."
                                       class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-800 bg-white focus:outline-none focus:ring-1 focus:ring-orange-500" />
                                @error('newFeatureName')
                                    <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="sm:col-span-3">
                                <select wire:model="newFeatureCategory"
                                        class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 bg-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                    <option value="Kasir & Transaksi">Kasir & Transaksi</option>
                                    <option value="Inventori">Inventori</option>
                                    <option value="Laporan">Laporan</option>
                                    <option value="Paket Bundling">Paket Bundling</option>
                                    <option value="Platform">Platform</option>
                                    <option value="Integrasi">Integrasi</option>
                                    <option value="Layanan & Bantuan">Layanan & Bantuan</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <button type="button"
                                        wire:click="createMasterFeature"
                                        class="w-full h-full py-2 px-3 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs shadow-sm flex items-center justify-center gap-1.5 transition">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                    <span>Tambah</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Daftar Fitur -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 font-semibold text-slate-700 flex items-center justify-between">
                            <span>Daftar Fitur ({{ $allPlatformFeatures->count() }})</span>
                            <span class="text-slate-400 font-normal text-[11px]">Digunakan di Paket</span>
                        </div>
                        <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                            @forelse ($allPlatformFeatures as $feat)
                                <div class="px-4 py-3 flex items-center justify-between hover:bg-slate-50/70 transition">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-slate-800">{{ $feat->name }}</span>
                                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-medium">
                                                {{ $feat->category ?? 'Umum' }}
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap gap-1 items-center">
                                            @php
                                                $usedPlans = $featurePlanUsage[$feat->name] ?? [];
                                            @endphp
                                            @if (count($usedPlans) > 0)
                                                @foreach ($usedPlans as $pName)
                                                    <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[10px] font-semibold border border-emerald-200/60">
                                                        {{ $pName }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-[10px] text-slate-400 italic">Belum digunakan di paket manapun</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-1">
                                        <button type="button"
                                                wire:click="openEditFeatureModal({{ $feat->id }})"
                                                class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:text-orange-600 hover:border-orange-200 text-xs font-semibold flex items-center gap-1 transition">
                                            <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                            <span>Edit</span>
                                        </button>
                                        <button type="button"
                                                wire:click="deleteFeature({{ $feat->id }})"
                                                wire:confirm="Yakin ingin menghapus fitur '{{ $feat->name }}'? Fitur akan dicabut dari seluruh paket yang memakainya."
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                                title="Hapus Fitur">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-slate-400 italic">Belum ada fitur terdaftar.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 rounded-b-2xl flex items-center justify-end shrink-0">
                    <button type="button"
                            wire:click="closeManageFeaturesModal"
                            @click.prevent="$wire.set('showManageFeaturesModal', false)"
                            class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs transition cursor-pointer">
                        Selesai
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= MODAL EDIT NAMA FITUR ================= -->
    @if ($showEditFeatureModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
             x-data
             @keydown.escape.window="$wire.closeEditFeatureModal()"
             @click.self="$wire.closeEditFeatureModal()">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full p-6 animate-scale-up space-y-4"
                 @click.outside="$wire.closeEditFeatureModal()">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-xs">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Edit Fitur Platform</h3>
                    </div>
                    <button type="button"
                            wire:click="closeEditFeatureModal"
                            @click.prevent="$wire.set('showEditFeatureModal', false)"
                            class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <div class="space-y-3.5 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">
                            Nama Fitur <span class="text-rose-500 font-bold">*</span>
                        </label>
                        <input type="text"
                               wire:model="editingFeatureName"
                               class="w-full border rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none @error('editingFeatureName') border-rose-400 ring-1 ring-rose-400 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                        @error('editingFeatureName')
                            <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kategori Fitur</label>
                        <select wire:model="editingFeatureCategory"
                                class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 bg-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <option value="Kasir & Transaksi">Kasir & Transaksi</option>
                            <option value="Inventori">Inventori</option>
                            <option value="Laporan">Laporan</option>
                            <option value="Paket Bundling">Paket Bundling</option>
                            <option value="Platform">Platform</option>
                            <option value="Integrasi">Integrasi</option>
                            <option value="Layanan & Bantuan">Layanan & Bantuan</option>
                        </select>
                    </div>

                    <div class="p-3 bg-amber-50 border border-amber-200/80 rounded-xl text-amber-800 text-[11px]">
                        <i class="fa-solid fa-circle-info mr-1 text-amber-600"></i>
                        Mengubah nama fitur ini akan otomatis memperbarui daftar fitur di seluruh paket yang memakainya.
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button"
                            wire:click="closeEditFeatureModal"
                            @click.prevent="$wire.set('showEditFeatureModal', false)"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button"
                            wire:click="updateFeature"
                            class="px-5 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs shadow-sm transition cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= MODAL TAMBAH PAKET BARU ================= -->
    @if ($showCreatePlanModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto"
             x-data
             @keydown.escape.window="$wire.closeCreatePlanModal()"
             @click.self="$wire.closeCreatePlanModal()">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-2xl w-full my-8 animate-scale-up flex flex-col max-h-[90vh]"
                 @click.outside="$wire.closeCreatePlanModal()">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 shrink-0 bg-slate-50/50 rounded-t-2xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-base shadow-sm">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Tambah Paket Baru</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Daftarkan paket langganan SaaS baru untuk platform Lokara</p>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="closeCreatePlanModal"
                            @click.prevent="$wire.set('showCreatePlanModal', false)"
                            class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="overflow-y-auto px-6 py-5 space-y-5 flex-1 text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Nama Paket <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <input type="text"
                                   wire:model="newPlanName"
                                   placeholder="Contoh: Enterprise Plus"
                                   class="w-full border rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none @error('newPlanName') border-rose-400 ring-1 ring-rose-400 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                            @error('newPlanName')
                                <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Badge Label</label>
                            <input type="text"
                                   wire:model="newPlanBadge"
                                   placeholder="Contoh: Khusus, Custom"
                                   class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-orange-50/40 p-4 rounded-xl border border-orange-100">
                        <div>
                            <label class="block font-semibold text-slate-800 mb-1">
                                Tarif Bulanan (Rp) <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <input type="number"
                                   step="1000"
                                   wire:model="newPlanPriceMonthly"
                                   class="w-full px-3.5 py-2 border rounded-xl text-xs font-bold text-slate-900 bg-white focus:outline-none focus:ring-1 focus:ring-orange-500" />
                            @error('newPlanPriceMonthly')
                                <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-800 mb-1">
                                Tarif Tahunan (Rp) <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <input type="number"
                                   step="1000"
                                   wire:model="newPlanPriceYearly"
                                   class="w-full px-3.5 py-2 border rounded-xl text-xs font-bold text-slate-900 bg-white focus:outline-none focus:ring-1 focus:ring-orange-500" />
                            @error('newPlanPriceYearly')
                                <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Max Outlet</label>
                            <input type="number" min="1" wire:model="newPlanMaxOutlets" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs" />
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Max Karyawan</label>
                            <input type="number" min="1" wire:model="newPlanMaxUsers" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs" />
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Max Produk</label>
                            <input type="number" min="1" wire:model="newPlanMaxProducts" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block font-semibold text-slate-700">Checklist Fitur Awal</label>
                        <div class="max-h-48 overflow-y-auto border border-slate-200 rounded-xl p-3 grid grid-cols-1 sm:grid-cols-2 gap-2 bg-white">
                            @foreach ($allPlatformFeatures as $f)
                                <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                    <input type="checkbox"
                                           wire:click="toggleFeatureInNewPlan('{{ $f->name }}')"
                                           {{ in_array($f->name, $newPlanFeatures) ? 'checked' : '' }}
                                           class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                    <span class="text-slate-800">{{ $f->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 rounded-b-2xl flex items-center justify-end gap-3 shrink-0">
                    <button type="button"
                            wire:click="closeCreatePlanModal"
                            @click.prevent="$wire.set('showCreatePlanModal', false)"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-semibold text-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="button"
                            wire:click="createPlan"
                            class="px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs shadow-sm cursor-pointer">
                        Buat Paket Sekarang
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= MODAL UBAH PRESET TEMPLATE USAHA ================= -->
    @if ($showEditPresetModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-2xl w-full my-8 animate-scale-up flex flex-col max-h-[90vh]">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 shrink-0 bg-slate-50/50 rounded-t-2xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-base shadow-sm">
                            <i class="fa-solid fa-sliders"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Ubah Preset Template Usaha</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Template: <span class="font-semibold text-orange-600">{{ $editBtName }}</span> • Atur modul bawaan & konfigurasi tenant</p>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="closeEditPresetModal"
                            class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <!-- Alert Validation Error -->
                @if ($errors->any())
                    <div class="mx-6 mt-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm mt-0.5 shrink-0"></i>
                        <div>
                            <div class="font-bold text-rose-900">Periksa Kembali Data Formulir</div>
                            <div class="text-[11px] text-rose-600 mt-0.5">Terdapat kolom wajib yang belum valid.</div>
                        </div>
                    </div>
                @endif

                <!-- Body (Scrollable) -->
                <div class="overflow-y-auto px-6 py-5 space-y-6 flex-1 text-xs text-slate-700">
                    <!-- SECTION 1: INFORMASI UTAMA TEMPLATE -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                            <i class="fa-solid fa-store text-orange-500 text-xs"></i>
                            <span class="font-bold text-slate-900 text-xs uppercase tracking-wider">Identitas Template</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                            <div class="sm:col-span-8">
                                <label class="block font-semibold text-slate-700 mb-1">
                                    Nama Template Usaha <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <input type="text"
                                       wire:model="editBtName"
                                       class="w-full border rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none @error('editBtName') border-rose-400 ring-1 ring-rose-400 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                                @error('editBtName')
                                    <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="sm:col-span-4">
                                <label class="block font-semibold text-slate-700 mb-1">Ikon FontAwesome</label>
                                <div class="flex items-center gap-2">
                                    <div class="w-9 h-9 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-sm shrink-0 border border-orange-100">
                                        <i class="fa-solid {{ $editBtIcon }}"></i>
                                    </div>
                                    <select wire:model.live="editBtIcon"
                                            class="w-full border border-slate-200 rounded-xl px-2.5 py-2 text-xs text-slate-800 bg-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                        <option value="fa-utensils">F&B / Resto (fa-utensils)</option>
                                        <option value="fa-store">Retail / Toko (fa-store)</option>
                                        <option value="fa-scissors">Salon / Barbershop (fa-scissors)</option>
                                        <option value="fa-shirt">Laundry / Jasa (fa-shirt)</option>
                                        <option value="fa-boxes-stacked">Grosir / Distributor (fa-boxes-stacked)</option>
                                        <option value="fa-mug-hot">Kafe / Coffee Shop (fa-mug-hot)</option>
                                        <option value="fa-cart-shopping">Minimarket (fa-cart-shopping)</option>
                                        <option value="fa-spa">Spa & Beauty (fa-spa)</option>
                                        <option value="fa-car-side">Bengkel / Otomotif (fa-car-side)</option>
                                        <option value="fa-paw">Pet Shop (fa-paw)</option>
                                        <option value="fa-pills">Apotek / Farmasi (fa-pills)</option>
                                        <option value="fa-bowl-food">Kuliner / Fast Food (fa-bowl-food)</option>
                                        <option value="fa-shapes">Lainnya (fa-shapes)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Deskripsi Template <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <textarea wire:model="editBtDescription"
                                      rows="2"
                                      class="w-full border rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none @error('editBtDescription') border-rose-400 ring-1 ring-rose-400 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror"></textarea>
                            @error('editBtDescription')
                                <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox"
                                       wire:model="editBtIsActive"
                                       class="w-4 h-4 text-orange-500 rounded border-slate-300 focus:ring-orange-400 cursor-pointer">
                                <span class="text-xs font-semibold text-slate-800">Status Template Aktif & Tersedia untuk Registrasi Tenant</span>
                            </label>
                        </div>
                    </div>

                    <!-- SECTION 2: PRESET MODUL AKTIF (LIST & TAMBAH/HAPUS) -->
                    <div class="space-y-3 pt-3 border-t border-slate-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-layer-group text-orange-500 text-xs"></i>
                                <span class="font-bold text-slate-900 text-xs uppercase tracking-wider">Preset Modul Bawaan</span>
                                <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 font-bold text-[11px]">
                                    {{ count($editBtPresetModules) }} aktif
                                </span>
                            </div>
                        </div>

                        <!-- Bar Tambah Modul Preset Baru -->
                        <div class="flex items-center gap-2 p-2.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                            <input type="text"
                                   wire:model="newPresetModuleName"
                                   wire:keydown.enter.prevent="addPresetModule"
                                   placeholder="+ Ketik modul baru (misal: Reservasi Meja, Integrasi KDS)..."
                                   class="flex-1 border border-slate-200 rounded-lg px-3 py-1.5 text-xs text-slate-800 bg-white focus:outline-none focus:ring-1 focus:ring-orange-500" />
                            <button type="button"
                                    wire:click="addPresetModule"
                                    class="px-3.5 py-1.5 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs shadow-sm flex items-center gap-1.5 shrink-0 transition cursor-pointer">
                                <i class="fa-solid fa-plus text-[10px]"></i>
                                <span>Tambah Modul</span>
                            </button>
                        </div>
                        @error('newPresetModuleName')
                            <div class="text-rose-600 font-medium text-[10px]">{{ $message }}</div>
                        @enderror

                        <!-- Daftar Modul yang Sedang Aktif dalam Preset -->
                        <div class="space-y-1.5">
                            <div class="text-[11px] font-semibold text-slate-500">Daftar Modul yang Aktif pada Template Ini:</div>
                            <div class="border border-slate-200 rounded-xl divide-y divide-slate-100 max-h-48 overflow-y-auto bg-white">
                                @forelse ($editBtPresetModules as $mod)
                                    <div class="px-3 py-2 flex items-center justify-between hover:bg-slate-50/60 transition">
                                        <div class="flex items-center gap-2 text-xs font-semibold text-slate-800">
                                            <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
                                            <span>{{ $mod }}</span>
                                        </div>
                                        <button type="button"
                                                wire:click="removePresetModule('{{ $mod }}')"
                                                title="Hapus dari preset"
                                                class="text-slate-400 hover:text-rose-600 p-1 transition cursor-pointer">
                                            <i class="fa-solid fa-xmark text-xs"></i>
                                        </button>
                                    </div>
                                @empty
                                    <div class="p-4 text-center text-slate-400 italic text-xs">
                                        Belum ada modul aktif pada preset ini. Klik pilihan modul di bawah untuk menambahkan.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Pilihan Cepat Modul Populer -->
                        <div class="p-3 bg-orange-50/40 border border-orange-100 rounded-xl space-y-2">
                            <div class="text-[11px] font-bold text-slate-700 flex items-center gap-1.5">
                                <i class="fa-solid fa-lightbulb text-orange-500"></i>
                                <span>Pilihan Cepat Modul Standar (Klik untuk Aktifkan / Nonaktifkan):</span>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                @php
                                    $presetChoices = [
                                        'Resep & Bill of Materials (BOM) Gram/ml',
                                        'Manajemen Meja & Cetak Dapur/Bar',
                                        'Peringatan Bahan Kedaluwarsa',
                                        'Barcode Scanning Cepat (EAN-13)',
                                        'Varian Ukuran & Warna',
                                        'Multi-Gudang & Grosir',
                                        'Komisi Pegawai / Kapster',
                                        'Layanan Jasa & Perawatan',
                                        'Layanan Kiloan & Satuan',
                                        'Pelacakan Status Cuci-Kering-Setrika',
                                        'Harga Bertingkat Grosir',
                                        'Piutang Penjualan Tempo',
                                        'Multi-Modifier & Topping Tambahan',
                                        'Cetak Struk Dapur / KDS',
                                        'Reservasi & Jadwal Booking',
                                    ];
                                @endphp
                                @foreach ($presetChoices as $choice)
                                    @php $activeInPreset = in_array($choice, $editBtPresetModules); @endphp
                                    <button type="button"
                                            wire:click="togglePresetModule('{{ $choice }}')"
                                            class="px-2.5 py-1 rounded-lg text-[11px] font-semibold border transition cursor-pointer flex items-center gap-1.5 {{ $activeInPreset ? 'bg-orange-500 text-white border-orange-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:border-orange-300 hover:text-slate-900' }}">
                                        <i class="fa-solid {{ $activeInPreset ? 'fa-check' : 'fa-plus' }} text-[9px]"></i>
                                        <span>{{ $choice }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: SATUAN STANDAR (DEFAULT UNITS) -->
                    <div class="space-y-2 pt-3 border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-scale-balanced text-orange-500 text-xs"></i>
                            <span class="font-bold text-slate-900 text-xs uppercase tracking-wider">Satuan Standar Produk (Default Units)</span>
                        </div>
                        <p class="text-[11px] text-slate-500">Satuan dasar yang otomatis tersedia saat tenant dengan template ini membuat produk baru.</p>
                        <input type="text"
                               wire:model="editBtDefaultUnits"
                               placeholder="Contoh: gram, ml, cup, porsi, pcs"
                               class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500 font-mono" />
                        <div class="text-[10px] text-slate-400">Pisahkan dengan tanda koma (contoh: <code>pcs, pack, dus, lusin</code>).</div>
                    </div>

                    <!-- SECTION 4: TOGGLE FITUR SISTEM SPESIFIK -->
                    <div class="space-y-2.5 pt-3 border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-toggle-on text-orange-500 text-xs"></i>
                            <span class="font-bold text-slate-900 text-xs uppercase tracking-wider">Flag Fitur Sistem</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 bg-slate-50/70 p-3.5 rounded-xl border border-slate-200/70">
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                <input type="checkbox" wire:model="editBtHasRecipes" class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                <span>Modul Resep & Bahan Baku (BOM)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                <input type="checkbox" wire:model="editBtHasTables" class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                <span>Manajemen Meja & Dine-In</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                <input type="checkbox" wire:model="editBtHasModifiers" class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                <span>Modifier & Opsi Topping</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                <input type="checkbox" wire:model="editBtHasBarcode" class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                <span>Barcode Scanner Cepat</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                <input type="checkbox" wire:model="editBtHasVariants" class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                <span>Varian Ukuran / Warna</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                <input type="checkbox" wire:model="editBtHasCommissions" class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                <span>Komisi Pegawai / Kapster</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                <input type="checkbox" wire:model="editBtHasOrderStatus" class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                <span>Pelacakan Status Pesanan / Cuci</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                <input type="checkbox" wire:model="editBtHasCreditSales" class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                <span>Penjualan Tempo & Piutang</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                <input type="checkbox" wire:model="editBtHasTieredPricing" class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                <span>Harga Bertingkat Grosir</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                <input type="checkbox" wire:model="editBtHasServices" class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                <span>Layanan Jasa & Perawatan</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 rounded-b-2xl flex items-center justify-end gap-3 shrink-0">
                    <button type="button"
                            wire:click="closeEditPresetModal"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-semibold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button"
                            wire:click="saveBusinessTypePreset"
                            class="px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs shadow-sm shadow-orange-500/20 transition flex items-center gap-2 cursor-pointer">
                        <span wire:loading.remove wire:target="saveBusinessTypePreset">Simpan Perubahan Preset</span>
                        <span wire:loading wire:target="saveBusinessTypePreset" class="flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-notch fa-spin"></i>
                            <span>Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= MODAL TAMBAH TEMPLATE USAHA BARU ================= -->
    @if ($showCreateBusinessTypeModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-xl w-full my-8 animate-scale-up flex flex-col max-h-[90vh]">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 shrink-0 bg-slate-50/50 rounded-t-2xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-base shadow-sm">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Tambah Template Usaha Baru</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Daftarkan kategori jenis usaha baru untuk onboarding tenant</p>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="closeCreateBusinessTypeModal"
                            class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="overflow-y-auto px-6 py-5 space-y-4 flex-1 text-xs text-slate-700">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-8">
                            <label class="block font-semibold text-slate-700 mb-1">
                                Nama Jenis Usaha <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <input type="text"
                                   wire:model="newBtName"
                                   placeholder="Contoh: Pet Shop & Klinik Hewan"
                                   class="w-full border rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none @error('newBtName') border-rose-400 ring-1 ring-rose-400 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                            @error('newBtName')
                                <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="sm:col-span-4">
                            <label class="block font-semibold text-slate-700 mb-1">Ikon</label>
                            <select wire:model="newBtIcon"
                                    class="w-full border border-slate-200 rounded-xl px-2.5 py-2 text-xs text-slate-800 bg-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <option value="fa-store">Toko (fa-store)</option>
                                <option value="fa-utensils">Resto / F&B (fa-utensils)</option>
                                <option value="fa-scissors">Salon (fa-scissors)</option>
                                <option value="fa-shirt">Laundry (fa-shirt)</option>
                                <option value="fa-boxes-stacked">Grosir (fa-boxes-stacked)</option>
                                <option value="fa-paw">Pet Shop (fa-paw)</option>
                                <option value="fa-pills">Apotek (fa-pills)</option>
                                <option value="fa-car-side">Bengkel (fa-car-side)</option>
                                <option value="fa-spa">Spa (fa-spa)</option>
                                <option value="fa-shapes">Lainnya (fa-shapes)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">
                            Deskripsi Singkat <span class="text-rose-500 font-bold">*</span>
                        </label>
                        <textarea wire:model="newBtDescription"
                                  rows="2"
                                  placeholder="Jelaskan kebutuhan dan cakupan bisnis ini..."
                                  class="w-full border rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none @error('newBtDescription') border-rose-400 ring-1 ring-rose-400 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror"></textarea>
                        @error('newBtDescription')
                            <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Satuan Standar Produk (Default Units)</label>
                        <input type="text"
                               wire:model="newBtDefaultUnits"
                               placeholder="pcs, pack, dus"
                               class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-800 font-mono focus:outline-none focus:ring-1 focus:ring-orange-500" />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Preset Modul Awal</label>
                        <div class="border border-slate-200 rounded-xl p-3 max-h-40 overflow-y-auto space-y-1.5 bg-slate-50/50">
                            @php
                                $commonMods = [
                                    'Kasir Cepat & Struk WhatsApp',
                                    'Manajemen Stok Standar',
                                    'Multi-Metode Bayar',
                                    'Laporan Penjualan Harian',
                                    'Barcode Scanner',
                                    'Resep & Bahan Baku (BOM)',
                                    'Manajemen Meja Dine-In',
                                    'Komisi Pegawai',
                                    'Pelacakan Status Pesanan',
                                ];
                            @endphp
                            @foreach ($commonMods as $cMod)
                                <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                    <input type="checkbox"
                                           wire:click="togglePresetModuleInNew('{{ $cMod }}')"
                                           {{ in_array($cMod, $newBtPresetModules) ? 'checked' : '' }}
                                           class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                    <span class="text-slate-800">{{ $cMod }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 rounded-b-2xl flex items-center justify-end gap-3 shrink-0">
                    <button type="button"
                            wire:click="closeCreateBusinessTypeModal"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-semibold text-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="button"
                            wire:click="createBusinessType"
                            class="px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs shadow-sm cursor-pointer">
                        Buat Template Sekarang
                    </button>
                </div>
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

