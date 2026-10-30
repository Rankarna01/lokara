<!-- ================= MODAL: SHIFT & KAS ================= -->
@if ($showShiftModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm animate-in fade-in duration-150">
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xl max-w-lg w-full p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-base">
                        <i class="fa-solid fa-cash-register"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Manajemen Shift & Rekonsiliasi Kas</h3>
                        <p class="text-[11px] text-slate-400">Kasir: {{ auth()->user()->name ?? 'Dina Putri' }} • {{ $currentOutlet->name ?? 'Outlet Utama' }}</p>
                    </div>
                </div>
                <button wire:click="$set('showShiftModal', false)" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Cash Summary Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 uppercase font-semibold">Modal Awal</span>
                    <div class="font-bold text-slate-800 text-sm mt-0.5">Rp{{ number_format($shiftStartingCash, 0, ',', '.') }}</div>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 uppercase font-semibold">Penjualan Tunai</span>
                    <div class="font-bold text-emerald-600 text-sm mt-0.5">+Rp{{ number_format($cashSalesTotal, 0, ',', '.') }}</div>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 uppercase font-semibold">Kas Keluar</span>
                    <div class="font-bold text-rose-500 text-sm mt-0.5">-Rp{{ number_format(array_sum(array_column(array_filter($pettyCashMovements, fn($p) => $p['type'] === 'out'), 'amount')), 0, ',', '.') }}</div>
                </div>
                <div class="p-3 rounded-2xl bg-orange-50 border border-orange-100">
                    <span class="text-[10px] text-orange-600 uppercase font-bold">Uang di Laci</span>
                    <div class="font-extrabold text-orange-600 text-sm mt-0.5">Rp{{ number_format($expectedCashDrawer, 0, ',', '.') }}</div>
                </div>
            </div>

            @if ($showCloseShiftModal)
                <!-- Form Rekonsiliasi Kas & Tutup Shift -->
                <div class="p-4 bg-amber-50/70 border border-amber-200 rounded-2xl space-y-3 text-xs">
                    <div class="flex items-center justify-between font-bold text-amber-900">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-scale-balanced text-amber-600"></i>
                            Hitung Fisik Uang di Laci (Rekonsiliasi)
                        </span>
                        <button type="button" wire:click="$set('showCloseShiftModal', false)" class="text-amber-700 hover:text-amber-900 text-[11px]">
                            Batal
                        </button>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Total Uang Fisik Hasil Hitung (Rp)</label>
                        <input type="number" wire:model.live="shiftActualCash" placeholder="Hitung uang kertas & koin di laci..."
                               class="w-full px-3.5 py-2.5 bg-white border border-amber-300 rounded-xl text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500" />
                    </div>

                    <!-- Perhitungan Selisih Kas -->
                    <div class="p-3 bg-white rounded-xl border border-amber-200 space-y-1">
                        <div class="flex justify-between text-slate-500">
                            <span>Ekspektasi Kas Laci (Sistem):</span>
                            <span class="font-semibold">Rp{{ number_format($expectedCashDrawer, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Uang Fisik Kasir (Aktual):</span>
                            <span class="font-semibold">Rp{{ number_format($shiftActualCash ?: 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="border-t border-slate-100 pt-1 flex justify-between font-bold text-xs">
                            <span>Selisih Kas (*Difference*):</span>
                            @php
                                $diff = ($shiftActualCash ?: 0) - $expectedCashDrawer;
                            @endphp
                            @if ($diff === 0.0 || $diff == 0)
                                <span class="text-emerald-600">Pas (Rp 0)</span>
                            @elseif ($diff > 0)
                                <span class="text-blue-600">+Rp{{ number_format($diff, 0, ',', '.') }} (Lebih)</span>
                            @else
                                <span class="text-rose-600">-Rp{{ number_format(abs($diff), 0, ',', '.') }} (Kurang)</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Catatan Penutupan Shift (Opsional)</label>
                        <input type="text" wire:model="closeShiftNotes" placeholder="Contoh: Seluruh setoran tunai disimpan ke brankas"
                               class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs" />
                    </div>

                    <div class="pt-1">
                        <button type="button" wire:click="closeShift"
                                class="w-full py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-lock text-xs"></i>
                            <span>Konfirmasi Tutup Shift & Cetak Z-Report</span>
                        </button>
                    </div>
                </div>
            @else
                <!-- Petty Cash Entry Form -->
                <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 space-y-2 text-xs">
                    <div class="font-bold text-slate-800 flex items-center justify-between">
                        <span>Pencatatan Kas Masuk / Keluar (Petty Cash)</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <select wire:model="pettyCashType" class="border border-slate-200 rounded-xl px-2.5 py-2 bg-white text-slate-700 font-medium">
                            <option value="out">Kas Keluar (-)</option>
                            <option value="in">Kas Masuk (+)</option>
                        </select>
                        <input type="number" wire:model="pettyCashAmount" placeholder="Nominal Rp" class="border border-slate-200 rounded-xl px-3 py-2 bg-white font-medium" />
                        <input type="text" wire:model="pettyCashReason" placeholder="Keterangan (misal: Es Batu)" class="border border-slate-200 rounded-xl px-3 py-2 bg-white" />
                    </div>
                    <div class="text-right">
                        <button type="button" wire:click="recordPettyCash" class="px-3.5 py-1.5 rounded-xl bg-slate-800 text-white font-semibold text-[11px] hover:bg-slate-900 transition">
                            Simpan Mutasi Kas
                        </button>
                    </div>
                </div>

                <!-- Petty Cash History List -->
                <div class="space-y-1.5 max-h-32 overflow-y-auto text-xs">
                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Riwayat Kas Selama Shift:</div>
                    @forelse ($pettyCashMovements as $pcm)
                        <div class="flex items-center justify-between p-2 rounded-xl bg-white border border-slate-100 text-[11px]">
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full {{ $pcm['type'] === 'out' ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                                <span class="font-medium text-slate-800">{{ $pcm['reason'] }}</span>
                                <span class="text-slate-400 font-normal">({{ $pcm['time'] }})</span>
                            </div>
                            <span class="font-bold {{ $pcm['type'] === 'out' ? 'text-rose-500' : 'text-emerald-600' }}">
                                {{ $pcm['type'] === 'out' ? '-' : '+' }}Rp{{ number_format($pcm['amount'], 0, ',', '.') }}
                            </span>
                        </div>
                    @empty
                        <div class="text-[11px] text-slate-400 italic py-1">Belum ada mutasi kas kecil selama shift berjalan.</div>
                    @endforelse
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-between items-center text-xs">
                    <button wire:click="kickCashDrawer" class="text-slate-600 hover:text-slate-900 font-medium flex items-center gap-1.5">
                        <i class="fa-solid fa-vault text-slate-400"></i>
                        <span>Buka Laci Kas (RJ11)</span>
                    </button>
                    <button wire:click="$set('showCloseShiftModal', true)" class="px-4 py-2 rounded-xl bg-orange-500 text-white font-bold hover:bg-orange-600 transition flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-lock text-xs"></i>
                        <span>Tutup Shift & Rekonsiliasi</span>
                    </button>
                </div>
            @endif
        </div>
    </div>
@endif


