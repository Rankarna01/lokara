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
