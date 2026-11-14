    <!-- 5. Modal Catat Pengeluaran OPEX -->
    @if ($showExpenseModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-base">
                        {{ $editingExpenseId ? 'Edit Beban Operasional (OPEX)' : 'Catat Beban Operasional (OPEX)' }}
                    </h3>
                    <button wire:click="$set('showExpenseModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="p-5 space-y-4 text-sm">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-slate-700">Kategori Biaya</label>
                            <button type="button" wire:click="$toggle('showNewExpenseCategory')"
                                class="text-[11px] font-semibold text-orange-600 hover:text-orange-700">
                                {{ $showNewExpenseCategory ? 'Batal' : '+ Tambah kategori baru' }}
                            </button>
                        </div>

                        @if ($showNewExpenseCategory)
                            <div class="flex items-center gap-2 mb-2">
                                <input type="text" wire:model="expenseNewCategory" wire:keydown.enter="addExpenseCategory" placeholder="Contoh: Perlengkapan Kebersihan"
                                    class="flex-1 bg-white border border-orange-300 rounded-xl px-3 py-2 text-sm focus:ring-1 focus:ring-orange-500">
                                <button type="button" wire:click="addExpenseCategory"
                                    class="px-3 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl">Tambah</button>
                            </div>
                            @error('expenseNewCategory') <div class="text-rose-600 text-[11px] mb-2">{{ $message }}</div> @enderror
                        @endif

                        <select wire:model="expenseCategory"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-1 focus:ring-orange-500">
                            @foreach ($expenseCategoryOptions as $categoryOption)
                                <option value="{{ $categoryOption }}">{{ $categoryOption }}</option>
                            @endforeach
                        </select>
                        @error('expenseCategory') <div class="text-rose-600 text-[11px] mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Pengeluaran</label>
                        <input type="date" wire:model="expenseDate" max="{{ now()->toDateString() }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-1 focus:ring-orange-500">
                        @error('expenseDate') <div class="text-rose-600 text-[11px] mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Judul / Deskripsi Pengeluaran</label>
                        <input type="text" wire:model="expenseTitle" placeholder="Contoh: Beli Token Listrik PLN"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-1 focus:ring-orange-500">
                        @error('expenseTitle') <div class="text-rose-600 text-[11px] mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal (Rp)</label>
                        <input type="number" wire:model="expenseAmount"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-1 focus:ring-orange-500 font-bold">
                        @error('expenseAmount') <div class="text-rose-600 text-[11px] mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Sumber Kas</label>
                        <select wire:model="expensePaymentMethod"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-1 focus:ring-orange-500">
                            <option value="Kas Toko / Tunai">Kas Toko / Tunai</option>
                            <option value="Transfer Bank">Transfer Bank Rekening</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                        <input type="text" wire:model="expenseNotes" placeholder="Catatan tambahan atau nomor bukti..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-1 focus:ring-orange-500">
                        @error('expenseNotes') <div class="text-rose-600 text-[11px] mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button wire:click="$set('showExpenseModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Batal</button>
                    <button wire:click="saveExpense"
                        class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm">
                        {{ $editingExpenseId ? 'Simpan Perubahan' : 'Simpan Beban' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
