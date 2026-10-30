    <!-- Modal Tambah / Edit Rekanan Supplier -->
    @if ($showSupplierModal)
        <div class="fixed inset-0 z-[80] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                            <i class="fa-solid fa-truck-field text-indigo-500"></i>
                            <span>{{ $editingSupplierId ? 'Edit Data Rekanan Supplier' : 'Tambah Rekanan Supplier Baru' }}</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftarkan profil pemasok untuk mempermudah penerbitan Purchase Order (PO).</p>
                    </div>
                    <button type="button" wire:click="$set('showSupplierModal', false)" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 space-y-4 text-sm max-h-[75vh] overflow-y-auto">
                    <!-- Nama Supplier -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Perusahaan / Supplier <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="supplierName" placeholder="Contoh: PT L'Oreal Indonesia, Toko Kemasan Mandiri"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-1 focus:ring-orange-500">
                        @error('supplierName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- PIC & No. Telepon -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama PIC (Kontak)</label>
                            <input type="text" wire:model="supplierContactPerson" placeholder="Contoh: Ibu Maya / Pak Hendro"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:ring-1 focus:ring-orange-500">
                            @error('supplierContactPerson') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">No. Telepon / WhatsApp</label>
                            <input type="text" wire:model="supplierPhone" placeholder="Contoh: 081233445566"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:ring-1 focus:ring-orange-500">
                            @error('supplierPhone') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Email Pemasok</label>
                        <input type="email" wire:model="supplierEmail" placeholder="Contoh: order@supplier.co.id"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:ring-1 focus:ring-orange-500">
                        @error('supplierEmail') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Alamat Lengkap -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Kantor / Gudang</label>
                        <textarea wire:model="supplierAddress" rows="2" placeholder="Alamat pengiriman / lokasi pemasok..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:ring-1 focus:ring-orange-500"></textarea>
                        @error('supplierAddress') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Syarat Tempo Pembayaran -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Syarat Pembayaran (Tempo Hari)</label>
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <button type="button" wire:click="$set('supplierTermsDays', 0)"
                                class="px-2.5 py-1 text-xs font-medium rounded-lg border transition {{ $supplierTermsDays === 0 ? 'bg-orange-500 text-white border-orange-500' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200' }}">
                                COD / Tunai (0 Hari)
                            </button>
                            <button type="button" wire:click="$set('supplierTermsDays', 7)"
                                class="px-2.5 py-1 text-xs font-medium rounded-lg border transition {{ $supplierTermsDays === 7 ? 'bg-orange-500 text-white border-orange-500' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200' }}">
                                Tempo 7 Hari
                            </button>
                            <button type="button" wire:click="$set('supplierTermsDays', 14)"
                                class="px-2.5 py-1 text-xs font-medium rounded-lg border transition {{ $supplierTermsDays === 14 ? 'bg-orange-500 text-white border-orange-500' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200' }}">
                                Tempo 14 Hari
                            </button>
                            <button type="button" wire:click="$set('supplierTermsDays', 30)"
                                class="px-2.5 py-1 text-xs font-medium rounded-lg border transition {{ $supplierTermsDays === 30 ? 'bg-orange-500 text-white border-orange-500' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200' }}">
                                Tempo 30 Hari
                            </button>
                        </div>
                        <div class="relative w-44">
                            <input type="number" min="0" max="180" wire:model="supplierTermsDays"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-semibold text-slate-900 focus:ring-1 focus:ring-orange-500">
                            <span class="absolute right-3 top-2 text-xs text-slate-400">Hari</span>
                        </div>
                        @error('supplierTermsDays') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Status Aktif -->
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer select-none text-xs font-medium text-slate-700">
                            <input type="checkbox" wire:model="supplierIsActive" class="rounded text-orange-500 focus:ring-orange-500">
                            <span>Supplier Aktif Bekerjasama</span>
                        </label>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" wire:click="$set('showSupplierModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl cursor-pointer">Batal</button>
                    <button type="button" wire:click="saveSupplier"
                        class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-check"></i>
                        <span>{{ $editingSupplierId ? 'Simpan Perubahan' : 'Tambah Supplier' }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

