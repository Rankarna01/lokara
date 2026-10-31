<div class="space-y-6">
    <!-- Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold text-slate-900">
                    {{ $isSalon ? 'Stok & Opname Produk Ritel Salon' : 'Stok & Inventori Multi-Lokasi' }}
                </h1>
                @if ($isSalon)
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-orange-100 text-orange-700 border border-orange-200">
                        <i class="fa-solid fa-scissors mr-1"></i> Modul Salon & Barbershop
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-1">
                {{ $isSalon 
                    ? 'Audit fisik produk langsung (pomade, toner, shampoo, serum), kartu mutasi stok real-time, dan rekonsiliasi varians barang ritel.' 
                    : 'Saldo persediaan barang & bahan baku, kartu mutasi stok real-time, dan rekonsiliasi stok opname fisik.' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="openOpnameModal(null, '{{ $isSalon ? 'product' : 'ingredient' }}')"
                class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition cursor-pointer">
                <i class="fa-solid fa-clipboard-check"></i>
                <span>{{ $isSalon ? 'Mulai Opname Produk' : 'Mulai Stok Opname' }}</span>
            </button>
        </div>
    </div>

    <!-- 4 Summary Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Catalog Item -->
        <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                <span class="font-semibold text-slate-700">
                    <i class="fa-solid fa-box-open text-orange-500 mr-1"></i> {{ $isSalon ? 'Produk Ritel Aktif' : 'Bahan Baku Aktif' }}
                </span>
                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 font-bold text-[10px]">Tersedia</span>
            </div>
            <div class="text-2xl font-bold text-slate-900">
                {{ $isSalon ? $totalDirectProductsCount : $ingredients->count() }}
                <span class="text-xs font-semibold text-slate-400 font-normal">{{ $isSalon ? 'Produk' : 'Item' }}</span>
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
                {{ $isSalon ? 'Pomade, hair tonic, shampoo & serum' : 'Biji kopi, susu, sirup & kemasan' }}
            </div>
        </div>

        <!-- Card 2: Total Units in Stock -->
        <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                <span class="font-semibold text-slate-700">
                    <i class="fa-solid fa-cubes text-blue-500 mr-1"></i> Total Saldo Fisik
                </span>
                <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 font-bold text-[10px]">Akumulasi</span>
            </div>
            <div class="text-2xl font-bold text-slate-900">
                {{ number_format($isSalon ? $totalDirectStockQty : (float)$ingredients->sum('current_stock'), 0, ',', '.') }}
                <span class="text-xs font-semibold text-slate-400 font-normal">{{ $isSalon ? 'Unit' : 'Satuan' }}</span>
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
                {{ $isSalon ? 'Tersebar di rak display & gudang outlet' : 'Total saldo berjalan di outlet' }}
            </div>
        </div>

        <!-- Card 3: Low Stock Warning -->
        <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                <span class="font-semibold text-slate-700">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500 mr-1"></i> Peringatan Stok Tipis
                </span>
                <span class="px-2 py-0.5 rounded-full {{ ($isSalon ? $lowStockDirectProducts : $lowStockIngredients->count()) > 0 ? 'bg-rose-50 text-rose-600 font-bold' : 'bg-slate-100 text-slate-500' }} text-[10px]">
                    ≤ Min. Stok
                </span>
            </div>
            <div class="text-2xl font-bold {{ ($isSalon ? $lowStockDirectProducts : $lowStockIngredients->count()) > 0 ? 'text-rose-600' : 'text-slate-900' }}">
                {{ $isSalon ? $lowStockDirectProducts : $lowStockIngredients->count() }}
                <span class="text-xs font-semibold text-slate-400 font-normal">Item Butuh Restock</span>
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
                {{ ($isSalon ? $lowStockDirectProducts : $lowStockIngredients->count()) > 0 ? 'Segera terbitkan PO ke supplier' : 'Semua persediaan aman' }}
            </div>
        </div>

        <!-- Card 4: Monthly Opname Audit -->
        <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                <span class="font-semibold text-slate-700">
                    <i class="fa-solid fa-calendar-check text-purple-500 mr-1"></i> Audit Opname Bulan Ini
                </span>
                <span class="px-2 py-0.5 rounded-full bg-purple-50 text-purple-600 font-bold text-[10px]">Audit Trail</span>
            </div>
            <div class="text-2xl font-bold text-slate-900">
                {{ $totalOpnameCountThisMonth }}
                <span class="text-xs font-semibold text-slate-400 font-normal">Kali Opname</span>
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
                @if ($lastOpnameMovement)
                    Terakhir: {{ $lastOpnameMovement->created_at->format('d/m/Y H:i') }} WIB
                @else
                    Belum ada sesi opname bulan ini
                @endif
            </div>
        </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-sm space-y-3.5">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            <!-- Search bar -->
            <div class="md:col-span-4 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" wire:model.live.debounce.300ms="stockSearch"
                    placeholder="Cari item, SKU, atau catatan mutasi..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3.5 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-orange-500">
            </div>

            <!-- Filter Mutation Type -->
            <div class="md:col-span-3">
                <select wire:model.live="stockTypeFilter"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500">
                    <option value="all">Semua Jenis Mutasi</option>
                    <option value="opname">📋 Stok Opname Fisik</option>
                    <option value="sale_pos">🛒 Penjualan Kasir POS</option>
                    <option value="purchase">📥 Penerimaan Barang PO</option>
                    <option value="waste">⚠️ Rusak / Waste / Hilang</option>
                    <option value="adjustment">🔄 Penyesuaian Saldo</option>
                </select>
            </div>

            <!-- Filter Item Type -->
            <div class="md:col-span-2">
                <select wire:model.live="stockItemTypeFilter"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500">
                    <option value="all">Semua Tipe Item</option>
                    <option value="product">🧴 Produk Langsung / Ritel</option>
                    <option value="ingredient">🌾 Bahan Baku</option>
                </select>
            </div>

            <!-- Filter Date Presets -->
            <div class="md:col-span-2">
                <select wire:model.live="stockDateFilter"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500 font-medium">
                    <option value="all">Semua Waktu</option>
                    <option value="today">Hari Ini</option>
                    <option value="yesterday">Kemarin</option>
                    <option value="7_days">7 Hari Terakhir</option>
                    <option value="30_days">30 Hari Terakhir</option>
                    <option value="this_month">Bulan Ini</option>
                    <option value="custom">⏱️ Rentang Kustom & Waktu</option>
                </select>
            </div>

            <!-- Reset Filter Button -->
            <div class="md:col-span-1 flex justify-end">
                <button type="button" wire:click="resetStockFilters" title="Reset Semua Filter"
                    class="w-full py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold flex items-center justify-center gap-1 transition">
                    <i class="fa-solid fa-rotate-left text-[11px]"></i>
                    <span class="md:hidden">Reset</span>
                </button>
            </div>
        </div>

        <!-- Custom Date & Time Filter Bar (Visible when 'custom' is selected) -->
        @if ($stockDateFilter === 'custom')
            <div class="pt-3 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 bg-orange-50/50 p-3 rounded-xl border border-orange-100 animate-in fade-in duration-150">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">
                        <i class="fa-regular fa-calendar text-orange-500 mr-1"></i> Tanggal Mulai:
                    </label>
                    <input type="date" wire:model.live="stockStartDate"
                        class="w-full bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-800 focus:ring-1 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">
                        <i class="fa-regular fa-clock text-orange-500 mr-1"></i> Jam Mulai:
                    </label>
                    <input type="time" wire:model.live="stockStartTime"
                        class="w-full bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-800 focus:ring-1 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">
                        <i class="fa-regular fa-calendar-check text-orange-500 mr-1"></i> Tanggal Sampai:
                    </label>
                    <input type="date" wire:model.live="stockEndDate"
                        class="w-full bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-800 focus:ring-1 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">
                        <i class="fa-regular fa-clock text-orange-500 mr-1"></i> Jam Sampai:
                    </label>
                    <input type="time" wire:model.live="stockEndTime"
                        class="w-full bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-800 focus:ring-1 focus:ring-orange-500">
                </div>
            </div>
        @endif
    </div>

    <!-- Stock Movement Ledger & Audit Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <span>Kartu Mutasi Stok & Riwayat Audit Opname</span>
                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[11px] font-semibold">
                        {{ $stockMovements->count() }} Data Tercatat
                    </span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Waktu transaksi terekam presisi bersama identitas petugas dan varians fisik.</p>
            </div>
            @if ($isSalon)
                <div class="flex items-center gap-1.5 text-xs text-slate-500">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                    <span class="font-medium text-slate-600">Mode Salon: Prioritas Produk Langsung</span>
                </div>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/90 border-b border-slate-200/80 text-[11px] text-slate-500 uppercase font-bold tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Waktu & Tanggal</th>
                        <th class="py-3 px-4">Nama Item / Produk</th>
                        <th class="py-3 px-4 text-center">Jenis Mutasi</th>
                        <th class="py-3 px-4 text-right">Perubahan (Qty)</th>
                        <th class="py-3 px-4 text-right">Saldo Akhir</th>
                        <th class="py-3 px-4">PIC / Petugas</th>
                        <th class="py-3 px-4">Keterangan & Catatan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($stockMovements as $mv)
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Waktu Masuk & Tanggal Jelas -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900 text-xs">
                                    {{ $mv->created_at->format('d/m/Y') }}
                                </div>
                                <div class="text-[11px] text-slate-500 font-semibold flex items-center gap-1 mt-0.5">
                                    <i class="fa-regular fa-clock text-[10px] text-slate-400"></i>
                                    <span>{{ $mv->created_at->format('H:i:s') }} WIB</span>
                                </div>
                                <div class="text-[10px] text-slate-400">
                                    {{ $mv->created_at->diffForHumans() }}
                                </div>
                            </td>

                            <!-- Nama Item & Identitas Produk -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    @if ($mv->product && $mv->product->image_url)
                                        <img src="{{ $mv->product->image_url }}" class="w-8 h-8 rounded-lg object-cover border border-slate-200 shrink-0" onerror="this.src='https://images.unsplash.com/photo-1597354984706-aec992b7d526?auto=format&fit=crop&w=100&h=100&q=80'">
                                    @else
                                        <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center shrink-0 text-xs font-bold">
                                            <i class="fa-solid {{ $mv->product_id ? 'fa-spray-can-sparkles' : 'fa-wheat-awn' }}"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-slate-900 text-xs leading-snug">
                                            {{ $mv->item_name }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1.5 flex-wrap">
                                            <span>SKU: {{ $mv->item_sku }}</span>
                                            <span>•</span>
                                            @if ($mv->product_id)
                                                <span class="px-1.5 py-0.2 rounded bg-indigo-50 text-indigo-600 font-semibold">Produk Langsung</span>
                                            @elseif ($mv->ingredient_id)
                                                <span class="px-1.5 py-0.2 rounded bg-amber-50 text-amber-700 font-semibold">Bahan Baku</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Jenis Mutasi Badge -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if ($mv->type === 'opname')
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 border border-purple-200">
                                        <i class="fa-solid fa-clipboard-check text-[10px]"></i>
                                        <span>Opname Fisik</span>
                                    </span>
                                @elseif ($mv->type === 'sale_pos')
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-lg bg-sky-50 text-sky-700 border border-sky-200">
                                        <i class="fa-solid fa-cash-register text-[10px]"></i>
                                        <span>Penjualan POS</span>
                                    </span>
                                @elseif ($mv->type === 'purchase')
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fa-solid fa-truck-ramp-box text-[10px]"></i>
                                        <span>Penerimaan PO</span>
                                    </span>
                                @elseif ($mv->type === 'waste')
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                                        <span>Waste / Rusak</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">
                                        <span>{{ ucfirst($mv->type) }}</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Perubahan Qty -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <span class="font-bold text-xs {{ $mv->qty >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $mv->qty >= 0 ? '+' : '' }}{{ number_format($mv->qty, 0, ',', '.') }} {{ $mv->item_unit }}
                                </span>
                            </td>

                            <!-- Saldo Akhir -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap font-bold text-slate-900 text-xs">
                                {{ number_format($mv->balance_after, 0, ',', '.') }} <span class="text-[11px] font-medium text-slate-400">{{ $mv->item_unit }}</span>
                            </td>

                            <!-- PIC / Petugas -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5 text-xs text-slate-700 font-semibold">
                                    <div class="w-5 h-5 rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($mv->user?->name ?? 'A', 0, 1)) }}
                                    </div>
                                    <span>{{ $mv->user?->name ?? 'Admin Toko' }}</span>
                                </div>
                            </td>

                            <!-- Keterangan / Referensi -->
                            <td class="py-3.5 px-4 text-xs text-slate-600 max-w-xs">
                                <p class="line-clamp-2" title="{{ $mv->reference_note }}">
                                    {{ $mv->reference_note ?: '-' }}
                                </p>
                            </td>

                            <!-- Aksi Shortcut -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <button type="button"
                                    wire:click="openOpnameModal({{ $mv->product_id ?? $mv->ingredient_id }}, '{{ $mv->product_id ? 'product' : 'ingredient' }}')"
                                    title="Audit Opname Ulang Item Ini"
                                    class="px-2.5 py-1 text-[11px] font-semibold text-orange-600 hover:text-white bg-orange-50 hover:bg-orange-500 rounded-lg transition border border-orange-200">
                                    <i class="fa-solid fa-clipboard-check mr-0.5"></i> Opname
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-2">
                                    <i class="fa-solid fa-boxes-stacked text-lg"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-600">Tidak ada kartu mutasi stok yang sesuai dengan filter.</p>
                                <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian, rentang tanggal, atau mulai sesi opname baru.</p>
                                <button type="button" wire:click="resetStockFilters"
                                    class="mt-3 px-3.5 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold rounded-xl transition">
                                    Reset Semua Filter
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
