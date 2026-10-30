    <!-- ================= MODAL UBAH PRESET TEMPLATE USAHA ================= -->
    @if ($showEditPresetModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-2xl w-full my-8 animate-scale-up flex flex-col max-h-[90vh]">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 shrink-0 bg-slate-50/50 rounded-t-2xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-base shadow-sm">
                            <i class="fa-solid fa-sliders"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Ubah Preset Template Usaha</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Template: <span class="font-semibold text-orange-600">{{ $editBtName }}</span> • Atur modul bawaan & konfigurasi tenant</p>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="closeEditPresetModal"
                            class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <!-- Alert Validation Error -->
                @if ($errors->any())
                    <div class="mx-6 mt-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm mt-0.5 shrink-0"></i>
                        <div>
                            <div class="font-bold text-rose-900">Periksa Kembali Data Formulir</div>
                            <div class="text-[11px] text-rose-600 mt-0.5">Terdapat kolom wajib yang belum valid.</div>
                        </div>
                    </div>
                @endif

                <!-- Body (Scrollable) -->
                <div class="overflow-y-auto px-6 py-5 space-y-6 flex-1 text-xs text-slate-700">
                    <!-- SECTION 1: INFORMASI UTAMA TEMPLATE -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                            <i class="fa-solid fa-store text-orange-500 text-xs"></i>
                            <span class="font-bold text-slate-900 text-xs uppercase tracking-wider">Identitas Template</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                            <div class="sm:col-span-8">
                                <label class="block font-semibold text-slate-700 mb-1">
                                    Nama Template Usaha <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <input type="text"
                                       wire:model="editBtName"
                                       class="w-full border rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none @error('editBtName') border-rose-400 ring-1 ring-rose-400 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                                @error('editBtName')
                                    <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="sm:col-span-4">
                                <label class="block font-semibold text-slate-700 mb-1">Ikon FontAwesome</label>
                                <div class="flex items-center gap-2">
                                    <div class="w-9 h-9 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-sm shrink-0 border border-orange-100">
                                        <i class="fa-solid {{ $editBtIcon }}"></i>
                                    </div>
                                    <select wire:model.live="editBtIcon"
                                            class="w-full border border-slate-200 rounded-xl px-2.5 py-2 text-xs text-slate-800 bg-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                        <option value="fa-utensils">F&B / Resto (fa-utensils)</option>
                                        <option value="fa-store">Retail / Toko (fa-store)</option>
                                        <option value="fa-scissors">Salon / Barbershop (fa-scissors)</option>
                                        <option value="fa-shirt">Laundry / Jasa (fa-shirt)</option>
                                        <option value="fa-boxes-stacked">Grosir / Distributor (fa-boxes-stacked)</option>
                                        <option value="fa-mug-hot">Kafe / Coffee Shop (fa-mug-hot)</option>
                                        <option value="fa-cart-shopping">Minimarket (fa-cart-shopping)</option>
                                        <option value="fa-spa">Spa & Beauty (fa-spa)</option>
                                        <option value="fa-car-side">Bengkel / Otomotif (fa-car-side)</option>
                                        <option value="fa-paw">Pet Shop (fa-paw)</option>
                                        <option value="fa-pills">Apotek / Farmasi (fa-pills)</option>
                                        <option value="fa-bowl-food">Kuliner / Fast Food (fa-bowl-food)</option>
                                        <option value="fa-shapes">Lainnya (fa-shapes)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Deskripsi Template <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <textarea wire:model="editBtDescription"
                                      rows="2"
                                      class="w-full border rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none @error('editBtDescription') border-rose-400 ring-1 ring-rose-400 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror"></textarea>
                            @error('editBtDescription')
                                <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox"
                                       wire:model="editBtIsActive"
                                       class="w-4 h-4 text-orange-500 rounded border-slate-300 focus:ring-orange-400 cursor-pointer">
                                <span class="text-xs font-semibold text-slate-800">Status Template Aktif & Tersedia untuk Registrasi Tenant</span>
                            </label>
                        </div>
                    </div>

                    <!-- SECTION 2: PRESET MODUL AKTIF (LIST & TAMBAH/HAPUS) -->
                    <div class="space-y-3 pt-3 border-t border-slate-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-layer-group text-orange-500 text-xs"></i>
                                <span class="font-bold text-slate-900 text-xs uppercase tracking-wider">Preset Modul Bawaan</span>
                                <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 font-bold text-[11px]">
                                    {{ count($editBtPresetModules) }} aktif
                                </span>
                            </div>
                        </div>

                        <!-- Bar Tambah Modul Preset Baru -->
                        <div class="flex items-center gap-2 p-2.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                            <input type="text"
                                   wire:model="newPresetModuleName"
                                   wire:keydown.enter.prevent="addPresetModule"
                                   placeholder="+ Ketik modul baru (misal: Reservasi Meja, Integrasi KDS)..."
                                   class="flex-1 border border-slate-200 rounded-lg px-3 py-1.5 text-xs text-slate-800 bg-white focus:outline-none focus:ring-1 focus:ring-orange-500" />
                            <button type="button"
                                    wire:click="addPresetModule"
                                    class="px-3.5 py-1.5 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs shadow-sm flex items-center gap-1.5 shrink-0 transition cursor-pointer">
                                <i class="fa-solid fa-plus text-[10px]"></i>
                                <span>Tambah Modul</span>
                            </button>
                        </div>
                        @error('newPresetModuleName')
                            <div class="text-rose-600 font-medium text-[10px]">{{ $message }}</div>
                        @enderror

                        <!-- Daftar Modul yang Sedang Aktif dalam Preset -->
                        <div class="space-y-1.5">
                            <div class="text-[11px] font-semibold text-slate-500">Daftar Modul yang Aktif pada Template Ini:</div>
                            <div class="border border-slate-200 rounded-xl divide-y divide-slate-100 max-h-48 overflow-y-auto bg-white">
                                @forelse ($editBtPresetModules as $mod)
                                    <div class="px-3 py-2 flex items-center justify-between hover:bg-slate-50/60 transition">
                                        <div class="flex items-center gap-2 text-xs font-semibold text-slate-800">
                                            <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
                                            <span>{{ $mod }}</span>
                                        </div>
                                        <button type="button"
                                                wire:click="removePresetModule('{{ $mod }}')"
                                                title="Hapus dari preset"
                                                class="text-slate-400 hover:text-rose-600 p-1 transition cursor-pointer">
                                            <i class="fa-solid fa-xmark text-xs"></i>
                                        </button>
                                    </div>
                                @empty
                                    <div class="p-4 text-center text-slate-400 italic text-xs">
                                        Belum ada modul aktif pada preset ini. Klik pilihan modul di bawah untuk menambahkan.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Pilihan Cepat Modul Populer -->
                        <div class="p-3 bg-orange-50/40 border border-orange-100 rounded-xl space-y-2">
                            <div class="text-[11px] font-bold text-slate-700 flex items-center gap-1.5">
                                <i class="fa-solid fa-lightbulb text-orange-500"></i>
                                <span>Pilihan Cepat Modul Standar (Klik untuk Aktifkan / Nonaktifkan):</span>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                @php
                                    $presetChoices = [
                                        'Resep & Bill of Materials (BOM) Gram/ml',
                                        'Manajemen Meja & Cetak Dapur/Bar',
                                        'Peringatan Bahan Kedaluwarsa',
                                        'Barcode Scanning Cepat (EAN-13)',
                                        'Varian Ukuran & Warna',
                                        'Multi-Gudang & Grosir',
                                        'Komisi Pegawai / Kapster',
                                        'Layanan Jasa & Perawatan',
                                        'Layanan Kiloan & Satuan',
                                        'Pelacakan Status Cuci-Kering-Setrika',
                                        'Harga Bertingkat Grosir',
                                        'Piutang Penjualan Tempo',
                                        'Multi-Modifier & Topping Tambahan',
                                        'Cetak Struk Dapur / KDS',
                                        'Reservasi & Jadwal Booking',
                                    ];
                                @endphp
                                @foreach ($presetChoices as $choice)
                                    @php $activeInPreset = in_array($choice, $editBtPresetModules); @endphp
                                    <button type="button"
                                            wire:click="togglePresetModule('{{ $choice }}')"
                                            class="px-2.5 py-1 rounded-lg text-[11px] font-semibold border transition cursor-pointer flex items-center gap-1.5 {{ $activeInPreset ? 'bg-orange-500 text-white border-orange-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:border-orange-300 hover:text-slate-900' }}">
                                        <i class="fa-solid {{ $activeInPreset ? 'fa-check' : 'fa-plus' }} text-[9px]"></i>
                                        <span>{{ $choice }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: SATUAN STANDAR (DEFAULT UNITS) -->
                    <div class="space-y-2 pt-3 border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-scale-balanced text-orange-500 text-xs"></i>
                            <span class="font-bold text-slate-900 text-xs uppercase tracking-wider">Satuan Standar Produk (Default Units)</span>
                        </div>
                        <p class="text-[11px] text-slate-500">Satuan dasar yang otomatis tersedia saat tenant dengan template ini membuat produk baru.</p>
                        <input type="text"
                               wire:model="editBtDefaultUnits"
                               placeholder="Contoh: gram, ml, cup, porsi, pcs"
                               class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500 font-mono" />
                        <div class="text-[10px] text-slate-400">Pisahkan dengan tanda koma (contoh: <code>pcs, pack, dus, lusin</code>).</div>
                    </div>

                    <!-- SECTION 4: TOGGLE FITUR SISTEM SPESIFIK -->
                    <div class="space-y-2.5 pt-3 border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-toggle-on text-orange-500 text-xs"></i>
                            <span class="font-bold text-slate-900 text-xs uppercase tracking-wider">Flag Fitur Sistem</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 bg-slate-50/70 p-3.5 rounded-xl border border-slate-200/70">
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                <input type="checkbox" wire:model="editBtHasRecipes" class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                <span>Modul Resep & Bahan Baku (BOM)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                <input type="checkbox" wire:model="editBtHasTables" class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                <span>Manajemen Meja & Dine-In</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                <input type="checkbox" wire:model="editBtHasModifiers" class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                <span>Modifier & Opsi Topping</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                <input type="checkbox" wire:model="editBtHasBarcode" class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                <span>Barcode Scanner Cepat</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                <input type="checkbox" wire:model="editBtHasVariants" class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                <span>Varian Ukuran / Warna</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                <input type="checkbox" wire:model="editBtHasCommissions" class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                <span>Komisi Pegawai / Kapster</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                <input type="checkbox" wire:model="editBtHasOrderStatus" class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                <span>Pelacakan Status Pesanan / Cuci</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                <input type="checkbox" wire:model="editBtHasCreditSales" class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                <span>Penjualan Tempo & Piutang</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                <input type="checkbox" wire:model="editBtHasTieredPricing" class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                <span>Harga Bertingkat Grosir</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs">
                                <input type="checkbox" wire:model="editBtHasServices" class="w-4 h-4 text-orange-500 rounded border-slate-300">
                                <span>Layanan Jasa & Perawatan</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 rounded-b-2xl flex items-center justify-end gap-3 shrink-0">
                    <button type="button"
                            wire:click="closeEditPresetModal"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-semibold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button"
                            wire:click="saveBusinessTypePreset"
                            class="px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs shadow-sm shadow-orange-500/20 transition flex items-center gap-2 cursor-pointer">
                        <span wire:loading.remove wire:target="saveBusinessTypePreset">Simpan Perubahan Preset</span>
                        <span wire:loading wire:target="saveBusinessTypePreset" class="flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-notch fa-spin"></i>
                            <span>Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
