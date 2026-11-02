<!-- ================= TOP HEADER NAVBAR ================= -->
<header class="h-16 bg-white border-b border-slate-200/80 px-4 sm:px-6 flex items-center justify-between flex-shrink-0">
    <!-- Left: Toggler, Breadcrumb & Module Icon -->
    <div class="flex items-center gap-3">
        <button type="button" @click="togglePosSidebar()"
                class="w-8 h-8 rounded-lg border border-slate-200 text-slate-500 hover:text-orange-600 hover:bg-orange-50 hover:border-orange-200 flex items-center justify-center transition shadow-2xs cursor-pointer"
                title="Perkecil / Perbesar Sidebar POS">
            <i class="fa-solid fa-bars text-sm" x-show="!sidebarExpanded"></i>
            <i class="fa-solid fa-bars-staggered text-sm" x-show="sidebarExpanded"></i>
        </button>

        <div class="flex items-center gap-2 text-xs text-slate-400 font-medium">
            <span class="hidden sm:inline">POS Terminal</span>
            <span class="text-slate-300 hidden sm:inline">/</span>
            <span class="text-slate-800 font-bold capitalize">{{ $activeNav }}</span>
        </div>

        <!-- Module Icon Only (Nama Modul di-hide, diganti icon saja agar rapi) -->
        @if ($isSalon ?? false)
            <div title="Modul Salon & Barbershop"
                 class="w-7 h-7 rounded-lg bg-orange-50 border border-orange-200 text-orange-600 flex items-center justify-center text-xs shadow-2xs"
                 title="Modul Salon & Barbershop">
                <i class="fa-solid fa-scissors text-[11px]"></i>
            </div>
        @else
            <div title="Modul F&B / Retail"
                 class="w-7 h-7 rounded-lg bg-orange-50 border border-orange-200 text-orange-600 flex items-center justify-center text-xs shadow-2xs"
                 title="Modul F&B / Retail">
                <i class="fa-solid fa-utensils text-[11px]"></i>
            </div>
        @endif

        <!-- Shift Active Status Indicator (Khusus Non-Salon) -->
        @if (! ($isSalon ?? false) && ! ($tenant?->isSalon()))
            @if ($activeShiftId)
                <button wire:click="openShiftManagement"
                        title="Shift Kasir Aktif: Rp{{ number_format($shiftStartingCash, 0, ',', '.') }}"
                        class="hidden md:flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-bold hover:bg-emerald-100 transition shadow-2xs cursor-pointer">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Shift Aktif</span>
                </button>
            @endif
        @endif
    </div>

    <!-- Right: Quick Actions, Outlet Selector & Profile Dropdown ke Pages -->
    <div class="flex items-center gap-2.5 sm:gap-3">
        <!-- Thermal Printer Quick Action -->
        <button wire:click="$set('showPrinterModal', true)"
                title="Pengaturan Printer Thermal ({{ ucfirst($printerConnectionType) }})"
                class="relative p-2 text-slate-500 hover:text-orange-600 hover:bg-slate-50 rounded-xl border border-slate-200/80 transition shadow-2xs cursor-pointer">
            <i class="fa-solid fa-print text-xs"></i>
            <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-white"></span>
        </button>

        <!-- Outlet Selector / Badge -->
        @php
            $canSwitchOutlet = auth()->check() 
                ? in_array(auth()->user()->role, ['owner', 'manager', 'super_admin'])
                : true;
        @endphp

        @if ($canSwitchOutlet && $outlets->count() > 1)
            <!-- Dropdown for Owner / Manager with multiple outlets -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-left text-xs hover:bg-slate-50 transition shadow-2xs cursor-pointer">
                    <i class="fa-solid fa-location-dot text-orange-500 text-xs"></i>
                    <div>
                        <div class="font-bold text-slate-900 leading-tight">{{ $tenant->name ?? 'Lokara' }}</div>
                        <div class="text-[10px] text-slate-400 font-medium">{{ $currentOutlet->name ?? 'Outlet Utama' }}</div>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-0.5"></i>
                </button>

                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute right-0 mt-1 w-52 bg-white border border-slate-200 rounded-2xl shadow-xl py-1.5 z-40 text-xs">
                    <div class="px-3 py-1 text-[10px] font-bold uppercase text-slate-400 tracking-wider border-b border-slate-100">
                        Pilih Cabang POS:
                    </div>
                    @foreach ($outlets as $out)
                        <button wire:click="switchOutlet({{ $out->id }})" @click="open = false"
                                class="w-full text-left px-3 py-2 hover:bg-orange-50 flex items-center justify-between transition {{ $selectedOutletId === $out->id ? 'text-orange-600 font-bold bg-orange-50/50' : 'text-slate-700 font-medium' }}">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-store text-xs {{ $selectedOutletId === $out->id ? 'text-orange-500' : 'text-slate-400' }}"></i>
                                <span>{{ $out->name }}</span>
                            </div>
                            @if ($selectedOutletId === $out->id)
                                <i class="fa-solid fa-check text-xs text-orange-600"></i>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Static Badge for Cashier / Staff or Single Outlet -->
            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-left text-xs shadow-2xs"
                 title="Cabang penugasan akun kasir">
                <i class="fa-solid fa-location-dot text-orange-500 text-xs"></i>
                <div>
                    <div class="font-bold text-slate-900 leading-tight">{{ $tenant->name ?? 'Lokara' }}</div>
                    <div class="text-[10px] text-slate-500 font-medium">{{ $currentOutlet->name ?? 'Outlet Utama' }}</div>
                </div>
            </div>
        @endif

        <!-- ================= USER PROFILE DI UJUNG DENGAN NAVIGASI PAGES ================= -->
        @php
            $currentUser = auth()->user();
            $isManagement = $currentUser && in_array($currentUser->role, ['owner', 'manager', 'super_admin']);
            $userRoleLabel = match($currentUser?->role) {
                'super_admin' => 'Super Admin',
                'owner' => 'Owner',
                'manager' => 'Manajer',
                'cashier' => 'Kasir',
                'staff' => 'Staf',
                default => 'Kasir'
            };
        @endphp

        <div class="relative" x-data="{ profileOpen: false }">
            <button @click="profileOpen = !profileOpen"
                    type="button"
                    class="flex items-center gap-2 pl-2 pr-1.5 py-1 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300 transition shadow-2xs cursor-pointer group">
                <div class="w-7 h-7 rounded-lg bg-orange-100 text-orange-600 font-bold flex items-center justify-center text-xs ring-1 ring-orange-200 shrink-0">
                    {{ strtoupper(substr($currentUser?->name ?? 'Dina', 0, 2)) }}
                </div>
                <div class="text-left hidden md:block">
                    <div class="text-xs font-bold text-slate-800 leading-tight group-hover:text-orange-600 transition truncate max-w-[110px]">
                        {{ $currentUser?->name ?? 'Dina Putri' }}
                    </div>
                    <div class="text-[10px] text-slate-400 font-medium">
                        {{ $userRoleLabel }}
                    </div>
                </div>
                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 group-hover:text-slate-600 transition ml-0.5"></i>
            </button>

            <!-- Dropdown Menu Profil Ringkas (Tanpa Menu Halaman) -->
            <div x-show="profileOpen"
                 @click.outside="profileOpen = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-2xl shadow-xl p-2 z-50 text-xs">
                
                <!-- Identitas Profil -->
                <div class="px-3 py-2 bg-slate-50/80 rounded-xl border border-slate-100 mb-1.5">
                    <div class="font-bold text-slate-900 text-xs">{{ $currentUser?->name ?? 'Dina Putri' }}</div>
                    <div class="text-[11px] text-slate-400 truncate">{{ $currentUser?->email ?? 'kasir@lokara.id' }}</div>
                    <div class="flex items-center gap-1.5 mt-1.5">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 text-orange-700">
                            {{ $userRoleLabel }}
                        </span>
                        <span class="text-[10px] text-slate-400 truncate">• {{ $currentOutlet->name ?? 'Outlet Utama' }}</span>
                    </div>
                </div>

                <!-- Keluar / Logout -->
                <button type="button"
                        wire:click="logout"
                        class="w-full text-left flex items-center gap-2 px-3 py-2 text-rose-600 hover:bg-rose-50 rounded-xl transition font-semibold cursor-pointer">
                    <i class="fa-solid fa-arrow-right-from-bracket text-xs w-4 text-center"></i>
                    <span>Kunci Kasir / Ganti Akun</span>
                </button>
            </div>
        </div>
    </div>
</header>
