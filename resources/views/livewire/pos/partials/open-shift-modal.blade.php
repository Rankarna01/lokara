<!-- ================= MODAL: BUKA SHIFT KASIR BARU ================= -->
@if ($showOpenShiftModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-in fade-in duration-150">
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xl max-w-md w-full overflow-hidden">
            <!-- Header (Clean, High-Contrast & Consistent with Lokara Design System) -->
            <div class="p-6 border-b border-slate-100 flex items-start justify-between bg-white relative">
                <div class="flex items-start gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-xl shadow-xs shrink-0">
                        <i class="fa-solid fa-cash-register"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-orange-600 bg-orange-50 border border-orange-200/80 px-2 py-0.5 rounded-md">Shift Kasir Baru</span>
                            <span class="text-[11px] text-slate-400 font-medium">POS Terminal</span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 leading-tight mt-1.5">Buka Kasir & Laci Kas</h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Masukkan modal kas awal di mesin kasir (*starting cash*) sebelum transaksi penjualan shift ini dimulai.
                        </p>
                    </div>
                </div>
                <button type="button" wire:click="$set('showOpenShiftModal', false)"
                        class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition shrink-0 ml-2"
                        title="Tutup Modal">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4 text-xs">
                <!-- Info Kasir & Outlet -->
                <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-medium">Kasir Bertugas:</span>
                        <span class="font-bold text-slate-800">{{ auth()->user()->name ?? 'Kasir Default' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-medium">Cabang Outlet:</span>
                        <span class="font-bold text-slate-800">{{ $currentOutlet->name ?? 'Outlet Utama' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-medium">Waktu Buka Shift:</span>
                        <span class="font-semibold text-slate-700">{{ $openShiftTime ?: now()->format('d/m/Y - H:i') . ' WIB' }}</span>
                    </div>
                </div>

                <!-- Input Modal Kas Awal -->
                <div class="space-y-2">
                    <label class="block font-bold text-slate-800">
                        Modal Kas Awal di Laci (Starting Cash)
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-slate-400 text-sm">Rp</span>
                        <input type="number"
                               wire:model="shiftStartingCash"
                               step="1000"
                               placeholder="200000"
                               class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-base font-extrabold text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition" />
                    </div>
                    @error('shiftStartingCash') <span class="text-rose-500 text-[11px] font-semibold">{{ $message }}</span> @enderror

                    <!-- Quick Cash Chips -->
                    <div class="grid grid-cols-4 gap-1.5 pt-1">
                        <button type="button" wire:click="$set('shiftStartingCash', 100000)"
                                class="py-1.5 px-2 bg-slate-100 hover:bg-orange-50 hover:text-orange-600 rounded-xl font-bold text-[11px] text-slate-700 transition">
                            100 Rb
                        </button>
                        <button type="button" wire:click="$set('shiftStartingCash', 200000)"
                                class="py-1.5 px-2 bg-slate-100 hover:bg-orange-50 hover:text-orange-600 rounded-xl font-bold text-[11px] text-slate-700 transition">
                            200 Rb
                        </button>
                        <button type="button" wire:click="$set('shiftStartingCash', 500000)"
                                class="py-1.5 px-2 bg-slate-100 hover:bg-orange-50 hover:text-orange-600 rounded-xl font-bold text-[11px] text-slate-700 transition">
                            500 Rb
                        </button>
                        <button type="button" wire:click="$set('shiftStartingCash', 1000000)"
                                class="py-1.5 px-2 bg-slate-100 hover:bg-orange-50 hover:text-orange-600 rounded-xl font-bold text-[11px] text-slate-700 transition">
                            1 Jt
                        </button>
                    </div>
                </div>

                <!-- Catatan Buka Shift -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Catatan Kasir (Opsional)</label>
                    <input type="text"
                           wire:model="openShiftNotes"
                           placeholder="Contoh: Pecahan Rp 5.000 & Rp 10.000 disiapkan"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white" />
                </div>
            </div>

            <!-- Footer Action -->
            <div class="p-5 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between gap-3">
                <button type="button" wire:click="$set('showOpenShiftModal', false)"
                        class="px-4 py-2.5 rounded-xl font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-200/70 transition text-xs">
                    Nanti Saja
                </button>
                <button type="button" wire:click="startShift"
                        class="flex-1 py-3 px-5 rounded-2xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs shadow-md shadow-orange-500/20 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-lock-open text-xs"></i>
                    <span>Mulai Shift & Buka Laci Kas</span>
                </button>
            </div>
        </div>
    </div>
@endif
