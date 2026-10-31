<!-- ================= CATALOG CONTENT AREA ================= -->
<div class="p-4 sm:p-5 lg:p-6 space-y-4 sm:space-y-5">

    @if (! $activeShiftId && ! ($isSalon ?? false))
        <!-- Warning: Kasir Belum Buka Shift / Sistem Ditutup (Hanya untuk F&B / Retail, disembunyikan untuk Salon & Barbershop) -->
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-300 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-bold text-lg shadow-xs shrink-0">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-rose-700 bg-rose-100 border border-rose-200 px-2 py-0.5 rounded">Sistem Kasir Ditutup</span>
                        <span class="text-xs font-bold text-slate-800">Shift Belum Dibuka</span>
                    </div>
                    <p class="text-xs text-slate-600 mt-1">
                        Kasir wajib membuka shift dan memasukkan modal kas awal di mesin kasir sebelum dapat melakukan transaksi penjualan.
                    </p>
                </div>
            </div>
            <button wire:click="$set('showOpenShiftModal', true)"
                    class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center justify-center gap-2 shrink-0">
                <i class="fa-solid fa-key text-xs"></i>
                <span>Buka Shift Sekarang</span>
            </button>
        </div>
    @endif

    <!-- 1. Horizontal Category Tabs Bar (Ringkas & Lebih Kecil) -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 max-w-full no-scrollbar">
        <!-- Tab: Semua Menu -->
        <button wire:click="setCategory('all')"
                type="button"
                class="px-3 py-2 rounded-xl border text-left transition flex items-center gap-2 shrink-0 cursor-pointer {{ $selectedCategory === 'all' ? 'bg-orange-500 border-orange-500 text-white shadow-xs' : 'bg-white border-slate-200/80 hover:border-slate-300 text-slate-700' }}">
            <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 {{ $selectedCategory === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                <i class="fa-solid fa-shapes text-[11px]"></i>
            </div>
            <div class="flex items-center gap-1.5 whitespace-nowrap">
                <span class="font-bold text-xs">{{ ($isSalon ?? false) ? 'Semua Katalog' : 'Semua Menu' }}</span>
                <span class="text-[10px] font-semibold px-1.5 py-0.2 rounded-full {{ $selectedCategory === 'all' ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $allMenuCount }}</span>
            </div>
        </button>

        @foreach ($categories as $cat)
            <button wire:click="setCategory('{{ $cat->slug }}')"
                    type="button"
                    class="px-3 py-2 rounded-xl border text-left transition flex items-center gap-2 shrink-0 cursor-pointer {{ $selectedCategory === $cat->slug ? 'bg-orange-500 border-orange-500 text-white shadow-xs' : 'bg-white border-slate-200/80 hover:border-slate-300 text-slate-700' }}">
                <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 {{ $selectedCategory === $cat->slug ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                    <i class="fa-solid {{ $cat->icon ?: 'fa-tag' }} text-[11px]"></i>
                </div>
                <div class="flex items-center gap-1.5 whitespace-nowrap">
                    <span class="font-bold text-xs">{{ $cat->name }}</span>
                    <span class="text-[10px] font-semibold px-1.5 py-0.2 rounded-full {{ $selectedCategory === $cat->slug ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $cat->products_count }}</span>
                </div>
            </button>
        @endforeach
    </div>

    <!-- 2. Section Header: Title & Scan Barcode Button -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-sm font-bold text-slate-900">{{ ($isSalon ?? false) ? 'Katalog Layanan & Produk' : 'Katalog Menu' }}</h2>
            <p class="text-[11px] text-slate-400 mt-0.5">{{ ($isSalon ?? false) ? 'Pilih layanan salon atau produk perawatan untuk menambahkan ke pesanan' : 'Pilih produk untuk menambahkan ke pesanan' }}</p>
        </div>

        <button wire:click="$set('showBarcodeModal', true)"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs">
            <i class="fa-solid fa-barcode text-slate-500"></i>
            <span>Scan Barcode</span>
        </button>
    </div>

    <!-- 3. Product Cards Grid (Responsive Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-3 sm:gap-3.5">
        @forelse ($products as $product)
            <div wire:click="addToCart({{ $product->id }})"
                 class="group bg-white border border-slate-200/80 hover:border-orange-300 rounded-xl p-3 flex items-center justify-between transition cursor-pointer shadow-xs hover:shadow-sm">
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Product Thumbnail -->
                    <img src="{{ $product->image_url ?? 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=200&h=200&q=80' }}"
                         alt="{{ $product->name }}"
                         class="w-14 h-14 rounded-lg object-cover flex-shrink-0 bg-slate-100 ring-1 ring-slate-100" />

                    <div class="min-w-0">
                        <div class="font-bold text-xs text-slate-900 group-hover:text-orange-600 transition truncate">
                            {{ $product->name }}
                        </div>
                        <div class="text-[10px] text-slate-400 mt-0.5 truncate">
                            {{ $product->category_tag ?? 'Kopi • Hot / Ice' }}
                        </div>
                        <div class="text-xs font-bold text-slate-900 mt-1">
                            Rp{{ number_format($product->base_price, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <!-- Circular Orange Plus Button -->
                <button type="button"
                        class="w-7 h-7 rounded-full bg-orange-500 text-white flex items-center justify-center flex-shrink-0 hover:bg-orange-600 transition shadow-xs ml-2">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                </button>
            </div>
        @empty
            <div class="col-span-3 py-12 text-center text-slate-400 text-xs">
                <i class="fa-solid fa-magnifying-glass text-2xl mb-2 text-slate-300"></i>
                <p>Tidak ada produk ditemukan untuk kategori ini.</p>
            </div>
        @endforelse
    </div>
</div>

