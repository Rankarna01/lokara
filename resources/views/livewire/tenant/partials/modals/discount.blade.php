    <!-- 11. Modal Tambah / Edit Diskon Lengkap (% dan Rp) -->
    @if ($showDiscountModal)
        <div class="fixed inset-0 z-[60] bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">
                            {{ $editingDiscountId ? 'Edit Program Diskon / Promo' : 'Tambah Program Diskon Baru' }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Diskon dapat berupa persentase (%) atau potongan uang langsung (Rp)</p>
                    </div>
                    <button wire:click="$set('showDiscountModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto text-sm">
                    <!-- Nama Diskon & Kode Kupon -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Promo / Diskon <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="discountName" placeholder="Contoh: Diskon Pelajar & Mahasiswa 15%, Potongan Cukur 15K"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                            @error('discountName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Kupon / Voucher (Opsional)</label>
                            <input type="text" wire:model="discountCode" placeholder="Contoh: PELAJAR15, CUKUR15K"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm font-mono uppercase focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <span class="text-[10px] text-slate-400">Kosongkan jika promo otomatis tanpa kode</span>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Berlaku Untuk</label>
                            <select wire:model="discountApplicableTo"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <option value="all">Semua Menu & Layanan</option>
                                <option value="services">Khusus Layanan Jasa</option>
                                <option value="products">Khusus Produk Retail</option>
                            </select>
                        </div>
                    </div>

                    <!-- Pilihan Tipe Diskon: Persentase (%) vs Nominal Uang (Rp) -->
                    <div class="p-4 rounded-xl border border-orange-200 bg-orange-50/50 space-y-3">
                        <label class="block text-xs font-bold text-slate-800">Skema & Tipe Potongan Harga <span class="text-rose-500">*</span></label>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer transition {{ $discountType === 'percentage' ? 'bg-white border-orange-500 shadow-xs' : 'bg-slate-50 border-slate-200' }}">
                                <input type="radio" value="percentage" wire:model.live="discountType" class="text-orange-500 focus:ring-orange-500">
                                <div>
                                    <div class="text-xs font-bold text-slate-900">Persentase (%)</div>
                                    <div class="text-[10px] text-slate-500">Misal: 10%, 20%, 50%</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer transition {{ $discountType === 'fixed' ? 'bg-white border-orange-500 shadow-xs' : 'bg-slate-50 border-slate-200' }}">
                                <input type="radio" value="fixed" wire:model.live="discountType" class="text-orange-500 focus:ring-orange-500">
                                <div>
                                    <div class="text-xs font-bold text-slate-900">Nominal Langsung (Rp)</div>
                                    <div class="text-[10px] text-slate-500">Misal: Potongan Rp 15.000</div>
                                </div>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    {{ $discountType === 'percentage' ? 'Besar Diskon (%)' : 'Besar Potongan (Rp)' }} <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" wire:model="discountValue" min="1"
                                        class="w-full bg-white border border-slate-300 rounded-xl pl-3.5 pr-10 py-2 text-sm font-bold text-orange-600 focus:ring-1 focus:ring-orange-500">
                                    <span class="absolute right-3 top-2.5 text-xs font-bold text-slate-400">
                                        {{ $discountType === 'percentage' ? '%' : 'Rp' }}
                                    </span>
                                </div>
                                @error('discountValue') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            @if($discountType === 'percentage')
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Maksimal Potongan (Rp)</label>
                                    <input type="number" wire:model="discountMaxAmount" placeholder="e.g. 25000"
                                        class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2 text-sm font-semibold focus:ring-1 focus:ring-orange-500">
                                    <span class="text-[10px] text-slate-400">Batas maksimal nominal diskon</span>
                                </div>
                            @else
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan Nilai</label>
                                    <div class="py-2 text-xs text-slate-500">
                                        Total belanja dipotong langsung Rp {{ number_format($discountValue ?: 0, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Syarat & Kuota -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Syarat Min. Belanja (Rp)</label>
                            <input type="number" wire:model="discountMinPurchase" placeholder="0"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <span class="text-[10px] text-slate-400">Isi 0 jika tanpa syarat minimum</span>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Batas Kuota Penggunaan</label>
                            <input type="number" wire:model="discountMaxUsage" placeholder="e.g. 100"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <span class="text-[10px] text-slate-400">Kosongkan jika kuota tak terbatas</span>
                        </div>
                    </div>

                    <!-- Periode Promo -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai Berlaku</label>
                            <input type="date" wire:model="discountStartDate"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Berakhir</label>
                            <input type="date" wire:model="discountEndDate"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                    </div>

                    <!-- Catatan / S&K -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan / Syarat & Ketentuan</label>
                        <input type="text" wire:model="discountNotes" placeholder="Contoh: Khusus hari Senin - Kamis, berlaku untuk member"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                    </div>

                    <div class="flex items-center gap-4 pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                            <input type="checkbox" wire:model="discountIsActive" class="rounded text-orange-500 focus:ring-orange-500">
                            <span>Promo ini aktif dan siap digunakan saat transaksi kasir</span>
                        </label>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2">
                    @if ($editingDiscountId)
                        <button type="button"
                            wire:click="deleteDiscount({{ $editingDiscountId }})"
                            wire:confirm="Yakin ingin menghapus diskon ini?"
                            class="px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition flex items-center gap-1.5">
                            <i class="fa-solid fa-trash-can"></i>
                            <span>Hapus</span>
                        </button>
                    @else
                        <div></div>
                    @endif

                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="$set('showDiscountModal', false)"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Batal</button>
                        <button type="button" wire:click="saveDiscount"
                            class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm flex items-center gap-1.5">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ $editingDiscountId ? 'Simpan Perubahan' : 'Buat Diskon' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

