                    <div class="space-y-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div>
                                <h1 class="text-xl font-bold text-slate-900">Stok & Inventori Multi-Lokasi</h1>
                                <p class="text-xs text-slate-500 mt-0.5">Saldo persediaan bahan, kartu mutasi stok real-time, dan audit stok opname fisik.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="openOpnameModal({{ $ingredients->first()->id ?? 1 }})"
                                    class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition">
                                    <i class="fa-solid fa-clipboard-check"></i>
                                    <span>Mulai Stok Opname</span>
                                </button>
                            </div>
                        </div>

                        <!-- 3 Multi-Storage Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                                <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                                    <span class="font-semibold text-slate-700">📍 Bar Depan & Display</span>
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 font-bold">Aktif Kasir</span>
                                </div>
                                <div class="text-2xl font-bold text-slate-900">4 Bahan Utama</div>
                                <div class="text-xs text-slate-500 mt-1">Biji kopi, gula aren, cup takeaway</div>
                            </div>

                            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                                <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                                    <span class="font-semibold text-slate-700">❄️ Freezer & Chiller Dapur</span>
                                    <span class="px-2 py-0.5 rounded-full bg-sky-50 text-sky-600 font-bold">Suhu Dingin</span>
                                </div>
                                <div class="text-2xl font-bold text-slate-900">3 Bahan Segar</div>
                                <div class="text-xs text-slate-500 mt-1">Susu Fresh Milk, Ayam, Kentang Beku</div>
                            </div>

                            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                                <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                                    <span class="font-semibold text-slate-700">📦 Gudang Belakang / Logistik</span>
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold">Stok Cadangan</span>
                                </div>
                                <div class="text-2xl font-bold text-slate-900">Stok Buffer</div>
                                <div class="text-xs text-slate-500 mt-1">Kemasan dus kardus & sirup cadangan</div>
                            </div>
                        </div>

                        <!-- Stock Movement Ledger Table -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <div class="p-4 border-b border-slate-100">
                                <h3 class="font-bold text-slate-900 text-sm">Kartu Mutasi Stok Terakhir (Audit Trail)</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 border-b border-slate-200/80 text-xs text-slate-500 uppercase font-semibold">
                                        <tr>
                                            <th class="py-3 px-4">Waktu</th>
                                            <th class="py-3 px-4">Bahan Baku</th>
                                            <th class="py-3 px-4 text-center">Jenis Mutasi</th>
                                            <th class="py-3 px-4 text-right">Perubahan Qty</th>
                                            <th class="py-3 px-4 text-right">Saldo Akhir</th>
                                            <th class="py-3 px-4">Keterangan / Referensi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($stockMovements as $mv)
                                            <tr class="hover:bg-slate-50/60 transition">
                                                <td class="py-3 px-4 text-xs text-slate-500">{{ $mv->created_at->format('d/m/Y H:i') }}</td>
                                                <td class="py-3 px-4 font-bold text-slate-900">{{ $mv->ingredient->name ?? '-' }}</td>
                                                <td class="py-3 px-4 text-center">
                                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md uppercase {{ $mv->type === 'purchase' ? 'bg-emerald-50 text-emerald-600' : ($mv->type === 'sale_pos' ? 'bg-sky-50 text-sky-600' : 'bg-amber-50 text-amber-600') }}">
                                                        {{ $mv->type }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-right font-bold {{ $mv->qty >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                                    {{ $mv->qty >= 0 ? '+' : '' }}{{ number_format($mv->qty, 0, ',', '.') }} {{ $mv->ingredient->usage_unit ?? '' }}
                                                </td>
                                                <td class="py-3 px-4 text-right font-semibold text-slate-800">
                                                    {{ number_format($mv->balance_after, 0, ',', '.') }} {{ $mv->ingredient->usage_unit ?? '' }}
                                                </td>
                                                <td class="py-3 px-4 text-xs text-slate-500">{{ $mv->reference_note ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-6 text-center text-slate-400 text-sm">Belum ada mutasi stok tercatat.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
