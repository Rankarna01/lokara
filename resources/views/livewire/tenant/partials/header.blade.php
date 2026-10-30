            <!-- TOP NAVBAR -->
            <header class="h-16 bg-white border-b border-slate-200/80 px-6 flex items-center justify-between shrink-0">
                <!-- Breadcrumbs -->
                <div class="flex items-center gap-2 text-sm text-slate-500">
                    <span class="font-medium text-slate-700">{{ $tenant->name ?? 'Kopi Ruang Pagi' }}</span>
                    <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
                    <span class="capitalize font-semibold text-orange-600">{{ str_replace('_', ' ', $activeTab) }}</span>
                </div>

                <!-- Top Right Controls -->
                <div class="flex items-center gap-3">
                    <!-- Super Admin Only: Switcher to inspect any tenant -->
                    @if(auth()->check() && auth()->user()->isSuperAdmin() && isset($allTenants) && $allTenants->count() > 1)
                        <div class="flex items-center gap-1.5 bg-amber-50 border border-amber-300 rounded-xl px-2.5 py-1 text-xs shadow-2xs" title="Mode Super Admin: Simulasi Tampilan Tenant">
                            <i class="fa-solid fa-user-shield text-amber-600 text-xs"></i>
                            <span class="text-[10px] uppercase font-bold text-amber-800 tracking-wider hidden md:inline">Super Admin View:</span>
                            <select wire:change="switchTenant($event.target.value)"
                                class="bg-transparent text-xs font-bold text-amber-900 focus:outline-none cursor-pointer">
                                @foreach($allTenants as $t)
                                    <option value="{{ $t->id }}" {{ $tenantId == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }} ({{ $t->businessType?->name ?? 'Bisnis' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <!-- Owner Tenant: Jenis Usaha Badge (Static Pill, Bukan Dropdown) -->
                        <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-50 text-slate-700 border border-slate-200 shadow-2xs">
                            <i class="fa-solid {{ $tenant?->businessType?->icon ?: 'fa-store' }} text-orange-500"></i>
                            <span>{{ $tenant?->businessType?->name ?? 'Bisnis' }}</span>
                        </div>
                    @endif

                    <!-- Outlet Switcher (Cabang Toko Milik Tenant) -->
                    @if($outlets->count() > 1)
                        <div class="relative">
                            <select wire:change="switchOutlet($event.target.value)"
                                class="bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 py-1.5 px-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500 cursor-pointer shadow-2xs">
                                @foreach($outlets as $out)
                                    <option value="{{ $out->id }}" {{ $selectedOutletId == $out->id ? 'selected' : '' }}>
                                        📍 {{ $out->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="text-xs font-medium text-slate-700 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl flex items-center gap-1.5 shadow-2xs">
                            <i class="fa-solid fa-location-dot text-orange-500"></i>
                            <span>{{ $selectedOutlet->name ?? 'Outlet Utama' }}</span>
                        </div>
                    @endif

                    <!-- Switch to POS Kasir Quick Button -->
                    <a href="{{ route('pos.terminal') }}"
                        class="hidden sm:flex items-center gap-2 text-xs font-semibold px-3 py-1.5 bg-orange-50 text-orange-600 border border-orange-200 rounded-xl hover:bg-orange-100 transition shadow-2xs">
                        <i class="fa-solid fa-desktop"></i>
                        <span>Layar POS Kasir</span>
                    </a>

                    <!-- Switch to Super Admin (if user is superadmin) -->
                    @if(auth()->check() && auth()->user()->isSuperAdmin())
                        <a href="{{ route('superadmin.dashboard') }}"
                            class="text-xs font-semibold px-3 py-1.5 bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition shadow-2xs">
                            <i class="fa-solid fa-arrow-left mr-1"></i> Super Admin
                        </a>
                    @endif

                    <!-- Notification -->
                    <button class="relative p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-50">
                        <i class="fa-regular fa-bell"></i>
                        @if($lowStockIngredients->count() > 0)
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full animate-pulse"></span>
                        @endif
                    </button>
                </div>
            </header>
