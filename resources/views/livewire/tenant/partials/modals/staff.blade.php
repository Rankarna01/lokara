    <!-- 13. Modal Tambah / Edit Akun Pegawai & Kasir -->
    @if ($showStaffModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">{{ $editingStaffId ? 'Edit Akun Pegawai / Kasir' : 'Tambah Akun Pegawai Baru' }}</h3>
                    </div>
                    <button wire:click="$set('showStaffModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto text-sm">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Pegawai</label>
                        <input type="text" wire:model="staffName" placeholder="Contoh: Siti Rahmawati"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        @error('staffName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email Login</label>
                            <input type="email" wire:model="staffEmail" placeholder="siti@kopiruangpagi.id"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                            @error('staffEmail') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">No. Handphone / WhatsApp</label>
                            <input type="text" wire:model="staffPhone" placeholder="08123456789"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Peran (Role Akses)</label>
                            <select wire:model="staffRole"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 font-medium">
                                <option value="cashier">Kasir POS (Hanya Layar Kasir)</option>
                                <option value="manager">Manajer Cabang (Kasir + Back Office)</option>
                                <option value="staff">Staf Layanan / Kitchen / Kapster</option>
                            </select>
                            @error('staffRole') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Cabang Penugasan</label>
                            <select wire:model="staffOutletId"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 font-medium">
                                @foreach($outlets as $out)
                                    <option value="{{ $out->id }}">{{ $out->name }}</option>
                                @endforeach
                            </select>
                            @error('staffOutletId') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">6-Digit PIN Kasir POS</label>
                            <input type="text" wire:model="staffPinCode" maxlength="6" placeholder="123456"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm font-mono font-bold tracking-widest text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <span class="text-[10px] text-slate-400">Untuk otorisasi cepat di layar POS</span>
                            @error('staffPinCode') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi (Password)</label>
                            <input type="password" wire:model="staffPassword" placeholder="{{ $editingStaffId ? 'Kosongkan jika tak diubah' : 'Default: password' }}"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <span class="text-[10px] text-slate-400">Digunakan untuk login ke sistem</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                            <input type="checkbox" wire:model="staffIsActive" class="rounded text-orange-500 focus:ring-orange-500">
                            <span>Akun aktif dan diizinkan masuk ke sistem Lokara POS</span>
                        </label>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" wire:click="$set('showStaffModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Batal</button>
                    <button type="button" wire:click="saveStaff"
                        class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i>
                        <span>{{ $editingStaffId ? 'Simpan Perubahan' : 'Buat Akun Pegawai' }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

