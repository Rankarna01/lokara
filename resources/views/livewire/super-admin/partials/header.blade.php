            <!-- Top Header Navbar (Matching Image 1) -->
            <header class="h-16 bg-white border-b border-slate-200/80 px-4 lg:px-8 flex items-center justify-between sticky top-0 z-30">
                <div class="flex items-center gap-3">
                    <!-- Mobile Hamburger -->
                    <button wire:click="toggleSidebar" class="lg:hidden text-slate-500 hover:text-slate-800 p-1.5 rounded-lg hover:bg-slate-100">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>

                    <!-- Desktop Sidebar Toggler -->
                    <button type="button" @click="toggleSidebarExpanded()"
                        class="hidden lg:flex w-8 h-8 rounded-lg border border-slate-200 text-slate-500 hover:text-orange-600 hover:bg-orange-50 hover:border-orange-200 items-center justify-center transition shadow-2xs"
                        title="Perkecil / Perbesar Sidebar">
                        <i class="fa-solid fa-bars text-sm" x-show="!sidebarExpanded"></i>
                        <i class="fa-solid fa-bars-staggered text-sm" x-show="sidebarExpanded"></i>
                    </button>

                    <!-- Breadcrumbs -->
                    <div class="flex items-center gap-2 text-sm text-slate-500 font-normal">
                        <span>Platform</span>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-900 font-medium capitalize">{{ str_replace('-', ' ', $activeTab) }}</span>
                    </div>
                </div>

                <!-- Right Header Controls -->
                <div class="flex items-center gap-4">
                    <!-- Search Input -->
                    <div class="relative hidden sm:block w-64 lg:w-72">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text"
                               wire:model.live.debounce.300ms="searchQuery"
                               placeholder="Cari tenant, invoice..."
                               class="w-full bg-slate-50/80 border border-slate-200/90 rounded-lg pl-9 pr-3.5 py-1.5 text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition" />
                    </div>

                    <!-- Notification Bell -->
                    <button class="relative p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-50 transition">
                        <i class="fa-regular fa-bell text-base"></i>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-orange-500"></span>
                    </button>

                    <!-- User Profile Dropdown -->
                    <div class="relative" x-data="{ userMenuOpen: false }">
                        <button @click="userMenuOpen = !userMenuOpen"
                                class="flex items-center gap-2.5 pl-2 border-l border-slate-200 focus:outline-none">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=80&h=80&q=80"
                                 alt="Admin"
                                 class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200" />
                            <span class="hidden md:inline-block text-xs font-semibold text-slate-800">{{ auth()->user()->name ?? 'Admin Lokara' }}</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                        </button>

                        <div x-show="userMenuOpen"
                             @click.outside="userMenuOpen = false"
                             x-transition
                             class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-xl shadow-xl py-1 z-50 text-xs">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <div class="font-bold text-slate-900">{{ auth()->user()->name ?? 'Admin Lokara' }}</div>
                                <div class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email ?? 'admin@lokarapos.id' }}</div>
                            </div>
                            <button wire:click="setTab('settings')" @click="userMenuOpen = false" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-slate-700 flex items-center gap-2">
                                <i class="fa-solid fa-gear text-slate-400"></i>
                                <span>Pengaturan Platform</span>
                            </button>
                            <button wire:click="logout" class="w-full text-left px-4 py-2 hover:bg-rose-50 text-rose-600 flex items-center gap-2 font-medium">
                                <i class="fa-solid fa-arrow-right-from-bracket text-rose-500"></i>
                                <span>Keluar (Logout)</span>
                            </button>
                        </div>
                    </div>
                </div>
            </header>
