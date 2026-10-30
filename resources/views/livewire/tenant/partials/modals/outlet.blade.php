    <!-- 12. Modal Tambah / Edit Cabang Outlet -->
    @if ($showOutletModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">{{ $editingOutletId ? 'Edit Konfigurasi Cabang' : 'Tambah Cabang Lokasi Baru' }}</h3>
                    </div>
                    <button wire:click="$set('showOutletModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto text-sm">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Cabang / Outlet</label>
                        <input type="text" wire:model="outletName" placeholder="Contoh: Cabang Kemang / Cabang Dago"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        @error('outletName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
                        <textarea rows="2" wire:model="outletAddress" placeholder="Jl. Kemang Raya No. 45, Jakarta Selatan"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500"></textarea>
                        @error('outletAddress') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Telepon Cabang</label>
                        <input type="text" wire:model="outletPhone" placeholder="081298765432"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tarif Pajak PPn (%)</label>
                            <input type="number" wire:model="outletTaxRate"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 font-semibold">
                            @error('outletTaxRate') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Service Charge (%)</label>
                            <input type="number" wire:model="outletServiceRate"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 font-semibold">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pesan Footer Struk Kasir</label>
                        <input type="text" wire:model="outletReceiptFooter" placeholder="Terima kasih atas kunjungan Anda!"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                    </div>

                    <div class="flex items-center gap-4 pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                            <input type="checkbox" wire:model="outletIsActive" class="rounded text-orange-500 focus:ring-orange-500">
                            <span>Cabang ini aktif beroperasi dan dapat dipilih oleh kasir</span>
                        </label>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" wire:click="$set('showOutletModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Batal</button>
                    <button type="button" wire:click="saveOutlet"
                        class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i>
                        <span>{{ $editingOutletId ? 'Simpan Perubahan' : 'Buat Cabang' }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

