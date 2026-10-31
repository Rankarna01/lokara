<div class="space-y-6">
    <!-- 1. Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-receipt"></i>
                </span>
                <h1 class="text-xl font-bold text-slate-900">Riwayat Transaksi Penjualan</h1>
                <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-2 py-0.5 rounded-full flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Sinkronisasi POS Kasir
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Audit log struk belanja, status order, rincian pembayaran, rekapitulasi omset, dan analisis visual tren transaksi kasir.
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('pos.terminal') }}" target="_blank"
               class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-cash-register"></i>
                <span>Buka Layar POS Kasir</span>
            </a>
        </div>
    </div>

    <!-- 2. Executive Stat Cards (4 Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Omset -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Omset</span>
                <span class="w-7 h-7 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-coins"></i>
                </span>
            </div>
            <div class="my-3">
                <div class="text-2xl font-black text-slate-900 tracking-tight whitespace-nowrap">
                    Rp {{ number_format($transactionAnalytics['totalRevenue'], 0, ',', '.') }}
                </div>
                <div class="text-[11px] font-medium text-slate-400 mt-0.5">
                    Periode: {{ match($transactionDateFilter) { 'today' => 'Hari Ini', 'yesterday' => 'Kemarin', '7_days' => '7 Hari Terakhir', '30_days' => '30 Hari Terakhir', 'this_month' => 'Bulan Ini', 'custom' => 'Rentang Kustom', default => 'Semua Waktu' } }}
                </div>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Rata-rata/hari:</span>
                <span class="font-bold text-slate-700 whitespace-nowrap">Rp {{ number_format($transactionAnalytics['avgDailyRevenue'], 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Card 2: Transaksi Selesai -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Transaksi Lunas</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-circle-check"></i>
                </span>
            </div>
            <div class="my-3">
                <div class="text-2xl font-black text-slate-900 tracking-tight">
                    {{ $transactionAnalytics['completedCount'] }} <span class="text-sm font-semibold text-slate-400">Struk</span>
                </div>
                <div class="text-[11px] font-medium text-emerald-600 mt-0.5 flex items-center gap-1">
                    <i class="fa-solid fa-shield-halved text-[10px]"></i>
                    <span>Tervalidasi & lunas</span>
                </div>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Pesanan Batal/Void:</span>
                <span class="font-bold {{ $transactionAnalytics['voidedOrdersCount'] > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                    {{ $transactionAnalytics['voidedOrdersCount'] }} pesanan
                </span>
            </div>
        </div>

        <!-- Card 3: Rata-rata Transaksi (AOV) -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Rata-rata Struk (AOV)</span>
                <span class="w-7 h-7 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-basket-shopping"></i>
                </span>
            </div>
            <div class="my-3">
                <div class="text-2xl font-black text-slate-900 tracking-tight whitespace-nowrap">
                    Rp {{ number_format($transactionAnalytics['avgTicket'], 0, ',', '.') }}
                </div>
                <div class="text-[11px] font-medium text-slate-400 mt-0.5">
                    Basket size nilai rata-rata order
                </div>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Tren transaksi:</span>
                <span class="font-bold text-sky-600">Konsisten</span>
            </div>
        </div>

        <!-- Card 4: Metode Bayar Terbanyak -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pembayaran Terfavorit</span>
                <span class="w-7 h-7 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-credit-card"></i>
                </span>
            </div>
            <div class="my-3">
                <div class="text-2xl font-black text-slate-900 tracking-tight uppercase">
                    {{ $transactionAnalytics['topPaymentMethod'] }}
                </div>
                <div class="text-[11px] font-medium text-violet-600 mt-0.5">
                    {{ $transactionAnalytics['topPaymentSharePct'] }}% dari total volume omset
                </div>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Kanal bayar aktif:</span>
                <span class="font-bold text-slate-700">{{ count($transactionAnalytics['paymentAmounts']) }} Saluran</span>
            </div>
        </div>
    </div>

    <!-- 3. Chart Trend Penjualan & Transaksi POS Lengkap -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 space-y-4">
        <!-- Chart Controls Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                    <h2 class="text-sm font-bold text-slate-900">Tren Penjualan & Frekuensi Transaksi POS</h2>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">
                    Grafik pergerakan omset kasir per hari dalam kurun waktu audit.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- Metric Selector: Revenue vs Count -->
                <div class="bg-slate-100 p-0.5 rounded-xl flex items-center text-xs font-bold">
                    <button wire:click="setTransactionTrendMetric('revenue')"
                            class="px-3 py-1.5 rounded-lg transition {{ $transactionTrendMetric === 'revenue' ? 'bg-white text-orange-600 shadow-xs' : 'text-slate-500 hover:text-slate-900' }}">
                        <i class="fa-solid fa-coins text-[10px] mr-1"></i> Omset (Rp)
                    </button>
                    <button wire:click="setTransactionTrendMetric('count')"
                            class="px-3 py-1.5 rounded-lg transition {{ $transactionTrendMetric === 'count' ? 'bg-white text-sky-600 shadow-xs' : 'text-slate-500 hover:text-slate-900' }}">
                        <i class="fa-solid fa-receipt text-[10px] mr-1"></i> Volume (Struk)
                    </button>
                </div>

                <!-- Trend Period Selector -->
                <div class="bg-slate-100 p-0.5 rounded-xl flex items-center text-xs font-bold">
                    <button wire:click="setTransactionTrendPeriod('7_days')"
                            class="px-2.5 py-1.5 rounded-lg transition {{ $transactionTrendPeriod === '7_days' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900' }}">
                        7 Hari
                    </button>
                    <button wire:click="setTransactionTrendPeriod('14_days')"
                            class="px-2.5 py-1.5 rounded-lg transition {{ $transactionTrendPeriod === '14_days' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900' }}">
                        14 Hari
                    </button>
                    <button wire:click="setTransactionTrendPeriod('30_days')"
                            class="px-2.5 py-1.5 rounded-lg transition {{ $transactionTrendPeriod === '30_days' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900' }}">
                        30 Hari
                    </button>
                </div>
            </div>
        </div>

        <!-- Chart Body: Visual Bars + Side Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 pt-2">
            <!-- Left: Interactive Daily Bar Chart (8 cols) -->
            <div class="lg:col-span-8 flex flex-col justify-between">
                <!-- Top Summary Tag -->
                <div class="flex items-center justify-between text-xs mb-3">
                    <div class="flex items-center gap-3">
                        <span class="text-slate-500">Total Periode:</span>
                        <span class="font-bold text-slate-900">
                            @if($transactionTrendMetric === 'revenue')
                                Rp {{ number_format($transactionAnalytics['trendTotalRevenue'], 0, ',', '.') }}
                            @else
                                {{ $transactionAnalytics['trendTotalCount'] }} Struk
                            @endif
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-400">
                        Puncak Tertinggi:
                        <span class="font-bold text-slate-700">
                            @if($transactionTrendMetric === 'revenue')
                                Rp {{ number_format($transactionAnalytics['maxRevenue'], 0, ',', '.') }}
                            @else
                                {{ $transactionAnalytics['maxCount'] }} Struk
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Bars Container -->
                <div class="h-44 flex items-end gap-2 pt-4 px-2 border-b border-slate-100">
                    @forelse($transactionAnalytics['chartDays'] as $day)
                        <div class="flex-1 flex flex-col items-center h-full justify-end group relative cursor-pointer">
                            <!-- Tooltip on Hover -->
                            <div class="absolute -top-12 z-20 hidden group-hover:flex flex-col items-center pointer-events-none transition-all">
                                <div class="bg-slate-900 text-white text-[10px] font-medium py-1 px-2.5 rounded-lg shadow-lg whitespace-nowrap text-center">
                                    <div class="font-bold">{{ $day['full_label'] }}</div>
                                    <div class="text-orange-400">Rp {{ number_format($day['revenue'], 0, ',', '.') }} • {{ $day['tx_count'] }} Struk</div>
                                </div>
                                <div class="w-2 h-1 bg-slate-900 rotate-45 -mt-0.5"></div>
                            </div>

                            <!-- Bar Column -->
                            <div class="w-full max-w-[28px] rounded-t-lg transition-all duration-300 {{ $day['is_today'] ? ($transactionTrendMetric === 'revenue' ? 'bg-orange-500 group-hover:bg-orange-600' : 'bg-sky-500 group-hover:bg-sky-600') : ($transactionTrendMetric === 'revenue' ? 'bg-orange-200 group-hover:bg-orange-400' : 'bg-sky-200 group-hover:bg-sky-400') }}"
                                 style="height: {{ $day['pct'] }}%;">
                            </div>

                            <!-- Value Label on top of bar (desktop) -->
                            @if($day['tx_count'] > 0)
                                <div class="absolute text-[9px] font-bold text-slate-600 group-hover:text-slate-900 transition mb-1"
                                     style="bottom: {{ min(90, $day['pct'] + 2) }}%;">
                                    @if($transactionTrendMetric === 'revenue')
                                        {{ $day['revenue'] >= 1000000 ? round($day['revenue'] / 1000000, 1) . 'jt' : ($day['revenue'] >= 1000 ? round($day['revenue'] / 1000) . 'k' : $day['revenue']) }}
                                    @else
                                        {{ $day['tx_count'] }}
                                    @endif
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="w-full h-full flex items-center justify-center text-xs text-slate-400">
                            Belum ada riwayat transaksi pada kurun waktu ini.
                        </div>
                    @endforelse
                </div>

                <!-- X-Axis Date Labels -->
                <div class="flex items-center gap-2 pt-2 px-2 text-[10px] font-semibold text-slate-400">
                    @foreach($transactionAnalytics['chartDays'] as $day)
                        <div class="flex-1 text-center truncate {{ $day['is_today'] ? 'text-orange-600 font-bold' : '' }}">
                            <div>{{ $day['short_label'] }}</div>
                            <div class="text-[9px] text-slate-300">{{ $day['day_name'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Right: Sebaran Metode & Tipe Order (4 cols) -->
            <div class="lg:col-span-4 space-y-4 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-6 pt-4 lg:pt-0">
                <!-- Breakdown Kanal Pembayaran -->
                <div>
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-2 flex items-center justify-between">
                        <span>Sebaran Metode Bayar</span>
                        <span class="text-[10px] text-slate-400 font-normal">Selesai</span>
                    </h3>
                    <div class="space-y-2">
                        @php
                            $methods = [
                                'qris' => ['label' => 'QRIS', 'color' => 'bg-emerald-500', 'icon' => 'fa-qrcode'],
                                'cash' => ['label' => 'Tunai (Cash)', 'color' => 'bg-amber-500', 'icon' => 'fa-money-bill-wave'],
                                'transfer' => ['label' => 'Transfer Bank', 'color' => 'bg-sky-500', 'icon' => 'fa-building-columns'],
                                'debit' => ['label' => 'Kartu Debit', 'color' => 'bg-violet-500', 'icon' => 'fa-credit-card'],
                                'credit' => ['label' => 'Kartu Kredit', 'color' => 'bg-rose-500', 'icon' => 'fa-credit-card'],
                            ];
                        @endphp

                        @forelse($transactionAnalytics['paymentAmounts'] as $key => $amount)
                            @php
                                $cfg = $methods[$key] ?? ['label' => strtoupper($key), 'color' => 'bg-slate-500', 'icon' => 'fa-wallet'];
                                $pct = $transactionAnalytics['totalRevenue'] > 0 ? round(($amount / $transactionAnalytics['totalRevenue']) * 100) : 0;
                            @endphp
                            <div class="text-xs space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-slate-700 flex items-center gap-1.5">
                                        <i class="fa-solid {{ $cfg['icon'] }} text-[10px] text-slate-400"></i>
                                        {{ $cfg['label'] }}
                                    </span>
                                    <span class="font-bold text-slate-900">
                                        Rp {{ number_format($amount, 0, ',', '.') }}
                                        <span class="text-[10px] font-normal text-slate-400">({{ $pct }}%)</span>
                                    </span>
                                </div>
                                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                    <div class="{{ $cfg['color'] }} h-full rounded-full" style="width: {{ $pct }}%;"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-xs text-slate-400 py-2">Belum ada data pembayaran.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Breakdown Tipe Order -->
                <div class="pt-3 border-t border-slate-100">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-2">
                        Distribusi Tipe Pesanan
                    </h3>
                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                        <div class="bg-slate-50 p-2 rounded-xl border border-slate-200/60">
                            <span class="text-[10px] font-semibold text-slate-400 block">Dine In</span>
                            <span class="font-black text-slate-900 text-sm mt-0.5 block">
                                {{ $transactionAnalytics['orderTypes']['dine_in'] ?? 0 }}
                            </span>
                        </div>
                        <div class="bg-slate-50 p-2 rounded-xl border border-slate-200/60">
                            <span class="text-[10px] font-semibold text-slate-400 block">Take Away</span>
                            <span class="font-black text-slate-900 text-sm mt-0.5 block">
                                {{ $transactionAnalytics['orderTypes']['take_away'] ?? 0 }}
                            </span>
                        </div>
                        <div class="bg-slate-50 p-2 rounded-xl border border-slate-200/60">
                            <span class="text-[10px] font-semibold text-slate-400 block">Delivery</span>
                            <span class="font-black text-slate-900 text-sm mt-0.5 block">
                                {{ $transactionAnalytics['orderTypes']['delivery'] ?? 0 }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($tenant?->isSalon() || $tenant?->hasFeature('has_services') || $isSalon)
        <!-- Salon & Barbershop Dual Tab Selector (Layanan vs Produk) -->
        <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-scissors"></i>
                    </span>
                    <div>
                        <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                            <span>Kategori Riwayat Transaksi:</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-50 text-orange-600 border border-orange-200">
                                Modul Salon & Barbershop
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-400">Pemisahan audit transaksi antara pengerjaan layanan jasa (stylist/kapster) dan penjualan produk retail.</p>
                    </div>
                </div>

                <div class="flex items-center gap-1.5 bg-slate-100/90 p-1 rounded-xl self-start md:self-auto overflow-x-auto max-w-full">
                    <!-- Tab Layanan Jasa -->
                    <button wire:click="setTransactionTypeTab('services')"
                            type="button"
                            class="px-3.5 py-1.5 rounded-lg text-xs font-bold flex items-center gap-2 transition cursor-pointer whitespace-nowrap {{ $transactionTypeTab === 'services' ? 'bg-white text-orange-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        <i class="fa-solid fa-scissors text-xs"></i>
                        <span>Penjualan Layanan (Jasa)</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $transactionTypeTab === 'services' ? 'bg-orange-100 text-orange-700' : 'bg-slate-200 text-slate-600' }}">
                            {{ $serviceTransactionsCount }}
                        </span>
                    </button>

                    <!-- Tab Produk Retail -->
                    <button wire:click="setTransactionTypeTab('products')"
                            type="button"
                            class="px-3.5 py-1.5 rounded-lg text-xs font-bold flex items-center gap-2 transition cursor-pointer whitespace-nowrap {{ $transactionTypeTab === 'products' ? 'bg-white text-orange-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        <i class="fa-solid fa-box text-xs"></i>
                        <span>Penjualan Produk (Retail)</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $transactionTypeTab === 'products' ? 'bg-orange-100 text-orange-700' : 'bg-slate-200 text-slate-600' }}">
                            {{ $productTransactionsCount }}
                        </span>
                    </button>

                    <!-- Tab Semua Transaksi -->
                    <button wire:click="setTransactionTypeTab('all')"
                            type="button"
                            class="px-3.5 py-1.5 rounded-lg text-xs font-bold flex items-center gap-2 transition cursor-pointer whitespace-nowrap {{ $transactionTypeTab === 'all' ? 'bg-white text-orange-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        <i class="fa-solid fa-receipt text-xs"></i>
                        <span>Semua Transaksi</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $transactionTypeTab === 'all' ? 'bg-orange-100 text-orange-700' : 'bg-slate-200 text-slate-600' }}">
                            {{ $allTransactionsCount }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- 4. Filter & Search Controls -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <!-- Search Bar -->
            <div class="relative lg:col-span-2">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text"
                       wire:model.live.debounce.300ms="searchTransaction"
                       placeholder="Cari No. Order, pelanggan, kasir, item..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-8 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition" />
                @if($searchTransaction)
                    <button wire:click="$set('searchTransaction', '')"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                @endif
            </div>

            <!-- Filter Status Order -->
            <div>
                <select wire:model.live="transactionStatusFilter"
                        class="w-full bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 py-2 px-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500">
                    <option value="all">Semua Status Order</option>
                    <option value="completed">● Selesai (Lunas)</option>
                    <option value="hold">⏸ Hold (Tertahan)</option>
                    <option value="voided">✕ Void (Dibatalkan)</option>
                </select>
            </div>

            <!-- Filter Metode Pembayaran -->
            <div>
                <select wire:model.live="transactionPaymentMethodFilter"
                        class="w-full bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 py-2 px-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500">
                    <option value="all">Semua Pembayaran</option>
                    <option value="cash">💵 Tunai (Cash)</option>
                    <option value="qris">📱 QRIS</option>
                    <option value="transfer">🏦 Transfer Bank</option>
                    <option value="debit">💳 Kartu Debit</option>
                    <option value="credit">💳 Kartu Kredit</option>
                </select>
            </div>

            <!-- Filter Tipe Order -->
            <div>
                <select wire:model.live="transactionOrderTypeFilter"
                        class="w-full bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 py-2 px-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500">
                    <option value="all">Semua Tipe Order</option>
                    <option value="dine_in">Makan di Tempat (Dine In)</option>
                    <option value="take_away">Bawa Pulang (Take Away)</option>
                    <option value="delivery">Pesan Antar (Delivery)</option>
                </select>
            </div>

            <!-- Filter Rentang Waktu -->
            <div class="flex items-center gap-1.5">
                <select wire:model.live="transactionDateFilter"
                        class="w-full bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 py-2 px-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500">
                    <option value="30_days">30 Hari Terakhir</option>
                    <option value="7_days">7 Hari Terakhir</option>
                    <option value="today">Hari Ini</option>
                    <option value="yesterday">Kemarin</option>
                    <option value="this_month">Bulan Ini</option>
                    <option value="all">Semua Waktu</option>
                    <option value="custom">📅 Rentang Kustom...</option>
                </select>

                <!-- Reset Button -->
                <button wire:click="resetTransactionFilters"
                        title="Reset Semua Filter"
                        class="p-2 border border-slate-200 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition shrink-0">
                    <i class="fa-solid fa-rotate-right text-xs"></i>
                </button>
            </div>
        </div>

        <!-- Custom Date & Time Filter Row -->
        @if($transactionDateFilter === 'custom')
            <div class="p-3 bg-orange-50/60 border border-orange-200/80 rounded-xl flex flex-wrap items-center gap-3 text-xs animate-in fade-in duration-150">
                <div class="flex items-center gap-1.5 font-bold text-orange-900">
                    <i class="fa-solid fa-calendar-days text-orange-500"></i>
                    <span>Kustom Periode:</span>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-slate-500 font-medium">Dari:</span>
                    <input type="date" wire:model.live="transactionStartDate"
                           class="bg-white border border-slate-200 rounded-lg px-2.5 py-1 text-xs font-medium text-slate-700" />
                    <input type="time" wire:model.live="transactionStartTime"
                           class="bg-white border border-slate-200 rounded-lg px-2 py-1 text-xs font-medium text-slate-700" />
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-slate-500 font-medium">Sampai:</span>
                    <input type="date" wire:model.live="transactionEndDate"
                           class="bg-white border border-slate-200 rounded-lg px-2.5 py-1 text-xs font-medium text-slate-700" />
                    <input type="time" wire:model.live="transactionEndTime"
                           class="bg-white border border-slate-200 rounded-lg px-2 py-1 text-xs font-medium text-slate-700" />
                </div>
            </div>
        @endif
    </div>

    <!-- 5. Transactions Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-500 uppercase font-bold text-[11px] tracking-wider">
                    <tr>
                        <th class="py-3 px-4">No. Order & Kasir</th>
                        <th class="py-3 px-4">Waktu & Tanggal</th>
                        <th class="py-3 px-4">Pelanggan & Meja</th>
                        <th class="py-3 px-4">Item Pesanan</th>
                        <th class="py-3 px-4">Metode Bayar</th>
                        <th class="py-3 px-4 text-right">Total Transaksi</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $tx)
                        @php
                            $firstPayment = $tx->payments->first();
                            $paymentMethod = $firstPayment ? strtolower($firstPayment->payment_method) : 'cash';
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- No. Order & Kasir -->
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                <button wire:click="viewTransactionDetail({{ $tx->id }})"
                                        class="hover:text-orange-600 transition text-left font-mono">
                                    {{ $tx->order_number }}
                                </button>
                                <div class="text-[11px] font-normal text-slate-400 flex items-center gap-1.5 mt-0.5">
                                    <i class="fa-solid fa-user-circle text-slate-400 text-[10px]"></i>
                                    <span>Kasir: {{ $tx->user?->name ?? 'Kasir Lokara' }}</span>
                                    <span>•</span>
                                    <span>{{ $tx->outlet?->name ?? 'Outlet Utama' }}</span>
                                </div>
                            </td>

                            <!-- Waktu & Tanggal -->
                            <td class="py-3.5 px-4 text-slate-600">
                                <div class="font-semibold text-slate-800 whitespace-nowrap">
                                    {{ $tx->created_at->format('d M Y, H:i') }} WIB
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    {{ $tx->created_at->diffForHumans() }}
                                </div>
                            </td>

                            <!-- Pelanggan & Meja -->
                            <td class="py-3.5 px-4 text-slate-600">
                                <div class="font-semibold text-slate-900 flex items-center gap-1.5">
                                    <i class="fa-solid fa-user text-slate-400 text-[10px]"></i>
                                    <span>{{ $tx->customer?->name ?? 'Pelanggan Reguler' }}</span>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1">
                                    <span class="capitalize font-medium">{{ str_replace('_', ' ', $tx->order_type) }}</span>
                                    @if($tx->table_number)
                                        <span class="text-orange-600 font-bold">• Meja {{ $tx->table_number }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Item Pesanan -->
                            <td class="py-3.5 px-4">
                                @php
                                    $hasServiceItems = $tx->items->contains(fn($i) => $i->product?->type === 'service');
                                    $hasProductItems = $tx->items->contains(fn($i) => $i->product?->type !== 'service');
                                @endphp
                                <div class="flex items-center gap-1.5 mb-1 flex-wrap">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-[10px]">
                                        <i class="fa-solid fa-cube text-[9px] text-slate-400"></i>
                                        {{ $tx->items->count() }} Macam Item
                                    </span>
                                    @if($hasServiceItems)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-orange-50 text-orange-700 border border-orange-200">
                                            <i class="fa-solid fa-scissors text-[9px]"></i> Layanan
                                        </span>
                                    @endif
                                    @if($hasProductItems)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                            <i class="fa-solid fa-box text-[9px]"></i> Produk
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-600 truncate max-w-xs" title="{{ $tx->items->map(fn($i) => $i->qty . 'x ' . $i->product_name)->implode(', ') }}">
                                    {{ $tx->items->take(2)->map(fn($i) => ((float)$i->qty == (int)$i->qty ? (int)$i->qty : $i->qty) . 'x ' . $i->product_name)->implode(', ') }}
                                    @if($tx->items->count() > 2)
                                        <span class="text-slate-400 font-medium">+{{ $tx->items->count() - 2 }} lainnya</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Metode Bayar -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $paymentMethod === 'qris' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($paymentMethod === 'cash' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-sky-50 text-sky-700 border border-sky-200') }}">
                                    @if($paymentMethod === 'qris')
                                        <i class="fa-solid fa-qrcode text-[10px]"></i> QRIS
                                    @elseif($paymentMethod === 'cash')
                                        <i class="fa-solid fa-money-bill-wave text-[10px]"></i> TUNAI
                                    @elseif($paymentMethod === 'transfer')
                                        <i class="fa-solid fa-building-columns text-[10px]"></i> TRANSFER
                                    @else
                                        <i class="fa-solid fa-credit-card text-[10px]"></i> {{ strtoupper($paymentMethod) }}
                                    @endif
                                </span>
                                @if($firstPayment && $firstPayment->payment_method === 'cash' && ($firstPayment->change_due ?? 0) > 0)
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        Kembali: Rp {{ number_format($firstPayment->change_due, 0, ',', '.') }}
                                    </div>
                                @endif
                            </td>

                            <!-- Total Transaksi -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="font-black text-slate-900 text-sm">
                                    Rp {{ number_format($tx->total_amount, 0, ',', '.') }}
                                </div>
                                @if($tx->discount_amount > 0)
                                    <div class="text-[10px] text-emerald-600 font-medium">
                                        Hemat Rp {{ number_format($tx->discount_amount, 0, ',', '.') }}
                                    </div>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full {{ $tx->status === 'completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($tx->status === 'voided' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-amber-50 text-amber-700 border border-amber-200') }}">
                                    {{ $tx->status === 'completed' ? '● Lunas' : ($tx->status === 'voided' ? '✕ Void' : '⏸ Hold') }}
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Detail Modal Button -->
                                    <button wire:click="viewTransactionDetail({{ $tx->id }})"
                                            title="Lihat Detail Transaksi"
                                            class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg border border-slate-200 transition">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>

                                    <!-- Print Receipt Button -->
                                    <button wire:click="openPrintReceiptModal({{ $tx->id }})"
                                            title="Cetak Struk Kasir"
                                            class="p-1.5 text-orange-600 hover:bg-orange-50 rounded-lg border border-orange-200 transition">
                                        <i class="fa-solid fa-print text-xs"></i>
                                    </button>

                                    <!-- Void Order Button -->
                                    @if($tx->status === 'completed')
                                        <button wire:click="openVoidModal({{ $tx->id }})"
                                                title="Otorisasi Void Pesanan"
                                                class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg border border-rose-200 transition">
                                            <i class="fa-solid fa-ban text-xs"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-400 mx-auto flex items-center justify-center text-xl">
                                        <i class="fa-solid fa-receipt"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 text-sm">Tidak Ada Transaksi Ditemukan</div>
                                        <p class="text-xs text-slate-400 mt-1">
                                            @if($searchTransaction || $transactionStatusFilter !== 'all' || $transactionPaymentMethodFilter !== 'all' || $transactionOrderTypeFilter !== 'all' || $transactionDateFilter !== '30_days')
                                                Tidak ada riwayat transaksi yang cocok dengan kriteria filter saat ini.
                                            @else
                                                Belum ada transaksi POS kasir tercatat untuk outlet ini.
                                            @endif
                                        </p>
                                    </div>
                                    @if($searchTransaction || $transactionStatusFilter !== 'all' || $transactionPaymentMethodFilter !== 'all' || $transactionOrderTypeFilter !== 'all' || $transactionDateFilter !== '30_days')
                                        <button wire:click="resetTransactionFilters"
                                                class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                                            Reset Filter Pencarian
                                        </button>
                                    @else
                                        <a href="{{ route('pos.terminal') }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition">
                                            <i class="fa-solid fa-cash-register"></i>
                                            <span>Mulai Transaksi di Layar Kasir</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer: Count & Per Page Selector -->
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/70 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
            <div>
                Menampilkan <span class="font-bold text-slate-800">{{ $transactions->count() }}</span> dari <span class="font-bold text-slate-800">{{ $totalFilteredTransactionsCount }}</span> total transaksi
            </div>

            <div class="flex items-center gap-2">
                <span>Tampilkan:</span>
                <select wire:model.live="transactionPerPage"
                        class="bg-white border border-slate-200 text-xs font-semibold text-slate-700 py-1 px-2.5 rounded-lg focus:outline-none focus:ring-1 focus:ring-orange-500">
                    <option value="10">10 Struk</option>
                    <option value="20">20 Struk</option>
                    <option value="50">50 Struk</option>
                    <option value="100">100 Struk</option>
                </select>
            </div>
        </div>
    </div>
</div>
