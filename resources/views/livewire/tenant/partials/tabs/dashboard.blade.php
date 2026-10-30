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
