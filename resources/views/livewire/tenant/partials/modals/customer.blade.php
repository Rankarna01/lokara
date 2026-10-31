<!-- Modal Tambah / Edit Pelanggan CRM -->
@if ($showCustomerModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in duration-150">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/80">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm shadow-xs">
                        <i class="fa-solid fa-user-tag"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">
                            {{ $editingCustomerId ? 'Edit Data Pelanggan' : 'Tambah Pelanggan CRM Baru' }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Kelola data member, informasi kontak, preferensi layanan, dan poin loyalitas.
                        </p>
                    </div>
                </div>
                <button wire:click="$set('showCustomerModal', false)" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Form Body -->
            <form wire:submit.prevent="saveCustomer" class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Nama Lengkap Pelanggan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           wire:model="customerName"
                           placeholder="Contoh: Sarah Amanda, Dr. Hendra..."
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition" />
                    @error('customerName')
                        <span class="text-[11px] text-rose-500 mt-0.5 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Kontak: WhatsApp & Email -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            No. WhatsApp / HP
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">
                                <i class="fa-brands fa-whatsapp text-emerald-500"></i>
                            </span>
                            <input type="text"
                                   wire:model="customerPhone"
                                   placeholder="081234567890"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-3 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition" />
                        </div>
                        @error('customerPhone')
                            <span class="text-[11px] text-rose-500 mt-0.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Email
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs">
                                <i class="fa-regular fa-envelope"></i>
                            </span>
                            <input type="email"
                                   wire:model="customerEmail"
                                   placeholder="nama@email.com"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-3 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition" />
                        </div>
                        @error('customerEmail')
                            <span class="text-[11px] text-rose-500 mt-0.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Biodata: Gender & Tanggal Lahir -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Jenis Kelamin
                        </label>
                        <select wire:model="customerGender"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="female">Wanita</option>
                            <option value="male">Pria</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Tanggal Lahir / Ulang Tahun
                        </label>
                        <input type="date"
                               wire:model="customerBirthDate"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-medium text-slate-700 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                    </div>
                </div>

                <!-- Tier Member & Saldo Poin -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-orange-50/50 p-3.5 rounded-xl border border-orange-100">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Tier Keanggotaan
                        </label>
                        <select wire:model="customerMembershipTier"
                                class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <option value="silver">🥈 Silver Member (Standar)</option>
                            <option value="gold">🥇 Gold Member (VIP)</option>
                            <option value="platinum">👑 Platinum Member (Prioritas)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Saldo Poin Loyalti
                        </label>
                        <input type="number"
                               wire:model="customerPoints"
                               min="0"
                               placeholder="0"
                               class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-orange-600 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                    </div>
                </div>

                <!-- Alamat Domisili -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Alamat Lengkap
                    </label>
                    <textarea wire:model="customerAddress"
                              rows="2"
                              placeholder="Alamat rumah / domisili pelanggan..."
                              class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition"></textarea>
                </div>

                <!-- Catatan Preferensi / Alergi / Profil Khusus -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Catatan Khusus & Preferensi Pelanggan
                    </label>
                    <textarea wire:model="customerNotes"
                              rows="2"
                              placeholder="Contoh: Alergi bleaching, preferensi kopi oatmilk no sugar, stylist favorit Rona..."
                              class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition"></textarea>
                    <span class="text-[10px] text-slate-400 mt-1 block">Catatan ini akan tersinkronisasi dan tampil saat kasir melayani di POS.</span>
                </div>

                <!-- Modal Actions -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button"
                            wire:click="$set('showCustomerModal', false)"
                            class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i>
                        <span>{{ $editingCustomerId ? 'Simpan Perubahan' : 'Tambah Pelanggan' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

