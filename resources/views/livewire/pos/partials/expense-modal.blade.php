<!-- ================= MODAL: CATAT PENGELUARAN TOKO (KASIR) ================= -->
@if ($showPosExpenseModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Catat Pengeluaran Toko</h3>
                    <p class="text-[11px] text-slate-400">Biaya operasional yang dibayar dari kas toko</p>
                </div>
                <button wire:click="$set('showPosExpenseModal', false)" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block font-semibold text-slate-700">Kategori Biaya</label>
                        <button type="button" wire:click="$toggle('showPosNewExpenseCategory')"
                            class="text-[11px] font-semibold text-orange-600 hover:text-orange-700">
                            {{ $showPosNewExpenseCategory ? 'Batal' : '+ Tambah kategori baru' }}
                        </button>
                    </div>

                    @if ($showPosNewExpenseCategory)
                        <div class="flex items-center gap-2 mb-2">
                            <input type="text" wire:model="posExpenseNewCategory" wire:keydown.enter="addPosExpenseCategory" placeholder="Contoh: Perlengkapan Kebersihan"
                                class="flex-1 px-3 py-2 border border-orange-300 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500" />
                            <button type="button" wire:click="addPosExpenseCategory"
                                class="px-3 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold">Tambah</button>
                        </div>
                        @error('posExpenseNewCategory') <div class="text-rose-600 text-[11px] mb-2">{{ $message }}</div> @enderror
                    @endif

                    <select wire:model="posExpenseCategory" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                        @foreach ($posExpenseCategoryOptions as $categoryOption)
                            <option value="{{ $categoryOption }}">{{ $categoryOption }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Pengeluaran</label>
                    <input type="date" wire:model="posExpenseDate" max="{{ now()->toDateString() }}"
                        class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500" />
                    @error('posExpenseDate') <div class="text-rose-600 text-[11px] mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Deskripsi Pengeluaran</label>
                    <input type="text" wire:model="posExpenseTitle" placeholder="Contoh: Beli galon air"
                        class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500" />
                    @error('posExpenseTitle') <div class="text-rose-600 text-[11px] mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nominal (Rp)</label>
                    <input type="number" wire:model="posExpenseAmount"
                        class="w-full px-3 py-2 border border-slate-200 rounded-xl font-bold focus:outline-none focus:ring-1 focus:ring-orange-500" />
                    @error('posExpenseAmount') <div class="text-rose-600 text-[11px] mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Sumber Kas</label>
                    <select wire:model="posExpensePaymentMethod" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                        <option value="Kas Toko / Tunai">Kas Toko / Tunai</option>
                        <option value="Transfer Bank">Transfer Bank Rekening</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" wire:click="$set('showPosExpenseModal', false)"
                    class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                <button type="button" wire:click="savePosExpense" wire:loading.attr="disabled"
                    class="px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 disabled:opacity-60 text-white font-bold text-xs">
                    Simpan Pengeluaran
                </button>
            </div>
        </div>
    </div>
@endif
