        <!-- Mobile Sidebar Backdrop -->
        <div class="fixed inset-0 bg-slate-900/40 z-40 lg:hidden transition-opacity"
             x-show="$wire.sidebarOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="$wire.toggleSidebar()">
        </div>

        <!-- ================= SIDEBAR ================= -->
        <aside class="fixed inset-y-0 left-0 z-50 bg-white border-r border-slate-200/80 flex flex-col justify-between transition-all duration-300 select-none lg:static"
               :class="[
                   $wire.sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
                   sidebarExpanded ? 'w-64' : 'w-64 lg:w-20'
               ]">

            <div>
                <!-- Brand Logo (Orange Icon + Lokara POS badge) -->
                <div class="h-16 flex items-center border-b border-slate-100 transition-all duration-300"
                     :class="sidebarExpanded ? 'justify-between px-6' : 'justify-between px-6 lg:justify-center lg:px-2'">
                    <div class="flex items-center gap-2.5 overflow-hidden" :title="sidebarExpanded ? '' : 'Lokara POS Super Admin'">
                        <div class="w-8 h-8 rounded-lg bg-orange-500 flex items-center justify-center text-white shadow-sm shadow-orange-500/20 shrink-0">
                            <i class="fa-solid fa-cubes text-sm"></i>
                        </div>
                        <div class="flex items-center" x-show="sidebarExpanded" x-transition.opacity.duration.200ms>
                            <span class="text-xl font-bold tracking-tight text-slate-900">Lokara</span>
                            <span class="ml-1.5 px-1.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-orange-500 text-white tracking-wider">POS</span>
                        </div>
                    </div>

                    <!-- Desktop Collapse Toggle Button -->
                    <button type="button" @click="toggleSidebarExpanded()" x-show="sidebarExpanded"
                        class="hidden lg:block text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition"
                        title="Perkecil Sidebar">
                        <i class="fa-solid fa-angles-left text-xs"></i>
                    </button>
                </div>

                <!-- Navigation List -->
                <div class="p-3 space-y-4">
                    <div>
                        <div x-show="sidebarExpanded" x-transition.opacity.duration.200ms class="px-3 pb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            Platform
                        </div>
                        <div x-show="!sidebarExpanded" class="hidden lg:block h-2 border-b border-slate-100 mb-2"></div>

                        <nav class="space-y-1">
                            <!-- Dashboard -->
                            <button wire:click="setTab('dashboard')"
                                    class="w-full flex items-center rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'dashboard' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
                                    :class="sidebarExpanded ? 'px-3.5 py-2.5 gap-3' : 'px-3.5 py-2.5 gap-3 lg:px-2 lg:py-2.5 lg:justify-center'"
                                    title="Dashboard">
                                <i class="fa-solid fa-house text-base w-5 text-center shrink-0 {{ $activeTab === 'dashboard' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span x-show="sidebarExpanded" x-transition.opacity.duration.200ms class="truncate">Dashboard</span>
                            </button>

                            <!-- Tenant -->
                            <button wire:click="setTab('tenants')"
                                    class="w-full flex items-center rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'tenants' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
                                    :class="sidebarExpanded ? 'px-3.5 py-2.5 gap-3' : 'px-3.5 py-2.5 gap-3 lg:px-2 lg:py-2.5 lg:justify-center'"
                                    title="Tenant">
                                <i class="fa-solid fa-store text-base w-5 text-center shrink-0 {{ $activeTab === 'tenants' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span x-show="sidebarExpanded" x-transition.opacity.duration.200ms class="truncate">Tenant</span>
                            </button>

                            <!-- Akun Tenant -->
                            <button wire:click="setTab('tenant-users')"
                                    class="w-full flex items-center rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'tenant-users' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
                                    :class="sidebarExpanded ? 'px-3.5 py-2.5 gap-3' : 'px-3.5 py-2.5 gap-3 lg:px-2 lg:py-2.5 lg:justify-center'"
                                    title="Akun Tenant">
                                <i class="fa-solid fa-users text-base w-5 text-center shrink-0 {{ $activeTab === 'tenant-users' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span x-show="sidebarExpanded" x-transition.opacity.duration.200ms class="truncate">Akun Tenant</span>
                            </button>

                            <!-- Paket & Langganan -->
                            <button wire:click="setTab('plans')"
                                    class="w-full flex items-center rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'plans' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
                                    :class="sidebarExpanded ? 'px-3.5 py-2.5 gap-3' : 'px-3.5 py-2.5 gap-3 lg:px-2 lg:py-2.5 lg:justify-center'"
                                    title="Paket & Langganan">
                                <i class="fa-solid fa-credit-card text-base w-5 text-center shrink-0 {{ $activeTab === 'plans' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span x-show="sidebarExpanded" x-transition.opacity.duration.200ms class="truncate">Paket & Langganan</span>
                            </button>

                            <!-- Billing & Invoice -->
                            <button wire:click="setTab('invoices')"
                                    class="w-full flex items-center rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'invoices' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
                                    :class="sidebarExpanded ? 'px-3.5 py-2.5 gap-3' : 'px-3.5 py-2.5 gap-3 lg:px-2 lg:py-2.5 lg:justify-center'"
                                    title="Billing & Invoice">
                                <i class="fa-solid fa-file-invoice-dollar text-base w-5 text-center shrink-0 {{ $activeTab === 'invoices' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span x-show="sidebarExpanded" x-transition.opacity.duration.200ms class="truncate">Billing & Invoice</span>
                            </button>

                            <!-- Template Usaha -->
                            <button wire:click="setTab('business-types')"
                                    class="w-full flex items-center rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'business-types' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
                                    :class="sidebarExpanded ? 'px-3.5 py-2.5 gap-3' : 'px-3.5 py-2.5 gap-3 lg:px-2 lg:py-2.5 lg:justify-center'"
                                    title="Template Usaha">
                                <i class="fa-solid fa-shapes text-base w-5 text-center shrink-0 {{ $activeTab === 'business-types' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span x-show="sidebarExpanded" x-transition.opacity.duration.200ms class="truncate">Template Usaha</span>
                            </button>

                            <!-- Pengaturan Global -->
                            <button wire:click="setTab('settings')"
                                    class="w-full flex items-center rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'settings' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
                                    :class="sidebarExpanded ? 'px-3.5 py-2.5 gap-3' : 'px-3.5 py-2.5 gap-3 lg:px-2 lg:py-2.5 lg:justify-center'"
                                    title="Pengaturan Global">
                                <i class="fa-solid fa-gear text-base w-5 text-center shrink-0 {{ $activeTab === 'settings' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span x-show="sidebarExpanded" x-transition.opacity.duration.200ms class="truncate">Pengaturan Global</span>
                            </button>

                            <!-- Support & Pengumuman -->
                            <button wire:click="setTab('support')"
                                    class="w-full flex items-center rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'support' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
                                    :class="sidebarExpanded ? 'px-3.5 py-2.5 gap-3' : 'px-3.5 py-2.5 gap-3 lg:px-2 lg:py-2.5 lg:justify-center'"
                                    title="Support & Pengumuman">
                                <i class="fa-solid fa-bullhorn text-base w-5 text-center shrink-0 {{ $activeTab === 'support' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span x-show="sidebarExpanded" x-transition.opacity.duration.200ms class="truncate">Support & Pengumuman</span>
                            </button>

                            <!-- Hapus Riwayat Transaksi (terkunci dengan kredensial) -->
                            <button wire:click="setTab('history-cleanup')"
                                    class="w-full flex items-center rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'history-cleanup' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
                                    :class="sidebarExpanded ? 'px-3.5 py-2.5 gap-3' : 'px-3.5 py-2.5 gap-3 lg:px-2 lg:py-2.5 lg:justify-center'"
                                    title="Hapus Riwayat Transaksi">
                                <i class="fa-solid fa-trash-can text-base w-5 text-center shrink-0 {{ $activeTab === 'history-cleanup' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span x-show="sidebarExpanded" x-transition.opacity.duration.200ms class="truncate">Hapus Riwayat Transaksi</span>
                            </button>

                            <!-- Monitoring & Audit -->
                            <button wire:click="setTab('monitoring')"
                                    class="w-full flex items-center rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'monitoring' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
                                    :class="sidebarExpanded ? 'px-3.5 py-2.5 gap-3' : 'px-3.5 py-2.5 gap-3 lg:px-2 lg:py-2.5 lg:justify-center'"
                                    title="Monitoring & Audit">
                                <i class="fa-solid fa-shield-halved text-base w-5 text-center shrink-0 {{ $activeTab === 'monitoring' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span x-show="sidebarExpanded" x-transition.opacity.duration.200ms class="truncate">Monitoring & Audit</span>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Bottom User Profile Card -->
            <div class="p-3 border-t border-slate-100" x-data="{ bottomMenuOpen: false }">
                <div @click="bottomMenuOpen = !bottomMenuOpen"
                     class="flex items-center rounded-xl hover:bg-slate-50 transition cursor-pointer"
                     :class="sidebarExpanded ? 'justify-between p-2' : 'justify-between p-2 lg:justify-center lg:p-1.5'"
                     :title="sidebarExpanded ? '' : '{{ auth()->user()->name ?? 'Super Admin' }}'">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-semibold text-sm shrink-0">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div class="text-left" x-show="sidebarExpanded" x-transition.opacity.duration.200ms>
                            <div class="text-xs font-bold text-slate-900">{{ auth()->user()->name ?? 'Super Admin' }}</div>
                            <div class="text-[11px] text-slate-400">{{ auth()->user()->role ?? 'Platform Owner' }}</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs text-slate-400 shrink-0" :class="bottomMenuOpen ? 'rotate-90' : ''" x-show="sidebarExpanded"></i>
                </div>

                <!-- Bottom Popup Menu -->
                <div x-show="bottomMenuOpen"
                     @click.outside="bottomMenuOpen = false"
                     x-transition
                     class="mt-2 p-1.5 bg-white border border-slate-200 rounded-xl shadow-lg space-y-1 text-xs">
                    <button wire:click="setTab('settings')" @click="bottomMenuOpen = false" class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-slate-50 rounded-lg text-left">
                        <i class="fa-solid fa-gear text-slate-400"></i>
                        <span>Pengaturan Akun</span>
                    </button>
                    <button wire:click="logout" class="w-full flex items-center gap-2.5 px-3 py-2 text-rose-600 hover:bg-rose-50 rounded-lg text-left font-medium">
                        <i class="fa-solid fa-arrow-right-from-bracket text-rose-500"></i>
                        <span>Keluar (Logout)</span>
                    </button>
                </div>
            </div>
        </aside>
