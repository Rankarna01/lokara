<!-- ================= TOP HEADER NAVBAR ================= -->
<header class="h-16 bg-white border-b border-slate-200/80 px-4 sm:px-6 flex items-center justify-between flex-shrink-0">
    <!-- Left: Toggler & Breadcrumb -->
    <div class="flex items-center gap-3">
        <button type="button" @click="togglePosSidebar()"
                class="w-8 h-8 rounded-lg border border-slate-200 text-slate-500 hover:text-orange-600 hover:bg-orange-50 hover:border-orange-200 flex items-center justify-center transition shadow-2xs"
                title="Perkecil / Perbesar Sidebar POS">
            <i class="fa-solid fa-bars text-sm" x-show="!sidebarExpanded"></i>
            <i class="fa-solid fa-bars-staggered text-sm" x-show="sidebarExpanded"></i>
        </button>
        <div class="flex items-center gap-2 text-xs text-slate-400 font-normal">
            <span class="hidden sm:inline">POS Terminal</span>
            <span class="text-slate-300 hidden sm:inline">/</span>
            <span class="text-slate-800 font-semibold capitalize">{{ $activeNav }}</span>
        </div>
    </div>

    <!-- Center: Search Input with Barcode Icon & Shift Badge -->
    <div class="flex items-center gap-3">
        <div class="relative w-72 lg:w-84">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text"
                   wire:model.live.debounce.250ms="search"
                   placeholder="Cari menu / scan barcode..."
                   class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-9 pr-9 py-1.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition" />
            <button wire:click="$set('showBarcodeModal', true)"
                    title="Scan Barcode"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-orange-600 transition">
                <i class="fa-solid fa-barcode text-sm"></i>
            </button>
        </div>

        <!-- Shift Status Pill (Khusus non-salon, salon tidak menggunakan shift) -->
        @if (! ($isSalon ?? false) && ! ($tenant?->isSalon()))
            @if ($activeShiftId)
                <button wire:click="openShiftManagement"
                        title="Lihat status shift & kas laci"
                        class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-bold hover:bg-emerald-100 transition shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Shift Aktif: Rp{{ number_format($shiftStartingCash, 0, ',', '.') }}</span>
                </button>
            @else
                <button wire:click="openShiftManagement"
                        title="Sistem kasir ditutup! Buka shift kasir & isi modal awal untuk mulai transaksi"
                        class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-[11px] font-bold hover:bg-rose-100 transition shadow-2xs">
                    <i class="fa-solid fa-lock text-rose-600 text-[10px]"></i>
                    <span>Sistem Ditutup (Belum Buka Shift)</span>
                </button>
            @endif
        @else
            <!-- Badge Salon & Barbershop -->
            <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-orange-50 border border-orange-200 text-orange-700 text-[11px] font-bold">
                <i class="fa-solid fa-scissors text-[10px]"></i>
                <span>Salon & Barbershop POS</span>
            </div>
        @endif
    </div>

    <!-- Right: Outlet Selector, Printer, Bell, Cashier Profile -->
    <div class="flex items-center gap-3">
        <!-- Thermal Printer Quick Action -->
        <button wire:click="$set('showPrinterModal', true)"
                title="Pengaturan Printer Thermal ({{ ucfirst($printerConnectionType) }})"
                class="relative p-2 text-slate-500 hover:text-orange-600 hover:bg-slate-50 rounded-lg transition">
            <i class="fa-solid fa-print text-sm"></i>
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
                        class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-left text-xs hover:bg-slate-50 transition shadow-2xs">
                    <i class="fa-solid fa-location-dot text-orange-500 text-xs"></i>
                    <div>
                        <div class="font-bold text-slate-900 leading-tight">{{ $tenant->name ?? 'Kopi Ruang Pagi' }}</div>
                        <div class="text-[10px] text-slate-400 font-medium">{{ $currentOutlet->name ?? 'Outlet Utama' }}</div>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-1"></i>
                </button>

                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute right-0 mt-1 w-52 bg-white border border-slate-200 rounded-2xl shadow-xl py-1.5 z-30 text-xs">
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
            <!-- Static Badge for Cashier / Staff or Single Outlet (Cannot Switch) -->
            <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-left text-xs shadow-2xs"
                 title="Cabang penugasan akun kasir">
                <i class="fa-solid fa-location-dot text-orange-500 text-xs"></i>
                <div>
                    <div class="font-bold text-slate-900 leading-tight">{{ $tenant->name ?? 'Kopi Ruang Pagi' }}</div>
                    <div class="text-[10px] text-slate-500 font-medium flex items-center gap-1">
                        <span>{{ $currentOutlet->name ?? 'Outlet Utama' }}</span>
                        @if(auth()->check() && in_array(auth()->user()->role, ['cashier', 'staff']))
                            <span class="text-[9px] bg-slate-100 text-slate-600 px-1 rounded font-mono font-bold">Ditugaskan</span>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Notification Bell -->
        <button class="relative p-2 text-slate-400 hover:text-slate-600 transition">
            <i class="fa-regular fa-bell text-sm"></i>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-orange-500"></span>
        </button>

        <!-- Cashier Profile -->
        <div class="flex items-center gap-2.5 pl-3 border-l border-slate-200">
            <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-bold flex items-center justify-center text-xs ring-1 ring-orange-200 shrink-0">
                {{ substr(auth()->user()->name ?? 'Dina Putri', 0, 2) }}
            </div>
            <div class="text-left">
                <div class="text-xs font-bold text-slate-900 leading-tight">{{ auth()->user()->name ?? 'Dina Putri' }}</div>
                <div class="text-[10px] text-slate-400 capitalize">
                    {{ auth()->user()->role ?? 'Kasir' }} • {{ $currentOutlet->name ?? 'Outlet Utama' }}
                </div>
            </div>
            <button wire:click="logout" title="Kunci Kasir / Ganti Akun" class="ml-1 text-slate-400 hover:text-rose-600 transition p-1 rounded-lg hover:bg-rose-50">
                <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
            </button>
        </div>
    </div>
</header>

