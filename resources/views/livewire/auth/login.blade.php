<div class="min-h-screen bg-slate-100/90 flex items-center justify-center p-4 sm:p-6 lg:p-8 antialiased text-slate-800" x-data="{ showPassword: false }">

    <!-- Card Login 2 Sisi Sederhana & Bersih (Inspirasi Gambar 2) -->
    <div class="max-w-4xl w-full bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-200/80 overflow-hidden grid grid-cols-1 md:grid-cols-2">

        <!-- ================= SISI KIRI: MASKOT DARI GAMBAR OG ================= -->
        <div class="hidden md:flex p-3 sm:p-4 bg-slate-50 items-center justify-center">
            <div class="w-full h-full rounded-2xl overflow-hidden relative shadow-xs flex items-center justify-center bg-white border border-slate-200/60 min-h-[460px]">
                <img src="{{ asset('images/opengraph.png') }}"
                     alt="Maskot Lokara POS"
                     class="w-full h-full object-cover object-right" />
            </div>
        </div>

        <!-- ================= SISI KANAN: FORM PUTIH BERSIH & MINIMALIS ================= -->
        <div class="p-8 sm:p-10 lg:p-12 flex flex-col justify-between bg-white">
            <div class="space-y-6">

                <!-- Logo & Brand Minimalis -->
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('images/logo-app.png') }}" alt="Lokara POS" class="w-9 h-9 object-contain" />
                    <span class="text-xl font-black text-slate-900 tracking-tight">Lokara <span class="text-orange-500">POS</span></span>
                </div>

                <!-- Header Sederhana Tanpa Badge Informasi -->
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                        Masuk ke Sistem Lokara POS
                    </h1>
                    <p class="text-xs text-slate-400 mt-1">
                        Masukkan email dan kata sandi akun Anda
                    </p>
                </div>

                <!-- Form Login (Mendukung Livewire & Standar HTTP POST Fallback) -->
                <form method="POST" action="{{ route('login') }}" wire:submit="login" class="space-y-4">
                    @csrf

                    <!-- Input Email -->
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-semibold text-slate-700">
                            Alamat Email <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-regular fa-envelope text-sm"></i>
                            </div>
                            <input id="email"
                                   type="email"
                                   name="email"
                                   wire:model="email"
                                   value="{{ old('email') }}"
                                   placeholder="nama@email.com"
                                   autocomplete="email"
                                   autofocus
                                   required
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 focus:bg-white transition" />
                        </div>
                        @error('email')
                            <p class="text-xs text-rose-500 font-medium flex items-center gap-1 mt-1">
                                <i class="fa-solid fa-circle-exclamation text-[11px]"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Input Password -->
                    <div class="space-y-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-700">
                            Kata Sandi <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </div>
                            <input id="password"
                                   :type="showPassword ? 'text' : 'password'"
                                   name="password"
                                   wire:model="password"
                                   placeholder="Masukkan kata sandi..."
                                   autocomplete="current-password"
                                   required
                                   class="w-full pl-10 pr-11 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 focus:bg-white transition" />
                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition"
                                    title="Tampilkan / Sembunyikan Kata Sandi">
                                <i class="fa-regular text-sm" :class="showPassword ? 'fa-eye-slash text-orange-500' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-xs text-rose-500 font-medium flex items-center gap-1 mt-1">
                                <i class="fa-solid fa-circle-exclamation text-[11px]"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Ingat Sesi Saya -->
                    <div class="flex items-center justify-between text-xs pt-0.5">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox"
                                   name="remember"
                                   wire:model="remember"
                                   value="1"
                                   class="w-4 h-4 rounded border-slate-300 text-orange-500 focus:ring-orange-500 transition cursor-pointer" />
                            <span class="text-slate-600 font-medium text-xs">Ingat sesi saya</span>
                        </label>
                    </div>

                    <!-- Tombol Masuk -->
                    <div class="pt-2">
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 py-3 px-5 rounded-xl text-xs sm:text-sm font-bold text-white bg-orange-500 hover:bg-orange-600 active:scale-[0.99] shadow-sm shadow-orange-500/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all cursor-pointer">
                            <span wire:loading.remove>Masuk ke Aplikasi</span>
                            <span wire:loading class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-notch fa-spin"></i> Memverifikasi...
                            </span>
                            <i wire:loading.remove class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer Hak Cipta Sederhana -->
            <div class="pt-6 border-t border-slate-100 text-center">
                <p class="text-[11px] text-slate-400 font-medium">
                    &copy; 2026 Lokara POS. Hak Cipta Dilindungi.
                </p>
            </div>
        </div>

    </div>
</div>
