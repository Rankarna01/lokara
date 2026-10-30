                    <div class="space-y-6">
                        <!-- Top Header -->
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div>
                                <h1 class="text-xl font-bold text-slate-900">Bahan Baku & Resep BOM (Bill of Materials)</h1>
                                <p class="text-xs text-slate-500 mt-0.5">Konfigurasi takaran bahan per porsi, auto-deduct saat kasir menjual, dan kalkulasi HPP otomatis.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="openNewIngredientModal"
                                    class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>Tambah Bahan Baku</span>
                                </button>
                            </div>
                        </div>

                        <!-- 2 Columns: Left = Raw Materials Table, Right = Recipe Cost Calculator -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Raw Materials Table (2 cols) -->
                            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                                    <h3 class="font-bold text-slate-900 text-sm">Daftar Bahan Baku Mentah</h3>
                                    <span class="text-xs text-slate-400">{{ $ingredients->count() }} item terdaftar</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-sm">
                                        <thead class="bg-slate-50 border-b border-slate-200/80 text-xs text-slate-500 uppercase font-semibold">
                                            <tr>
                                                <th class="py-3 px-4">Bahan Baku</th>
                                                <th class="py-3 px-4">Satuan Beli & Pakai</th>
                                                <th class="py-3 px-4 text-right">Biaya Satuan Pakai</th>
                                                <th class="py-3 px-4 text-right">Stok Saat Ini</th>
                                                <th class="py-3 px-4 text-center">Status</th>
                                                <th class="py-3 px-4 text-right">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            @foreach($ingredients as $ing)
                                                <tr class="hover:bg-slate-50/60 transition">
                                                    <td class="py-3 px-4">
                                                        <div class="font-bold text-slate-900">{{ $ing->name }}</div>
                                                        <div class="text-[11px] text-slate-400">{{ $ing->code }} • {{ $ing->storage_location }}</div>
                                                    </td>
                                                    <td class="py-3 px-4 text-xs text-slate-600">
                                                        1 {{ $ing->purchase_unit }} = {{ (int)$ing->conversion_factor }} {{ $ing->usage_unit }}
                                                    </td>
                                                    <td class="py-3 px-4 text-right text-xs font-semibold text-slate-800">
                                                        Rp {{ number_format($ing->cost_per_usage_unit, 0, ',', '.') }} / {{ $ing->usage_unit }}
                                                    </td>
                                                    <td class="py-3 px-4 text-right font-bold {{ $ing->current_stock <= $ing->minimum_stock ? 'text-rose-600' : 'text-slate-900' }}">
                                                        {{ number_format($ing->current_stock, 0, ',', '.') }} {{ $ing->usage_unit }}
                                                    </td>
                                                    <td class="py-3 px-4 text-center">
                                                        @if($ing->current_stock <= $ing->minimum_stock)
                                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-50 text-rose-600 border border-rose-200 animate-pulse">
                                                                Menipis!
                                                            </span>
                                                        @else
                                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200">
                                                                Aman
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3 px-4 text-right">
                                                        <button wire:click="openOpnameModal({{ $ing->id }})"
                                                            title="Sesuaikan Stok (Opname)"
                                                            class="text-xs font-semibold px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg">
                                                            Opname
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Right: Recipe Matrix Sample (1 col) -->
                            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                                <h3 class="font-bold text-slate-900 text-base">Contoh Simulasi HPP Resep</h3>
                                <p class="text-xs text-slate-500">Klik tombol di bawah untuk melihat komposisi resep dan rekalkulasi HPP otomatis.</p>

                                <div class="space-y-3">
                                    @foreach($products->take(4) as $p)
                                        <div class="p-3 rounded-xl border border-slate-200/80 bg-slate-50 flex items-center justify-between gap-3">
                                            <div>
                                                <div class="text-sm font-bold text-slate-800">{{ $p->name }}</div>
                                                <div class="text-xs text-slate-500">
                                                    HPP: <span class="font-semibold text-orange-600">Rp {{ number_format($p->cost_price, 0, ',', '.') }}</span>
                                                    • Jual: Rp {{ number_format($p->base_price, 0, ',', '.') }}
                                                </div>
                                            </div>
                                            <button wire:click="openRecipeModal({{ $p->id }})"
                                                class="px-2.5 py-1.5 text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:border-orange-500 hover:text-orange-600 rounded-lg shadow-2xs">
                                                Resep BOM
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
