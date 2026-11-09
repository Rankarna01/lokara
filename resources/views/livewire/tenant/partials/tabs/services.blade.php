                    <div class="space-y-5">
                        <!-- Top Header & Action Controls -->
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h1 class="text-xl font-bold text-slate-900">Katalog & Master Layanan</h1>
                                    <!-- <span class="text-[10px] font-bold bg-indigo-50 text-indigo-600 border border-indigo-200 px-2 py-0.5 rounded-full uppercase tracking-wider">
                                        Modul Salon & Barbershop
                                    </span> -->
                                </div>
                                <!-- <p class="text-xs text-slate-500 mt-0.5">Kelola tarif potong rambut, perawatan spa, durasi waktu pengerjaan, modal bahan, dan foto layanan.</p> -->
                            </div>
                            <div class="flex items-center gap-2.5">
                                <button wire:click="switchTab('products')"
                                    class="px-3.5 py-2.5 bg-orange-50 hover:bg-orange-100 text-orange-700 border border-orange-200 font-semibold text-xs rounded-xl shadow-xs flex items-center gap-2 transition">
                                    <i class="fa-solid fa-boxes-stacked"></i>
                                    <span>Ke Master Produk ({{ $products->count() }})</span>
                                </button>
                                <button wire:click="openNewServiceModal"
                                    class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition">
                                    <i class="fa-solid fa-scissors"></i>
                                    <span>Tambah Layanan Baru</span>
                                </button>
                            </div>
                        </div>

                        <!-- 4 Stat Summary Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                                <div class="text-slate-500 text-xs font-medium">Total Layanan Aktif</div>
                                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $services->where('is_available', true)->count() }} Layanan</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $services->count() }} total terdaftar</div>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                                <div class="text-slate-500 text-xs font-medium">Rata-rata Durasi Layanan</div>
                                <div class="text-2xl font-bold text-indigo-600 mt-1">
                                    {{ $services->count() > 0 ? round($services->avg('duration_minutes')) : 40 }} Menit
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Waktu pengerjaan per sesi</div>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                                <div class="text-slate-500 text-xs font-medium">Rata-rata Tarif Layanan</div>
                                <div class="text-2xl font-bold text-emerald-600 mt-1">
                                    Rp {{ number_format($services->count() > 0 ? $services->avg('base_price') : 0, 0, ',', '.') }}
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Harga per transaksi jasa</div>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                                <div class="text-slate-500 text-xs font-medium">Layanan Tersedia</div>
                                <div class="text-2xl font-bold text-orange-600 mt-1">
                                    {{ $services->where('is_available', true)->count() }} Layanan
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $services->where('is_available', false)->count() }} tidak aktif / libur</div>
                            </div>
                        </div>

                        <!-- Search & Category Filters -->
                        <div class="flex flex-col gap-3 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-2 sm:pb-0">
                                <!-- Filter Semua Kategori -->
                                <button wire:click="$set('selectedServiceCategoryFilter', 'all')"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition shrink-0 {{ $selectedServiceCategoryFilter === 'all' ? 'bg-orange-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                    Semua Kategori ({{ $services->count() }})
                                </button>

                                <!-- Pill Kategori + Tombol Edit Langsung -->
                                @foreach($serviceCategories ?? $categories as $cat)
                                    <div class="inline-flex items-center rounded-lg border transition shrink-0 {{ $selectedServiceCategoryFilter == $cat->id ? 'bg-orange-500 border-orange-500 text-white shadow-sm' : 'bg-slate-100 border-transparent text-slate-600 hover:bg-slate-200' }}">
                                        <button wire:click="$set('selectedServiceCategoryFilter', {{ $cat->id }})"
                                            class="px-3 py-1.5 text-xs font-semibold whitespace-nowrap flex items-center gap-1.5">
                                            @if($cat->icon)
                                                <i class="fa-solid {{ $cat->icon }} text-[10px] {{ $selectedServiceCategoryFilter == $cat->id ? 'text-white' : 'text-slate-400' }}"></i>
                                            @endif
                                            <span>{{ $cat->name }} ({{ $cat->products_count }})</span>
                                        </button>
                                        <button wire:click="openEditCategoryModal({{ $cat->id }})"
                                            title="Edit Kategori: {{ $cat->name }}"
                                            class="pr-2 pl-0.5 py-1.5 text-[10px] {{ $selectedServiceCategoryFilter == $cat->id ? 'text-orange-200 hover:text-white' : 'text-slate-400 hover:text-slate-700' }} transition">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    </div>
                                @endforeach

                                <!-- Tombol Tambah Kategori Langsung di Samping Kategori -->
                                <button wire:click="openNewCategoryModal"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-dashed border-orange-400 bg-orange-50 text-orange-600 hover:bg-orange-100 hover:border-orange-500 transition flex items-center gap-1.5 whitespace-nowrap shrink-0 shadow-xs cursor-pointer"
                                    title="Tambah Kategori Layanan Baru">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                    <span>Tambah Kategori</span>
                                </button>

                                <!-- Tombol Kelola Semua Kategori -->
                                <button wire:click="openManageCategoriesModal"
                                    class="px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-800 transition flex items-center gap-1.5 whitespace-nowrap shrink-0 cursor-pointer"
                                    title="Kelola & Atur Urutan Kategori">
                                    <i class="fa-solid fa-sliders text-[11px] text-slate-500"></i>
                                    <span class="hidden sm:inline">Kelola Kategori</span>
                                </button>
                            </div>

                            <div class="relative w-full sm:w-80">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                                <input type="text" wire:model.live.debounce.300ms="searchService"
                                    placeholder="Cari layanan jasa..."
                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-8 pr-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
                            </div>
                        </div>

                        @php
                            $showMaterialCost = $tenant?->businessType?->slug !== 'salon';
                        @endphp

                        <!-- Service Table -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 border-b border-slate-200/80 text-xs text-slate-500 uppercase tracking-wider font-semibold">
                                        <tr>
                                            <th class="py-3 px-4">Nama Layanan</th>
                                            <th class="py-3 px-4">Kategori</th>
                                            <th class="py-3 px-4 text-center">Durasi</th>
                                            <th class="py-3 px-4 text-right">Tarif Jasa</th>
                                            @if ($showMaterialCost)
                                                <th class="py-3 px-4 text-right">Modal Bahan</th>
                                            @endif
                                            <th class="py-3 px-4 text-right">Fee Kapster</th>
                                            <th class="py-3 px-4 text-center">Margin Bersih</th>
                                            <th class="py-3 px-4 text-center">Status</th>
                                            <th class="py-3 px-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($servicesTable as $srv)
                                            <tr class="hover:bg-slate-50/60 transition">
                                                <td class="py-3.5 px-4">
                                                    <div class="flex items-center gap-3">
                                                        <img src="{{ $srv->image_url ?: 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?auto=format&fit=crop&w=150&h=150&q=80' }}"
                                                            class="w-10 h-10 rounded-xl object-cover border border-slate-200 shrink-0">
                                                        <div>
                                                            <div class="font-bold text-slate-900">{{ $srv->name }}</div>
                                                            <div class="text-[11px] text-slate-400">{{ $srv->sku ?? 'SRV-00' . $srv->id }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3.5 px-4 text-xs">
                                                    <span class="bg-slate-100 px-2.5 py-1 rounded-md font-medium text-slate-700">
                                                        {{ $srv->category->name ?? 'Layanan' }}
                                                    </span>
                                                </td>
                                                <td class="py-3.5 px-4 text-center">
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                                                        <i class="fa-regular fa-clock text-[10px]"></i>
                                                        {{ $srv->duration_minutes ?? 30 }} Menit
                                                    </span>
                                                </td>
                                                <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                                                    Rp {{ number_format($srv->base_price, 0, ',', '.') }}
                                                </td>
                                                @if ($showMaterialCost)
                                                    <td class="py-3.5 px-4 text-right text-xs text-slate-500">
                                                        Rp {{ number_format($srv->cost_price, 0, ',', '.') }}
                                                    </td>
                                                @endif
                                                <td class="py-3.5 px-4 text-right text-xs">
                                                    <div class="font-bold text-amber-600">Rp {{ number_format($srv->estimated_commission, 0, ',', '.') }}</div>
                                                    <div class="text-[10px] text-slate-400">
                                                        {{ $srv->commission_type === 'percentage' ? rtrim(rtrim(number_format((float) $srv->commission_value, 2, ',', '.'), '0'), ',').'% dari tarif' : 'Tetap per sesi' }}
                                                    </div>
                                                </td>
                                                <td class="py-3.5 px-4 text-center">
                                                    @php
                                                        $netRevenue = $srv->base_price - $srv->cost_price;
                                                        $marginPct = $srv->base_price > 0 ? round(($netRevenue / $srv->base_price) * 100) : 0;
                                                    @endphp
                                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $marginPct >= 50 ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                                        Rp {{ number_format($netRevenue, 0, ',', '.') }} ({{ $marginPct }}%)
                                                    </span>
                                                </td>
                                                <td class="py-3.5 px-4 text-center">
                                                    <button wire:click="toggleServiceAvailability({{ $srv->id }})"
                                                        class="text-xs font-semibold px-2.5 py-1 rounded-lg transition {{ $srv->is_available ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-rose-50 text-rose-600 border border-rose-200' }}">
                                                        {{ $srv->is_available ? 'Tersedia' : 'Libur' }}
                                                    </button>
                                                </td>
                                                <td class="py-3.5 px-4 text-right space-x-1">
                                                    <button wire:click="editService({{ $srv->id }})"
                                                        title="Edit Layanan"
                                                        class="p-1.5 text-xs text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>
                                                    <button wire:click="deleteService({{ $srv->id }})"
                                                        wire:confirm="Yakin ingin menghapus layanan '{{ $srv->name }}'?"
                                                        title="Hapus Layanan"
                                                        class="p-1.5 text-xs text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ $showMaterialCost ? 9 : 8 }}" class="py-12 text-center text-slate-400 text-sm">
                                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-xl">
                                                        <i class="fa-solid fa-scissors"></i>
                                                    </div>
                                                    <div>Belum ada layanan salon/barbershop yang ditambahkan.</div>
                                                    <button wire:click="openNewServiceModal" class="mt-2 text-xs text-orange-600 font-semibold hover:underline">
                                                        + Tambah Layanan Pertama
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                @if ($servicesTable->hasPages())
                                    <div class="px-4 py-3 border-t border-slate-100">
                                        {{ $servicesTable->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
