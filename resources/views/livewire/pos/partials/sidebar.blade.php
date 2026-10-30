<!-- ================= 1. LEFT SIDEBAR (POS NAVIGATION) ================= -->
<aside class="w-56 bg-white border-r border-slate-200/80 flex flex-col justify-between flex-shrink-0">
    <div>
        <!-- Brand Logo (Orange Icon + Lokara POS badge) -->
        <div class="h-16 flex items-center px-5 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-orange-500 flex items-center justify-center text-white shadow-sm shadow-orange-500/20">
                    <i class="fa-solid fa-shapes text-sm"></i>
                </div>
                <div class="flex items-center">
                    <span class="text-lg font-bold tracking-tight text-slate-900">Lokara</span>
                    <span class="ml-1.5 px-1.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-orange-500 text-white tracking-wider">POS</span>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="p-3 space-y-1 text-xs">
            <!-- Katalog -->
            <button wire:click="$set('activeNav', 'katalog')"
                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg font-medium transition-colors text-left {{ $activeNav === 'katalog' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <i class="fa-solid fa-shapes text-sm w-4 text-center {{ $activeNav === 'katalog' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                <span>Katalog</span>
            </button>

            <!-- Pesanan -->
            <button wire:click="$set('activeNav', 'pesanan')"
                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg font-medium transition-colors text-left {{ $activeNav === 'pesanan' ? 'bg-orange-50 text-orange-600 font-semibold border-l-4 border-orange-500 rounded-l-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <i class="fa-solid fa-clipboard-list text-sm w-4 text-center {{ $activeNav === 'pesanan' ? 'text-orange-500' : 'text-slate-400' }}"></i>
                <span>Pesanan</span>
                @if (count($heldOrders) > 0)
                    <span class="ml-auto bg-orange-500 text-white text-[10px] font-bold px-1.5 py-0.2 rounded-full">{{ count($heldOrders) }}</span>
                @endif
            </button>

            <!-- Shift & Kas -->
            <button wire:click="openShiftManagement"
                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg font-medium transition-colors text-left text-slate-600 hover:text-slate-900 hover:bg-slate-50">
                <i class="fa-solid fa-cash-register text-sm w-4 text-center text-slate-400"></i>
                <span>Shift & Kas</span>
                @if ($activeShiftId)
                    <span class="ml-auto w-2 h-2 rounded-full bg-emerald-500" title="Shift Aktif"></span>
                @else
                    <span class="ml-auto text-[9px] bg-amber-100 text-amber-700 px-1.5 py-0.2 rounded font-bold">Buka</span>
                @endif
            </button>

            <!-- Transaksi -->
            <button wire:click="$set('showTransactionsModal', true)"
                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg font-medium transition-colors text-left text-slate-600 hover:text-slate-900 hover:bg-slate-50">
                <i class="fa-solid fa-receipt text-sm w-4 text-center text-slate-400"></i>
                <span>Transaksi</span>
            </button>

            <!-- Pelanggan -->
            <button wire:click="$set('showCustomerModal', true)"
                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg font-medium transition-colors text-left text-slate-600 hover:text-slate-900 hover:bg-slate-50">
                <i class="fa-solid fa-user-group text-sm w-4 text-center text-slate-400"></i>
                <span>Pelanggan</span>
                @if ($selectedCustomer)
                    <span class="ml-auto w-2 h-2 rounded-full bg-emerald-500"></span>
                @endif
            </button>

            <!-- Printer Struk Thermal -->
            <button wire:click="$set('showPrinterModal', true)"
                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg font-medium transition-colors text-left text-slate-600 hover:text-slate-900 hover:bg-slate-50">
                <i class="fa-solid fa-print text-sm w-4 text-center text-slate-400"></i>
                <span>Printer Struk</span>
                <span class="ml-auto text-[9px] font-bold uppercase px-1.5 py-0.5 rounded {{ $printerConnectionType === 'bluetooth' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600' }}">
                    {{ $printerConnectionType }}
                </span>
            </button>

            <!-- Bantuan -->
            <button wire:click="$set('showHelpModal', true)"
                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg font-medium transition-colors text-left text-slate-600 hover:text-slate-900 hover:bg-slate-50">
                <i class="fa-regular fa-circle-question text-sm w-4 text-center text-slate-400"></i>
                <span>Bantuan</span>
            </button>
        </nav>
    </div>

    <!-- Bottom Back to Admin / Logout Link -->
    <div class="p-3 border-t border-slate-100 space-y-1">
        @if(!auth()->check() || auth()->user()->canAccessBackoffice())
            <a href="{{ route('tenant.dashboard') }}"
               class="w-full flex items-center gap-3 px-3.5 py-2 text-xs text-orange-600 hover:bg-orange-50 rounded-lg transition font-semibold">
                <i class="fa-solid fa-chart-pie text-sm w-4 text-center"></i>
                <span>Back Office Toko</span>
            </a>
        @endif
        <button wire:click="logout"
                class="w-full flex items-center gap-3 px-3.5 py-2 text-xs text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition font-medium">
            <i class="fa-solid fa-arrow-right-from-bracket text-sm w-4 text-center"></i>
            <span>Keluar / Ganti Akun</span>
        </button>
    </div>
</aside>

