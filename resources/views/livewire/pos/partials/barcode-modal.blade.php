<!-- ================= MODAL: SCAN BARCODE ================= -->
@if ($showBarcodeModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-sm w-full p-6 text-center space-y-4">
            <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center mx-auto text-xl">
                <i class="fa-solid fa-barcode"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-900">Scan Barcode Menu</h3>
                <p class="text-xs text-slate-400 mt-0.5">Arahkan scanner atau ketikkan kode barcode di bawah</p>
            </div>

            <form wire:submit.prevent="scanBarcode" class="space-y-3">
                <input type="text" wire:model="barcodeInput" autofocus placeholder="Scan atau input barcode..." class="w-full text-center tracking-widest font-mono text-sm px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500" />
                <div class="flex gap-2">
                    <button type="button" wire:click="$set('showBarcodeModal', false)" class="w-1/2 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-600">Tutup</button>
                    <button type="submit" class="w-1/2 py-2 rounded-xl bg-orange-500 text-white text-xs font-bold hover:bg-orange-600 transition">Cari</button>
                </div>
            </form>
        </div>
    </div>
@endif
