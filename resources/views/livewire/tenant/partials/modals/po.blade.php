    <!-- 6. Modal Buat Purchase Order (PO) -->
    @if ($showPoModal)
        <div class="fixed inset-0 z-[70] bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                            <i class="fa-solid fa-cart-plus text-orange-500"></i>
                            <span>Buat Purchase Order (PO) Baru</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Terbitkan pesanan resmi ke rekanan pemasok.</p>
                    </div>
                    <button type="button" wire:click="$set('showPoModal', false)" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="p-5 space-y-4 text-sm max-h-[75vh] overflow-y-auto">
                    <!-- Supplier Selection with Quick Add -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-slate-700">Pilih Supplier Rekanan <span class="text-rose-500">*</span></label>
                            <button type="button" wire:click="openNewSupplierModal"
                                class="text-[11px] font-semibold text-orange-600 hover:text-orange-700 hover:underline flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-plus text-[9px]"></i> Tambah Supplier Baru
                            </button>
                        </div>
                        <select wire:model="poSupplierId"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-1 focus:ring-orange-500 cursor-pointer">
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($allSuppliersList as $sup)
                                <option value="{{ $sup->id }}">
                                    {{ $sup->name }} ({{ $sup->terms_days == 0 ? 'COD / Tunai' : 'Tempo ' . $sup->terms_days . ' Hari' }})
                                </option>
                            @endforeach
                        </select>
                        @error('poSupplierId') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tanggal Estimasi & Outlet -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Estimasi Tiba / Pengiriman</label>
                            <input type="date" wire:model="poExpectedDate"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-1 focus:ring-orange-500">
                            @error('poExpectedDate') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Outlet Penerima</label>
                            <div class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-700 truncate">
                                {{ $selectedOutlet->name ?? 'Outlet Utama' }}
                            </div>
                        </div>
                    </div>

                    <!-- Total Nilai Tagihan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Total Nilai Tagihan Pesanan (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" wire:model="poTotalAmount" placeholder="1500000" min="1000"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3.5 py-2 text-sm focus:ring-1 focus:ring-orange-500 font-bold text-slate-900">
                        </div>
                        @error('poTotalAmount') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Catatan Pengadaan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Pengadaan / Rincian Barang</label>
                        <textarea wire:model="poNotes" rows="3" placeholder="Contoh: Restock Shampoo Anti-Dandruff 20 btl, Pomade 30 pcs, dan Handuk Microfiber..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:ring-1 focus:ring-orange-500"></textarea>
                        @error('poNotes') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" wire:click="$set('showPoModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl cursor-pointer">Batal</button>
                    <button type="button" wire:click="savePurchaseOrder"
                        class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-check"></i>
                        <span>Terbitkan PO Resmi</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

