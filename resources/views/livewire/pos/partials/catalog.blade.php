<!-- ================= CATALOG CONTENT AREA ================= -->
<div class="flex-1 overflow-y-auto p-6 space-y-6">

    <!-- 1. Horizontal Category Tabs Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
        <!-- Tab: Semua Menu -->
        <button wire:click="setCategory('all')"
                class="p-3.5 rounded-xl border text-left transition flex items-center gap-3.5 {{ $selectedCategory === 'all' ? 'bg-white border-orange-500 ring-1 ring-orange-500 shadow-xs' : 'bg-white border-slate-200/80 hover:border-slate-300' }}">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $selectedCategory === 'all' ? 'bg-orange-500 text-white shadow-xs' : 'bg-slate-100 text-slate-600' }}">
                <i class="fa-solid fa-shapes text-base"></i>
            </div>
            <div>
                <div class="font-bold text-xs {{ $selectedCategory === 'all' ? 'text-orange-600' : 'text-slate-800' }}">Semua Menu</div>
                <div class="text-[11px] text-slate-400 mt-0.5">{{ $allMenuCount }} menu</div>
            </div>
        </button>

        <!-- Tab: Kopi -->
        <button wire:click="setCategory('kopi')"
                class="p-3.5 rounded-xl border text-left transition flex items-center gap-3.5 {{ $selectedCategory === 'kopi' ? 'bg-white border-orange-500 ring-1 ring-orange-500 shadow-xs' : 'bg-white border-slate-200/80 hover:border-slate-300' }}">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $selectedCategory === 'kopi' ? 'bg-orange-500 text-white shadow-xs' : 'bg-slate-100 text-slate-600' }}">
                <i class="fa-solid fa-mug-hot text-base"></i>
            </div>
            <div>
                <div class="font-bold text-xs {{ $selectedCategory === 'kopi' ? 'text-orange-600' : 'text-slate-800' }}">Kopi</div>
                <div class="text-[11px] text-slate-400 mt-0.5">12 menu</div>
            </div>
        </button>

        <!-- Tab: Non-Kopi -->
        <button wire:click="setCategory('non-kopi')"
                class="p-3.5 rounded-xl border text-left transition flex items-center gap-3.5 {{ $selectedCategory === 'non-kopi' ? 'bg-white border-orange-500 ring-1 ring-orange-500 shadow-xs' : 'bg-white border-slate-200/80 hover:border-slate-300' }}">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $selectedCategory === 'non-kopi' ? 'bg-orange-500 text-white shadow-xs' : 'bg-slate-100 text-slate-600' }}">
                <i class="fa-solid fa-glass-water text-base"></i>
            </div>
            <div>
                <div class="font-bold text-xs {{ $selectedCategory === 'non-kopi' ? 'text-orange-600' : 'text-slate-800' }}">Non-Kopi</div>
                <div class="text-[11px] text-slate-400 mt-0.5">10 menu</div>
            </div>
        </button>

        <!-- Tab: Makanan -->
        <button wire:click="setCategory('makanan')"
                class="p-3.5 rounded-xl border text-left transition flex items-center gap-3.5 {{ $selectedCategory === 'makanan' ? 'bg-white border-orange-500 ring-1 ring-orange-500 shadow-xs' : 'bg-white border-slate-200/80 hover:border-slate-300' }}">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $selectedCategory === 'makanan' ? 'bg-orange-500 text-white shadow-xs' : 'bg-slate-100 text-slate-600' }}">
                <i class="fa-solid fa-utensils text-base"></i>
            </div>
            <div>
                <div class="font-bold text-xs {{ $selectedCategory === 'makanan' ? 'text-orange-600' : 'text-slate-800' }}">Makanan</div>
                <div class="text-[11px] text-slate-400 mt-0.5">12 menu</div>
            </div>
        </button>

        <!-- Tab: Snack -->
        <button wire:click="setCategory('snack')"
                class="p-3.5 rounded-xl border text-left transition flex items-center gap-3.5 {{ $selectedCategory === 'snack' ? 'bg-white border-orange-500 ring-1 ring-orange-500 shadow-xs' : 'bg-white border-slate-200/80 hover:border-slate-300' }}">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $selectedCategory === 'snack' ? 'bg-orange-500 text-white shadow-xs' : 'bg-slate-100 text-slate-600' }}">
                <i class="fa-solid fa-cookie-bite text-base"></i>
            </div>
            <div>
                <div class="font-bold text-xs {{ $selectedCategory === 'snack' ? 'text-orange-600' : 'text-slate-800' }}">Snack</div>
                <div class="text-[11px] text-slate-400 mt-0.5">8 menu</div>
            </div>
        </button>
    </div>

    <!-- 2. Section Header: Title & Scan Barcode Button -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-sm font-bold text-slate-900">Katalog Menu</h2>
            <p class="text-[11px] text-slate-400 mt-0.5">Pilih produk untuk menambahkan ke pesanan</p>
        </div>

        <button wire:click="$set('showBarcodeModal', true)"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-xs">
            <i class="fa-solid fa-barcode text-slate-500"></i>
            <span>Scan Barcode</span>
        </button>
    </div>

    <!-- 3. Product Cards Grid (3 Columns) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
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
