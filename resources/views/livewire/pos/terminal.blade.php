<div class="h-screen w-screen overflow-hidden bg-surface flex flex-col antialiased text-dark select-none"
     x-data="{
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

        <!-- 2. CENTER SECTION: CATALOG & PRODUCT GRID -->
        <main class="flex-1 flex flex-col min-w-0 bg-surface overflow-hidden">
            @include('livewire.pos.partials.header')
            @include('livewire.pos.partials.catalog')
        </main>

        <!-- 3. RIGHT PANEL: ORDER CART (PESANAN BARU) -->
        @include('livewire.pos.partials.cart')
    </div>

    <!-- ================= MODALS ================= -->
    @include('livewire.pos.partials.payment-modal')
    @include('livewire.pos.partials.success-modal')
    @include('livewire.pos.partials.variant-modal')
    @include('livewire.pos.partials.barcode-modal')
    @include('livewire.pos.partials.hold-modal')
    @include('livewire.pos.partials.shift-modal')
    @include('livewire.pos.partials.transactions-modal')
    @include('livewire.pos.partials.customer-modal')
    @include('livewire.pos.partials.help-modal')

</div>
