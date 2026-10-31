            <!-- TOP NAVBAR -->
            <header class="h-16 bg-white border-b border-slate-200/80 px-4 md:px-6 flex items-center justify-between shrink-0">
                <!-- Left: Sidebar Toggler & Breadcrumbs -->
                <div class="flex items-center gap-3">
                    <button type="button" @click="toggleSidebar()"
                        class="w-9 h-9 rounded-xl border border-slate-200 text-slate-600 hover:text-orange-600 hover:border-orange-300 hover:bg-orange-50 flex items-center justify-center transition shadow-2xs"
                        title="Perkecil / Perbesar Sidebar">
                        <i class="fa-solid fa-bars text-sm" x-show="!sidebarOpen"></i>
                        <i class="fa-solid fa-bars-staggered text-sm" x-show="sidebarOpen"></i>
                    </button>

                    <div class="flex items-center gap-2 text-sm text-slate-500">
                        <span class="font-medium text-slate-700 hidden sm:inline">{{ $tenant->name ?? 'Kopi Ruang Pagi' }}</span>
                        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400 hidden sm:inline"></i>
                        <span class="capitalize font-semibold text-orange-600">{{ str_replace('_', ' ', $activeTab) }}</span>
                    </div>
                </div>

                <!-- Top Right Controls -->
                <div class="flex items-center gap-2 md:gap-3">
                    <!-- PWA Mobile Install Button (Auto-hides when installed / standalone) -->
                    <div x-data="pwaInstallManager()" x-init="init()" x-show="canInstall" x-cloak style="display: none;">
                        <button type="button" @click="promptInstall()"
                            class="flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white rounded-xl shadow-xs transition transform hover:scale-102"
                            title="Pasang Lokara POS sebagai aplikasi native di perangkat Anda">
                            <i class="fa-solid fa-download text-xs"></i>
                            <span class="hidden sm:inline">Install App</span>
                            <span class="sm:hidden">Install</span>
                        </button>

                        <!-- PWA Installation Modal (For Safari iOS or fallback guidance) -->
                        <div x-show="showIosModal"
                             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
                             @click.self="showIosModal = false">
                            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-sm w-full p-6 space-y-4 animate-scale-up text-left">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-xl bg-orange-500 text-white flex items-center justify-center text-sm shadow-xs">
                                            <i class="fa-solid fa-mobile-screen"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-bold text-slate-900">Install Lokara POS</h3>
                                            <p class="text-[11px] text-slate-400">Jalankan seperti aplikasi HP tanpa browser</p>
                                        </div>
                                    </div>
                                    <button @click="showIosModal = false" class="text-slate-400 hover:text-slate-600">
                                        <i class="fa-solid fa-xmark text-sm"></i>
                                    </button>
                                </div>

                                <div class="space-y-3 text-xs text-slate-600">
                                    <template x-if="isIos">
                                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-2">
                                            <div class="font-bold text-slate-800 flex items-center gap-1.5">
                                                <i class="fa-brands fa-apple text-sm"></i> Cara di iPhone / iPad (Safari):
                                            </div>
                                            <ol class="list-decimal list-inside space-y-1 text-[11px]">
                                                <li>Ketuk tombol <strong class="text-slate-900">Bagikan (Share / 📤)</strong> di bar bawah Safari.</li>
                                                <li>Pilih <strong class="text-orange-600">"Add to Home Screen"</strong> (Tambah ke Layar Utama).</li>
                                                <li>Ketuk <strong class="text-slate-900">"Add / Tambah"</strong> di kanan atas.</li>
                                            </ol>
                                        </div>
                                    </template>
                                    <template x-if="!isIos">
                                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-2">
                                            <div class="font-bold text-slate-800 flex items-center gap-1.5">
                                                <i class="fa-brands fa-chrome text-sm"></i> Cara di Chrome / Edge:
                                            </div>
                                            <p class="text-[11px]">
                                                Klik ikon <strong class="text-orange-600">Install (⬇️)</strong> di bar alamat URL atau menu Titik Tiga (⋮) browser lalu pilih <strong>"Install Lokara POS"</strong>.
                                            </p>
                                        </div>
                                    </template>
                                </div>

                                <div class="pt-2 border-t border-slate-100 flex gap-2">
                                    <button type="button" @click="markAsInstalled()" class="w-1/2 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-xs">
                                        Sudah Terpasang
                                    </button>
                                    <button type="button" @click="showIosModal = false" class="w-1/2 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs shadow-xs">
                                        Saya Mengerti
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Super Admin Only: Switcher to inspect any tenant -->
                    @if(auth()->check() && auth()->user()->isSuperAdmin() && isset($allTenants) && $allTenants->count() > 1)
                        <div class="flex items-center gap-1.5 bg-amber-50 border border-amber-300 rounded-xl px-2.5 py-1 text-xs shadow-2xs" title="Mode Super Admin: Simulasi Tampilan Tenant">
                            <i class="fa-solid fa-user-shield text-amber-600 text-xs"></i>
                            <span class="text-[10px] uppercase font-bold text-amber-800 tracking-wider hidden md:inline">Super Admin View:</span>
                            <select wire:change="switchTenant($event.target.value)"
                                class="bg-transparent text-xs font-bold text-amber-900 focus:outline-none cursor-pointer">
                                @foreach($allTenants as $t)
                                    <option value="{{ $t->id }}" {{ $tenantId == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }} ({{ $t->businessType?->name ?? 'Bisnis' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <!-- Owner Tenant: Jenis Usaha Badge (Static Pill, Bukan Dropdown) -->
                        <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-50 text-slate-700 border border-slate-200 shadow-2xs">
                            <i class="fa-solid {{ $tenant?->businessType?->icon ?: 'fa-store' }} text-orange-500"></i>
                            <span>{{ $tenant?->businessType?->name ?? 'Bisnis' }}</span>
                        </div>
                    @endif

                    <!-- Outlet Switcher (Cabang Toko Milik Tenant) -->
                    @if($outlets->count() > 1)
                        <div class="relative">
                            <select wire:change="switchOutlet($event.target.value)"
                                class="bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 py-1.5 px-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500 cursor-pointer shadow-2xs">
                                @foreach($outlets as $out)
                                    <option value="{{ $out->id }}" {{ $selectedOutletId == $out->id ? 'selected' : '' }}>
                                        📍 {{ $out->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="text-xs font-medium text-slate-700 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl flex items-center gap-1.5 shadow-2xs">
                            <i class="fa-solid fa-location-dot text-orange-500"></i>
                            <span>{{ $selectedOutlet->name ?? 'Outlet Utama' }}</span>
                        </div>
                    @endif

                    <!-- Switch to POS Kasir Quick Button -->
                    <a href="{{ route('pos.terminal') }}"
                        class="hidden sm:flex items-center gap-2 text-xs font-semibold px-3 py-1.5 bg-orange-50 text-orange-600 border border-orange-200 rounded-xl hover:bg-orange-100 transition shadow-2xs">
                        <i class="fa-solid fa-desktop"></i>
                        <span>Layar POS Kasir</span>
                    </a>

                    <!-- Switch to Super Admin (if user is superadmin) -->
                    @if(auth()->check() && auth()->user()->isSuperAdmin())
                        <a href="{{ route('superadmin.dashboard') }}"
                            class="text-xs font-semibold px-3 py-1.5 bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition shadow-2xs">
                            <i class="fa-solid fa-arrow-left mr-1"></i> Super Admin
                        </a>
                    @endif

                    <!-- Notification -->
                    <button class="relative p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-50">
                        <i class="fa-regular fa-bell"></i>
                        @if($lowStockIngredients->count() > 0)
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full animate-pulse"></span>
                        @endif
                    </button>
                </div>
            </header>
