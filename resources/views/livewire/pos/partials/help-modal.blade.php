<!-- ================= MODAL: BANTUAN & PINTASAN KEYBOARD ================= -->
@if ($showHelpModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-keyboard"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Pintasan Keyboard (Shortcuts)</h3>
                        <p class="text-[11px] text-slate-400">Gunakan tombol keyboard untuk operasi kasir super cepat</p>
                    </div>
                </div>
                <button wire:click="$set('showHelpModal', false)" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="p-2.5 rounded-xl border border-slate-100 bg-slate-50 flex items-center justify-between">
                    <span class="text-slate-600">Buka Pembayaran</span>
                    <kbd class="px-2 py-0.5 bg-white border border-slate-200 rounded font-mono font-bold text-slate-800 shadow-xs">F1 / Enter</kbd>
                </div>
                <div class="p-2.5 rounded-xl border border-slate-100 bg-slate-50 flex items-center justify-between">
                    <span class="text-slate-600">Cari Menu</span>
                    <kbd class="px-2 py-0.5 bg-white border border-slate-200 rounded font-mono font-bold text-slate-800 shadow-xs">F2 / /</kbd>
                </div>
                <div class="p-2.5 rounded-xl border border-slate-100 bg-slate-50 flex items-center justify-between">
                    <span class="text-slate-600">Tahan Pesanan (Hold)</span>
                    <kbd class="px-2 py-0.5 bg-white border border-slate-200 rounded font-mono font-bold text-slate-800 shadow-xs">F3</kbd>
                </div>
                <div class="p-2.5 rounded-xl border border-slate-100 bg-slate-50 flex items-center justify-between">
                    <span class="text-slate-600">Scan Barcode</span>
                    <kbd class="px-2 py-0.5 bg-white border border-slate-200 rounded font-mono font-bold text-slate-800 shadow-xs">F4</kbd>
                </div>
                <div class="p-2.5 rounded-xl border border-slate-100 bg-slate-50 flex items-center justify-between">
                    <span class="text-slate-600">Batal / Tutup Dialog</span>
                    <kbd class="px-2 py-0.5 bg-white border border-slate-200 rounded font-mono font-bold text-slate-800 shadow-xs">Esc</kbd>
                </div>
                <div class="p-2.5 rounded-xl border border-slate-100 bg-slate-50 flex items-center justify-between">
                    <span class="text-slate-600">Ubah Kuantitas</span>
                    <kbd class="px-2 py-0.5 bg-white border border-slate-200 rounded font-mono font-bold text-slate-800 shadow-xs">+ / -</kbd>
                </div>
            </div>

            <div class="pt-2 text-center text-[11px] text-slate-400">
                Perlu bantuan teknis? Hubungi Owner atau Super Admin di <span class="font-bold text-slate-700">support@lokarapos.id</span>
            </div>
        </div>
    </div>
@endif

