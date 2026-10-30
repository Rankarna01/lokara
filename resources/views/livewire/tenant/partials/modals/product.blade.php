    <!-- 1. Modal Tambah / Edit Produk -->
    @if ($showProductModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-base">{{ $editingProductId ? 'Edit Produk Menu' : 'Tambah Menu Produk Baru' }}</h3>
                    <button wire:click="$set('showProductModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto text-sm">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Menu</label>
                        <input type="text" wire:model="productName" placeholder="Contoh: Es Kopi Susu Pandan"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        @error('productName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-semibold text-slate-700">Kategori</label>
                                <button type="button" wire:click="openNewCategoryModal"
                                    class="text-[11px] font-semibold text-orange-600 hover:text-orange-700 hover:underline flex items-center gap-1">
                                    <i class="fa-solid fa-plus text-[9px]"></i> Kategori Baru
                                </button>
                            </div>
                            <select wire:model="productCategoryId"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tag Sub-kategori</label>
                            <input type="text" wire:model="productCategoryTag" placeholder="e.g. Kopi • Ice"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Harga Jual (Rp)</label>
                            <input type="number" wire:model="productBasePrice"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 font-bold">
                            @error('productBasePrice') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">HPP / Biaya Modal (Rp)</label>
                            <input type="number" wire:model="productCostPrice"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                    </div>

                    <!-- Upload Foto Produk -->
                    <div class="border border-slate-200 rounded-2xl p-3.5 bg-slate-50/70 space-y-3" x-data="{ showProductUrlInput: {{ !empty($productImageUrl) ? 'true' : 'false' }} }">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-semibold text-slate-800">
                                <i class="fa-solid fa-camera text-orange-500 mr-1"></i> Foto Menu Produk
                            </label>
                            <button type="button" @click="showProductUrlInput = !showProductUrlInput"
                                class="text-[11px] font-medium text-slate-500 hover:text-orange-600 underline transition">
                                <span x-text="showProductUrlInput ? 'Sembunyikan Opsi URL' : 'Gunakan URL Foto Web'"></span>
                            </button>
                        </div>

                        <div class="flex items-center gap-3.5">
                            <!-- Image Preview Thumbnail -->
                            <div class="relative shrink-0 w-20 h-20 rounded-xl overflow-hidden bg-white border border-slate-200 shadow-xs flex items-center justify-center">
                                @if ($productImageFile)
                                    <img src="{{ $productImageFile->temporaryUrl() }}" class="w-full h-full object-cover">
                                    <span class="absolute bottom-0 inset-x-0 bg-emerald-600/95 text-white text-[9px] font-bold text-center py-0.5">Preview Baru</span>
                                @elseif ($productImageUrl)
                                    <img src="{{ $productImageUrl }}" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=150&h=150&q=80'">
                                    <span class="absolute bottom-0 inset-x-0 bg-slate-800/80 text-white text-[9px] font-semibold text-center py-0.5">Tersimpan</span>
                                @else
                                    <div class="text-center p-2 text-slate-400">
                                        <i class="fa-regular fa-image text-2xl text-slate-300"></i>
                                        <div class="text-[9px] mt-0.5 font-medium text-slate-400">Tanpa Foto</div>
                                    </div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="flex-1 min-w-0 space-y-1.5">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-semibold cursor-pointer transition shadow-xs">
                                        <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                                        <span>Pilih / Unggah Gambar</span>
                                        <input type="file" wire:model="productImageFile" accept="image/png,image/jpeg,image/webp,image/jpg" class="hidden">
                                    </label>

                                    @if ($productImageFile || $productImageUrl)
                                        <button type="button" wire:click="removeProductImage"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-600 rounded-xl text-xs font-semibold transition">
                                            <i class="fa-solid fa-trash-can text-[11px]"></i>
                                            <span>Hapus</span>
                                        </button>
                                    @endif
                                </div>

                                <div wire:loading wire:target="productImageFile" class="text-xs text-orange-600 font-semibold flex items-center gap-1.5">
                                    <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                                    <span>Mengunggah file foto...</span>
                                </div>

                                <p class="text-[10px] text-slate-400 leading-tight">
                                    Format: JPG, PNG, atau WEBP. Ukuran maksimal 3MB.
                                </p>

                                @error('productImageFile')
                                    <span class="text-[11px] text-rose-500 font-medium block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- URL Input Option -->
                        <div x-show="showProductUrlInput" class="pt-2.5 border-t border-slate-200/80 space-y-1">
                            <label class="block text-[11px] font-semibold text-slate-600">URL Gambar Eksternal (Opsional):</label>
                            <input type="text" wire:model="productImageUrl" placeholder="https://images.unsplash.com/..."
                                class="w-full bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-2">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                            <input type="checkbox" wire:model="productIsAvailable" class="rounded text-orange-500 focus:ring-orange-500">
                            <span>Tersedia untuk dijual</span>
                        </label>
                    </div>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button wire:click="$set('showProductModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Batal</button>
                    <button wire:click="saveProduct"
                        class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm">
                        Simpan Produk
                    </button>
                </div>
            </div>
        </div>
    @endif

