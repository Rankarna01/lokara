    <!-- ================= MODAL BUAT AKUN PENGGUNA BARU ================= -->
    @if ($showCreateUserModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
             x-data
             @keydown.escape.window="$wire.closeCreateUserModal()"
             @click.self="$wire.closeCreateUserModal()">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 animate-scale-up space-y-4"
                 @click.outside="$wire.closeCreateUserModal()">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Buat Akun Pengguna Baru</h3>
                            <p class="text-[11px] text-slate-400">Daftarkan akun login staf kasir, manajer, atau pemilik tenant</p>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="closeCreateUserModal"
                            @click.prevent="$wire.set('showCreateUserModal', false)"
                            class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                @if ($errors->any())
                    <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-2.5">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm shrink-0 mt-0.5"></i>
                        <div>
                            <div class="font-bold text-rose-900">Terdapat Kesalahan Pengisian:</div>
                            <div class="text-[11px] text-rose-600 mt-0.5">Silakan lengkapi atau perbaiki kolom yang bergaris merah di bawah.</div>
                        </div>
                    </div>
                @endif

                <form wire:submit.prevent="createUser" class="space-y-3.5 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Nama Lengkap <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <input type="text"
                                   wire:model="createUserName"
                                   placeholder="Contoh: Budi Santoso"
                                   class="w-full border rounded-xl px-3.5 py-2.5 text-xs text-slate-800 transition focus:outline-none @error('createUserName') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                            @error('createUserName')
                                <div class="text-rose-600 font-medium text-[11px] mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Alamat Email <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <input type="email"
                                   wire:model="createUserEmail"
                                   placeholder="budi@kopiruangpagi.id"
                                   class="w-full border rounded-xl px-3.5 py-2.5 text-xs text-slate-800 transition focus:outline-none @error('createUserEmail') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                            @error('createUserEmail')
                                <div class="text-rose-600 font-medium text-[11px] mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Tenant Asal <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <select wire:model="createUserTenantId"
                                    class="w-full border rounded-xl px-3 py-2 text-xs text-slate-800 transition focus:outline-none @error('createUserTenantId') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror">
                                <option value="">-- Pilih Tenant --</option>
                                @foreach ($tenantOptions as $tn)
                                    <option value="{{ $tn->id }}">{{ $tn->name }}</option>
                                @endforeach
                            </select>
                            @error('createUserTenantId')
                                <div class="text-rose-600 font-medium text-[11px] mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Peran (Role) <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <select wire:model="createUserRole"
                                    class="w-full border rounded-xl px-3 py-2 text-xs text-slate-800 transition focus:outline-none @error('createUserRole') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror">
                                <option value="cashier">Kasir (Layar POS)</option>
                                <option value="manager">Manajer Toko</option>
                                <option value="owner">Pemilik (Owner)</option>
                            </select>
                            @error('createUserRole')
                                <div class="text-rose-600 font-medium text-[11px] mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="bg-amber-50/60 border border-amber-200/80 p-2.5 rounded-xl flex items-center justify-between">
                        <span class="text-[11px] text-amber-800">
                            <i class="fa-solid fa-key text-amber-500 mr-1"></i>
                            Buat password otomatis?
                        </span>
                        <button type="button"
                                wire:click="generateCreateUserPassword"
                                class="px-2.5 py-1 bg-white border border-amber-300 rounded-lg text-amber-800 hover:bg-amber-100 font-bold text-[11px] transition shadow-2xs cursor-pointer">
                            Generate Acak
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Password Akun <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <input type="password"
                                   wire:model="createUserPassword"
                                   placeholder="Minimal 6 karakter..."
                                   class="w-full border rounded-xl px-3.5 py-2.5 text-xs text-slate-800 transition focus:outline-none @error('createUserPassword') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                            @error('createUserPassword')
                                <div class="text-rose-600 font-medium text-[11px] mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Ulangi Password <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <input type="password"
                                   wire:model="createUserPassword_confirmation"
                                   placeholder="Ulangi password..."
                                   class="w-full border rounded-xl px-3.5 py-2.5 text-xs text-slate-800 transition focus:outline-none @error('createUserPassword') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button"
                                wire:click="closeCreateUserModal"
                                @click.prevent="$wire.set('showCreateUserModal', false)"
                                class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium text-xs transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs shadow-sm shadow-orange-500/20 transition flex items-center gap-2 cursor-pointer">
                            <span wire:loading.remove wire:target="createUser">Buat Akun Sekarang</span>
                            <span wire:loading wire:target="createUser" class="flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-notch fa-spin"></i>
                                <span>Menyimpan...</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
