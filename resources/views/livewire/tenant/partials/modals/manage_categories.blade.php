    <!-- 9. Modal Kelola Semua Kategori Menu -->
    @if ($showManageCategoriesModal)
        <div class="fixed inset-0 z-[75] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-2xl w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Kelola Semua Kategori Menu</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar kategori produk tenant Anda. Kategori baru akan langsung tampil di Master Produk & POS Kasir.</p>
                    </div>
                    <button wire:click="$set('showManageCategoriesModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 max-h-[70vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-xs font-semibold text-slate-500">Total: {{ $categories->count() }} Kategori</div>
                        <button type="button" wire:click="openNewCategoryModal"
                            class="px-3 py-1.5 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm flex items-center gap-1.5">
                            <i class="fa-solid fa-plus text-[10px]"></i>
                            <span>Tambah Kategori Baru</span>
                        </button>
                    </div>

                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="py-2.5 px-3">Urutan</th>
                                    <th class="py-2.5 px-3">Kategori</th>
                                    <th class="py-2.5 px-3">Slug</th>
                                    <th class="py-2.5 px-3 text-center">Jumlah Menu</th>
                                    <th class="py-2.5 px-3 text-center">Status</th>
                                    <th class="py-2.5 px-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($categories as $cat)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-3 font-mono text-slate-400">#{{ $cat->sort_order }}</td>
                                        <td class="py-3 px-3">
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center shrink-0">
                                                    <i class="fa-solid {{ $cat->icon ?: 'fa-tag' }}"></i>
                                                </div>
                                                <span class="font-bold text-slate-900">{{ $cat->name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-3 font-mono text-slate-400 text-[11px]">{{ $cat->slug }}</td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="px-2 py-0.5 rounded-full bg-slate-100 font-semibold text-slate-700">
                                                {{ $cat->products_count ?? 0 }} Produk
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="px-2 py-0.5 rounded-full font-semibold {{ $cat->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                                                {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-right space-x-1">
                                            <button type="button" wire:click="openEditCategoryModal({{ $cat->id }})"
                                                class="px-2.5 py-1 text-slate-600 hover:text-orange-600 hover:bg-orange-50 rounded-lg font-semibold transition"
                                                title="Edit Kategori">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </button>
                                            <button type="button" wire:click="deleteCategory({{ $cat->id }})"
                                                wire:confirm="Hapus kategori '{{ $cat->name }}'? Produk di dalamnya tidak akan terhapus."
                                                class="px-2.5 py-1 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg font-semibold transition"
                                                title="Hapus Kategori">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-6 text-center text-slate-400">
                                            Belum ada kategori yang dibuat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                    <button type="button" wire:click="$set('showManageCategoriesModal', false)"
                        class="px-4 py-2 text-xs font-semibold bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

