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
                            <span>{{ $isSalon ? 'Cabang & Staf' : 'Cabang, Staf & Shift' }}</span>
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
