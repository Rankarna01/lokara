                    <div class="space-y-5">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Manajemen Tenant</h1>
                                <p class="text-sm text-slate-500 mt-0.5">Kelola seluruh tenant bisnis yang terdaftar di platform Lokara POS</p>
                            </div>
                            <button wire:click="$set('showCreateTenantModal', true)"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold shadow-sm transition">
                                <i class="fa-solid fa-plus text-xs"></i>
                                <span>Tambah Tenant Baru</span>
                            </button>
                        </div>

                        <!-- Filter Status Pills -->
                        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                            <button wire:click="$set('tenantStatusFilter', 'all')" class="px-3.5 py-1.5 rounded-lg font-medium transition {{ $tenantStatusFilter === 'all' ? 'bg-orange-500 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Semua ({{ $totalTenantCount }})</button>
                            <button wire:click="$set('tenantStatusFilter', 'active')" class="px-3.5 py-1.5 rounded-lg font-medium transition {{ $tenantStatusFilter === 'active' ? 'bg-orange-500 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Aktif ({{ $activeTenantCount }})</button>
                            <button wire:click="$set('tenantStatusFilter', 'trial')" class="px-3.5 py-1.5 rounded-lg font-medium transition {{ $tenantStatusFilter === 'trial' ? 'bg-orange-500 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Trial ({{ $trialTenantCount }})</button>
                            <button wire:click="$set('tenantStatusFilter', 'suspended')" class="px-3.5 py-1.5 rounded-lg font-medium transition {{ $tenantStatusFilter === 'suspended' ? 'bg-orange-500 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Suspended ({{ $suspendedTenantCount }})</button>
                            <button wire:click="$set('tenantStatusFilter', 'expired')" class="px-3.5 py-1.5 rounded-lg font-medium transition {{ $tenantStatusFilter === 'expired' ? 'bg-orange-500 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Expired ({{ $expiredTenantCount }})</button>
                        </div>

                        <!-- Tenant Table Card -->
                        <div class="bg-white border border-slate-200/80 rounded-xl shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-slate-500">
                                        <tr>
                                            <th class="py-3 px-4 font-semibold">Bisnis & Subdomain</th>
                                            <th class="py-3 px-4 font-semibold">Jenis Usaha</th>
                                            <th class="py-3 px-4 font-semibold">Owner & Kontak</th>
                                            <th class="py-3 px-4 font-semibold">Paket</th>
                                            <th class="py-3 px-4 font-semibold">Status</th>
                                            <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse ($allTenants as $t)
                                            <tr class="hover:bg-slate-50/50 transition">
                                                <td class="py-3 px-4">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center font-bold text-xs">
                                                            <i class="fa-solid {{ $t->icon ?? 'fa-store' }}"></i>
                                                        </div>
                                                        <div>
                                                            <div class="font-bold text-slate-900 text-sm">{{ $t->name }}</div>
                                                            <div class="text-[11px] text-slate-400">{{ $t->slug }}.lokara.id</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-4 text-slate-600">
                                                    <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-700 px-2.5 py-1 rounded-md text-[11px]">
                                                        {{ $t->businessType ? $t->businessType->name : 'F&B' }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4">
                                                    <div class="font-medium text-slate-800">{{ $t->owner_name ?? '-' }}</div>
                                                    <div class="text-[11px] text-slate-400">{{ $t->owner_phone ?? $t->owner_email }}</div>
                                                </td>
                                                <td class="py-3 px-4">
                                                    <span class="font-bold text-slate-800">{{ $t->plan ? $t->plan->name : 'Pro' }}</span>
                                                </td>
                                                <td class="py-3 px-4">
                                                    @if ($t->status === 'active')
                                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-600">Aktif</span>
                                                    @elseif ($t->status === 'trial')
                                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-600">Trial</span>
                                                    @elseif ($t->status === 'suspended')
                                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-600">Suspended</span>
                                                    @else
                                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600">Expired</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4 text-right space-x-2">
                                                    <button wire:click="impersonateTenant({{ $t->id }})" title="Login sebagai Owner" class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 transition">
                                                        <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
                                                    </button>
                                                    <button wire:click="toggleTenantStatus({{ $t->id }})" title="{{ $t->status === 'active' ? 'Suspend Tenant' : 'Aktifkan Tenant' }}" class="p-1.5 rounded-lg border border-slate-200 {{ $t->status === 'active' ? 'text-rose-500 hover:bg-rose-50' : 'text-emerald-500 hover:bg-emerald-50' }} transition">
                                                        <i class="fa-solid {{ $t->status === 'active' ? 'fa-ban' : 'fa-check' }} text-xs"></i>
                                                    </button>
                                                    @if ($t->status === 'suspended')
                                                        <button wire:click="deleteTenant({{ $t->id }})"
                                                                wire:confirm="Hapus permanen tenant '{{ $t->name }}' beserta seluruh datanya? Tindakan ini tidak bisa dibatalkan."
                                                                title="Hapus Tenant"
                                                                class="p-1.5 rounded-lg border border-slate-200 text-rose-600 hover:bg-rose-50 hover:border-rose-200 transition">
                                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-8 text-center text-slate-400">Tidak ada tenant yang cocok dengan pencarian.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3 border-t border-slate-100">
                                {{ $allTenants->links() }}
                            </div>
                        </div>
                    </div>
