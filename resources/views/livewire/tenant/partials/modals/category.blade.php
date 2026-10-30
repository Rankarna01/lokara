    <!-- 8. Modal Tambah / Edit Kategori Menu -->
    @if ($showCategoryModal)
        <div class="fixed inset-0 z-[80] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">
                            {{ $editingCategoryId ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Kategori tersimpan langsung ke database & tampil di POS Kasir</p>
                    </div>
                    <button wire:click="$set('showCategoryModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto text-sm">
                    <!-- Nama Kategori -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Nama Kategori <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" wire:model="categoryName" placeholder="Contoh: Signature Coffee, Haircut, Treatment"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        @error('categoryName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Pilih Ikon -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Ikon Cepat</label>
                        <div class="grid grid-cols-6 gap-2 mb-2">
                            @php
                                $presetIcons = [
                                    ['icon' => 'fa-mug-hot', 'label' => 'Kopi'],
                                    ['icon' => 'fa-scissors', 'label' => 'Cukur'],
                                    ['icon' => 'fa-spa', 'label' => 'Spa'],
                                    ['icon' => 'fa-paintbrush', 'label' => 'Coloring'],
                                    ['icon' => 'fa-utensils', 'label' => 'Makanan'],
                                    ['icon' => 'fa-cookie-bite', 'label' => 'Snack'],
                                    ['icon' => 'fa-burger', 'label' => 'Burger'],
                                    ['icon' => 'fa-glass-water', 'label' => 'Minuman'],
                                    ['icon' => 'fa-bottle-water', 'label' => 'Botol'],
                                    ['icon' => 'fa-tag', 'label' => 'Tag Umum'],
                                    ['icon' => 'fa-box', 'label' => 'Paket'],
                                    ['icon' => 'fa-star', 'label' => 'Favorit'],
                                ];
                            @endphp
                            @foreach($presetIcons as $item)
                                <button type="button" wire:click="$set('categoryIcon', '{{ $item['icon'] }}')"
                                    title="{{ $item['label'] }}"
                                    class="h-10 rounded-xl border flex items-center justify-center text-sm transition {{ $categoryIcon === $item['icon'] ? 'bg-orange-50 border-orange-500 text-orange-600 font-bold shadow-xs' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                                    <i class="fa-solid {{ $item['icon'] }}"></i>
                                </button>
                            @endforeach
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-500">FontAwesome:</span>
                            <div class="relative flex-1">
                                <i class="fa-solid {{ $categoryIcon ?: 'fa-tag' }} absolute left-3 top-2.5 text-xs text-orange-500"></i>
                                <input type="text" wire:model="categoryIcon" placeholder="fa-scissors"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-8 pr-3 py-1.5 text-xs font-mono focus:outline-none focus:ring-1 focus:ring-orange-500">
                            </div>
                        </div>
                    </div>

                    <!-- Urutan Tampil & Status -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Urutan Tampil (Sort)</label>
                            <input type="number" wire:model="categorySortOrder" min="0"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 font-medium">
                            @error('categorySortOrder') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Status Kategori</label>
                            <label class="flex items-center gap-2 mt-2.5 cursor-pointer text-xs font-medium text-slate-700">
                                <input type="checkbox" wire:model="categoryIsActive" class="rounded text-orange-500 focus:ring-orange-500">
                                <span>Aktif di POS & Menu</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2">
                    @if ($editingCategoryId)
                        <button type="button"
                            wire:click="deleteCategory({{ $editingCategoryId }})"
                            wire:confirm="Yakin ingin menghapus kategori ini? Produk dalam kategori ini tidak akan terhapus, hanya status kategorinya menjadi kosong."
                            class="px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition flex items-center gap-1.5">
                            <i class="fa-solid fa-trash-can"></i>
                            <span>Hapus</span>
                        </button>
                    @else
                        <div></div>
                    @endif

                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="$set('showCategoryModal', false)"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">
                            Batal
                        </button>
                        <button type="button" wire:click="saveCategory"
                            class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm flex items-center gap-1.5">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ $editingCategoryId ? 'Simpan Perubahan' : 'Tambah Kategori' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

