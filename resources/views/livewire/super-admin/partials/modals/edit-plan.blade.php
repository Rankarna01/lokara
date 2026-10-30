    <!-- ================= MODAL UBAH TARIF DINAMIS & CHECKLIST FITUR ================= -->
    @if ($showEditPlanModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto"
             x-data
             @keydown.escape.window="$wire.closeEditPlanModal()"
             @click.self="$wire.closeEditPlanModal()">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-2xl w-full my-8 animate-scale-up flex flex-col max-h-[90vh]"
                 @click.outside="$wire.closeEditPlanModal()">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 shrink-0 bg-slate-50/50 rounded-t-2xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-base shadow-sm">
                            <i class="fa-solid fa-credit-card"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Ubah Tarif & Checklist Fitur</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Paket: <span class="font-semibold text-orange-600">{{ $editPlanName }}</span> • Sesuaikan harga dan checklist fitur</p>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="closeEditPlanModal"
                            @click.prevent="$wire.set('showEditPlanModal', false)"
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
                            <div class="text-[11px] text-rose-600 mt-0.5">Terdapat kolom yang belum valid. Silakan tinjau kembali input bertanda merah.</div>
                        </div>
                    </div>
                @endif

                <!-- Modal Body (Scrollable) -->
                <div class="overflow-y-auto px-6 py-5 space-y-6 flex-1 text-xs text-slate-700">
                    <!-- SECTION 1: TARIF & IDENTITAS PAKET -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                            <i class="fa-solid fa-tag text-orange-500 text-xs"></i>
                            <span class="font-bold text-slate-900 text-xs uppercase tracking-wider">Tarif & Kuota Paket</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Nama Paket -->
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">
                                    Nama Paket <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <input type="text"
                                       wire:model="editPlanName"
                                       placeholder="Contoh: Basic, Pro, Business"
                                       class="w-full border rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none @error('editPlanName') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                                @error('editPlanName')
                                    <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Badge Label -->
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">
                                    Badge Label <span class="text-slate-400 font-normal">(Opsional)</span>
                                </label>
                                <input type="text"
                                       wire:model="editPlanBadge"
                                       placeholder="Contoh: Terpopuler, Pemula, Enterprise"
                                       class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                            </div>
                        </div>

                        <!-- Tarif Dinamis Bulanan & Tahunan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-orange-50/40 p-4 rounded-xl border border-orange-100">
                            <div>
                                <label class="block font-semibold text-slate-800 mb-1">
                                    Tarif Bulanan (Rp) <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-2 text-slate-400 font-bold text-xs">Rp</span>
                                    <input type="number"
                                           step="1000"
                                           wire:model.live.debounce.300ms="editPlanPriceMonthly"
                                           placeholder="299000"
                                           class="w-full pl-10 pr-3.5 py-2 border rounded-xl text-xs font-bold text-slate-900 bg-white focus:outline-none @error('editPlanPriceMonthly') border-rose-400 ring-1 ring-rose-400 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                                </div>
                                <div class="text-[11px] text-orange-600 font-medium mt-1">
                                    Preview: <strong>Rp {{ number_format((float) str_replace(['.', ',', ' '], '', (string)($editPlanPriceMonthly ?: 0)), 0, ',', '.') }}</strong> / bulan
                                </div>
                                @error('editPlanPriceMonthly')
                                    <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-800 mb-1">
                                    Tarif Tahunan (Rp) <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-2 text-slate-400 font-bold text-xs">Rp</span>
                                    <input type="number"
                                           step="1000"
                                           wire:model.live.debounce.300ms="editPlanPriceYearly"
                                           placeholder="2990000"
                                           class="w-full pl-10 pr-3.5 py-2 border rounded-xl text-xs font-bold text-slate-900 bg-white focus:outline-none @error('editPlanPriceYearly') border-rose-400 ring-1 ring-rose-400 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                                </div>
                                <div class="text-[11px] text-slate-500 mt-1">
                                    Preview: <strong>Rp {{ number_format((float) str_replace(['.', ',', ' '], '', (string)($editPlanPriceYearly ?: 0)), 0, ',', '.') }}</strong> / tahun
                                </div>
                                @error('editPlanPriceYearly')
                                    <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Batasan Kuota (Limits) -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">
                                    Max Outlet <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <input type="number"
                                       min="1"
                                       wire:model="editPlanMaxOutlets"
                                       class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                                @error('editPlanMaxOutlets')
                                    <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">
                                    Max Karyawan <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <input type="number"
                                       min="1"
                                       wire:model="editPlanMaxUsers"
                                       class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                                @error('editPlanMaxUsers')
                                    <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">
                                    Max Produk <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <input type="number"
                                       min="1"
                                       wire:model="editPlanMaxProducts"
                                       class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                                @error('editPlanMaxProducts')
                                    <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Opsi Unggulan / Featured -->
                        <div class="flex flex-wrap items-center gap-6 pt-2">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox"
                                       wire:model="editPlanIsFeatured"
                                       class="w-4 h-4 text-orange-500 rounded border-slate-300 focus:ring-orange-400 cursor-pointer">
                                <span class="text-xs font-semibold text-slate-700">Tandai sebagai Paket Unggulan / Terpopuler</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox"
                                       wire:model="editPlanIsActive"
                                       class="w-4 h-4 text-orange-500 rounded border-slate-300 focus:ring-orange-400 cursor-pointer">
                                <span class="text-xs font-semibold text-slate-700">Paket Aktif & Tersedia</span>
                            </label>
                        </div>
                    </div>

                    <!-- SECTION 2: CHECKLIST FITUR PAKET -->
                    <div class="space-y-3 pt-3 border-t border-slate-100">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-list-check text-orange-500 text-xs"></i>
                                <span class="font-bold text-slate-900 text-xs uppercase tracking-wider">Checklist Fitur Paket</span>
                                <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 font-bold text-[11px]">
                                    {{ count($editPlanFeatures) }} dari {{ $allPlatformFeatures->count() }} dipilih
                                </span>
                            </div>
                            <div class="flex items-center gap-2 text-xs">
                                <button type="button" wire:click="selectAllPlanFeatures" class="text-orange-600 hover:text-orange-700 font-semibold hover:underline">
                                    Pilih Semua
                                </button>
                                <span class="text-slate-300">•</span>
                                <button type="button" wire:click="deselectAllPlanFeatures" class="text-slate-500 hover:text-slate-700 hover:underline">
                                    Hapus Pilihan
                                </button>
                            </div>
                        </div>

                        <!-- Quick Inline Tambah Fitur Baru Bar -->
                        <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-xl space-y-2">
                            <div class="text-[11px] font-semibold text-slate-600 flex items-center gap-1.5">
                                <i class="fa-solid fa-plus-circle text-orange-500"></i>
                                <span>Tambah Fitur Baru Langsung ke Checklist:</span>
                            </div>
                            <div class="flex flex-col sm:flex-row items-center gap-2">
                                <input type="text"
                                       wire:model="quickAddFeatureName"
                                       wire:keydown.enter.prevent="addFeatureInline"
                                       placeholder="Ketik nama fitur baru (misal: Integrasi GrabFood)..."
                                       class="w-full sm:flex-1 border border-slate-200 rounded-lg px-3 py-1.5 text-xs text-slate-800 bg-white focus:outline-none focus:ring-1 focus:ring-orange-500" />
                                <select wire:model="quickAddFeatureCategory"
                                        class="w-full sm:w-44 border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-slate-700 bg-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                    <option value="Kasir & Transaksi">Kasir & Transaksi</option>
                                    <option value="Inventori">Inventori</option>
                                    <option value="Laporan">Laporan</option>
                                    <option value="Paket Bundling">Paket Bundling</option>
                                    <option value="Platform">Platform</option>
                                    <option value="Integrasi">Integrasi</option>
                                    <option value="Layanan & Bantuan">Layanan & Bantuan</option>
                                </select>
                                <button type="button"
                                        wire:click="addFeatureInline"
                                        class="w-full sm:w-auto px-3.5 py-1.5 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs shadow-sm flex items-center justify-center gap-1.5 shrink-0 transition">
                                    <i class="fa-solid fa-check text-[10px]"></i>
                                    <span>Tambah & Centang</span>
                                </button>
                            </div>
                            @error('quickAddFeatureName')
                                <div class="text-rose-600 font-medium text-[10px]">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Checklist Items Container -->
                        <div class="border border-slate-200 rounded-xl overflow-hidden bg-white">
                            <div class="p-3 bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-semibold text-slate-500 flex items-center justify-between">
                                <span>Centang fitur yang aktif pada paket ini</span>
                                <span>Aksi Item</span>
                            </div>
                            <div class="max-h-64 overflow-y-auto divide-y divide-slate-100 p-1">
                                @forelse ($allPlatformFeatures as $feat)
                                    @php
                                        $isChecked = in_array($feat->name, $editPlanFeatures);
                                    @endphp
                                    <div class="flex items-center justify-between p-2.5 rounded-lg transition {{ $isChecked ? 'bg-orange-50/60' : 'hover:bg-slate-50' }}">
                                        <label class="flex items-center gap-3 flex-1 cursor-pointer select-none">
                                            <input type="checkbox"
                                                   wire:click="toggleFeatureInPlan('{{ $feat->name }}')"
                                                   {{ $isChecked ? 'checked' : '' }}
                                                   class="w-4 h-4 text-orange-500 rounded border-slate-300 focus:ring-orange-400 focus:ring-offset-0 cursor-pointer">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-semibold {{ $isChecked ? 'text-slate-900' : 'text-slate-600' }}">
                                                    {{ $feat->name }}
                                                </span>
                                                @if ($feat->category)
                                                    <span class="text-[10px] px-2 py-0.5 rounded-full {{ $isChecked ? 'bg-orange-200/70 text-orange-800' : 'bg-slate-100 text-slate-500' }}">
                                                        {{ $feat->category }}
                                                    </span>
                                                @endif
                                            </div>
                                        </label>

                                        <!-- Aksi Item: Edit nama fitur & Hapus fitur -->
                                        <div class="flex items-center gap-1 ml-3 shrink-0">
                                            <button type="button"
                                                    wire:click="openEditFeatureModal({{ $feat->id }})"
                                                    title="Edit nama fitur"
                                                    class="p-1.5 text-slate-400 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition">
                                                <i class="fa-solid fa-pen text-[10px]"></i>
                                            </button>
                                            <button type="button"
                                                    wire:click="deleteFeature({{ $feat->id }})"
                                                    wire:confirm="Hapus fitur '{{ $feat->name }}' dari seluruh paket dan platform?"
                                                    title="Hapus fitur dari platform"
                                                    class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                                                <i class="fa-solid fa-trash-can text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-6 text-center text-slate-400 italic">
                                        Belum ada fitur terdaftar. Silakan tambahkan fitur di atas.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 rounded-b-2xl flex items-center justify-end gap-3 shrink-0">
                    <button type="button"
                            wire:click="closeEditPlanModal"
                            @click.prevent="$wire.set('showEditPlanModal', false)"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-semibold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button"
                            wire:click="savePlan"
                            class="px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs shadow-sm shadow-orange-500/20 transition flex items-center gap-2">
                        <span wire:loading.remove wire:target="savePlan">Simpan Perubahan Tarif & Fitur</span>
                        <span wire:loading wire:target="savePlan" class="flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-notch fa-spin"></i>
                            <span>Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
