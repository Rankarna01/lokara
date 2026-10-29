<!-- ================= MODAL: VARIANT & NOTE SELECTION ================= -->
@if ($showVariantModal && $selectedProductForVariant)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-sm w-full p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-sm text-slate-900">{{ $selectedProductForVariant->name }}</h3>
                <button wire:click="$set('showVariantModal', false)" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="space-y-3 text-xs">
                <label class="block font-semibold text-slate-700">Pilih Ukuran / Varian:</label>
                <div class="space-y-2">
                    @foreach ($selectedProductForVariant->variants as $var)
                        <label class="flex items-center justify-between p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 {{ $selectedVariantName === $var->name ? 'border-orange-500 bg-orange-50/50' : '' }}">
                            <div class="flex items-center gap-2">
                                <input type="radio" name="variant" wire:click="$set('selectedVariantName', '{{ $var->name }}'); $set('selectedVariantPrice', {{ $var->final_price }})" {{ $selectedVariantName === $var->name ? 'checked' : '' }} class="text-orange-500 focus:ring-orange-500" />
                                <span class="font-medium text-slate-800">{{ $var->name }}</span>
                            </div>
                            <span class="font-bold text-slate-900">Rp{{ number_format($var->final_price, 0, ',', '.') }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="pt-2">
                    <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan (Add Note):</label>
                    <input type="text" wire:model="itemNotes" placeholder="Contoh: Tanpa Gula, Extra Panas" class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500" />
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex gap-2">
                <button type="button" wire:click="$set('showVariantModal', false)" class="w-1/3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600">Batal</button>
                <button type="button" wire:click="confirmVariantSelection" class="w-2/3 py-2 rounded-xl bg-orange-500 text-white text-xs font-bold hover:bg-orange-600 transition">Tambahkan</button>
            </div>
        </div>
    </div>
@endif

