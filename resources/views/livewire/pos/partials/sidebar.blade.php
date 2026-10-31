<!-- ================= 1. LEFT SIDEBAR (POS NAVIGATION) ================= -->
<aside class="bg-white border-r border-slate-200/80 flex flex-col justify-between flex-shrink-0 transition-all duration-300 ease-in-out select-none"
       :class="sidebarExpanded ? 'w-56' : 'w-20'">
    <div>
        <!-- Brand Logo (Orange Icon + Lokara POS badge) -->
        <div class="h-16 flex items-center border-b border-slate-100 transition-all duration-300"
             :class="sidebarExpanded ? 'justify-between px-5' : 'justify-center px-2'">
            <div class="flex items-center gap-2 overflow-hidden" :title="sidebarExpanded ? '' : 'Lokara POS'">
                <div class="w-8 h-8 rounded-lg bg-orange-500 flex items-center justify-center text-white shadow-sm shadow-orange-500/20 shrink-0">
                    <i class="fa-solid fa-shapes text-sm"></i>
                </div>
                <div class="flex items-center" x-show="sidebarExpanded" x-transition.opacity.duration.200ms>
                    <span class="text-lg font-bold tracking-tight text-slate-900">Lokara</span>
                    <span class="ml-1.5 px-1.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-orange-500 text-white tracking-wider">POS</span>
                </div>
            </div>

            <!-- Collapse Button inside header -->
            <button type="button" @click="togglePosSidebar()" x-show="sidebarExpanded"
                class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                title="Perkecil Sidebar">
                <i class="fa-solid fa-angles-left text-xs"></i>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="p-2.5 space-y-1 text-xs">
            <!-- Katalog -->
            <button wire:click="$set('activeNav', 'katalog')"
                    class="w-full flex items-center rounded-lg font-medium transition-colors text-left {{ $activeNav === 'katalog' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
                    :class="sidebarExpanded ? 'px-3.5 py-2.5 gap-3' : 'px-2 py-2.5 justify-center'"
                    title="Katalog Produk">
                <i class="fa-solid fa-shapes text-sm w-4 text-center shrink-0 {{ $activeNav === 'katalog' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                <span x-show="sidebarExpanded" x-transition.opacity.duration.200ms class="truncate">Katalog</span>
            </button>

            <!-- Pesanan -->
            <button wire:click="$set('activeNav', 'pesanan')"
                    class="w-full flex items-center rounded-lg font-medium transition-colors text-left relative {{ $activeNav === 'pesanan' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}"
                    :class="sidebarExpanded ? 'px-3.5 py-2.5 gap-3' : 'px-2 py-2.5 justify-center'"
                    title="Daftar Pesanan">
                <i class="fa-solid fa-clipboard-list text-sm w-4 text-center shrink-0 {{ $activeNav === 'pesanan' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                <span x-show="sidebarExpanded" x-transition.opacity.duration.200ms class="truncate">Pesanan</span>
                @if (count($heldOrders) > 0)
                    <span x-show="sidebarExpanded" class="ml-auto bg-orange-500 text-white text-[10px] font-bold px-1.5 py-0.2 rounded-full">{{ count($heldOrders) }}</span>
                    <span x-show="!sidebarExpanded" class="absolute top-1.5 right-2 w-2 h-2 rounded-full bg-orange-500"></span>
                @endif
            </button>

            <!-- Shift & Kas -->
            <button wire:click="openShiftManagement"
                    class="w-full flex items-center rounded-lg font-medium transition-colors text-left relative text-slate-600 hover:text-slate-900 hover:bg-slate-50"
                    :class="sidebarExpanded ? 'px-3.5 py-2.5 gap-3' : 'px-2 py-2.5 justify-center'"
                    title="Shift Kasir & Kas Laci">
                <i class="fa-solid fa-cash-register text-sm w-4 text-center shrink-0 text-slate-400"></i>
                <span x-show="sidebarExpanded" x-transition.opacity.duration.200ms class="truncate">Shift & Kas</span>
                @if ($activeShiftId)
                    <span x-show="sidebarExpanded" class="ml-auto w-2 h-2 rounded-full bg-emerald-500" title="Shift Aktif"></span>
                    <span x-show="!sidebarExpanded" class="absolute top-1.5 right-2 w-2 h-2 rounded-full bg-emerald-500"></span>
                @else
                    <span x-show="sidebarExpanded" class="ml-auto text-[9px] bg-amber-100 text-amber-700 px-1.5 py-0.2 rounded font-bold">Buka</span>
                    <span x-show="!sidebarExpanded" class="absolute top-1.5 right-2 w-2 h-2 rounded-full bg-amber-500"></span>
                @endif
            </button>

            <!-- Transaksi -->
            <button wire:click="$set('showTransactionsModal', true)"
                    class="w-full flex items-center rounded-lg font-medium transition-colors text-left text-slate-600 hover:text-slate-900 hover:bg-slate-50"
                    :class="sidebarExpanded ? 'px-3.5 py-2.5 gap-3' : 'px-2 py-2.5 justify-center'"
                    title="Riwayat Transaksi POS">
                <i class="fa-solid fa-receipt text-sm w-4 text-center shrink-0 text-slate-400"></i>
                <span x-show="sidebarExpanded" x-transition.opacity.duration.200ms class="truncate">Transaksi</span>
            </button>

            <!-- Pelanggan -->
            <button wire:click="$set('showCustomerModal', true)"
                    class="w-full flex items-center rounded-lg font-medium transition-colors text-left relative text-slate-600 hover:text-slate-900 hover:bg-slate-50"
                    :class="sidebarExpanded ? 'px-3.5 py-2.5 gap-3' : 'px-2 py-2.5 justify-center'"
                    title="Pelanggan Member POS">
                <i class="fa-solid fa-user-group text-sm w-4 text-center shrink-0 text-slate-400"></i>
                <span x-show="sidebarExpanded" x-transition.opacity.duration.200ms class="truncate">Pelanggan</span>
                @if ($selectedCustomer)
                    <span x-show="sidebarExpanded" class="ml-auto w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span x-show="!sidebarExpanded" class="absolute top-1.5 right-2 w-2 h-2 rounded-full bg-emerald-500"></span>
                @endif
            </button>

            <!-- Printer Struk Thermal -->
            <button wire:click="$set('showPrinterModal', true)"
                    class="w-full flex items-center rounded-lg font-medium transition-colors text-left text-slate-600 hover:text-slate-900 hover:bg-slate-50"
                    :class="sidebarExpanded ? 'px-3.5 py-2.5 gap-3' : 'px-2 py-2.5 justify-center'"
                    title="Pengaturan Printer Thermal ({{ $printerConnectionType }})">
                <i class="fa-solid fa-print text-sm w-4 text-center shrink-0 text-slate-400"></i>
                <span x-show="sidebarExpanded" x-transition.opacity.duration.200ms class="truncate">Printer Struk</span>
                <span x-show="sidebarExpanded" class="ml-auto text-[9px] font-bold uppercase px-1.5 py-0.5 rounded {{ $printerConnectionType === 'bluetooth' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600' }}">
                    {{ $printerConnectionType }}
                </span>
            </button>

            <!-- Bantuan -->
            <button wire:click="$set('showHelpModal', true)"
                    class="w-full flex items-center rounded-lg font-medium transition-colors text-left text-slate-600 hover:text-slate-900 hover:bg-slate-50"
                    :class="sidebarExpanded ? 'px-3.5 py-2.5 gap-3' : 'px-2 py-2.5 justify-center'"
                    title="Bantuan & Panduan Cepat">
                <i class="fa-regular fa-circle-question text-sm w-4 text-center shrink-0 text-slate-400"></i>
                <span x-show="sidebarExpanded" x-transition.opacity.duration.200ms class="truncate">Bantuan</span>
            </button>
        </nav>
    </div>

    <!-- Bottom Back to Admin / Logout Link -->
    <div class="p-2.5 border-t border-slate-100 space-y-1">
        @if(!auth()->check() || auth()->user()->canAccessBackoffice())
            <a href="{{ route('tenant.dashboard') }}"
               class="w-full flex items-center text-xs text-orange-600 hover:bg-orange-50 rounded-lg transition font-semibold"
               :class="sidebarExpanded ? 'px-3.5 py-2 gap-3' : 'p-2.5 justify-center'"
               title="Ke Dashboard Back Office">
                <i class="fa-solid fa-chart-pie text-sm w-4 text-center shrink-0"></i>
                <span x-show="sidebarExpanded" x-transition.opacity.duration.200ms class="truncate">Back Office Toko</span>
            </a>
        @endif
        <button wire:click="logout"
                class="w-full flex items-center text-xs text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition font-medium"
                :class="sidebarExpanded ? 'px-3.5 py-2 gap-3' : 'p-2.5 justify-center'"
                title="Keluar / Ganti Akun">
            <i class="fa-solid fa-arrow-right-from-bracket text-sm w-4 text-center shrink-0"></i>
            <span x-show="sidebarExpanded" x-transition.opacity.duration.200ms class="truncate">Keluar / Ganti Akun</span>
        </button>
    </div>
</aside>
