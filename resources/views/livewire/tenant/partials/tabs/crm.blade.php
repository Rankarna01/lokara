<div class="space-y-6">
    <!-- 1. Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-users"></i>
                </span>
                <h1 class="text-xl font-bold text-slate-900">Pelanggan & CRM Loyalti</h1>
                <span class="text-[11px] font-semibold text-orange-700 bg-orange-50 border border-orange-200/80 px-2 py-0.5 rounded-full flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                    Sinkronisasi POS Kasir
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Database pelanggan terpusat, tier membership, profil preferensi layanan, saldo poin loyalti, dan jejak riwayat transaksi belanja.
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <button wire:click="openNewCustomerModal"
                    class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>+ Tambah Pelanggan Baru</span>
            </button>
        </div>
    </div>

    <!-- 2. Executive Stat Cards (4 Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Pelanggan -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pelanggan</span>
                <span class="w-7 h-7 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-address-book"></i>
                </span>
            </div>
            <div class="my-3">
                <div class="text-2xl font-black text-slate-900 tracking-tight">
                    {{ $crmStats['totalCustomers'] }} <span class="text-sm font-semibold text-slate-400">Orang</span>
                </div>
                <div class="text-[11px] font-medium text-slate-400 mt-0.5">
                    Basis data pelanggan aktif
                </div>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Rasio repeat order:</span>
                <span class="font-bold text-slate-700">Tinggi</span>
            </div>
        </div>

        <!-- Card 2: Member VIP & Gold -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Member VIP & Prioritas</span>
                <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-crown"></i>
                </span>
            </div>
            <div class="my-3">
                <div class="text-2xl font-black text-slate-900 tracking-tight">
                    {{ $crmStats['goldVipCount'] }} <span class="text-sm font-semibold text-slate-400">Member</span>
                </div>
                <div class="text-[11px] font-medium text-amber-600 mt-0.5 flex items-center gap-1">
                    <i class="fa-solid fa-award text-[10px]"></i>
                    <span>Tier Gold & Platinum</span>
                </div>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Kontribusi omset:</span>
                <span class="font-bold text-amber-700">Prioritas</span>
            </div>
        </div>

        <!-- Card 3: Total Akumulasi Poin -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Poin Loyalti</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-coins"></i>
                </span>
            </div>
            <div class="my-3">
                <div class="text-2xl font-black text-slate-900 tracking-tight whitespace-nowrap">
                    {{ number_format($crmStats['totalPoints'], 0, ',', '.') }} <span class="text-sm font-semibold text-slate-400">Pts</span>
                </div>
                <div class="text-[11px] font-medium text-slate-400 mt-0.5">
                    Poin aktif dapat ditukarkan
                </div>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Program reward:</span>
                <span class="font-bold text-emerald-600">Berjalan</span>
            </div>
        </div>

        <!-- Card 4: Nilai Belanja Pelanggan (LTV) -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Omset CRM (LTV)</span>
                <span class="w-7 h-7 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </span>
            </div>
            <div class="my-3">
                <div class="text-2xl font-black text-slate-900 tracking-tight whitespace-nowrap">
                    Rp {{ number_format($crmStats['totalLifetimeRevenue'], 0, ',', '.') }}
                </div>
                <div class="text-[11px] font-medium text-sky-600 mt-0.5">
                    Rata-rata: Rp {{ number_format($crmStats['avgSpendingPerCustomer'], 0, ',', '.') }}/org
                </div>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Basis transaksi:</span>
                <span class="font-bold text-slate-700">Tervalidasi POS</span>
            </div>
        </div>
    </div>

    <!-- 3. Filter & Search Controls -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search Bar -->
            <div class="relative lg:col-span-2">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text"
                       wire:model.live.debounce.300ms="searchCustomer"
                       placeholder="Cari nama, WhatsApp, email, catatan preferensi..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-8 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition" />
                @if($searchCustomer)
                    <button wire:click="$set('searchCustomer', '')"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                @endif
            </div>

            <!-- Filter Tier Member -->
            <div>
                <select wire:model.live="customerTierFilter"
                        class="w-full bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 py-2 px-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500">
                    <option value="all">Semua Tier Keanggotaan</option>
                    <option value="silver">🥈 Silver Member</option>
                    <option value="gold">🥇 Gold Member (VIP)</option>
                    <option value="platinum">👑 Platinum Member (Prioritas)</option>
                </select>
            </div>

            <!-- Sort By -->
            <div>
                <select wire:model.live="customerSortBy"
                        class="w-full bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 py-2 px-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500">
                    <option value="latest">Urutan: Terdaftar Baru</option>
                    <option value="points_desc">Urutan: Poin Tertinggi</option>
                    <option value="spent_desc">Urutan: Total Belanja Terbesar</option>
                    <option value="visits_desc">Urutan: Kunjungan Terbanyak</option>
                    <option value="name_asc">Urutan: Nama A-Z</option>
                </select>
            </div>

            <!-- Reset Button -->
            <div class="flex items-center gap-2">
                <button wire:click="resetCustomerFilters"
                        title="Reset Filter"
                        class="w-full py-2 px-3 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-rotate-right text-xs"></i>
                    <span>Reset</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 4. Customers Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-500 uppercase font-bold text-[11px] tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Pelanggan & Profil</th>
                        <th class="py-3 px-4">Kontak & WhatsApp</th>
                        <th class="py-3 px-4 text-center">Tier & Poin</th>
                        <th class="py-3 px-4 text-right">Total Belanja (LTV)</th>
                        <th class="py-3 px-4 text-center">Kunjungan</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customers as $c)
                        @php
                            $tier = strtolower($c->membership_tier ?? 'silver');
                            $spent = (float) ($c->orders_total_spent ?? $c->total_spent ?? 0);
                            $visits = (int) ($c->completed_orders_count ?? $c->visit_count ?? 0);
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Pelanggan & Profil -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-black text-xs shrink-0">
                                        {{ strtoupper(substr($c->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <button wire:click="viewCustomerDetail({{ $c->id }})"
                                                class="font-bold text-slate-900 text-xs hover:text-orange-600 transition text-left">
                                            {{ $c->name }}
                                        </button>
                                        <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                            @if($c->gender)
                                                <span class="capitalize">{{ $c->gender === 'female' ? 'Wanita' : 'Pria' }}</span>
                                                <span>•</span>
                                            @endif
                                            <span>{{ $c->email ?? 'Email belum diisi' }}</span>
                                        </div>
                                        @if($c->notes)
                                            <div class="text-[10px] text-orange-700 bg-orange-50 px-1.5 py-0.5 rounded mt-1 truncate max-w-xs" title="{{ $c->notes }}">
                                                <i class="fa-solid fa-note-sticky text-[9px] mr-1"></i>{{ $c->notes }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Kontak & WhatsApp -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($c->phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $c->phone) }}" target="_blank"
                                       title="Kirim pesan WhatsApp"
                                       class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition">
                                        <i class="fa-brands fa-whatsapp text-emerald-600 text-xs"></i>
                                        <span>{{ $c->phone }}</span>
                                    </a>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>

                            <!-- Tier & Poin -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full {{ $tier === 'platinum' ? 'bg-purple-100 text-purple-700 border border-purple-200' : ($tier === 'gold' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                                    {{ $tier === 'platinum' ? '👑 Platinum VIP' : ($tier === 'gold' ? '🥇 Gold VIP' : '🥈 Silver Member') }}
                                </span>
                                <div class="text-[11px] font-bold text-orange-600 mt-1">
                                    {{ number_format($c->points, 0, ',', '.') }} Pts
                                </div>
                            </td>

                            <!-- Total Belanja (LTV) -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="font-black text-slate-900 text-xs">
                                    Rp {{ number_format($spent, 0, ',', '.') }}
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    Lifetime Value
                                </div>
                            </td>

                            <!-- Kunjungan -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="font-bold text-slate-800 text-xs">
                                    {{ $visits }}x
                                </span>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    Transaksi POS
                                </div>
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button wire:click="viewCustomerDetail({{ $c->id }})"
                                            title="Lihat Profil & Riwayat Transaksi"
                                            class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg border border-slate-200 transition">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                    <button wire:click="editCustomer({{ $c->id }})"
                                            title="Edit Data Pelanggan"
                                            class="p-1.5 text-orange-600 hover:bg-orange-50 rounded-lg border border-orange-200 transition">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    <button wire:click="deleteCustomer({{ $c->id }})"
                                            wire:confirm="Yakin ingin menghapus pelanggan {{ $c->name }}?"
                                            title="Hapus Pelanggan"
                                            class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg border border-rose-200 transition">
                                        <i class="fa-regular fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-400 mx-auto flex items-center justify-center text-xl">
                                        <i class="fa-solid fa-users"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 text-sm">Tidak Ada Pelanggan Ditemukan</div>
                                        <p class="text-xs text-slate-400 mt-1">
                                            @if($searchCustomer || $customerTierFilter !== 'all')
                                                Tidak ada member yang cocok dengan kriteria filter pencarian.
                                            @else
                                                Belum ada data pelanggan yang terdaftar di sistem CRM Anda.
                                            @endif
                                        </p>
                                    </div>
                                    @if($searchCustomer || $customerTierFilter !== 'all')
                                        <button wire:click="resetCustomerFilters"
                                                class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                                            Reset Filter Pencarian
                                        </button>
                                    @else
                                        <button wire:click="openNewCustomerModal"
                                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition">
                                            <i class="fa-solid fa-user-plus text-xs"></i>
                                            <span>Tambah Pelanggan Pertama</span>
                                        </button>
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
                Menampilkan <span class="font-bold text-slate-800">{{ $customers->count() }}</span> dari <span class="font-bold text-slate-800">{{ $totalCustomersCount }}</span> pelanggan terdaftar
            </div>

            <div class="flex items-center gap-2">
                <span>Tampilkan:</span>
                <select wire:model.live="customerPerPage"
                        class="bg-white border border-slate-200 text-xs font-semibold text-slate-700 py-1 px-2.5 rounded-lg focus:outline-none focus:ring-1 focus:ring-orange-500">
                    <option value="10">10 Orang</option>
                    <option value="20">20 Orang</option>
                    <option value="50">50 Orang</option>
                    <option value="100">100 Orang</option>
                </select>
            </div>
        </div>
    </div>
</div>
