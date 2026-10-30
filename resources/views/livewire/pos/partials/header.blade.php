<!-- ================= TOP HEADER NAVBAR ================= -->
<header class="h-16 bg-white border-b border-slate-200/80 px-6 flex items-center justify-between flex-shrink-0">
    <!-- Left: Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-slate-400 font-normal">
        <span>POS Terminal</span>
        <span class="text-slate-300">/</span>
        <span class="text-slate-800 font-semibold capitalize">{{ $activeNav }}</span>
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

        <!-- Shift Status Pill -->
        @if ($activeShiftId)
            <button wire:click="openShiftManagement"
                    title="Lihat status shift & kas laci"
                    class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-bold hover:bg-emerald-100 transition">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Shift Aktif: Rp{{ number_format($shiftStartingCash, 0, ',', '.') }}</span>
            </button>
        @else
            <button wire:click="$set('showOpenShiftModal', true)"
                    title="Mulai shift kasir & isi modal awal"
                    class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-[11px] font-bold hover:bg-amber-100 transition">
                <i class="fa-solid fa-lock text-amber-600 text-[10px]"></i>
                <span>Buka Kasir Baru</span>
            </button>
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

        <!-- Outlet Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-left text-xs hover:bg-slate-50 transition">
                <i class="fa-solid fa-location-dot text-slate-500 text-xs"></i>
                <div>
                    <div class="font-bold text-slate-900 leading-tight">{{ $tenant->name ?? 'Kopi Ruang Pagi' }}</div>
                    <div class="text-[10px] text-slate-400">{{ $currentOutlet->name ?? 'Outlet Utama' }}</div>
                </div>
                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-1"></i>
            </button>

            <div x-show="open" @click.outside="open = false" x-transition
                 class="absolute right-0 mt-1 w-48 bg-white border border-slate-200 rounded-xl shadow-lg py-1 z-30 text-xs">
                @foreach ($outlets as $out)
                    <button wire:click="$set('selectedOutletId', {{ $out->id }})" @click="open = false"
                            class="w-full text-left px-3 py-2 hover:bg-slate-50 flex items-center justify-between {{ $selectedOutletId === $out->id ? 'text-orange-600 font-semibold' : 'text-slate-700' }}">
                        <span>{{ $out->name }}</span>
                        @if ($selectedOutletId === $out->id)
                            <i class="fa-solid fa-check text-orange-500 text-[10px]"></i>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

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

