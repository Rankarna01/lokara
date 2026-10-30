                    <div class="space-y-5">
                        <!-- Top Header & Action Controls -->
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div>
                                <h1 class="text-xl font-bold text-slate-900">Manajemen Diskon & Promosi</h1>
                                <p class="text-xs text-slate-500 mt-0.5">Atur skema diskon persentase (%) atau potongan nominal langsung (Rp) untuk layanan dan produk.</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <button wire:click="openNewDiscountModal"
                                    class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition">
                                    <i class="fa-solid fa-tags"></i>
                                    <span>Tambah Diskon Baru</span>
                                </button>
                            </div>
                        </div>

                        <!-- 4 Stat Summary Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                                <div class="text-slate-500 text-xs font-medium">Promo Aktif</div>
                                <div class="text-2xl font-bold text-emerald-600 mt-1">
                                    {{ $discounts->where('is_active', true)->count() }} Promo
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Siap digunakan di POS kasir</div>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                                <div class="text-slate-500 text-xs font-medium">Diskon Persentase (%)</div>
                                <div class="text-2xl font-bold text-indigo-600 mt-1">
                                    {{ $discounts->where('type', 'percentage')->count() }} Kupon
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Potongan berbasis rasio %</div>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                                <div class="text-slate-500 text-xs font-medium">Diskon Nominal Uang (Rp)</div>
                                <div class="text-2xl font-bold text-purple-600 mt-1">
                                    {{ $discounts->where('type', 'fixed')->count() }} Kupon
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Potongan rupiah langsung</div>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                                <div class="text-slate-500 text-xs font-medium">Total Penggunaan Promo</div>
                                <div class="text-2xl font-bold text-slate-900 mt-1">
                                    {{ $discounts->sum('usage_count') }} Kali
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Digunakan oleh pelanggan</div>
                            </div>
                        </div>

                        <!-- Filter & Search Controls -->
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div class="flex items-center gap-2">
                                <button wire:click="$set('discountStatusFilter', 'all')"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $discountStatusFilter === 'all' ? 'bg-orange-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                    Semua Promo ({{ $discounts->count() }})
                                </button>
                                <button wire:click="$set('discountStatusFilter', 'active')"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $discountStatusFilter === 'active' ? 'bg-orange-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                    Sedang Aktif
                                </button>
                                <button wire:click="$set('discountStatusFilter', 'inactive')"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $discountStatusFilter === 'inactive' ? 'bg-orange-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                    Nonaktif
                                </button>
                            </div>

                            <div class="relative w-full sm:w-64">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                                <input type="text" wire:model.live.debounce.300ms="searchDiscount"
                                    placeholder="Cari nama atau kode promo..."
                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-8 pr-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
                            </div>
                        </div>

                        <!-- Discounts Table -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 border-b border-slate-200/80 text-xs text-slate-500 uppercase tracking-wider font-semibold">
                                        <tr>
                                            <th class="py-3 px-4">Nama Diskon & Kupon</th>
                                            <th class="py-3 px-4 text-center">Tipe & Nilai Potongan</th>
                                            <th class="py-3 px-4 text-right">Syarat Min. Belanja</th>
                                            <th class="py-3 px-4 text-center">Berlaku Untuk</th>
                                            <th class="py-3 px-4 text-center">Periode Berlaku</th>
                                            <th class="py-3 px-4 text-center">Penggunaan</th>
                                            <th class="py-3 px-4 text-center">Status</th>
                                            <th class="py-3 px-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($discounts as $disc)
                                            <tr class="hover:bg-slate-50/60 transition">
                                                <td class="py-3.5 px-4">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $disc->type === 'percentage' ? 'bg-emerald-50 text-emerald-600' : 'bg-purple-50 text-purple-600' }}">
                                                            <i class="fa-solid {{ $disc->type === 'percentage' ? 'fa-percent' : 'fa-money-bill-wave' }}"></i>
                                                        </div>
                                                        <div>
                                                            <div class="font-bold text-slate-900">{{ $disc->name }}</div>
                                                            <div class="flex items-center gap-2 mt-0.5">
                                                                @if($disc->code)
                                                                    <span class="font-mono text-[10px] bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded border border-slate-200 font-semibold">
                                                                        {{ $disc->code }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-[10px] text-slate-400">Otomatis / Tanpa Kode</span>
                                                                @endif
                                                                @if($disc->notes)
                                                                    <span class="text-[11px] text-slate-400 truncate max-w-xs">• {{ $disc->notes }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3.5 px-4 text-center">
                                                    @if($disc->type === 'percentage')
                                                        <span class="inline-flex flex-col items-center">
                                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                                Diskon {{ (int)$disc->value }}%
                                                            </span>
                                                            @if($disc->max_discount)
                                                                <span class="text-[10px] text-slate-400 mt-0.5">Maks. Rp {{ number_format($disc->max_discount, 0, ',', '.') }}</span>
                                                            @endif
                                                        </span>
                                                    @else
                                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                            Potongan Rp {{ number_format($disc->value, 0, ',', '.') }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-3.5 px-4 text-right font-medium text-slate-700">
                                                    {{ $disc->min_purchase > 0 ? 'Rp ' . number_format($disc->min_purchase, 0, ',', '.') : 'Tanpa Minimum' }}
                                                </td>
                                                <td class="py-3.5 px-4 text-center">
                                                    <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">
                                                        @if($disc->applicable_to === 'services')
                                                            Khusus Layanan Jasa
                                                        @elseif($disc->applicable_to === 'products')
                                                            Khusus Produk Retail
                                                        @else
                                                            Semua Menu & Layanan
                                                        @endif
                                                    </span>
                                                </td>
                                                <td class="py-3.5 px-4 text-center text-xs text-slate-500">
                                                    @if($disc->start_date && $disc->end_date)
                                                        <div>{{ $disc->start_date->format('d M') }} - {{ $disc->end_date->format('d M Y') }}</div>
                                                    @elseif($disc->end_date)
                                                        <div>s/d {{ $disc->end_date->format('d M Y') }}</div>
                                                    @else
                                                        <span class="text-emerald-600 font-medium">Berlaku Selamanya</span>
                                                    @endif
                                                </td>
                                                <td class="py-3.5 px-4 text-center text-xs">
                                                    <span class="font-bold text-slate-700">{{ $disc->usage_count }}</span>
                                                    <span class="text-slate-400">/ {{ $disc->max_usage ?: '∞' }} kali</span>
                                                </td>
                                                <td class="py-3.5 px-4 text-center">
                                                    <button wire:click="toggleDiscountStatus({{ $disc->id }})"
                                                        class="text-xs font-semibold px-2.5 py-1 rounded-lg transition {{ $disc->is_active ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                                        {{ $disc->is_active ? 'Aktif' : 'Nonaktif' }}
                                                    </button>
                                                </td>
                                                <td class="py-3.5 px-4 text-right space-x-1">
                                                    <button wire:click="editDiscount({{ $disc->id }})"
                                                        title="Edit Promo"
                                                        class="p-1.5 text-xs text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>
                                                    <button wire:click="deleteDiscount({{ $disc->id }})"
                                                        wire:confirm="Yakin ingin menghapus diskon '{{ $disc->name }}'?"
                                                        title="Hapus Promo"
                                                        class="p-1.5 text-xs text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="py-12 text-center text-slate-400 text-sm">
                                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-xl">
                                                        <i class="fa-solid fa-tags"></i>
                                                    </div>
                                                    <div>Belum ada program diskon atau promosi aktif.</div>
                                                    <button wire:click="openNewDiscountModal" class="mt-2 text-xs text-orange-600 font-semibold hover:underline">
                                                        + Buat Promo Pertama
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
