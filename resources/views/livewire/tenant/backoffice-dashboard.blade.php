<div class="min-h-screen bg-slate-50 flex flex-col antialiased text-slate-800" x-data="{ sidebarOpen: true }">
    <!-- TOAST NOTIFICATION -->
    @include('livewire.tenant.partials.toast')

    <div class="flex flex-1">
        <!-- SIDEBAR -->
        @include('livewire.tenant.partials.sidebar')

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- TOP NAVBAR -->
            @include('livewire.tenant.partials.header')

            <!-- MAIN TAB BODY -->
            <main class="flex-1 p-6 overflow-y-auto">
                @if ($activeTab === 'dashboard')
                    @include('livewire.tenant.partials.tabs.dashboard')
                @elseif ($activeTab === 'products')
                    @include('livewire.tenant.partials.tabs.products')
                @elseif ($activeTab === 'services')
                    @include('livewire.tenant.partials.tabs.services')
                @elseif ($activeTab === 'recipes')
                    @include('livewire.tenant.partials.tabs.recipes')
                @elseif ($activeTab === 'stock')
                    @include('livewire.tenant.partials.tabs.stock')
                @elseif ($activeTab === 'purchases')
                    @include('livewire.tenant.partials.tabs.purchases')
                @elseif ($activeTab === 'transactions')
                    @include('livewire.tenant.partials.tabs.transactions')
                @elseif ($activeTab === 'discounts')
                    @include('livewire.tenant.partials.tabs.discounts')
                @elseif ($activeTab === 'finance')
                    @include('livewire.tenant.partials.tabs.finance')
                @elseif ($activeTab === 'crm')
                    @include('livewire.tenant.partials.tabs.crm')
                @elseif ($activeTab === 'settings')
                    @include('livewire.tenant.partials.tabs.settings')
                @endif
            </main>
        </div>
    </div>

    <!-- ==================== MODALS ==================== -->
    @include('livewire.tenant.partials.modals.index')
</div>
