<div class="min-h-screen bg-surface flex flex-col antialiased text-dark" 
     x-data="{ 
         sidebarExpanded: (localStorage.getItem('sa_sidebar_expanded') !== 'false'),
         toggleSidebarExpanded() {
             this.sidebarExpanded = !this.sidebarExpanded;
             localStorage.setItem('sa_sidebar_expanded', this.sidebarExpanded);
         },
         userMenuOpen: false, 
         dateDropdownOpen: false 
     }">

    <!-- Toast Notification -->
    @include('livewire.super-admin.partials.toast')

    <div class="flex-1 flex overflow-hidden">

        <!-- Sidebar Navigation -->
        @include('livewire.super-admin.partials.sidebar')

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

            <!-- Top Header Navbar -->
            @include('livewire.super-admin.partials.header')

            <!-- Main Body Content -->
            <main class="flex-1 p-4 lg:p-8 space-y-6">
                @if ($activeTab === 'dashboard')
                    @include('livewire.super-admin.partials.tabs.dashboard')
                @elseif ($activeTab === 'tenants')
                    @include('livewire.super-admin.partials.tabs.tenants')
                @elseif ($activeTab === 'tenant-users')
                    @include('livewire.super-admin.partials.tabs.tenant-users')
                @elseif ($activeTab === 'plans')
                    @include('livewire.super-admin.partials.tabs.plans')
                @elseif ($activeTab === 'invoices')
                    @include('livewire.super-admin.partials.tabs.invoices')
                @elseif ($activeTab === 'business-types')
                    @include('livewire.super-admin.partials.tabs.business-types')
                @elseif ($activeTab === 'settings')
                    @include('livewire.super-admin.partials.tabs.settings')
                @elseif ($activeTab === 'support')
                    @include('livewire.super-admin.partials.tabs.support')
                @elseif ($activeTab === 'history-cleanup')
                    @include('livewire.super-admin.partials.tabs.history-cleanup')
                @elseif ($activeTab === 'monitoring')
                    @include('livewire.super-admin.partials.tabs.monitoring')
                @endif
            </main>
        </div>
    </div>

    <!-- Modals -->
    @include('livewire.super-admin.partials.modals.index')

</div>

<!-- Chart.js Scripts Initialization -->
@include('livewire.super-admin.partials.scripts')
