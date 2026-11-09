    <!-- 10. Modal Tambah / Edit Layanan Salon & Barbershop -->
    @if ($showServiceModal)
        <div class="fixed inset-0 z-[60] bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">
                            {{ $editingServiceId ? 'Edit Layanan Jasa' : 'Tambah Layanan Jasa Baru' }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Konfigurasi tarif layanan, durasi pengerjaan, dan foto layanan</p>
                    </div>
                    <button wire:click="$set('showServiceModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto text-sm">
                    <!-- Nama Layanan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Layanan Jasa <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="serviceName" placeholder="Contoh: Gentleman Haircut, Creambath Spa, Beard Trim"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        @error('serviceName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Kategori Layanan -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-slate-700">Kategori Layanan <span class="text-rose-500">*</span></label>
                            <button type="button" wire:click="openNewCategoryModal"
                                class="text-[11px] font-semibold text-orange-600 hover:text-orange-700 hover:underline flex items-center gap-1">
                                <i class="fa-solid fa-plus text-[9px]"></i> Kategori Baru
                            </button>
                        </div>
                        <select wire:model="serviceCategoryId"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tarif Jasa & Durasi -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tarif Layanan (Rp) <span class="text-rose-500">*</span></label>
                            <input type="number" wire:model="servicePrice"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm font-bold text-slate-900 focus:outline-none focus:ring-1 focus:ring-orange-500">
                            @error('servicePrice') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Estimasi Durasi (Menit) <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input type="number" wire:model="serviceDuration" min="5" step="5" placeholder="30"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-3.5 pr-12 py-2 text-sm font-semibold focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-medium">Menit</span>
                            </div>
                            @error('serviceDuration') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Modal Bahan / HPP Produk (disembunyikan untuk Salon & Klinik Kecantikan) -->
                    @unless ($tenant?->businessType?->slug === 'salon')
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Modal Bahan Produk (HPP per Sesi)</label>
                            <input type="number" wire:model="serviceCostPrice" placeholder="e.g. 5000 (shampoo, pomade, cream)"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <span class="text-[10px] text-slate-400">Pemakaian shampoo, pomade, obat cream, handuk, dll</span>
                        </div>
                    @endunless

                    <!-- Fee Kapster (Komisi per Sesi) -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Fee Kapster</label>
                            <select wire:model.live="serviceCommissionType"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <option value="fixed">Nominal tetap (Rp)</option>
                                <option value="percentage">Persentase dari tarif (%)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ $serviceCommissionType === 'percentage' ? 'Fee (%)' : 'Fee (Rp)' }}
                            </label>
                            <input type="number" wire:model="serviceCommissionValue" min="0" step="any"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm font-semibold focus:outline-none focus:ring-1 focus:ring-orange-500">
                            @error('serviceCommissionValue') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <span class="-mt-3 block text-[10px] text-slate-400">Fee dibayarkan ke kapster/terapis dari setiap sesi layanan ini.</span>

                    <!-- Upload Foto Layanan -->
                    <div class="border border-slate-200 rounded-2xl p-3.5 bg-slate-50/70 space-y-3" x-data="{ showUrlInput: {{ !empty($serviceImageUrl) ? 'true' : 'false' }} }">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-semibold text-slate-800">
                                <i class="fa-solid fa-camera text-orange-500 mr-1"></i> Foto Layanan Jasa
                            </label>
                            <button type="button" @click="showUrlInput = !showUrlInput"
                                class="text-[11px] font-medium text-slate-500 hover:text-orange-600 underline transition">
                                <span x-text="showUrlInput ? 'Sembunyikan Opsi URL' : 'Gunakan URL Foto Web'"></span>
                            </button>
                        </div>

                        <div class="flex items-center gap-3.5">
                            <!-- Image Preview Thumbnail -->
                            <div class="relative shrink-0 w-20 h-20 rounded-xl overflow-hidden bg-white border border-slate-200 shadow-xs flex items-center justify-center">
                                @if ($serviceImageFile)
                                    <img src="{{ $serviceImageFile->temporaryUrl() }}" class="w-full h-full object-cover">
                                    <span class="absolute bottom-0 inset-x-0 bg-emerald-600/95 text-white text-[9px] font-bold text-center py-0.5">Preview Baru</span>
                                @elseif ($serviceImageUrl)
                                    <img src="{{ $serviceImageUrl }}" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1503951914875-452162b0f3f1?auto=format&fit=crop&w=150&h=150&q=80'">
                                    <span class="absolute bottom-0 inset-x-0 bg-slate-800/80 text-white text-[9px] font-semibold text-center py-0.5">Tersimpan</span>
                                @else
                                    <div class="text-center p-2 text-slate-400">
                                        <i class="fa-regular fa-image text-2xl text-slate-300"></i>
                                        <div class="text-[9px] mt-0.5 font-medium text-slate-400">Tanpa Foto</div>
                                    </div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="flex-1 min-w-0 space-y-1.5">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-semibold cursor-pointer transition shadow-xs">
                                        <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                                        <span>Pilih / Unggah Gambar</span>
                                        <input type="file" wire:model="serviceImageFile" accept="image/png,image/jpeg,image/webp,image/jpg" class="hidden">
                                    </label>

                                    @if ($serviceImageFile || $serviceImageUrl)
                                        <button type="button" wire:click="removeServiceImage"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-600 rounded-xl text-xs font-semibold transition">
                                            <i class="fa-solid fa-trash-can text-[11px]"></i>
                                            <span>Hapus</span>
                                        </button>
                                    @endif
                                </div>

                                <div wire:loading wire:target="serviceImageFile" class="text-xs text-orange-600 font-semibold flex items-center gap-1.5">
                                    <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                                    <span>Mengunggah file foto...</span>
                                </div>

                                <p class="text-[10px] text-slate-400 leading-tight">
                                    Format: JPG, PNG, atau WEBP. Ukuran maksimal 3MB.
                                </p>

                                @error('serviceImageFile')
                                    <span class="text-[11px] text-rose-500 font-medium block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- URL Input Option -->
                        <div x-show="showUrlInput" class="pt-2.5 border-t border-slate-200/80 space-y-1">
                            <label class="block text-[11px] font-semibold text-slate-600">URL Gambar Eksternal (Opsional):</label>
                            <input type="text" wire:model="serviceImageUrl" placeholder="https://images.unsplash.com/..."
                                class="w-full bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                            <input type="checkbox" wire:model="serviceIsAvailable" class="rounded text-orange-500 focus:ring-orange-500">
                            <span>Layanan ini tersedia & aktif di POS Kasir</span>
                        </label>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2">
                    @if ($editingServiceId)
                        <button type="button"
                            wire:click="deleteService({{ $editingServiceId }})"
                            wire:confirm="Yakin ingin menghapus layanan ini?"
                            class="px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition flex items-center gap-1.5">
                            <i class="fa-solid fa-trash-can"></i>
                            <span>Hapus</span>
                        </button>
                    @else
                        <div></div>
                    @endif

                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="$set('showServiceModal', false)"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Batal</button>
                        <button type="button" wire:click="saveService"
                            class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm flex items-center gap-1.5">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ $editingServiceId ? 'Simpan Perubahan' : 'Tambah Layanan' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

