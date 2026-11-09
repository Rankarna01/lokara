<div class="h-screen w-screen overflow-hidden bg-surface flex flex-col antialiased text-dark select-none"
     @open-pos-receipt.window="window.open($event.detail.url, '_blank')"
     x-data="{
         sidebarExpanded: (localStorage.getItem('pos_sidebar_expanded') !== 'false'),
         togglePosSidebar() {
             this.sidebarExpanded = !this.sidebarExpanded;
             localStorage.setItem('pos_sidebar_expanded', this.sidebarExpanded);
         },
         outletDropdownOpen: false,
         tableDropdownOpen: false,
         cartMenuOpen: false
     }">

    <!-- Toast Notification -->
    @if ($toastMessage)
        <div class="fixed top-4 right-4 z-50 flex items-center gap-3 bg-white border border-slate-200 px-4 py-2.5 rounded-xl shadow-lg animate-bounce"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false; $wire.clearToast() }, 3000)">
            <div class="w-7 h-7 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center flex-shrink-0 text-xs">
                <i class="fa-solid fa-bell"></i>
            </div>
            <div class="text-xs font-semibold text-slate-800">{{ $toastMessage }}</div>
            <button wire:click="clearToast" class="text-slate-400 hover:text-slate-600 ml-1">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>
    @endif

    <div class="flex-1 flex overflow-hidden">
        <!-- 1. LEFT SIDEBAR (POS SPECIFIC NAVIGATION) -->
        @include('livewire.pos.partials.sidebar')

        <!-- 2. WORKSPACE CONTAINER (HEADER + SCROLLABLE AREA) -->
        <div class="flex-1 flex flex-col min-w-0 bg-surface overflow-hidden">
            @include('livewire.pos.partials.header')

            <!-- SCROLLABLE WORKSPACE: HORIZONTAL ON DESKTOP (CART ON RIGHT), VERTICAL ON TABLET / RESPONSIVE (CART BELOW) -->
            <div class="flex-1 flex flex-col xl:flex-row overflow-y-auto xl:overflow-hidden min-w-0">
                <!-- CENTER SECTION: CATALOG & PRODUCT GRID / ORDERS VIEW -->
                <main class="flex-1 flex flex-col min-w-0 bg-surface xl:overflow-y-auto">
                    @if ($activeNav === 'pesanan')
                        @include('livewire.pos.partials.orders-view')
                    @else
                        @include('livewire.pos.partials.catalog')
                    @endif
                </main>

                <!-- 3. ORDER CART (PESANAN BARU): SIDEBAR ON XL, BELOW CATALOG ON TABLET -->
                @include('livewire.pos.partials.cart')
            </div>
        </div>
    </div>

    <!-- Floating Quick Cart Bar for Tablet / Mobile (< xl) -->
    <div class="xl:hidden fixed bottom-4 inset-x-4 z-40 max-w-lg mx-auto bg-slate-900/95 backdrop-blur-md text-white px-4 py-3 rounded-2xl shadow-2xl border border-slate-800 flex items-center justify-between animate-in fade-in slide-in-from-bottom-2 duration-200">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-orange-500 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
            <div>
                <div class="text-xs font-bold">{{ array_sum(array_column($cart, 'qty')) }} Item Pesanan</div>
                <div class="text-[11px] text-orange-400 font-semibold">Total: Rp{{ number_format($totals['total'], 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button"
                    @click="document.getElementById('pos-cart-panel')?.scrollIntoView({ behavior: 'smooth' })"
                    class="px-3 py-1.5 bg-white/10 hover:bg-white/20 text-white font-semibold text-xs rounded-xl transition flex items-center gap-1.5">
                <i class="fa-solid fa-chevron-down text-xs"></i>
                <span>Lihat Keranjang</span>
            </button>
            @if (! $activeShiftId && ! ($isSalon ?? false) && ! ($tenant?->isSalon()))
                <button type="button" wire:click="$set('showOpenShiftModal', true)"
                        class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-xl shadow-xs transition">
                    Buka Shift
                </button>
            @else
                <button type="button" wire:click="openPaymentModal"
                        class="px-4 py-1.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1">
                    <span>Bayar</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </button>
            @endif
        </div>
    </div>

    <!-- ================= MODALS ================= -->
    @if (! ($isSalon ?? false) && ! ($tenant?->isSalon()))
        @include('livewire.pos.partials.open-shift-modal')
        @include('livewire.pos.partials.shift-modal')
    @endif
    @include('livewire.pos.partials.printer-modal')
    @include('livewire.pos.partials.payment-modal')
    @include('livewire.pos.partials.success-modal')
    @include('livewire.pos.partials.variant-modal')
    @include('livewire.pos.partials.barcode-modal')
    @include('livewire.pos.partials.hold-modal')
    @include('livewire.pos.partials.transactions-modal')
    @include('livewire.pos.partials.customer-modal')
    @include('livewire.pos.partials.expense-modal')
    @include('livewire.pos.partials.help-modal')

</div>
