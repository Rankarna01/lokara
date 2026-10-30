                    <div class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Paket & Langganan</h1>
                                <p class="text-sm text-slate-500 mt-0.5">Konfigurasi batasan fitur, kuota outlet, dan tarif langganan SaaS</p>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <button type="button"
                                        wire:click="openManageFeaturesModal"
                                        class="px-3.5 py-2 rounded-xl bg-white border border-slate-200/80 hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm flex items-center gap-2 transition hover:border-slate-300">
                                    <i class="fa-solid fa-list-check text-orange-500"></i>
                                    <span>Kelola Master Fitur</span>
                                    <span class="px-1.5 py-0.5 rounded-full bg-orange-100 text-orange-700 text-[10px] font-bold">{{ $allPlatformFeatures->count() }}</span>
                                </button>
                                <button type="button"
                                        wire:click="openCreatePlanModal"
                                        class="px-3.5 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold shadow-sm shadow-orange-500/20 flex items-center gap-2 transition">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>Tambah Paket Baru</span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                            @foreach ($plans as $plan)
                                <div class="bg-white border {{ $plan->is_featured ? 'border-orange-500 ring-2 ring-orange-500/20' : 'border-slate-200/80' }} rounded-xl p-6 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-lg font-bold text-slate-900">{{ $plan->name }}</h3>
                                            @if ($plan->badge)
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $plan->is_featured ? 'bg-orange-500 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $plan->badge }}</span>
                                            @endif
                                        </div>

                                        <div class="mt-4">
                                            <span class="text-2xl font-extrabold text-slate-900">Rp {{ number_format($plan->price_monthly, 0, ',', '.') }}</span>
                                            <span class="text-xs text-slate-400">/ bulan</span>
                                            @if ($plan->price_yearly > 0)
                                                <div class="text-[11px] text-slate-400 mt-0.5">Rp {{ number_format($plan->price_yearly, 0, ',', '.') }} / tahun</div>
                                            @endif
                                        </div>

                                        <div class="mt-6 space-y-2.5 border-t border-slate-100 pt-4 text-xs">
                                            <div class="flex items-center gap-2 text-slate-700">
                                                <i class="fa-solid fa-check text-emerald-500 text-xs"></i>
                                                <span>Max <strong>{{ $plan->limits['max_outlets'] ?? 1 }}</strong> Outlet Cabang</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-slate-700">
                                                <i class="fa-solid fa-check text-emerald-500 text-xs"></i>
                                                <span>Max <strong>{{ $plan->limits['max_users'] ?? 2 }}</strong> Akun Karyawan</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-slate-700">
                                                <i class="fa-solid fa-check text-emerald-500 text-xs"></i>
                                                <span>Max <strong>{{ $plan->limits['max_products'] ?? 50 }}</strong> Master Produk</span>
                                            </div>

                                            @if (is_array($plan->features) && count($plan->features) > 0)
                                                @foreach ($plan->features as $feat)
                                                    <div class="flex items-center gap-2 text-slate-600">
                                                        <i class="fa-solid fa-check text-orange-500 text-xs"></i>
                                                        <span>{{ $feat }}</span>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="text-[11px] text-slate-400 italic py-1">Belum ada fitur dicentang</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                                        <span class="text-slate-400">{{ $plan->tenants_count ?? 0 }} Tenant aktif</span>
                                        <button type="button"
                                                wire:click="openEditPlanModal({{ $plan->id }})"
                                                class="text-orange-600 hover:text-orange-700 font-semibold flex items-center gap-1.5 hover:underline">
                                            <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                            <span>Ubah Tarif</span>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
