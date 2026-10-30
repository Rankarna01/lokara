    <!-- ================= MODAL RESET PASSWORD PENGGUNA ================= -->
    @if ($showResetPasswordModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
             x-data
             @keydown.escape.window="$wire.closeResetPasswordModal()"
             @click.self="$wire.closeResetPasswordModal()">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full p-6 animate-scale-up space-y-4"
                 @click.outside="$wire.closeResetPasswordModal()">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Reset Password Pengguna</h3>
                            <p class="text-[11px] text-slate-400">Atur kata sandi baru untuk login akun tenant</p>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="closeResetPasswordModal"
                            @click.prevent="$wire.set('showResetPasswordModal', false)"
                            class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- Target User Profile Card -->
                <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-xl flex items-center justify-between text-xs">
                    <div class="min-w-0">
                        <div class="font-bold text-slate-900 truncate">{{ $resetUserName }}</div>
                        <div class="text-[11px] text-slate-500 truncate">{{ $resetUserEmail }}</div>
                        <div class="text-[10px] text-slate-400 mt-0.5">Tenant: <span class="font-semibold text-slate-700">{{ $resetTenantName }}</span></div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 font-semibold text-[10px] uppercase shrink-0">
                        {{ $resetUserRole }}
                    </span>
                </div>

                <!-- Error Alert if any -->
                @if ($errors->any())
                    <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-2.5">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm shrink-0 mt-0.5"></i>
                        <div class="text-[11px]">
                            @foreach ($errors->all() as $error)
                                <div>• {{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form wire:submit.prevent="updatePassword" class="space-y-3.5 text-xs">
                    <!-- Quick Random Password Generator -->
                    <div class="flex items-center justify-between bg-amber-50/60 border border-amber-200/80 p-2.5 rounded-xl">
                        <div class="text-[11px] text-amber-800">
                            <i class="fa-solid fa-wand-magic-sparkles text-amber-500 mr-1"></i>
                            <span>Butuh password cepat & aman?</span>
                        </div>
                        <button type="button"
                                wire:click="generateRandomPassword"
                                class="px-2.5 py-1 bg-white border border-amber-300 rounded-lg text-amber-800 hover:bg-amber-100 font-bold text-[11px] transition shadow-2xs cursor-pointer">
                            Generate Acak
                        </button>
                    </div>

                    <!-- Password Baru -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700">
                                Password Baru <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <button type="button"
                                    wire:click="$toggle('showPasswordText')"
                                    class="text-[10px] text-slate-500 hover:text-slate-800 cursor-pointer">
                                {{ $showPasswordText ? 'Sembunyikan' : 'Tampilkan' }}
                            </button>
                        </div>
                        <div class="relative">
                            <input type="{{ $showPasswordText ? 'text' : 'password' }}"
                                   wire:model="newPassword"
                                   placeholder="Minimal 6 karakter..."
                                   class="w-full border rounded-xl px-3.5 py-2.5 text-xs text-slate-800 transition focus:outline-none @error('newPassword') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                        </div>
                        @error('newPassword')
                            <div class="flex items-center gap-1.5 text-rose-600 font-medium text-[11px] mt-1">
                                <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">
                            Ulangi Password Baru <span class="text-rose-500 font-bold">*</span>
                        </label>
                        <input type="{{ $showPasswordText ? 'text' : 'password' }}"
                               wire:model="newPassword_confirmation"
                               placeholder="Ketik ulang password baru..."
                               class="w-full border rounded-xl px-3.5 py-2.5 text-xs text-slate-800 transition focus:outline-none @error('newPassword') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button"
                                wire:click="closeResetPasswordModal"
                                @click.prevent="$wire.set('showResetPasswordModal', false)"
                                class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium text-xs transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs shadow-sm shadow-orange-500/20 transition flex items-center gap-2 cursor-pointer">
                            <span wire:loading.remove wire:target="updatePassword">Simpan Password</span>
                            <span wire:loading wire:target="updatePassword" class="flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-notch fa-spin"></i>
                                <span>Menyimpan...</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
