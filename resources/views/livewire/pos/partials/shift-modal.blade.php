<!-- ================= MODAL: SHIFT & KAS ================= -->
@if ($showShiftModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Manajemen Shift & Kas Laci</h3>
                    <p class="text-[11px] text-slate-400">Kasir: Dina Putri • Shift Berjalan</p>
                </div>
                <button wire:click="$set('showShiftModal', false)" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Cash Summary Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 uppercase font-semibold">Modal Awal</span>
                    <div class="font-bold text-slate-800 text-sm mt-0.5">Rp{{ number_format($shiftStartingCash, 0, ',', '.') }}</div>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 uppercase font-semibold">Penjualan Tunai</span>
                    <div class="font-bold text-emerald-600 text-sm mt-0.5">+Rp{{ number_format($cashSalesTotal, 0, ',', '.') }}</div>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 uppercase font-semibold">Kas Keluar</span>
                    <div class="font-bold text-rose-500 text-sm mt-0.5">-Rp{{ number_format(array_sum(array_column(array_filter($pettyCashMovements, fn($p) => $p['type'] === 'out'), 'amount')), 0, ',', '.') }}</div>
                </div>
                <div class="p-3 rounded-xl bg-orange-50 border border-orange-100">
                    <span class="text-[10px] text-orange-600 uppercase font-bold">Uang di Laci</span>
                    <div class="font-extrabold text-orange-600 text-sm mt-0.5">Rp{{ number_format($expectedCashDrawer, 0, ',', '.') }}</div>
                </div>
            </div>

            <!-- Petty Cash Entry Form -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-2 text-xs">
                <div class="font-bold text-slate-800 flex items-center justify-between">
                    <span>Pencatatan Kas Masuk / Keluar (Petty Cash)</span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <select wire:model="pettyCashType" class="border border-slate-200 rounded-lg px-2 py-1.5 bg-white text-slate-700">
                        <option value="out">Kas Keluar (-)</option>
                        <option value="in">Kas Masuk (+)</option>
                    </select>
                    <input type="number" wire:model="pettyCashAmount" placeholder="Nominal Rp" class="border border-slate-200 rounded-lg px-2.5 py-1.5 bg-white" />
                    <input type="text" wire:model="pettyCashReason" placeholder="Keterangan (misal: Es Batu)" class="border border-slate-200 rounded-lg px-2.5 py-1.5 bg-white" />
                </div>
                <div class="text-right">
                    <button type="button" wire:click="recordPettyCash" class="px-3 py-1.5 rounded-lg bg-slate-800 text-white font-semibold text-[11px] hover:bg-slate-900 transition">
                        Simpan Mutasi Kas
                    </button>
                </div>
            </div>

            <!-- Petty Cash History List -->
            <div class="space-y-1.5 max-h-36 overflow-y-auto text-xs">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Riwayat Kas Selama Shift:</div>
                @foreach ($pettyCashMovements as $pcm)
                    <div class="flex items-center justify-between p-2 rounded-lg bg-white border border-slate-100 text-[11px]">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full {{ $pcm['type'] === 'out' ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                            <span class="font-medium text-slate-800">{{ $pcm['reason'] }}</span>
                            <span class="text-slate-400 font-normal">({{ $pcm['time'] }})</span>
                        </div>
                        <span class="font-bold {{ $pcm['type'] === 'out' ? 'text-rose-500' : 'text-emerald-600' }}">
                            {{ $pcm['type'] === 'out' ? '-' : '+' }}Rp{{ number_format($pcm['amount'], 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-between items-center text-xs">
                <button wire:click="notify('Laci kasir fisik (Cash Drawer) terbuka otomatis via perintah ESC/POS')" class="text-slate-600 hover:text-slate-900 font-medium">
                    <i class="fa-solid fa-vault mr-1 text-slate-400"></i> Buka Laci Kas
                </button>
                <button wire:click="notify('Shift ditutup. Laporan ringkasan kas dicetak ke printer.')" class="px-4 py-2 rounded-xl bg-orange-500 text-white font-bold hover:bg-orange-600 transition">
                    Tutup Shift & Rekonsiliasi
                </button>
            </div>
        </div>
    </div>
@endif

