    <!-- ================= MODAL TAMBAH PAKET BARU ================= -->
    @if ($showCreatePlanModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto"
             x-data
             @keydown.escape.window="$wire.closeCreatePlanModal()"
             @click.self="$wire.closeCreatePlanModal()">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-2xl w-full my-8 animate-scale-up flex flex-col max-h-[90vh]"
                 @click.outside="$wire.closeCreatePlanModal()">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 shrink-0 bg-slate-50/50 rounded-t-2xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-base shadow-sm">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Tambah Paket Baru</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Daftarkan paket langganan SaaS baru untuk platform Lokara</p>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="closeCreatePlanModal"
                            @click.prevent="$wire.set('showCreatePlanModal', false)"
                            class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="overflow-y-auto px-6 py-5 space-y-5 flex-1 text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Nama Paket <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <input type="text"
                                   wire:model="newPlanName"
                                   placeholder="Contoh: Enterprise Plus"
                                   class="w-full border rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none @error('newPlanName') border-rose-400 ring-1 ring-rose-400 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                            @error('newPlanName')
                                <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Badge Label</label>
                            <input type="text"
                                   wire:model="newPlanBadge"
                                   placeholder="Contoh: Khusus, Custom"
                                   class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-orange-50/40 p-4 rounded-xl border border-orange-100">
                        <div>
                            <label class="block font-semibold text-slate-800 mb-1">
                                Tarif Bulanan (Rp) <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <input type="number"
                                   step="1000"
                                   wire:model="newPlanPriceMonthly"
                                   class="w-full px-3.5 py-2 border rounded-xl text-xs font-bold text-slate-900 bg-white focus:outline-none focus:ring-1 focus:ring-orange-500" />
                            @error('newPlanPriceMonthly')
                                <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-800 mb-1">
                                Tarif Tahunan (Rp) <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <input type="number"
                                   step="1000"
                                   wire:model="newPlanPriceYearly"
                                   class="w-full px-3.5 py-2 border rounded-xl text-xs font-bold text-slate-900 bg-white focus:outline-none focus:ring-1 focus:ring-orange-500" />
                            @error('newPlanPriceYearly')
                                <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Max Outlet</label>
                            <input type="number" min="1" wire:model="newPlanMaxOutlets" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs" />
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Max Karyawan</label>
                            <input type="number" min="1" wire:model="newPlanMaxUsers" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs" />
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Max Produk</label>
                            <input type="number" min="1" wire:model="newPlanMaxProducts" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block font-semibold text-slate-700">Checklist Fitur Awal</label>
                        <div class="max-h-48 overflow-y-auto border border-slate-200 rounded-xl p-3 grid grid-cols-1 sm:grid-cols-2 gap-2 bg-white">
                            @foreach ($allPlatformFeatures as $f)
                                <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                    <input type="checkbox"
                                           wire:click="toggleFeatureInNewPlan('{{ $f->name }}')"
                                           {{ in_array($f->name, $newPlanFeatures) ? 'checked' : '' }}
                                           class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                    <span class="text-slate-800">{{ $f->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 rounded-b-2xl flex items-center justify-end gap-3 shrink-0">
                    <button type="button"
                            wire:click="closeCreatePlanModal"
                            @click.prevent="$wire.set('showCreatePlanModal', false)"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-semibold text-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="button"
                            wire:click="createPlan"
                            class="px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs shadow-sm cursor-pointer">
                        Buat Paket Sekarang
                    </button>
                </div>
            </div>
        </div>
    @endif
