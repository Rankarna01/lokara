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

        <!-- ================= SIDEBAR (Matching Image 1 & 2) ================= -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200/80 flex flex-col justify-between transition-transform duration-200 lg:static lg:translate-x-0"
               :class="$wire.sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

            <div>
                <!-- Brand Logo (Orange Icon + Lokara POS badge) -->
                <div class="h-16 flex items-center px-6 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-orange-500 flex items-center justify-center text-white shadow-sm shadow-orange-500/20">
                            <!-- Custom Hexagon / Layers Icon -->
                            <i class="fa-solid fa-cubes text-sm"></i>
                        </div>
                        <div class="flex items-center">
                            <span class="text-xl font-bold tracking-tight text-slate-900">Lokara</span>
                            <span class="ml-1.5 px-1.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-orange-500 text-white tracking-wider">POS</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation List -->
                <div class="px-3 py-4 space-y-6">
                    <div>
                        <div class="px-3 pb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            Platform
                        </div>
                        <nav class="space-y-1">
                            <!-- Dashboard -->
                            <button wire:click="setTab('dashboard')"
                                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'dashboard' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <i class="fa-solid fa-house text-base w-5 text-center {{ $activeTab === 'dashboard' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>Dashboard</span>
                            </button>

                            <!-- Tenant -->
                            <button wire:click="setTab('tenants')"
                                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'tenants' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <i class="fa-solid fa-store text-base w-5 text-center {{ $activeTab === 'tenants' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>Tenant</span>
                            </button>

                            <!-- Akun Tenant -->
                            <button wire:click="setTab('tenant-users')"
                                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'tenant-users' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <i class="fa-solid fa-users text-base w-5 text-center {{ $activeTab === 'tenant-users' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>Akun Tenant</span>
                            </button>

                            <!-- Paket & Langganan -->
                            <button wire:click="setTab('plans')"
                                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'plans' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <i class="fa-solid fa-credit-card text-base w-5 text-center {{ $activeTab === 'plans' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>Paket & Langganan</span>
                            </button>

                            <!-- Billing & Invoice -->
                            <button wire:click="setTab('invoices')"
                                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'invoices' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <i class="fa-solid fa-file-invoice-dollar text-base w-5 text-center {{ $activeTab === 'invoices' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>Billing & Invoice</span>
                            </button>

                            <!-- Template Usaha -->
                            <button wire:click="setTab('business-types')"
                                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'business-types' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <i class="fa-solid fa-shapes text-base w-5 text-center {{ $activeTab === 'business-types' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>Template Usaha</span>
                            </button>

                            <!-- Pengaturan Global -->
                            <button wire:click="setTab('settings')"
                                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'settings' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <i class="fa-solid fa-gear text-base w-5 text-center {{ $activeTab === 'settings' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>Pengaturan Global</span>
                            </button>

                            <!-- Support & Pengumuman -->
                            <button wire:click="setTab('support')"
                                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'support' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <i class="fa-solid fa-bullhorn text-base w-5 text-center {{ $activeTab === 'support' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>Support & Pengumuman</span>
                            </button>

                            <!-- Monitoring & Audit -->
                            <button wire:click="setTab('monitoring')"
                                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors text-left {{ $activeTab === 'monitoring' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <i class="fa-solid fa-shield-halved text-base w-5 text-center {{ $activeTab === 'monitoring' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>Monitoring & Audit</span>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Bottom User Profile Card (Matching Image 1) -->
            <div class="p-3 border-t border-slate-100" x-data="{ bottomMenuOpen: false }">
                <div @click="bottomMenuOpen = !bottomMenuOpen"
                     class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-semibold text-sm">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div class="text-left">
                            <div class="text-xs font-bold text-slate-900">{{ auth()->user()->name ?? 'Super Admin' }}</div>
                            <div class="text-[11px] text-slate-400">{{ auth()->user()->role ?? 'Platform Owner' }}</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs text-slate-400" :class="bottomMenuOpen ? 'rotate-90' : ''"></i>
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
