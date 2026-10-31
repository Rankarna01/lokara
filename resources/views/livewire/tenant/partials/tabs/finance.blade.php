<div class="space-y-6">
    <!-- Header Title Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-bold text-slate-900">Laporan Laba Rugi (P&L) & Keuangan</h1>
                @if ($tenant?->isSalon() || $tenant?->hasFeature('has_services'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-orange-100 text-orange-700">
                        <i class="fa-solid fa-scissors text-[10px]"></i>
                        Modul Salon & Barbershop
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Analisis keuntungan bersih: Pendapatan dikurangi Biaya HPP/Modal Bahan, Komisi Staf, dan Beban Operasional (OPEX).</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="openExpenseModal"
                class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition cursor-pointer">
                <i class="fa-solid fa-plus"></i>
                <span>Catat Pengeluaran Toko</span>
            </button>
        </div>
    </div>

    @if ($tenant?->isSalon() || $tenant?->hasFeature('has_services'))
        @php
            $fd = $financeData;
            $prodSum = $fd['productSummary'];
            $srvSum = $fd['serviceSummary'];
        @endphp

        <!-- Top Overview Waterfall for Salon & Barbershop -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Ringkasan Laba Rugi Salon & Barbershop</h3>
                    <p class="text-xs text-slate-400">Periode: <span class="font-semibold text-slate-700">{{ $fd['dateFilterLabel'] }}</span></p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">
                        Margin Kotor: {{ $fd['combinedMargin'] }}%
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Total Omzet Gabungan -->
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                    <div class="text-xs text-slate-500 font-medium">Total Omzet Gabungan</div>
                    <div class="text-xl font-bold text-slate-900 mt-1">Rp {{ number_format($fd['combinedSales'], 0, ',', '.') }}</div>
                    <div class="text-[11px] text-slate-400 mt-1.5 flex items-center justify-between">
                        <span>Produk: Rp {{ number_format($prodSum['total_sales'], 0, ',', '.') }}</span>
                        <span>Jasa: Rp {{ number_format($srvSum['total_sales'], 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Total HPP & Komisi -->
                <div class="p-4 rounded-xl bg-rose-50/70 border border-rose-200/80">
                    <div class="text-xs text-rose-700 font-medium">HPP, Bahan & Komisi</div>
                    <div class="text-xl font-bold text-rose-600 mt-1">- Rp {{ number_format($fd['combinedDirectCost'], 0, ',', '.') }}</div>
                    <div class="text-[11px] text-rose-600/80 mt-1.5 flex items-center justify-between">
                        <span>HPP/Bahan: Rp {{ number_format($prodSum['total_cogs'] + $srvSum['total_material_cost'], 0, ',', '.') }}</span>
                        <span>Komisi: Rp {{ number_format($srvSum['total_commission'], 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Total OPEX Toko -->
                <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200/80">
                    <div class="text-xs text-amber-800 font-medium">Beban Operasional (OPEX)</div>
                    <div class="text-xl font-bold text-amber-700 mt-1">- Rp {{ number_format($totalOpex, 0, ',', '.') }}</div>
                    <div class="text-[11px] text-amber-700/80 mt-1.5">Sewa, Gaji, Listrik, Air, & Marketing</div>
                </div>

                <!-- Laba Bersih Toko (Net Profit) -->
                @php
                    $salonNetProfit = $fd['combinedGrossProfit'] - $totalOpex;
                @endphp
                <div class="p-4 rounded-xl {{ $salonNetProfit >= 0 ? 'bg-emerald-900 text-white' : 'bg-rose-900 text-white' }} shadow-sm">
                    <div class="text-xs text-slate-200 font-medium">Laba Bersih Akhir (Net Profit)</div>
                    <div class="text-xl font-bold mt-1 {{ $salonNetProfit >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                        Rp {{ number_format($salonNetProfit, 0, ',', '.') }}
                    </div>
                    <div class="text-[11px] text-slate-300 mt-1.5">
                        {{ $salonNetProfit >= 0 ? 'Surplus / Profit Bersih Usaha' : 'Defisit Operasional' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- 2 TABS UTAMA: PENJUALAN PRODUK MASTER & PENJUALAN MASTER LAYANAN -->
        <div class="space-y-4">
            <!-- Tab Navigation Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl">
                    <!-- Tab 1: Penjualan Produk Master -->
                    <button wire:click="setFinanceSubTab('products')"
                        class="px-4 py-2 rounded-lg text-xs font-semibold flex items-center gap-2 transition cursor-pointer {{ $financeSubTab === 'products' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        <i class="fa-solid fa-box text-sm"></i>
                        <span>1. Penjualan Produk Master</span>
                        <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] {{ $financeSubTab === 'products' ? 'bg-orange-100 text-orange-700' : 'bg-slate-200 text-slate-600' }}">
                            {{ count($fd['productRows']) }} Produk
                        </span>
                    </button>

                    <!-- Tab 2: Penjualan Master Layanan -->
                    <button wire:click="setFinanceSubTab('services')"
                        class="px-4 py-2 rounded-lg text-xs font-semibold flex items-center gap-2 transition cursor-pointer {{ $financeSubTab === 'services' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        <i class="fa-solid fa-scissors text-sm"></i>
                        <span>2. Penjualan Master Layanan</span>
                        <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] {{ $financeSubTab === 'services' ? 'bg-orange-100 text-orange-700' : 'bg-slate-200 text-slate-600' }}">
                            {{ count($fd['serviceRows']) }} Layanan
                        </span>
                    </button>
                </div>

                <div class="text-xs text-slate-500 font-medium">
                    Analisis Profitabilitas Per {{ $financeSubTab === 'products' ? 'Produk Retail' : 'Layanan Jasa & Komisi' }}
                </div>
            </div>

            <!-- Toolbar: Filtering, Searching & Sorting -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="flex flex-1 flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <!-- Search Input -->
                    <div class="relative flex-1 max-w-md">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text"
                            wire:model.live.debounce.300ms="financeSearch"
                            placeholder="{{ $financeSubTab === 'products' ? 'Cari nama produk, SKU, kategori...' : 'Cari layanan jasa, tag, kategori...' }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-3 py-2 text-xs focus:ring-1 focus:ring-orange-500 focus:bg-white focus:outline-none transition" />
                        @if ($financeSearch)
                            <button wire:click="$set('financeSearch', '')" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        @endif
                    </div>

                    <!-- Category Filter Dropdown -->
                    <div class="w-full sm:w-48">
                        <select wire:model.live="financeCategoryFilter"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:ring-1 focus:ring-orange-500 focus:bg-white focus:outline-none transition">
                            <option value="all">Semua Kategori</option>
                            @foreach ($fd['activeCategories'] as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Period Filter -->
                    <div class="w-full sm:w-44">
                        <select wire:model.live="financeDateFilter"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:ring-1 focus:ring-orange-500 focus:bg-white focus:outline-none transition">
                            <option value="this_month">Bulan Ini</option>
                            <option value="today">Hari Ini</option>
                            <option value="7_days">7 Hari Terakhir</option>
                            <option value="all">Semua Periode</option>
                        </select>
                    </div>

                    <!-- Sort By Filter -->
                    <div class="w-full sm:w-44">
                        <select wire:model.live="financeSortBy"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:ring-1 focus:ring-orange-500 focus:bg-white focus:outline-none transition">
                            <option value="profit_desc">Laba Terbesar</option>
                            <option value="qty_desc">{{ $financeSubTab === 'products' ? 'Terjual Terbanyak' : 'Sesi Terbanyak' }}</option>
                            <option value="sales_desc">Omzet Tertinggi</option>
                            @if ($financeSubTab === 'products')
                                <option value="margin_desc">Margin Tertinggi (%)</option>
                            @else
                                <option value="commission_desc">Komisi Terbesar</option>
                            @endif
                            <option value="name_asc">Nama (A - Z)</option>
                        </select>
                    </div>
                </div>

                @if ($financeSearch || $financeCategoryFilter !== 'all' || $financeDateFilter !== 'this_month' || $financeSortBy !== 'profit_desc')
                    <button wire:click="resetFinanceFilters"
                        class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-medium flex items-center justify-center gap-1.5 transition cursor-pointer">
                        <i class="fa-solid fa-arrow-rotate-left text-[11px]"></i>
                        <span>Reset Filter</span>
                    </button>
                @endif
            </div>

            <!-- ================= TAB 1: PENJUALAN PRODUK MASTER ================= -->
            @if ($financeSubTab === 'products')
                <!-- Product Statistics Cards -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 shadow-sm">
                        <div class="text-[11px] text-slate-500 font-medium">Total Omzet Produk</div>
                        <div class="text-base font-bold text-slate-900 mt-1">Rp {{ number_format($prodSum['total_sales'], 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 shadow-sm">
                        <div class="text-[11px] text-slate-500 font-medium">Total Beban Pokok (HPP)</div>
                        <div class="text-base font-bold text-rose-600 mt-1">Rp {{ number_format($prodSum['total_cogs'], 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 shadow-sm">
                        <div class="text-[11px] text-slate-500 font-medium">Laba Kotor Produk</div>
                        <div class="text-base font-bold text-emerald-600 mt-1">Rp {{ number_format($prodSum['total_profit'], 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 shadow-sm">
                        <div class="text-[11px] text-slate-500 font-medium">Rata-rata Margin</div>
                        <div class="text-base font-bold text-orange-600 mt-1">{{ $prodSum['avg_margin'] }}%</div>
                    </div>
                    <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 shadow-sm col-span-2 md:col-span-1">
                        <div class="text-[11px] text-slate-500 font-medium">Total Unit Terjual</div>
                        <div class="text-base font-bold text-indigo-600 mt-1">{{ number_format($prodSum['total_qty'], 0, ',', '.') }} unit</div>
                    </div>
                </div>

                <!-- Product Profitability Table -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">Rincian Laba Rugi Penjualan Produk Master (Retail & Display)</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Menampilkan seluruh produk master salon & barbershop beserta performa margin penjualan</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-orange-50 text-orange-700">
                            {{ count($fd['productRows']) }} item ditemukan
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 border-b border-slate-200/80 text-[11px] text-slate-500 uppercase font-semibold">
                                <tr>
                                    <th class="py-3 px-4">Nama Produk & SKU</th>
                                    <th class="py-3 px-3">Kategori</th>
                                    <th class="py-3 px-3 text-right">Modal HPP/Unit</th>
                                    <th class="py-3 px-3 text-right">Harga Jual</th>
                                    <th class="py-3 px-3 text-center">Qty Terjual</th>
                                    <th class="py-3 px-3 text-right">Total Omzet</th>
                                    <th class="py-3 px-3 text-right">Total HPP</th>
                                    <th class="py-3 px-3 text-right">Laba Kotor</th>
                                    <th class="py-3 px-3 text-center">Margin (%)</th>
                                    <th class="py-3 px-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($fd['productRows'] as $p)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-3">
                                                <img src="{{ $p['image_url'] ?: 'https://images.unsplash.com/photo-1597354984706-aec992b7d526?auto=format&fit=crop&w=80&h=80&q=80' }}"
                                                    alt="{{ $p['name'] }}"
                                                    class="w-9 h-9 rounded-lg object-cover border border-slate-200 shrink-0" />
                                                <div>
                                                    <div class="font-bold text-slate-900 text-xs">{{ $p['name'] }}</div>
                                                    <div class="text-[11px] font-mono text-slate-400">{{ $p['sku'] }} • Saldo Rak: {{ $p['stock_quantity'] }} {{ $p['unit'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-3">
                                            <span class="text-[11px] font-medium px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">
                                                {{ $p['category_name'] }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-right text-xs text-slate-500 font-medium">
                                            Rp {{ number_format($p['unit_cost'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-3 text-right text-xs font-semibold text-slate-800">
                                            Rp {{ number_format($p['unit_price'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-3 text-center font-bold text-xs {{ $p['qty_sold'] > 0 ? 'text-indigo-600' : 'text-slate-400' }}">
                                            {{ number_format($p['qty_sold'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-3 text-right font-semibold text-xs text-slate-900">
                                            Rp {{ number_format($p['gross_sales'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-3 text-right text-xs font-medium text-rose-600">
                                            Rp {{ number_format($p['total_cost'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-3 text-right font-bold text-xs text-emerald-600">
                                            Rp {{ number_format($p['gross_profit'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="text-xs font-bold {{ $p['margin_pct'] >= 50 ? 'text-emerald-700' : ($p['margin_pct'] >= 30 ? 'text-amber-700' : 'text-slate-600') }}">
                                                {{ $p['margin_pct'] }}%
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @if ($p['qty_sold'] > 0)
                                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                                    Aktif Terjual
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-slate-100 text-slate-500">
                                                    Belum Ada Penjualan
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="py-8 text-center text-xs text-slate-400">
                                            Tidak ada produk master yang sesuai dengan pencarian atau filter kategori saat ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- ================= TAB 2: PENJUALAN MASTER LAYANAN ================= -->
            @if ($financeSubTab === 'services')
                <!-- Service Statistics Cards -->
                <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
                    <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 shadow-sm">
                        <div class="text-[11px] text-slate-500 font-medium">Total Omzet Layanan</div>
                        <div class="text-base font-bold text-slate-900 mt-1">Rp {{ number_format($srvSum['total_sales'], 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 shadow-sm">
                        <div class="text-[11px] text-slate-500 font-medium">Modal Bahan / Pakai</div>
                        <div class="text-base font-bold text-rose-600 mt-1">Rp {{ number_format($srvSum['total_material_cost'], 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 shadow-sm">
                        <div class="text-[11px] text-slate-500 font-medium">Komisi Kapster / Stylist</div>
                        <div class="text-base font-bold text-amber-600 mt-1">Rp {{ number_format($srvSum['total_commission'], 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 shadow-sm">
                        <div class="text-[11px] text-slate-500 font-medium">Laba Bersih Layanan</div>
                        <div class="text-base font-bold text-emerald-600 mt-1">Rp {{ number_format($srvSum['total_profit'], 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 shadow-sm">
                        <div class="text-[11px] text-slate-500 font-medium">Rata-rata Margin Bersih</div>
                        <div class="text-base font-bold text-orange-600 mt-1">{{ $srvSum['avg_margin'] }}%</div>
                    </div>
                    <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 shadow-sm col-span-2 md:col-span-1">
                        <div class="text-[11px] text-slate-500 font-medium">Sesi Dikerjakan</div>
                        <div class="text-base font-bold text-indigo-600 mt-1">{{ number_format($srvSum['total_sessions'], 0, ',', '.') }} sesi</div>
                    </div>
                </div>

                <!-- Service Profitability Table -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">Rincian Laba Rugi Master Layanan Jasa (Treatment & Styling)</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Analisis omzet per layanan jasa dikurangi modal bahan kerja (shampoo, cream, pomade) dan komisi kapster</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-orange-50 text-orange-700">
                            {{ count($fd['serviceRows']) }} layanan ditemukan
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 border-b border-slate-200/80 text-[11px] text-slate-500 uppercase font-semibold">
                                <tr>
                                    <th class="py-3 px-4">Nama Layanan Jasa</th>
                                    <th class="py-3 px-3">Tag Kategori</th>
                                    <th class="py-3 px-3 text-center">Durasi</th>
                                    <th class="py-3 px-3 text-right">Tarif Jasa</th>
                                    <th class="py-3 px-3 text-right">Modal Bahan</th>
                                    <th class="py-3 px-3 text-center">Skema Komisi</th>
                                    <th class="py-3 px-3 text-center">Sesi Selesai</th>
                                    <th class="py-3 px-3 text-right">Total Omzet</th>
                                    <th class="py-3 px-3 text-right">Total Bahan</th>
                                    <th class="py-3 px-3 text-right">Total Komisi</th>
                                    <th class="py-3 px-3 text-right">Laba Bersih</th>
                                    <th class="py-3 px-4 text-center">Margin (%)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($fd['serviceRows'] as $s)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-3">
                                                <img src="{{ $s['image_url'] ?: 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?auto=format&fit=crop&w=80&h=80&q=80' }}"
                                                    alt="{{ $s['name'] }}"
                                                    class="w-9 h-9 rounded-lg object-cover border border-slate-200 shrink-0" />
                                                <div>
                                                    <div class="font-bold text-slate-900 text-xs">{{ $s['name'] }}</div>
                                                    <div class="text-[11px] text-slate-400">{{ $s['category_name'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-3">
                                            <span class="text-[11px] font-medium px-2 py-0.5 rounded-md bg-orange-50 text-orange-700">
                                                {{ $s['category_tag'] }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-center text-xs text-slate-500 font-medium">
                                            {{ $s['duration_minutes'] }} mnt
                                        </td>
                                        <td class="py-3 px-3 text-right text-xs font-semibold text-slate-800">
                                            Rp {{ number_format($s['tarif'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-3 text-right text-xs text-slate-500 font-medium">
                                            Rp {{ number_format($s['unit_material_cost'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-3 text-center text-[11px] text-amber-700 font-semibold">
                                            {{ $s['commission_scheme'] }}
                                        </td>
                                        <td class="py-3 px-3 text-center font-bold text-xs {{ $s['sessions_count'] > 0 ? 'text-indigo-600' : 'text-slate-400' }}">
                                            {{ number_format($s['sessions_count'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-3 text-right font-semibold text-xs text-slate-900">
                                            Rp {{ number_format($s['gross_sales'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-3 text-right text-xs font-medium text-rose-600">
                                            Rp {{ number_format($s['total_material_cost'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-3 text-right text-xs font-medium text-amber-600">
                                            Rp {{ number_format($s['total_commission'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-3 text-right font-bold text-xs text-emerald-600">
                                            Rp {{ number_format($s['net_profit'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="text-xs font-bold {{ $s['margin_pct'] >= 50 ? 'text-emerald-700' : ($s['margin_pct'] >= 30 ? 'text-amber-700' : 'text-slate-600') }}">
                                                {{ $s['margin_pct'] }}%
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="py-8 text-center text-xs text-slate-400">
                                            Tidak ada master layanan yang sesuai dengan pencarian atau filter kategori saat ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- Standard P&L Waterfall Card (Non-salon / General F&B) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-900 text-base">Laporan Laba Rugi Bulan Ini</h3>

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
    @endif

    <!-- Expenses (OPEX) Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Rincian Beban Biaya Operasional (OPEX)</h3>
                <p class="text-xs text-slate-400 mt-0.5">Pengeluaran rutin outlet: sewa, listrik, air, gaji, dan pemeliharaan</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-rose-50 text-rose-700">
                Total OPEX: Rp {{ number_format($totalOpex, 0, ',', '.') }}
            </span>
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
                    @forelse($expenses as $exp)
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
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-xs text-slate-400">
                                Belum ada catatan pengeluaran operasional (OPEX) untuk toko ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
