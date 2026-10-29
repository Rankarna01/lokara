<!-- ================= TOP HEADER NAVBAR ================= -->
<header class="h-16 bg-white border-b border-slate-200/80 px-6 flex items-center justify-between flex-shrink-0">
    <!-- Left: Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-slate-400 font-normal">
        <span>POS Terminal</span>
        <span class="text-slate-300">/</span>
        <span class="text-slate-800 font-semibold capitalize">{{ $activeNav }}</span>
    </div>

    <!-- Center: Search Input with Barcode Icon -->
    <div class="relative w-80 lg:w-96">
        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
        <input type="text"
               wire:model.live.debounce.250ms="search"
               placeholder="Cari nama menu atau scan barcode..."
               class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-9 pr-9 py-1.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition" />
        <button wire:click="$set('showBarcodeModal', true)"
                title="Scan Barcode"
                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-orange-600 transition">
            <i class="fa-solid fa-barcode text-sm"></i>
        </button>
    </div>

    <!-- Right: Outlet Selector, Bell, Cashier Profile -->
    <div class="flex items-center gap-4">
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

        <!-- Cashier Profile (Dina Putri - Kasir) -->
        <div class="flex items-center gap-2.5 pl-3 border-l border-slate-200">
            <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=80&h=80&q=80"
                 alt="Cashier"
                 class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200" />
            <div class="text-left">
                <div class="text-xs font-bold text-slate-900 leading-tight">Dina Putri</div>
                <div class="text-[10px] text-slate-400">Kasir</div>
            </div>
        </div>
    </div>
</header>

