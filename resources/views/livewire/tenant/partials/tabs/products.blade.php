                    <div class="space-y-5">
                        <!-- Top Header & Filter Controls -->
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h1 class="text-xl font-bold text-slate-900">Katalog & Master Produk</h1>
                                    @if($tenant?->isSalon() || $tenant?->hasFeature('has_services'))
                                        <span class="text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-full uppercase tracking-wider">
                                            Produk Fisik & Retail
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $tenant?->isSalon() ? 'Kelola produk retail perawatan, pomade, shampoo, harga jual, dan stok barang.' : 'Kelola menu, harga jual cabang, HPP modal, dan ketersediaan stok.' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2.5">
                                @if($tenant?->isSalon() || $tenant?->hasFeature('has_services'))
                                    <button wire:click="switchTab('services')"
                                        class="px-3.5 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-semibold text-xs rounded-xl shadow-xs flex items-center gap-2 transition">
                                        <i class="fa-solid fa-scissors"></i>
                                        <span>Ke Master Layanan ({{ $services->count() }})</span>
                                    </button>
                                @endif
                                <button wire:click="openNewProductModal"
                                    class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>Tambah Produk Baru</span>
                                </button>
                            </div>
                        </div>

                        <!-- Search & Category Filters -->
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div class="flex items-center gap-2 overflow-x-auto w-full lg:w-auto pb-2 lg:pb-0">
                                <!-- Filter Semua Kategori -->
                                <button wire:click="$set('selectedCategoryFilter', 'all')"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition shrink-0 {{ $selectedCategoryFilter === 'all' ? 'bg-orange-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                    Semua Kategori ({{ $totalProductsCount ?? $products->count() }})
                                </button>

                                <!-- Pill Kategori + Tombol Edit Langsung -->
                                @foreach($categories as $cat)
                                    <div class="inline-flex items-center rounded-lg border transition shrink-0 {{ $selectedCategoryFilter == $cat->id ? 'bg-orange-500 border-orange-500 text-white shadow-sm' : 'bg-slate-100 border-transparent text-slate-600 hover:bg-slate-200' }}">
                                        <button wire:click="$set('selectedCategoryFilter', {{ $cat->id }})"
                                            class="px-3 py-1.5 text-xs font-semibold whitespace-nowrap flex items-center gap-1.5">
                                            @if($cat->icon)
                                                <i class="fa-solid {{ $cat->icon }} text-[10px] {{ $selectedCategoryFilter == $cat->id ? 'text-white' : 'text-slate-400' }}"></i>
                                            @endif
                                            <span>{{ $cat->name }}</span>
                                        </button>
                                        <button wire:click="openEditCategoryModal({{ $cat->id }})"
                                            title="Edit Kategori: {{ $cat->name }}"
                                            class="pr-2 pl-0.5 py-1.5 text-[10px] {{ $selectedCategoryFilter == $cat->id ? 'text-orange-200 hover:text-white' : 'text-slate-400 hover:text-slate-700' }} transition">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    </div>
                                @endforeach

                                <!-- Tombol Tambah Kategori Langsung di Samping Kategori -->
                                <button wire:click="openNewCategoryModal"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-dashed border-orange-400 bg-orange-50 text-orange-600 hover:bg-orange-100 hover:border-orange-500 transition flex items-center gap-1.5 whitespace-nowrap shrink-0 shadow-xs"
                                    title="Tambah Kategori Menu Baru">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                    <span>Tambah Kategori</span>
                                </button>

                                <!-- Tombol Kelola Semua Kategori -->
                                <button wire:click="openManageCategoriesModal"
                                    class="px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-800 transition flex items-center gap-1.5 whitespace-nowrap shrink-0"
                                    title="Kelola & Atur Urutan Kategori">
                                    <i class="fa-solid fa-sliders text-[11px] text-slate-500"></i>
                                    <span class="hidden sm:inline">Kelola Kategori</span>
                                </button>
                            </div>

                            <div class="relative w-full lg:w-64 shrink-0">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                                <input type="text" wire:model.live.debounce.300ms="searchProduct"
                                    placeholder="Cari nama menu..."
                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-8 pr-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
                            </div>
                        </div>

                        <!-- Product Table -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 border-b border-slate-200/80 text-xs text-slate-500 uppercase tracking-wider font-semibold">
                                        <tr>
                                            <th class="py-3 px-4">Menu Produk</th>
                                            <th class="py-3 px-4">Kategori</th>
                                            <th class="py-3 px-4 text-right">Harga Jual</th>
                                            <th class="py-3 px-4 text-right">HPP (Modal)</th>
                                            <th class="py-3 px-4 text-center">Margin %</th>
                                            <th class="py-3 px-4 text-center">Status</th>
                                            <th class="py-3 px-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($products as $p)
                                            <tr class="hover:bg-slate-50/60 transition">
                                                <td class="py-3.5 px-4">
                                                    <div class="flex items-center gap-3">
                                                        <img src="{{ $p->image_url ?: 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=150&h=150&q=80' }}"
                                                            class="w-10 h-10 rounded-xl object-cover border border-slate-200 shrink-0">
                                                        <div>
                                                            <div class="font-bold text-slate-900">{{ $p->name }}</div>
                                                            <div class="text-[11px] text-slate-400">{{ $p->sku ?? 'PRD-00' . $p->id }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3.5 px-4 text-xs text-slate-600">
                                                    @if($p->category)
                                                        <button wire:click="openEditCategoryModal({{ $p->category->id }})"
                                                            title="Edit kategori {{ $p->category->name }}"
                                                            class="bg-slate-100 hover:bg-orange-50 hover:text-orange-600 px-2.5 py-1 rounded-md font-medium text-slate-700 inline-flex items-center gap-1.5 transition">
                                                            @if($p->category->icon)
                                                                <i class="fa-solid {{ $p->category->icon }} text-[10px] text-slate-400"></i>
                                                            @endif
                                                            <span>{{ $p->category->name }}</span>
                                                            <i class="fa-solid fa-pen text-[9px] text-slate-400 ml-0.5"></i>
                                                        </button>
                                                    @else
                                                        <span class="bg-slate-100 px-2.5 py-1 rounded-md font-medium text-slate-400">
                                                            Tanpa Kategori
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                                                    Rp {{ number_format($p->base_price, 0, ',', '.') }}
                                                </td>
                                                <td class="py-3.5 px-4 text-right text-xs text-slate-500">
                                                    Rp {{ number_format($p->cost_price, 0, ',', '.') }}
                                                </td>
                                                <td class="py-3.5 px-4 text-center">
                                                    @php
                                                        $margin = $p->base_price > 0 ? round((($p->base_price - $p->cost_price) / $p->base_price) * 100) : 0;
                                                    @endphp
                                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $margin >= 60 ? 'bg-emerald-50 text-emerald-600' : ($margin >= 40 ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600') }}">
                                                        {{ $margin }}%
                                                    </span>
                                                </td>
                                                <td class="py-3.5 px-4 text-center">
                                                    <button wire:click="toggleProductAvailability({{ $p->id }})"
                                                        class="text-xs font-semibold px-2.5 py-1 rounded-lg transition {{ $p->is_available ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-rose-50 text-rose-600 border border-rose-200' }}">
                                                        {{ $p->is_available ? 'Tersedia' : 'Habis' }}
                                                    </button>
                                                </td>
                                                <td class="py-3.5 px-4 text-right space-x-1">
                                                    <button wire:click="openRecipeModal({{ $p->id }})"
                                                        title="Atur Resep BOM & HPP"
                                                        class="p-1.5 text-xs text-slate-500 hover:text-orange-600 hover:bg-orange-50 rounded-lg">
                                                        <i class="fa-solid fa-mortar-pestle"></i>
                                                    </button>
                                                    <button wire:click="editProduct({{ $p->id }})"
                                                        title="Edit Produk"
                                                        class="p-1.5 text-xs text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                                    Tidak ada menu yang sesuai filter.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
