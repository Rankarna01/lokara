<div class="h-screen w-screen overflow-hidden bg-surface flex flex-col antialiased text-dark select-none"
     x-data="{
         outletDropdownOpen: false,
         tableDropdownOpen: false,
         cartMenuOpen: false
     }">

    <!-- Toast Notification -->
    @if ($toastMessage)
        <div class="fixed top-4 right-4 z-50 flex items-center gap-3 bg-white border border-slate-200 px-4 py-2.5 rounded-xl shadow-lg animate-bounce"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false; $wire.clearToast() }, 3000)">
            <div class="w-7 h-7 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center flex-shrink-0 text-xs">
                <i class="fa-solid fa-bell"></i>
            </div>
            <div class="text-xs font-semibold text-slate-800">{{ $toastMessage }}</div>
            <button wire:click="clearToast" class="text-slate-400 hover:text-slate-600 ml-1">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>
    @endif

    <div class="flex-1 flex overflow-hidden">

        <!-- ================= 1. LEFT SIDEBAR (POS SPECIFIC - MATCHING IMAGE 3) ================= -->
        <aside class="w-56 bg-white border-r border-slate-200/80 flex flex-col justify-between flex-shrink-0">

            <div>
                <!-- Brand Logo (Orange Icon + Lokara POS badge) -->
                <div class="h-16 flex items-center px-5 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-orange-500 flex items-center justify-center text-white shadow-sm shadow-orange-500/20">
                            <i class="fa-solid fa-cubes text-sm"></i>
                        </div>
                        <div class="flex items-center">
                            <span class="text-lg font-bold tracking-tight text-slate-900">Lokara</span>
                            <span class="ml-1.5 px-1.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-orange-500 text-white tracking-wider">POS</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="p-3 space-y-1 text-xs">
                    <!-- Katalog (Active) -->
                    <button wire:click="$set('activeNav', 'katalog')"
                            class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg font-medium transition-colors text-left {{ $activeNav === 'katalog' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        <i class="fa-solid fa-shapes text-sm w-4 text-center {{ $activeNav === 'katalog' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                        <span>Katalog</span>
                    </button>

                    <!-- Pesanan -->
                    <button wire:click="$set('showHoldModal', true)"
                            class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg font-medium transition-colors text-left {{ $activeNav === 'pesanan' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        <i class="fa-solid fa-clipboard-list text-sm w-4 text-center text-slate-400"></i>
                        <span>Pesanan</span>
                        @if (count($heldOrders) > 0)
                            <span class="ml-auto bg-orange-500 text-white text-[10px] font-bold px-1.5 py-0.2 rounded-full">{{ count($heldOrders) }}</span>
                        @endif
                    </button>

                    <!-- Shift & Kas -->
                    <button wire:click="$set('showShiftModal', true)"
                            class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg font-medium transition-colors text-left {{ $activeNav === 'shift' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        <i class="fa-solid fa-cash-register text-sm w-4 text-center text-slate-400"></i>
                        <span>Shift & Kas</span>
                    </button>

                    <!-- Transaksi -->
                    <button wire:click="$set('showTransactionsModal', true)"
                            class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg font-medium transition-colors text-left {{ $activeNav === 'transaksi' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        <i class="fa-solid fa-receipt text-sm w-4 text-center text-slate-400"></i>
                        <span>Transaksi</span>
                    </button>

                    <!-- Pelanggan -->
                    <button wire:click="$set('showCustomerModal', true)"
                            class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg font-medium transition-colors text-left {{ $activeNav === 'pelanggan' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        <i class="fa-solid fa-user-group text-sm w-4 text-center text-slate-400"></i>
                        <span>Pelanggan</span>
                        @if ($selectedCustomer)
                            <span class="ml-auto w-2 h-2 rounded-full bg-emerald-500"></span>
                        @endif
                    </button>

                    <!-- Bantuan -->
                    <button wire:click="$set('showHelpModal', true)"
                            class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg font-medium transition-colors text-left {{ $activeNav === 'bantuan' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        <i class="fa-regular fa-circle-question text-sm w-4 text-center text-slate-400"></i>
                        <span>Bantuan</span>
                    </button>
                </nav>
            </div>

            <!-- Bottom Back to Admin / Logout Link -->
            <div class="p-3 border-t border-slate-100 space-y-1">
                <a href="{{ route('tenant.dashboard') }}"
                   class="w-full flex items-center gap-3 px-3.5 py-2 text-xs text-orange-600 hover:bg-orange-50 rounded-lg transition font-semibold">
                    <i class="fa-solid fa-chart-pie text-sm w-4 text-center"></i>
                    <span>Back Office Toko</span>
                </a>
                <button wire:click="logout"
                        class="w-full flex items-center gap-3 px-3.5 py-2 text-xs text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition font-medium">
                    <i class="fa-solid fa-arrow-right-from-bracket text-sm w-4 text-center"></i>
                    <span>Keluar</span>
                </button>
            </div>
        </aside>

        <!-- ================= 2. CENTER SECTION: CATALOG & PRODUCT GRID ================= -->
        <main class="flex-1 flex flex-col min-w-0 bg-surface overflow-hidden">

            <!-- Top Header Navbar (Matching Image 3) -->
            <header class="h-16 bg-white border-b border-slate-200/80 px-6 flex items-center justify-between flex-shrink-0">
                <!-- Left: Breadcrumb -->
                <div class="flex items-center gap-2 text-xs text-slate-400 font-normal">
                    <span>POS Terminal</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-800 font-semibold">Katalog</span>
                </div>

                <!-- Center: Search Input with Barcode Icon -->
                <div class="relative w-80 lg:w-96">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text"
                           wire:model.live.debounce.250ms="search"
                           placeholder="Cari nama menu atau scan barcode..."
                           class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-9 pr-9 py-1.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition" />
                    <button wire:click="$set('showBarcodeModal', true)"
                            title="Scan Barcode"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-orange-600 transition">
                        <i class="fa-solid fa-barcode text-sm"></i>
                    </button>
                </div>

                <!-- Right: Outlet Selector, Bell, Cashier Profile -->
                <div class="flex items-center gap-4">
                    <!-- Outlet Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-left text-xs hover:bg-slate-50 transition">
                            <i class="fa-solid fa-location-dot text-slate-500 text-xs"></i>
                            <div>
                                <div class="font-bold text-slate-900 leading-tight">Kopi Ruang Pagi</div>
                                <div class="text-[10px] text-slate-400">{{ $currentOutlet->name ?? 'Outlet Utama' }}</div>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-1"></i>
                        </button>

                        <div x-show="open" @click.outside="open = false" x-transition
                             class="absolute right-0 mt-1 w-48 bg-white border border-slate-200 rounded-xl shadow-lg py-1 z-30 text-xs">
                            @foreach ($outlets as $out)
                                <button wire:click="$set('selectedOutletId', {{ $out->id }})" @click="open = false"
                                        class="w-full text-left px-3 py-2 hover:bg-slate-50 flex items-center justify-between {{ $selectedOutletId === $out->id ? 'text-orange-600 font-semibold' : 'text-slate-700' }}">
                                    <span>{{ $out->name }}</span>
                                    @if ($selectedOutletId === $out->id)
                                        <i class="fa-solid fa-check text-orange-500 text-[10px]"></i>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Notification Bell -->
                    <button class="relative p-2 text-slate-400 hover:text-slate-600 transition">
                        <i class="fa-regular fa-bell text-sm"></i>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-orange-500"></span>
                    </button>

                    <!-- Cashier Profile (Dina Putri - Kasir) -->
                    <div class="flex items-center gap-2.5 pl-3 border-l border-slate-200">
                        <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=80&h=80&q=80"
                             alt="Cashier"
                             class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200" />
                        <div class="text-left">
                            <div class="text-xs font-bold text-slate-900 leading-tight">Dina Putri</div>
                            <div class="text-[10px] text-slate-400">Kasir</div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Scrollable Catalog Area -->
            <div class="flex-1 overflow-y-auto p-6 space-y-6">

                <!-- 1. Horizontal Category Tabs Bar (Matching Image 3) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
                    <!-- Tab: Semua Menu (Active) -->
                    <button wire:click="setCategory('all')"
                            class="p-3.5 rounded-xl border text-left transition flex items-center gap-3.5 {{ $selectedCategory === 'all' ? 'bg-white border-orange-500 ring-1 ring-orange-500 shadow-xs' : 'bg-white border-slate-200/80 hover:border-slate-300' }}">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $selectedCategory === 'all' ? 'bg-orange-500 text-white shadow-xs' : 'bg-slate-100 text-slate-600' }}">
                            <i class="fa-solid fa-shapes text-base"></i>
                        </div>
                        <div>
                            <div class="font-bold text-xs {{ $selectedCategory === 'all' ? 'text-orange-600' : 'text-slate-800' }}">Semua Menu</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">{{ $allMenuCount }} menu</div>
                        </div>
                    </button>

                    <!-- Tab: Kopi -->
                    <button wire:click="setCategory('kopi')"
                            class="p-3.5 rounded-xl border text-left transition flex items-center gap-3.5 {{ $selectedCategory === 'kopi' ? 'bg-white border-orange-500 ring-1 ring-orange-500 shadow-xs' : 'bg-white border-slate-200/80 hover:border-slate-300' }}">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $selectedCategory === 'kopi' ? 'bg-orange-500 text-white shadow-xs' : 'bg-slate-100 text-slate-600' }}">
                            <i class="fa-solid fa-mug-hot text-base"></i>
                        </div>
                        <div>
                            <div class="font-bold text-xs {{ $selectedCategory === 'kopi' ? 'text-orange-600' : 'text-slate-800' }}">Kopi</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">12 menu</div>
                        </div>
                    </button>

                    <!-- Tab: Non-Kopi -->
                    <button wire:click="setCategory('non-kopi')"
                            class="p-3.5 rounded-xl border text-left transition flex items-center gap-3.5 {{ $selectedCategory === 'non-kopi' ? 'bg-white border-orange-500 ring-1 ring-orange-500 shadow-xs' : 'bg-white border-slate-200/80 hover:border-slate-300' }}">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $selectedCategory === 'non-kopi' ? 'bg-orange-500 text-white shadow-xs' : 'bg-slate-100 text-slate-600' }}">
                            <i class="fa-solid fa-glass-water text-base"></i>
                        </div>
                        <div>
                            <div class="font-bold text-xs {{ $selectedCategory === 'non-kopi' ? 'text-orange-600' : 'text-slate-800' }}">Non-Kopi</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">10 menu</div>
                        </div>
                    </button>

                    <!-- Tab: Makanan -->
                    <button wire:click="setCategory('makanan')"
                            class="p-3.5 rounded-xl border text-left transition flex items-center gap-3.5 {{ $selectedCategory === 'makanan' ? 'bg-white border-orange-500 ring-1 ring-orange-500 shadow-xs' : 'bg-white border-slate-200/80 hover:border-slate-300' }}">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $selectedCategory === 'makanan' ? 'bg-orange-500 text-white shadow-xs' : 'bg-slate-100 text-slate-600' }}">
                            <i class="fa-solid fa-utensils text-base"></i>
                        </div>
                        <div>
                            <div class="font-bold text-xs {{ $selectedCategory === 'makanan' ? 'text-orange-600' : 'text-slate-800' }}">Makanan</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">12 menu</div>
                        </div>
                    </button>

                    <!-- Tab: Snack -->
                    <button wire:click="setCategory('snack')"
                            class="p-3.5 rounded-xl border text-left transition flex items-center gap-3.5 {{ $selectedCategory === 'snack' ? 'bg-white border-orange-500 ring-1 ring-orange-500 shadow-xs' : 'bg-white border-slate-200/80 hover:border-slate-300' }}">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $selectedCategory === 'snack' ? 'bg-orange-500 text-white shadow-xs' : 'bg-slate-100 text-slate-600' }}">
                            <i class="fa-solid fa-cookie-bite text-base"></i>
                        </div>
                        <div>
                            <div class="font-bold text-xs {{ $selectedCategory === 'snack' ? 'text-orange-600' : 'text-slate-800' }}">Snack</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">8 menu</div>
                        </div>
                    </button>
                </div>

                <!-- 2. Section Header: Title & Scan Barcode Button -->
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Katalog Menu</h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Pilih produk untuk menambahkan ke pesanan</p>
                    </div>

                    <button wire:click="$set('showBarcodeModal', true)"
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs">
                        <i class="fa-solid fa-barcode text-slate-500"></i>
                        <span>Scan Barcode</span>
                    </button>
                </div>

                <!-- 3. Product Cards Grid (3 Columns, Horizontal Card Layout - Matching Image 3) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($products as $product)
                        <div wire:click="addToCart({{ $product->id }})"
                             class="group bg-white border border-slate-200/80 hover:border-orange-300 rounded-xl p-3 flex items-center justify-between transition cursor-pointer shadow-xs hover:shadow-sm">
                            <div class="flex items-center gap-3 min-w-0">
                                <!-- Product Thumbnail -->
                                <img src="{{ $product->image_url ?? 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=200&h=200&q=80' }}"
                                     alt="{{ $product->name }}"
                                     class="w-14 h-14 rounded-lg object-cover flex-shrink-0 bg-slate-100 ring-1 ring-slate-100" />

                                <div class="min-w-0">
                                    <div class="font-bold text-xs text-slate-900 group-hover:text-orange-600 transition truncate">
                                        {{ $product->name }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5 truncate">
                                        {{ $product->category_tag ?? 'Kopi • Hot / Ice' }}
                                    </div>
                                    <div class="text-xs font-bold text-slate-900 mt-1">
                                        Rp{{ number_format($product->base_price, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Circular Orange Plus Button (Matching Image 3) -->
                            <button type="button"
                                    class="w-7 h-7 rounded-full bg-orange-500 text-white flex items-center justify-center flex-shrink-0 hover:bg-orange-600 transition shadow-xs ml-2">
                                <i class="fa-solid fa-plus text-[10px]"></i>
                            </button>
                        </div>
                    @empty
                        <div class="col-span-3 py-12 text-center text-slate-400 text-xs">
                            <i class="fa-solid fa-magnifying-glass text-2xl mb-2 text-slate-300"></i>
                            <p>Tidak ada produk ditemukan untuk kategori ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </main>

        <!-- ================= 3. RIGHT PANEL: ORDER CART (PESANAN BARU) ================= -->
        <aside class="w-80 lg:w-96 bg-white border-l border-slate-200/80 flex flex-col justify-between flex-shrink-0 h-full">

            <!-- Cart Header -->
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-sm text-slate-900">Pesanan Baru</h3>
                    <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-bold">
                        {{ array_sum(array_column($cart, 'qty')) }} item
                    </span>
                </div>

                <!-- Dropdown Dots Menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="text-slate-400 hover:text-slate-600 p-1">
                        <i class="fa-solid fa-ellipsis"></i>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition
                         class="absolute right-0 mt-1 w-44 bg-white border border-slate-200 rounded-xl shadow-lg py-1 z-30 text-xs">
                        <button wire:click="$set('showCustomerModal', true)" @click="open = false" class="w-full text-left px-3 py-1.5 text-slate-700 hover:bg-slate-50 font-medium">Pilih Member Pelanggan</button>
                        <button wire:click="holdOrder" @click="open = false" class="w-full text-left px-3 py-1.5 text-slate-700 hover:bg-slate-50 font-medium">Tahan Pesanan</button>
                        <button wire:click="clearCart" @click="open = false" class="w-full text-left px-3 py-1.5 text-rose-600 hover:bg-rose-50 font-medium border-t border-slate-100 mt-1">Kosongkan Keranjang</button>
                    </div>
                </div>
            </div>

            <!-- Cart Body: Order Type + Table + Items -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4">

                <!-- Order Type Switcher (Dine In vs Take Away - Matching Image 3) -->
                <div class="grid grid-cols-2 gap-2">
                    <button wire:click="setOrderType('dine_in')"
                            class="py-2 px-3 rounded-lg text-xs font-semibold flex items-center justify-center gap-2 transition {{ $orderType === 'dine_in' ? 'bg-orange-500 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                        <i class="fa-solid fa-utensils text-xs"></i>
                        <span>Dine In</span>
                    </button>

                    <button wire:click="setOrderType('take_away')"
                            class="py-2 px-3 rounded-lg text-xs font-semibold flex items-center justify-center gap-2 transition {{ $orderType === 'take_away' ? 'bg-orange-500 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                        <i class="fa-solid fa-bag-shopping text-xs"></i>
                        <span>Take Away</span>
                    </button>
                </div>

                <!-- Table Selector Dropdown (Dine In only - Matching Image 3: Meja 08) -->
                @if ($orderType === 'dine_in')
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="w-full py-2 px-3 rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-800 flex items-center justify-between hover:border-slate-300 transition">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-chair text-slate-400 text-xs"></i>
                                <span>{{ $selectedTable }}</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                        </button>

                        <div x-show="open" @click.outside="open = false" x-transition
                             class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg p-2 z-30 grid grid-cols-4 gap-1 text-xs">
                            @foreach (['Meja 01', 'Meja 02', 'Meja 03', 'Meja 04', 'Meja 05', 'Meja 06', 'Meja 07', 'Meja 08', 'Meja 09', 'Meja 10', 'Meja 11', 'Meja 12'] as $table)
                                <button type="button"
                                        wire:click="$set('selectedTable', '{{ $table }}')"
                                        @click="open = false"
                                        class="py-1.5 px-2 rounded-lg text-center font-medium transition {{ $selectedTable === $table ? 'bg-orange-500 text-white font-bold' : 'hover:bg-slate-100 text-slate-700' }}">
                                    {{ str_replace('Meja ', 'M', $table) }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Member Badge if selected -->
                @if ($selectedCustomer)
                    <div class="p-2.5 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-id-card text-emerald-600"></i>
                            <div>
                                <div class="font-bold text-emerald-900 leading-tight">{{ $selectedCustomer['name'] }}</div>
                                <div class="text-[10px] text-emerald-600">{{ $selectedCustomer['points'] }} Poin Member</div>
                            </div>
                        </div>
                        <button wire:click="removeSelectedCustomer" class="text-slate-400 hover:text-rose-500">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>
                @endif

                <!-- Items List (Matching Image 3) -->
                <div class="space-y-3 pt-1">
                    @forelse ($cart as $index => $item)
                        <div class="relative p-2.5 rounded-xl border border-slate-100 bg-slate-50/50 flex items-start justify-between gap-3 group">
                            <!-- Left: Thumbnail & Details -->
                            <div class="flex items-start gap-2.5 min-w-0">
                                <img src="{{ $item['image_url'] ?? 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=150&h=150&q=80' }}"
                                     alt="{{ $item['name'] }}"
                                     class="w-11 h-11 rounded-lg object-cover bg-white ring-1 ring-slate-200 flex-shrink-0" />

                                <div class="min-w-0">
                                    <div class="font-bold text-xs text-slate-900 truncate">{{ $item['name'] }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5 truncate">{{ $item['variant'] }}</div>
                                    <div class="text-xs font-semibold text-slate-700 mt-1">
                                        Rp{{ number_format($item['price'], 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Stepper & Subtotal -->
                            <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                                <!-- Remove Cross Button -->
                                <button wire:click="removeFromCart({{ $index }})"
                                        class="text-slate-300 hover:text-rose-500 text-xs">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>

                                <!-- Stepper (- Qty +) -->
                                <div class="flex items-center border border-slate-200 rounded-lg bg-white overflow-hidden text-xs">
                                    <button wire:click="decrementQty({{ $index }})"
                                            class="w-6 h-6 flex items-center justify-center text-slate-500 hover:bg-slate-100 transition">
                                        −
                                    </button>
                                    <span class="w-6 text-center font-bold text-slate-800 text-[11px]">{{ $item['qty'] }}</span>
                                    <button wire:click="incrementQty({{ $index }})"
                                            class="w-6 h-6 flex items-center justify-center text-slate-500 hover:bg-slate-100 transition">
                                        +
                                    </button>
                                </div>

                                <div class="text-xs font-bold text-slate-900 mt-0.5">
                                    Rp{{ number_format($item['subtotal'], 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center text-slate-400 text-xs">
                            <i class="fa-solid fa-bag-shopping text-3xl mb-2 text-slate-300"></i>
                            <p class="font-medium">Belum ada item pesanan.</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Klik produk di katalog untuk menambahkan.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Cart Footer: Calculations & Pay Button (Matching Image 3) -->
            <div class="p-4 border-t border-slate-100 bg-white space-y-3">
                <!-- Calculations Breakdown -->
                <div class="space-y-1.5 text-xs text-slate-600">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span class="font-semibold text-slate-900">Rp{{ number_format($totals['subtotal'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Diskon</span>
                        <span class="text-slate-400">Rp0</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Pajak (11%)</span>
                        <span class="font-semibold text-slate-900">Rp{{ number_format($totals['tax'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-slate-100 items-baseline">
                        <span class="font-bold text-slate-900">Total Pembayaran</span>
                        <span class="text-lg font-extrabold text-slate-900">Rp{{ number_format($totals['total'], 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Primary Button: Lanjut Pembayaran > -->
                <button wire:click="openPaymentModal"
                        class="w-full py-3 px-4 rounded-xl font-bold text-xs text-white bg-orange-500 hover:bg-orange-600 shadow-sm shadow-orange-500/20 flex items-center justify-center gap-1.5 transition">
                    <span>Lanjut Pembayaran</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </button>

                <!-- Secondary Button: Simpan Pesanan -->
                <button wire:click="holdOrder"
                        class="w-full py-2.5 px-4 rounded-xl font-semibold text-xs text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 flex items-center justify-center gap-2 transition">
                    <i class="fa-regular fa-floppy-disk text-slate-400"></i>
                    <span>Simpan Pesanan</span>
                </button>
            </div>
        </aside>

    </div>

    <!-- ================= MODAL: PEMBAYARAN & CHECKOUT ================= -->
    @if ($showPaymentModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full p-6 animate-scale-up space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Konfirmasi Pembayaran</h3>
                        <p class="text-[11px] text-slate-400">{{ $orderType === 'dine_in' ? 'Dine In • ' . $selectedTable : 'Take Away' }}</p>
                    </div>
                    <button wire:click="$set('showPaymentModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- Big Total Display -->
                <div class="text-center py-2 bg-orange-50 rounded-xl border border-orange-100">
                    <span class="text-[11px] font-semibold text-orange-600 uppercase tracking-wider">Total Tagihan</span>
                    <div class="text-2xl font-extrabold text-orange-600">Rp{{ number_format($totals['total'], 0, ',', '.') }}</div>
                </div>

                <!-- Payment Method Tabs -->
                <div class="grid grid-cols-3 gap-2 text-xs">
                    <button type="button" wire:click="$set('paymentMethod', 'cash')"
                            class="py-2.5 px-2 rounded-xl border text-center font-bold flex flex-col items-center gap-1 transition {{ $paymentMethod === 'cash' ? 'bg-orange-500 text-white border-orange-500 shadow-xs' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                        <i class="fa-solid fa-money-bill-wave text-sm"></i>
                        <span>Tunai</span>
                    </button>

                    <button type="button" wire:click="$set('paymentMethod', 'qris')"
                            class="py-2.5 px-2 rounded-xl border text-center font-bold flex flex-col items-center gap-1 transition {{ $paymentMethod === 'qris' ? 'bg-orange-500 text-white border-orange-500 shadow-xs' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                        <i class="fa-solid fa-qrcode text-sm"></i>
                        <span>QRIS</span>
                    </button>

                    <button type="button" wire:click="$set('paymentMethod', 'debit')"
                            class="py-2.5 px-2 rounded-xl border text-center font-bold flex flex-col items-center gap-1 transition {{ $paymentMethod === 'debit' ? 'bg-orange-500 text-white border-orange-500 shadow-xs' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                        <i class="fa-solid fa-credit-card text-sm"></i>
                        <span>Debit / EDC</span>
                    </button>
                </div>

                <!-- Cash Method Controls -->
                @if ($paymentMethod === 'cash')
                    <div class="space-y-3 pt-2 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Uang Tunai Diterima</label>
                            <input type="number" wire:model.live="cashTendered" class="w-full text-base font-bold px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500" />
                        </div>

                        <!-- Quick Cash Buttons -->
                        <div class="grid grid-cols-4 gap-1.5">
                            <button type="button" wire:click="setQuickCash({{ $totals['total'] }})" class="py-1.5 px-1 rounded-lg border border-slate-200 text-center font-bold hover:bg-slate-50 text-[11px]">Uang Pas</button>
                            <button type="button" wire:click="setQuickCash(100000)" class="py-1.5 px-1 rounded-lg border border-slate-200 text-center font-bold hover:bg-slate-50 text-[11px]">100.000</button>
                            <button type="button" wire:click="setQuickCash(150000)" class="py-1.5 px-1 rounded-lg border border-slate-200 text-center font-bold hover:bg-slate-50 text-[11px]">150.000</button>
                            <button type="button" wire:click="setQuickCash(200000)" class="py-1.5 px-1 rounded-lg border border-slate-200 text-center font-bold hover:bg-slate-50 text-[11px]">200.000</button>
                        </div>

                        <!-- Change Due -->
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                            <span class="font-semibold text-slate-600">Uang Kembalian:</span>
                            <span class="text-base font-extrabold text-emerald-600">Rp{{ number_format(max(0, $cashTendered - $totals['total']), 0, ',', '.') }}</span>
                        </div>
                    </div>
                @elseif ($paymentMethod === 'qris')
                    <!-- QRIS Dynamic Display -->
                    <div class="text-center py-4 space-y-3">
                        <div class="w-44 h-44 mx-auto p-2 border border-slate-200 rounded-2xl bg-white shadow-xs flex items-center justify-center">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=00020101021226670016ID.LOKARAPOS.WWW01189360099999999999990215INV-20261029-015802ID5916Kopi Ruang Pagi6013Jakarta Selatan"
                                 alt="QRIS Dinamis"
                                 class="w-full h-full object-contain" />
                        </div>
                        <p class="text-xs text-slate-500">Scan QRIS menggunakan GoPay, OVO, Dana, BCA, atau Livin Mandiri</p>
                    </div>
                @else
                    <div class="py-4 space-y-2 text-xs">
                        <label class="block font-semibold text-slate-700">Nomor Approval / Referensi EDC</label>
                        <input type="text" placeholder="Contoh: APPR-889901" class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500" />
                    </div>
                @endif

                <div class="pt-3 border-t border-slate-100 flex gap-2">
                    <button type="button" wire:click="$set('showPaymentModal', false)" class="w-1/3 py-2.5 rounded-xl border border-slate-200 font-semibold text-xs text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="button" wire:click="processPayment" class="w-2/3 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 font-bold text-xs text-white shadow-sm transition">
                        Selesaikan Pembayaran
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= MODAL: PEMBAYARAN BERHASIL (SUCCESS SCREEN) ================= -->
    @if ($showSuccessModal && $completedOrderData)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-sm w-full p-6 text-center animate-scale-up space-y-4">
                <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-2xl shadow-sm">
                    <i class="fa-solid fa-check"></i>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-slate-900">Pembayaran Berhasil!</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $completedOrderData['order_number'] }}</p>
                </div>

                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 text-xs text-left space-y-2">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tipe Pesanan:</span>
                        <span class="font-semibold text-slate-800">{{ $completedOrderData['order_type'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Pelanggan:</span>
                        <span class="font-semibold text-slate-800">{{ $completedOrderData['customer_name'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Metode Bayar:</span>
                        <span class="font-semibold text-slate-800">{{ $completedOrderData['payment_method'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Total Transaksi:</span>
                        <span class="font-bold text-slate-900">Rp{{ number_format($completedOrderData['total'], 0, ',', '.') }}</span>
                    </div>
                    @if ($completedOrderData['payment_method'] === 'CASH')
                        <div class="flex justify-between pt-1 border-t border-slate-200/60">
                            <span class="text-slate-500">Uang Diterima:</span>
                            <span class="font-semibold text-slate-800">Rp{{ number_format($completedOrderData['cash_tendered'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Uang Kembalian:</span>
                            <span class="font-bold text-emerald-600">Rp{{ number_format($completedOrderData['change_due'], 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>

                <div class="space-y-2 pt-2">
                    <button type="button" wire:click="notify('Perintah byte ESC/POS dikirim ke printer thermal')" class="w-full py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-print text-slate-500"></i>
                        <span>Cetak Struk Thermal</span>
                    </button>
                    <button type="button" wire:click="notify('Struk digital dikirim ke WhatsApp')" class="w-full py-2.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 text-xs font-bold flex items-center justify-center gap-2">
                        <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                        <span>Kirim Struk via WhatsApp</span>
                    </button>
                    <button type="button" wire:click="newTransaction" class="w-full py-3 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold shadow-sm transition">
                        Pesanan Baru
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= MODAL: VARIANT & NOTE SELECTION ================= -->
    @if ($showVariantModal && $selectedProductForVariant)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-sm w-full p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-sm text-slate-900">{{ $selectedProductForVariant->name }}</h3>
                    <button wire:click="$set('showVariantModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <label class="block font-semibold text-slate-700">Pilih Ukuran / Varian:</label>
                    <div class="space-y-2">
                        @foreach ($selectedProductForVariant->variants as $var)
                            <label class="flex items-center justify-between p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 {{ $selectedVariantName === $var->name ? 'border-orange-500 bg-orange-50/50' : '' }}">
                                <div class="flex items-center gap-2">
                                    <input type="radio" name="variant" wire:click="$set('selectedVariantName', '{{ $var->name }}'); $set('selectedVariantPrice', {{ $var->final_price }})" {{ $selectedVariantName === $var->name ? 'checked' : '' }} class="text-orange-500 focus:ring-orange-500" />
                                    <span class="font-medium text-slate-800">{{ $var->name }}</span>
                                </div>
                                <span class="font-bold text-slate-900">Rp{{ number_format($var->final_price, 0, ',', '.') }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="pt-2">
                        <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan (Add Note):</label>
                        <input type="text" wire:model="itemNotes" placeholder="Contoh: Tanpa Gula, Extra Panas" class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500" />
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex gap-2">
                    <button type="button" wire:click="$set('showVariantModal', false)" class="w-1/3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600">Batal</button>
                    <button type="button" wire:click="confirmVariantSelection" class="w-2/3 py-2 rounded-xl bg-orange-500 text-white text-xs font-bold hover:bg-orange-600 transition">Tambahkan</button>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= MODAL: SCAN BARCODE ================= -->
    @if ($showBarcodeModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-sm w-full p-6 text-center space-y-4">
                <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center mx-auto text-xl">
                    <i class="fa-solid fa-barcode"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Scan Barcode Menu</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Arahkan scanner atau ketikkan kode barcode di bawah</p>
                </div>

                <form wire:submit.prevent="scanBarcode" class="space-y-3">
                    <input type="text" wire:model="barcodeInput" autofocus placeholder="Scan atau input barcode..." class="w-full text-center tracking-widest font-mono text-sm px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500" />
                    <div class="flex gap-2">
                        <button type="button" wire:click="$set('showBarcodeModal', false)" class="w-1/2 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-600">Tutup</button>
                        <button type="submit" class="w-1/2 py-2 rounded-xl bg-orange-500 text-white text-xs font-bold hover:bg-orange-600 transition">Cari</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ================= MODAL: PESANAN TERTUNDA (HOLD ORDERS) ================= -->
    @if ($showHoldModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-slate-900">Daftar Pesanan Tertunda (Hold)</h3>
                    <button wire:click="$set('showHoldModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <div class="space-y-2 max-h-72 overflow-y-auto text-xs">
                    @forelse ($heldOrders as $held)
                        <div class="p-3 rounded-xl border border-slate-200 flex items-center justify-between hover:bg-slate-50 transition">
                            <div>
                                <div class="font-bold text-slate-900">{{ $held['table'] }} <span class="text-[10px] text-slate-400 font-normal">({{ $held['time'] }})</span></div>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ count($held['items']) }} item • Rp{{ number_format($held['total'], 0, ',', '.') }}</div>
                            </div>
                            <button wire:click="recallOrder('{{ $held['id'] }}')" class="px-3 py-1.5 rounded-lg bg-orange-500 text-white font-bold text-xs hover:bg-orange-600 transition">
                                Muat Kembali
                            </button>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400">
                            <p>Tidak ada pesanan yang sedang ditahan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <!-- ================= MODAL: SHIFT & KAS ================= -->
    @if ($showShiftModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Manajemen Shift & Kas Laci</h3>
                        <p class="text-[11px] text-slate-400">Kasir: Dina Putri • Shift Berjalan</p>
                    </div>
                    <button wire:click="$set('showShiftModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- Cash Summary Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] text-slate-400 uppercase font-semibold">Modal Awal</span>
                        <div class="font-bold text-slate-800 text-sm mt-0.5">Rp{{ number_format($shiftStartingCash, 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] text-slate-400 uppercase font-semibold">Penjualan Tunai</span>
                        <div class="font-bold text-emerald-600 text-sm mt-0.5">+Rp{{ number_format($cashSalesTotal, 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] text-slate-400 uppercase font-semibold">Kas Keluar</span>
                        <div class="font-bold text-rose-500 text-sm mt-0.5">-Rp{{ number_format(array_sum(array_column(array_filter($pettyCashMovements, fn($p) => $p['type'] === 'out'), 'amount')), 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3 rounded-xl bg-orange-50 border border-orange-100">
                        <span class="text-[10px] text-orange-600 uppercase font-bold">Uang di Laci</span>
                        <div class="font-extrabold text-orange-600 text-sm mt-0.5">Rp{{ number_format($expectedCashDrawer, 0, ',', '.') }}</div>
                    </div>
                </div>

                <!-- Petty Cash Entry Form -->
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-2 text-xs">
                    <div class="font-bold text-slate-800 flex items-center justify-between">
                        <span>Pencatatan Kas Masuk / Keluar (Petty Cash)</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <select wire:model="pettyCashType" class="border border-slate-200 rounded-lg px-2 py-1.5 bg-white text-slate-700">
                            <option value="out">Kas Keluar (-)</option>
                            <option value="in">Kas Masuk (+)</option>
                        </select>
                        <input type="number" wire:model="pettyCashAmount" placeholder="Nominal Rp" class="border border-slate-200 rounded-lg px-2.5 py-1.5 bg-white" />
                        <input type="text" wire:model="pettyCashReason" placeholder="Keterangan (misal: Es Batu)" class="border border-slate-200 rounded-lg px-2.5 py-1.5 bg-white" />
                    </div>
                    <div class="text-right">
                        <button type="button" wire:click="recordPettyCash" class="px-3 py-1.5 rounded-lg bg-slate-800 text-white font-semibold text-[11px] hover:bg-slate-900 transition">
                            Simpan Mutasi Kas
                        </button>
                    </div>
                </div>

                <!-- Petty Cash History List -->
                <div class="space-y-1.5 max-h-36 overflow-y-auto text-xs">
                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Riwayat Kas Selama Shift:</div>
                    @foreach ($pettyCashMovements as $pcm)
                        <div class="flex items-center justify-between p-2 rounded-lg bg-white border border-slate-100 text-[11px]">
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full {{ $pcm['type'] === 'out' ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                                <span class="font-medium text-slate-800">{{ $pcm['reason'] }}</span>
                                <span class="text-slate-400 font-normal">({{ $pcm['time'] }})</span>
                            </div>
                            <span class="font-bold {{ $pcm['type'] === 'out' ? 'text-rose-500' : 'text-emerald-600' }}">
                                {{ $pcm['type'] === 'out' ? '-' : '+' }}Rp{{ number_format($pcm['amount'], 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-between items-center text-xs">
                    <button wire:click="notify('Laci kasir fisik (Cash Drawer) terbuka otomatis via perintah ESC/POS')" class="text-slate-600 hover:text-slate-900 font-medium">
                        <i class="fa-solid fa-vault mr-1 text-slate-400"></i> Buka Laci Kas
                    </button>
                    <button wire:click="notify('Shift ditutup. Laporan ringkasan kas dicetak ke printer.')" class="px-4 py-2 rounded-xl bg-orange-500 text-white font-bold hover:bg-orange-600 transition">
                        Tutup Shift & Rekonsiliasi
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= MODAL: TRANSAKSI HARI INI ================= -->
    @if ($showTransactionsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-xl w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Riwayat Transaksi Shift Ini</h3>
                        <p class="text-[11px] text-slate-400">Daftar transaksi yang diselesaikan kasir hari ini</p>
                    </div>
                    <button wire:click="$set('showTransactionsModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <div class="overflow-x-auto max-h-80 overflow-y-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                            <tr>
                                <th class="py-2 px-3 font-semibold">No. Order</th>
                                <th class="py-2 px-3 font-semibold">Waktu / Tipe</th>
                                <th class="py-2 px-3 font-semibold">Metode</th>
                                <th class="py-2 px-3 font-semibold">Total</th>
                                <th class="py-2 px-3 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($todayOrders as $to)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-2.5 px-3 font-bold text-slate-900">{{ $to->order_number }}</td>
                                    <td class="py-2.5 px-3 text-slate-500">
                                        {{ $to->created_at ? $to->created_at->format('H:i') : '14:20' }} • {{ $to->order_type === 'dine_in' ? ($to->table_number ?? 'Dine In') : 'Take Away' }}
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                            {{ $to->payments->first() ? $to->payments->first()->payment_method : 'QRIS' }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 font-bold text-slate-900">Rp{{ number_format($to->total_amount, 0, ',', '.') }}</td>
                                    <td class="py-2.5 px-3 text-right">
                                        <button wire:click="notify('Struk order {{ $to->order_number }} dikirim ke printer thermal')" class="px-2.5 py-1 rounded border border-slate-200 text-slate-700 hover:bg-slate-100 text-[11px] font-semibold">
                                            <i class="fa-solid fa-print mr-1"></i> Cetak Ulang
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400">Belum ada transaksi di database untuk shift ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= MODAL: PELANGGAN (CRM / MEMBER) ================= -->
    @if ($showCustomerModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Pilih Pelanggan / Member</h3>
                        <p class="text-[11px] text-slate-400">Hubungkan transaksi ke akun member untuk menambah poin</p>
                    </div>
                    <button wire:click="$set('showCustomerModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                @if ($selectedCustomer)
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-xs">
                        <div>
                            <span class="text-[10px] text-emerald-600 uppercase font-bold">Member Terpilih:</span>
                            <div class="font-bold text-emerald-900 text-sm">{{ $selectedCustomer['name'] }} ({{ $selectedCustomer['phone'] }})</div>
                            <div class="text-[11px] text-emerald-700">{{ $selectedCustomer['points'] }} Poin Tersedia</div>
                        </div>
                        <button wire:click="removeSelectedCustomer" class="text-xs text-rose-600 font-semibold hover:underline">Lepas</button>
                    </div>
                @endif

                <div class="space-y-2 text-xs">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" placeholder="Cari nama atau nomor WhatsApp pelanggan..." class="w-full pl-8 pr-3 py-2 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500" />
                    </div>

                    <div class="space-y-2 max-h-60 overflow-y-auto pt-1">
                        @foreach ($customersList as $cust)
                            <div class="p-3 rounded-xl border border-slate-200 flex items-center justify-between hover:bg-slate-50 transition">
                                <div>
                                    <div class="font-bold text-slate-900">{{ $cust['name'] }} <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded-full font-semibold {{ $cust['tier'] === 'VIP Member' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}">{{ $cust['tier'] }}</span></div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $cust['phone'] }} • {{ $cust['points'] }} Poin</div>
                                </div>
                                <button type="button" wire:click="selectCustomer('{{ $cust['name'] }}', '{{ $cust['phone'] }}', {{ $cust['points'] }})"
                                        class="px-3 py-1.5 rounded-lg bg-orange-500 text-white font-bold text-[11px] hover:bg-orange-600 transition">
                                    Pilih
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= MODAL: BANTUAN & PINTASAN KEYBOARD ================= -->
    @if ($showHelpModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-keyboard"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Pintasan Keyboard (Shortcuts)</h3>
                            <p class="text-[11px] text-slate-400">Gunakan tombol keyboard untuk operasi kasir super cepat</p>
                        </div>
                    </div>
                    <button wire:click="$set('showHelpModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="p-2.5 rounded-xl border border-slate-100 bg-slate-50 flex items-center justify-between">
                        <span class="text-slate-600">Buka Pembayaran</span>
                        <kbd class="px-2 py-0.5 bg-white border border-slate-200 rounded font-mono font-bold text-slate-800 shadow-xs">F1 / Enter</kbd>
                    </div>
                    <div class="p-2.5 rounded-xl border border-slate-100 bg-slate-50 flex items-center justify-between">
                        <span class="text-slate-600">Cari Menu</span>
                        <kbd class="px-2 py-0.5 bg-white border border-slate-200 rounded font-mono font-bold text-slate-800 shadow-xs">F2 / /</kbd>
                    </div>
                    <div class="p-2.5 rounded-xl border border-slate-100 bg-slate-50 flex items-center justify-between">
                        <span class="text-slate-600">Tahan Pesanan (Hold)</span>
                        <kbd class="px-2 py-0.5 bg-white border border-slate-200 rounded font-mono font-bold text-slate-800 shadow-xs">F3</kbd>
                    </div>
                    <div class="p-2.5 rounded-xl border border-slate-100 bg-slate-50 flex items-center justify-between">
                        <span class="text-slate-600">Scan Barcode</span>
                        <kbd class="px-2 py-0.5 bg-white border border-slate-200 rounded font-mono font-bold text-slate-800 shadow-xs">F4</kbd>
                    </div>
                    <div class="p-2.5 rounded-xl border border-slate-100 bg-slate-50 flex items-center justify-between">
                        <span class="text-slate-600">Batal / Tutup Dialog</span>
                        <kbd class="px-2 py-0.5 bg-white border border-slate-200 rounded font-mono font-bold text-slate-800 shadow-xs">Esc</kbd>
                    </div>
                    <div class="p-2.5 rounded-xl border border-slate-100 bg-slate-50 flex items-center justify-between">
                        <span class="text-slate-600">Ubah Kuantitas</span>
                        <kbd class="px-2 py-0.5 bg-white border border-slate-200 rounded font-mono font-bold text-slate-800 shadow-xs">+ / -</kbd>
                    </div>
                </div>

                <div class="pt-2 text-center text-[11px] text-slate-400">
                    Perlu bantuan teknis? Hubungi Owner atau Super Admin di <span class="font-bold text-slate-700">support@lokarapos.id</span>
                </div>
            </div>
        </div>
    @endif

</div>
