    <!-- ================= MODAL TAMBAH TEMPLATE USAHA BARU ================= -->
    @if ($showCreateBusinessTypeModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-xl w-full my-8 animate-scale-up flex flex-col max-h-[90vh]">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 shrink-0 bg-slate-50/50 rounded-t-2xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-base shadow-sm">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Tambah Template Usaha Baru</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Daftarkan kategori jenis usaha baru untuk onboarding tenant</p>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="closeCreateBusinessTypeModal"
                            class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="overflow-y-auto px-6 py-5 space-y-4 flex-1 text-xs text-slate-700">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-8">
                            <label class="block font-semibold text-slate-700 mb-1">
                                Nama Jenis Usaha <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <input type="text"
                                   wire:model="newBtName"
                                   placeholder="Contoh: Pet Shop & Klinik Hewan"
                                   class="w-full border rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none @error('newBtName') border-rose-400 ring-1 ring-rose-400 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                            @error('newBtName')
                                <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="sm:col-span-4">
                            <label class="block font-semibold text-slate-700 mb-1">Ikon</label>
                            <select wire:model="newBtIcon"
                                    class="w-full border border-slate-200 rounded-xl px-2.5 py-2 text-xs text-slate-800 bg-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <option value="fa-store">Toko (fa-store)</option>
                                <option value="fa-utensils">Resto / F&B (fa-utensils)</option>
                                <option value="fa-scissors">Salon (fa-scissors)</option>
                                <option value="fa-shirt">Laundry (fa-shirt)</option>
                                <option value="fa-boxes-stacked">Grosir (fa-boxes-stacked)</option>
                                <option value="fa-paw">Pet Shop (fa-paw)</option>
                                <option value="fa-pills">Apotek (fa-pills)</option>
                                <option value="fa-car-side">Bengkel (fa-car-side)</option>
                                <option value="fa-spa">Spa (fa-spa)</option>
                                <option value="fa-shapes">Lainnya (fa-shapes)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">
                            Deskripsi Singkat <span class="text-rose-500 font-bold">*</span>
                        </label>
                        <textarea wire:model="newBtDescription"
                                  rows="2"
                                  placeholder="Jelaskan kebutuhan dan cakupan bisnis ini..."
                                  class="w-full border rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none @error('newBtDescription') border-rose-400 ring-1 ring-rose-400 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror"></textarea>
                        @error('newBtDescription')
                            <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Satuan Standar Produk (Default Units)</label>
                        <input type="text"
                               wire:model="newBtDefaultUnits"
                               placeholder="pcs, pack, dus"
                               class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-800 font-mono focus:outline-none focus:ring-1 focus:ring-orange-500" />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Preset Modul Awal</label>
                        <div class="border border-slate-200 rounded-xl p-3 max-h-40 overflow-y-auto space-y-1.5 bg-slate-50/50">
                            @php
                                $commonMods = [
                                    'Kasir Cepat & Struk WhatsApp',
                                    'Manajemen Stok Standar',
                                    'Multi-Metode Bayar',
                                    'Laporan Penjualan Harian',
                                    'Barcode Scanner',
                                    'Resep & Bahan Baku (BOM)',
                                    'Manajemen Meja Dine-In',
                                    'Komisi Pegawai',
                                    'Pelacakan Status Pesanan',
                                ];
                            @endphp
                            @foreach ($commonMods as $cMod)
                                <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                    <input type="checkbox"
                                           wire:click="togglePresetModuleInNew('{{ $cMod }}')"
                                           {{ in_array($cMod, $newBtPresetModules) ? 'checked' : '' }}
                                           class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                    <span class="text-slate-800">{{ $cMod }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 rounded-b-2xl flex items-center justify-end gap-3 shrink-0">
                    <button type="button"
                            wire:click="closeCreateBusinessTypeModal"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-semibold text-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="button"
                            wire:click="createBusinessType"
                            class="px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs shadow-sm cursor-pointer">
                        Buat Template Sekarang
                    </button>
                </div>
            </div>
        </div>
    @endif
