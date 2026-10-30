    <!-- 7. Modal Tambah / Edit Template Shift -->
    @if ($showShiftTemplateModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">{{ $editingShiftTemplateId ? 'Edit Template Shift' : 'Tambah Template Shift Baru' }}</h3>
                    </div>
                    <button wire:click="$set('showShiftTemplateModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Shift</label>
                        <input type="text" wire:model="shiftTemplateName" placeholder="Contoh: Shift 1 (Pagi)"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500">
                        @error('shiftTemplateName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Jam Mulai</label>
                            <input type="time" wire:model="shiftTemplateStartTime"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500">
                            @error('shiftTemplateStartTime') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Jam Selesai</label>
                            <input type="time" wire:model="shiftTemplateEndTime"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500">
                            @error('shiftTemplateEndTime') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Rekomendasi Modal Kas Awal (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold">Rp</span>
                            <input type="number" wire:model="shiftTemplateDefaultCash" step="1000" placeholder="200000"
                                class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        @error('shiftTemplateDefaultCash') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                            <input type="checkbox" wire:model="shiftTemplateIsActive" class="rounded text-orange-500 focus:ring-orange-500">
                            <span>Template shift aktif dan dapat dipilih di kasir</span>
                        </label>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" wire:click="$set('showShiftTemplateModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Batal</button>
                    <button type="button" wire:click="saveShiftTemplate"
                        class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i>
                        <span>{{ $editingShiftTemplateId ? 'Simpan Perubahan' : 'Buat Template Shift' }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
