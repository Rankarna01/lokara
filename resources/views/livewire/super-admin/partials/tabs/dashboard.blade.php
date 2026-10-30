                    <!-- Title & Date Filter Bar -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Dashboard Platform</h1>
                            <p class="text-sm text-slate-500 mt-0.5">Ringkasan operasional Lokara POS</p>
                        </div>

                        <!-- Date Filter Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition">
                                <i class="fa-regular fa-calendar text-slate-400"></i>
                                <span>
                                    @if ($dateFilter === '7_days') 7 hari terakhir
                                    @elseif ($dateFilter === '30_days') 30 hari terakhir
                                    @elseif ($dateFilter === '90_days') 90 hari terakhir
                                    @else Tahun 2026
                                    @endif
                                </span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-1"></i>
                            </button>

                            <div x-show="open"
                                 @click.outside="open = false"
                                 class="absolute right-0 mt-1 w-44 bg-white border border-slate-200 rounded-xl shadow-lg py-1 z-30 text-xs">
                                <button wire:click="setDateFilter('7_days')" @click="open = false" class="w-full text-left px-4 py-2 hover:bg-slate-50 {{ $dateFilter === '7_days' ? 'text-orange-600 font-semibold' : 'text-slate-700' }}">7 hari terakhir</button>
                                <button wire:click="setDateFilter('30_days')" @click="open = false" class="w-full text-left px-4 py-2 hover:bg-slate-50 {{ $dateFilter === '30_days' ? 'text-orange-600 font-semibold' : 'text-slate-700' }}">30 hari terakhir</button>
                                <button wire:click="setDateFilter('90_days')" @click="open = false" class="w-full text-left px-4 py-2 hover:bg-slate-50 {{ $dateFilter === '90_days' ? 'text-orange-600 font-semibold' : 'text-slate-700' }}">90 hari terakhir</button>
                                <button wire:click="setDateFilter('this_year')" @click="open = false" class="w-full text-left px-4 py-2 hover:bg-slate-50 {{ $dateFilter === 'this_year' ? 'text-orange-600 font-semibold' : 'text-slate-700' }}">Tahun 2026</button>
                            </div>
                        </div>
                    </div>

                    <!-- 4 Top Stat Cards (Matching Image 1) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Stat 1: Total Tenant -->
                        <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-sm hover:border-slate-300 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                                    <i class="fa-solid fa-store text-sm"></i>
                                </div>
                                <span class="text-xs font-medium text-slate-500">Total Tenant</span>
                            </div>
                            <div class="mt-4">
                                <div class="text-2xl lg:text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalTenantCount, 0, ',', '.') }}</div>
                                <div class="text-xs text-slate-400 mt-1">{{ number_format($activeTenantCount, 0, ',', '.') }} aktif</div>
                            </div>
                        </div>

                        <!-- Stat 2: Pendapatan Bulanan -->
                        <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-sm hover:border-slate-300 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                                    <i class="fa-solid fa-wallet text-sm"></i>
                                </div>
                                <span class="text-xs font-medium text-slate-500">Pendapatan Bulanan</span>
                            </div>
                            <div class="mt-4">
                                <div class="text-2xl lg:text-3xl font-bold tracking-tight text-slate-900">{{ $mrrValue }}</div>
                                <div class="text-xs text-slate-400 mt-1">MRR dari {{ $activeTenantCount }} tenant aktif</div>
                            </div>
                        </div>

                        <!-- Stat 3: Transaksi Platform -->
                        <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-sm hover:border-slate-300 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                                    <i class="fa-solid fa-right-left text-sm"></i>
                                </div>
                                <span class="text-xs font-medium text-slate-500">Transaksi Platform</span>
                            </div>
                            <div class="mt-4">
                                <div class="text-2xl lg:text-3xl font-bold tracking-tight text-slate-900">{{ $totalTransactions }}</div>
                                <div class="text-xs text-slate-400 mt-1">{{ $dateFilterLabel }} • {{ $totalVolumeFormatted }}</div>
                            </div>
                        </div>

                        <!-- Stat 4: Perlu Perhatian -->
                        <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-sm hover:border-slate-300 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                                    <i class="fa-regular fa-clock text-sm"></i>
                                </div>
                                <span class="text-xs font-medium text-slate-500">Perlu Perhatian</span>
                            </div>
                            <div class="mt-4">
                                <div class="text-2xl lg:text-3xl font-bold tracking-tight text-slate-900">{{ $attentionCount }}</div>
                                <div class="text-xs text-slate-400 mt-1">Tenant & tagihan butuh tindakan</div>
                            </div>
                        </div>
                    </div>

                    <!-- Middle Grid: Charts (Line Chart ~65% + Donut Chart ~35%) -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

                        <!-- Left Chart: Pertumbuhan Tenant -->
                        <div class="lg:col-span-8 bg-white border border-slate-200/80 rounded-xl p-6 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-bold text-slate-900">Pertumbuhan Tenant</h3>
                            </div>
                            <div class="h-64 relative">
                                <canvas id="growthChart"></canvas>
                            </div>
                        </div>

                        <!-- Right Chart: Status Langganan -->
                        <div class="lg:col-span-4 bg-white border border-slate-200/80 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                            <div class="mb-2">
                                <h3 class="text-sm font-bold text-slate-900">Status Langganan</h3>
                            </div>

                            <div class="flex items-center justify-center relative py-4">
                                <div class="w-44 h-44 relative flex items-center justify-center">
                                    <canvas id="subscriptionDonutChart"></canvas>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                        <span class="text-xl font-bold text-slate-900">{{ number_format($totalTenantCount, 0, ',', '.') }}</span>
                                        <span class="text-[11px] text-slate-400">Total Tenant</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Legend List (Matching Image 1) -->
                            <div class="space-y-2 pt-2 border-t border-slate-100 text-xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                                        <span class="text-slate-600">Active</span>
                                    </div>
                                    <span class="font-semibold text-slate-900">{{ number_format($activeTenantCount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-sky-300"></span>
                                        <span class="text-slate-600">Trial</span>
                                    </div>
                                    <span class="font-semibold text-slate-900">{{ number_format($trialTenantCount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                                        <span class="text-slate-600">Suspended</span>
                                    </div>
                                    <span class="font-semibold text-slate-900">{{ number_format($suspendedTenantCount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-slate-800"></span>
                                        <span class="text-slate-600">Expired</span>
                                    </div>
                                    <span class="font-semibold text-slate-900">{{ number_format($expiredTenantCount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Grid: Tenant Terbaru (Left) & Aktivitas Terbaru (Right) -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

                        <!-- Left: Tenant Terbaru Table (Matching Image 1) -->
                        <div class="lg:col-span-8 bg-white border border-slate-200/80 rounded-xl p-6 shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-bold text-slate-900">Tenant Terbaru</h3>
                                <button wire:click="setTab('tenants')" class="text-xs text-orange-600 hover:text-orange-700 font-medium">Lihat Semua</button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead>
                                        <tr class="text-slate-400 border-b border-slate-100 pb-2">
                                            <th class="py-2.5 font-medium">Nama Bisnis</th>
                                            <th class="py-2.5 font-medium">Jenis Usaha</th>
                                            <th class="py-2.5 font-medium">Paket</th>
                                            <th class="py-2.5 font-medium">Bergabung</th>
                                            <th class="py-2.5 font-medium text-right">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                        @forelse ($recentTenants as $tenant)
                                            <tr class="hover:bg-slate-50/60 transition">
                                                <td class="py-3 flex items-center gap-2.5 font-semibold text-slate-900">
                                                    <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600 flex-shrink-0">
                                                        <i class="fa-solid {{ $tenant->icon ?? 'fa-store' }} text-xs"></i>
                                                    </div>
                                                    <span>{{ $tenant->name }}</span>
                                                </td>
                                                <td class="py-3 text-slate-600">
                                                    {{ $tenant->businessType ? explode(' ', $tenant->businessType->name)[0] : 'Umum' }}
                                                </td>
                                                <td class="py-3 text-slate-600 font-medium">
                                                    {{ $tenant->plan ? $tenant->plan->name : 'Basic' }}
                                                </td>
                                                <td class="py-3 text-slate-500">
                                                    {{ $tenant->created_at ? $tenant->created_at->translatedFormat('d M Y') : '28 Okt 2026' }}
                                                </td>
                                                <td class="py-3 text-right">
                                                    @if ($tenant->status === 'active')
                                                        <span class="inline-flex items-center gap-1.5 text-slate-700 font-medium">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                                        </span>
                                                    @elseif ($tenant->status === 'trial')
                                                        <span class="inline-flex items-center gap-1.5 text-slate-700 font-medium">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Trial
                                                        </span>
                                                    @elseif ($tenant->status === 'suspended')
                                                        <span class="inline-flex items-center gap-1.5 text-slate-700 font-medium">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Suspended
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 text-slate-700 font-medium">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Expired
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="py-4 text-center text-slate-400">Belum ada tenant baru</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Right: Aktivitas Terbaru (Matching Image 1) -->
                        <div class="lg:col-span-4 bg-white border border-slate-200/80 rounded-xl p-6 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-bold text-slate-900">Aktivitas Terbaru</h3>
                                <button wire:click="setTab('monitoring')" class="text-xs text-orange-600 hover:text-orange-700 font-medium">Log Lengkap</button>
                            </div>

                            <div class="space-y-4">
                                @forelse ($activityLogs as $log)
                                    <div class="flex items-start justify-between gap-3 text-xs">
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-lg {{ $log->icon_color }} flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <i class="fa-solid {{ $log->icon }} text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-slate-800 leading-snug">{{ $log->action }}</p>
                                                <p class="text-slate-400 text-[11px] mt-0.5">{{ $log->description }}</p>
                                            </div>
                                        </div>
                                        <span class="text-[11px] text-slate-400 whitespace-nowrap">{{ $log->badge_text }}</span>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400">Belum ada aktivitas tercatat.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
