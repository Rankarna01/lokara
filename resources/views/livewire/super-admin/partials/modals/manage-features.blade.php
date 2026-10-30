    <!-- ================= MODAL KELOLA MASTER FITUR PLATFORM ================= -->
    @if ($showManageFeaturesModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto"
             x-data
             @keydown.escape.window="$wire.closeManageFeaturesModal()"
             @click.self="$wire.closeManageFeaturesModal()">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-2xl w-full my-8 animate-scale-up flex flex-col max-h-[90vh]"
                 @click.outside="$wire.closeManageFeaturesModal()">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 shrink-0 bg-slate-50/50 rounded-t-2xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-base shadow-sm">
                            <i class="fa-solid fa-list-check"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Kelola Master Fitur SaaS</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Daftar seluruh fitur yang dapat dicentang pada paket langganan</p>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="closeManageFeaturesModal"
                            @click.prevent="$wire.set('showManageFeaturesModal', false)"
                            class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <!-- Body (Scrollable) -->
                <div class="overflow-y-auto px-6 py-5 space-y-5 flex-1 text-xs">
                    <!-- Form Tambah Fitur Baru -->
                    <div class="p-4 bg-orange-50/50 border border-orange-200/70 rounded-xl space-y-3">
                        <div class="font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-plus text-orange-500"></i>
                            <span>Tambah Fitur Baru ke Master Platform</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5">
                            <div class="sm:col-span-7">
                                <input type="text"
                                       wire:model="newFeatureName"
                                       wire:keydown.enter.prevent="createMasterFeature"
                                       placeholder="Nama fitur (contoh: Multi-Currency, QRIS Dinamis)..."
                                       class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-800 bg-white focus:outline-none focus:ring-1 focus:ring-orange-500" />
                                @error('newFeatureName')
                                    <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="sm:col-span-3">
                                <select wire:model="newFeatureCategory"
                                        class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 bg-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                                    <option value="Kasir & Transaksi">Kasir & Transaksi</option>
                                    <option value="Inventori">Inventori</option>
                                    <option value="Laporan">Laporan</option>
                                    <option value="Paket Bundling">Paket Bundling</option>
                                    <option value="Platform">Platform</option>
                                    <option value="Integrasi">Integrasi</option>
                                    <option value="Layanan & Bantuan">Layanan & Bantuan</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <button type="button"
                                        wire:click="createMasterFeature"
                                        class="w-full h-full py-2 px-3 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs shadow-sm flex items-center justify-center gap-1.5 transition">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                    <span>Tambah</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Daftar Fitur -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 font-semibold text-slate-700 flex items-center justify-between">
                            <span>Daftar Fitur ({{ $allPlatformFeatures->count() }})</span>
                            <span class="text-slate-400 font-normal text-[11px]">Digunakan di Paket</span>
                        </div>
                        <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                            @forelse ($allPlatformFeatures as $feat)
                                <div class="px-4 py-3 flex items-center justify-between hover:bg-slate-50/70 transition">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-slate-800">{{ $feat->name }}</span>
                                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-medium">
                                                {{ $feat->category ?? 'Umum' }}
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap gap-1 items-center">
                                            @php
                                                $usedPlans = $featurePlanUsage[$feat->name] ?? [];
                                            @endphp
                                            @if (count($usedPlans) > 0)
                                                @foreach ($usedPlans as $pName)
                                                    <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[10px] font-semibold border border-emerald-200/60">
                                                        {{ $pName }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-[10px] text-slate-400 italic">Belum digunakan di paket manapun</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-1">
                                        <button type="button"
                                                wire:click="openEditFeatureModal({{ $feat->id }})"
                                                class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:text-orange-600 hover:border-orange-200 text-xs font-semibold flex items-center gap-1 transition">
                                            <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                            <span>Edit</span>
                                        </button>
                                        <button type="button"
                                                wire:click="deleteFeature({{ $feat->id }})"
                                                wire:confirm="Yakin ingin menghapus fitur '{{ $feat->name }}'? Fitur akan dicabut dari seluruh paket yang memakainya."
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                                title="Hapus Fitur">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-slate-400 italic">Belum ada fitur terdaftar.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 rounded-b-2xl flex items-center justify-end shrink-0">
                    <button type="button"
                            wire:click="closeManageFeaturesModal"
                            @click.prevent="$wire.set('showManageFeaturesModal', false)"
                            class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs transition cursor-pointer">
                        Selesai
                    </button>
                </div>
            </div>
        </div>
    @endif
