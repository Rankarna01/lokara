    <!-- 7. Modal Otorisasi Void Pesanan dengan PIN -->
    @if ($showVoidModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-sm w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-rose-600">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <h3 class="font-bold text-slate-900 text-base">Otorisasi Void Order</h3>
                    </div>
                    <button wire:click="$set('showVoidModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="p-5 space-y-4 text-sm">
                    <p class="text-xs text-slate-500">Masukkan 6-digit PIN Manajer / Owner untuk membatalkan transaksi ini.</p>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">6-Digit PIN Manajer</label>
                        <input type="password" maxlength="6" wire:model="voidPin" placeholder="••••••"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-center text-lg tracking-widest font-mono focus:ring-1 focus:ring-rose-500">
                        <div class="text-[10px] text-slate-400 text-center mt-1">Hint Demo PIN: 888888 atau 123456</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan Pembatalan</label>
                        <input type="text" wire:model="voidReason"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:ring-1 focus:ring-rose-500">
                    </div>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button wire:click="$set('showVoidModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Batal</button>
                    <button wire:click="executeVoidOrder"
                        class="px-4 py-2 text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow-sm">
                        Konfirmasi Void
                    </button>
                </div>
            </div>
        </div>
    @endif

