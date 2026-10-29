<div class="min-h-screen bg-slate-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 antialiased text-slate-800" x-data="{ showPassword: false }">

    <!-- Top Logo & Heading -->
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <div class="inline-flex items-center gap-2.5 mb-3">
            <div class="w-10 h-10 rounded-xl bg-orange-500 flex items-center justify-center text-white shadow-md shadow-orange-500/20">
                <i class="fa-solid fa-shapes text-lg"></i>
            </div>
            <div class="flex items-center">
                <span class="text-2xl font-bold tracking-tight text-slate-900">Lokara</span>
                <span class="ml-1.5 px-1.5 py-0.5 rounded text-[11px] font-extrabold uppercase bg-orange-500 text-white tracking-wider">POS</span>
            </div>
        </div>
        <h2 class="text-xl font-bold tracking-tight text-slate-900">Masuk ke Sistem Lokara POS</h2>
        <p class="mt-1 text-xs text-slate-500">Pilih akun untuk masuk ke Platform Admin, Tenant Back Office, atau Layar Kasir</p>
    </div>

    <!-- Login Card -->
    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white py-8 px-6 sm:px-8 border border-slate-200/80 rounded-2xl shadow-sm space-y-6">

            <!-- Quick Demo Login Bar -->
            <div class="p-3.5 bg-orange-50/60 border border-orange-100 rounded-xl">
                <div class="flex items-center justify-between text-xs font-semibold text-orange-800 mb-2">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-bolt text-orange-500"></i> Akun Demo Siap Pakai:
                    </span>
                    <span class="text-[10px] text-orange-600 bg-orange-100/60 px-1.5 py-0.5 rounded">1-Klik Isi</span>
                </div>
                <div class="grid grid-cols-3 gap-1.5 text-xs">
                    <button type="button" wire:click="fillDemoAdmin"
                            class="p-2 bg-white border border-orange-200 rounded-lg text-slate-700 hover:border-orange-500 hover:text-orange-600 font-medium text-left transition shadow-2xs">
                        <div class="font-bold text-[11px] text-slate-900">Super Admin</div>
                        <div class="text-[9px] text-slate-400 truncate">Platform</div>
                    </button>
                    <button type="button" wire:click="fillDemoOwner"
                            class="p-2 bg-white border border-orange-200 rounded-lg text-slate-700 hover:border-orange-500 hover:text-orange-600 font-medium text-left transition shadow-2xs">
                        <div class="font-bold text-[11px] text-slate-900">Owner Tenant</div>
                        <div class="text-[9px] text-slate-400 truncate">Admin POS</div>
                    </button>
                    <button type="button" wire:click="fillDemoCashier"
                            class="p-2 bg-white border border-orange-200 rounded-lg text-slate-700 hover:border-orange-500 hover:text-orange-600 font-medium text-left transition shadow-2xs">
                        <div class="font-bold text-[11px] text-slate-900">Kasir Toko</div>
                        <div class="text-[9px] text-slate-400 truncate">Layar POS</div>
                    </button>
                </div>
            </div>

            <!-- Login Form -->
            <form wire:submit.prevent="login" class="space-y-4">

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-regular fa-envelope text-xs"></i>
                        </div>
                        <input id="email"
                               type="email"
                               wire:model.defer="email"
                               placeholder="nama@kopiruangpagi.id"
                               class="w-full pl-9 pr-3.5 py-2.5 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 transition" />
                    </div>
                    @error('email')
                        <p class="mt-1 text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-700">Kata Sandi</label>
                        <span class="text-[11px] text-slate-400">Default: password</span>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </div>
                        <input id="password"
                               :type="showPassword ? 'text' : 'password'"
                               wire:model.defer="password"
                               placeholder="••••••••"
                               class="w-full pl-9 pr-10 py-2.5 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500 transition" />
                        <button type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition">
                            <i class="fa-regular" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox"
                               wire:model="remember"
                               class="rounded border-slate-300 text-orange-500 focus:ring-orange-500" />
                        <span class="text-slate-600">Ingat sesi saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-semibold text-white bg-orange-500 hover:bg-orange-600 shadow-sm shadow-orange-500/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition">
                        <span wire:loading.remove>Masuk ke Aplikasi</span>
                        <span wire:loading class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin"></i> Memproses...
                        </span>
                        <i wire:loading.remove class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </form>

            <div class="pt-4 border-t border-slate-100 text-center">
                <p class="text-[11px] text-slate-400">
                    &copy; 2026 Lokara POS Multi-Tenant SaaS. Hak Cipta Dilindungi.
                </p>
            </div>
        </div>
    </div>
</div>
