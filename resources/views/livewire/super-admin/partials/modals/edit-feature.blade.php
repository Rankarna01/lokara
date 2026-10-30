    <!-- ================= MODAL EDIT NAMA FITUR ================= -->
    @if ($showEditFeatureModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
             x-data
             @keydown.escape.window="$wire.closeEditFeatureModal()"
             @click.self="$wire.closeEditFeatureModal()">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full p-6 animate-scale-up space-y-4"
                 @click.outside="$wire.closeEditFeatureModal()">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-xs">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Edit Fitur Platform</h3>
                    </div>
                    <button type="button"
                            wire:click="closeEditFeatureModal"
                            @click.prevent="$wire.set('showEditFeatureModal', false)"
                            class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <div class="space-y-3.5 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">
                            Nama Fitur <span class="text-rose-500 font-bold">*</span>
                        </label>
                        <input type="text"
                               wire:model="editingFeatureName"
                               class="w-full border rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none @error('editingFeatureName') border-rose-400 ring-1 ring-rose-400 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                        @error('editingFeatureName')
                            <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kategori Fitur</label>
                        <select wire:model="editingFeatureCategory"
                                class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 bg-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <option value="Kasir & Transaksi">Kasir & Transaksi</option>
                            <option value="Inventori">Inventori</option>
                            <option value="Laporan">Laporan</option>
                            <option value="Paket Bundling">Paket Bundling</option>
                            <option value="Platform">Platform</option>
                            <option value="Integrasi">Integrasi</option>
                            <option value="Layanan & Bantuan">Layanan & Bantuan</option>
                        </select>
                    </div>

                    <div class="p-3 bg-amber-50 border border-amber-200/80 rounded-xl text-amber-800 text-[11px]">
                        <i class="fa-solid fa-circle-info mr-1 text-amber-600"></i>
                        Mengubah nama fitur ini akan otomatis memperbarui daftar fitur di seluruh paket yang memakainya.
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button"
                            wire:click="closeEditFeatureModal"
                            @click.prevent="$wire.set('showEditFeatureModal', false)"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button"
                            wire:click="updateFeature"
                            class="px-5 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs shadow-sm transition cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    @endif
