                    <div class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Template Jenis Usaha</h1>
                                <p class="text-sm text-slate-500 mt-0.5">Konfigurasi modul dan preset standar saat tenant baru mendaftar</p>
                            </div>
                            <button type="button"
                                    wire:click="openCreateBusinessTypeModal"
                                    class="px-3.5 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold shadow-sm shadow-orange-500/20 flex items-center gap-2 transition self-start sm:self-auto cursor-pointer">
                                <i class="fa-solid fa-plus"></i>
                                <span>Tambah Template Baru</span>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach ($businessTypes as $bt)
                                <div class="bg-white border {{ $bt->is_active ? 'border-slate-200/80' : 'border-slate-200 opacity-80 bg-slate-50/50' }} rounded-xl p-6 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                                    <div>
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-lg shadow-sm">
                                                    <i class="fa-solid {{ $bt->icon }}"></i>
                                                </div>
                                                <div>
                                                    <h3 class="font-bold text-slate-900">{{ $bt->name }}</h3>
                                                    <span class="text-[11px] text-slate-400">{{ $bt->tenants_count ?? 0 }} Bisnis Menggunakan</span>
                                                </div>
                                            </div>
                                            <button type="button"
                                                    wire:click="toggleBusinessTypeActive({{ $bt->id }})"
                                                    title="Klik untuk ubah status aktif/nonaktif"
                                                    class="text-xs transition cursor-pointer">
                                                @if ($bt->is_active)
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center gap-1 hover:bg-emerald-100">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200 flex items-center gap-1 hover:bg-slate-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Nonaktif
                                                    </span>
                                                @endif
                                            </button>
                                        </div>

                                        <p class="text-xs text-slate-500 mt-4 leading-relaxed">{{ $bt->description }}</p>

                                        <div class="mt-4 p-3.5 rounded-xl bg-slate-50/80 border border-slate-100 text-[11px] text-slate-600 space-y-1.5">
                                            <div class="font-semibold text-slate-700 flex items-center justify-between">
                                                <span>Preset Modul Aktif:</span>
                                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-orange-100 text-orange-700 font-bold">
                                                    {{ count($bt->preset_modules) }} modul
                                                </span>
                                            </div>
                                            <div class="space-y-1 pt-0.5">
                                                @forelse ($bt->preset_modules as $mod)
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="text-orange-500 font-bold">•</span>
                                                        <span>{{ $mod }}</span>
                                                    </div>
                                                @empty
                                                    <div class="text-[11px] text-slate-400 italic">Belum ada modul preset</div>
                                                @endforelse
                                            </div>

                                            @if (!empty($bt->default_settings['default_units']))
                                                <div class="pt-2 mt-2 border-t border-slate-200/60 flex items-center gap-1 flex-wrap text-[10px]">
                                                    <span class="text-slate-400">Satuan:</span>
                                                    @foreach ($bt->default_settings['default_units'] as $unit)
                                                        <span class="px-1.5 py-0.5 rounded bg-white border border-slate-200 text-slate-600 font-mono text-[10px]">
                                                            {{ $unit }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                        <span class="text-slate-400 text-[11px]">Slug: <code class="font-mono text-slate-500">{{ $bt->slug }}</code></span>
                                        <button type="button"
                                                wire:click="openEditPresetModal({{ $bt->id }})"
                                                class="text-orange-600 hover:text-orange-700 font-semibold flex items-center gap-1.5 hover:underline cursor-pointer">
                                            <i class="fa-solid fa-sliders text-[11px]"></i>
                                            <span>Ubah Preset</span>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
