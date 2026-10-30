    <!-- 2. Modal Tambah Bahan Baku -->
    @if ($showIngredientModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-base">Tambah Bahan Baku Mentah</h3>
                    <button wire:click="$set('showIngredientModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto text-sm">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Bahan Baku</label>
                        <input type="text" wire:model="ingredientName" placeholder="Contoh: Biji Kopi Arabika Gayo"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Satuan Beli (Pembelian)</label>
                            <input type="text" wire:model="ingredientPurchaseUnit" placeholder="e.g. Kg, Dus, Liter"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Satuan Pakai (Resep)</label>
                            <input type="text" wire:model="ingredientUsageUnit" placeholder="e.g. Gram, ml, Pcs"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Faktor Konversi (1 Beli = ... Pakai)</label>
                            <input type="number" wire:model="ingredientConversionFactor" placeholder="1000"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 font-semibold">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Biaya per Satuan Pakai (Rp)</label>
                            <input type="number" wire:model="ingredientCostPerUsage" placeholder="180"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 font-semibold">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Stok Saldo Awal</label>
                            <input type="number" wire:model="ingredientCurrentStock"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Batas Minimal Peringatan</label>
                            <input type="number" wire:model="ingredientMinStock"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button wire:click="$set('showIngredientModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Batal</button>
                    <button wire:click="saveIngredient"
                        class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm">
                        Simpan Bahan Baku
                    </button>
                </div>
            </div>
        </div>
    @endif

