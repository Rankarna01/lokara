                    <div class="space-y-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div>
                                <h1 class="text-xl font-bold text-slate-900">Laporan Laba Rugi (P&L) & Arus Kas</h1>
                                <p class="text-xs text-slate-500 mt-0.5">Analisis keuntungan bersih: Omzet Kotor dikurangi Biaya HPP dan Biaya Operasional (OPEX).</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="openExpenseModal"
                                    class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>Catat Pengeluaran Toko</span>
                                </button>
                            </div>
                        </div>

                        <!-- P&L Financial Waterfall Card -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                            <h3 class="font-bold text-slate-900 text-base">Laporan Laba Rugi Bulan Ini (Oktober 2026)</h3>

                            <div class="space-y-2.5 text-sm">
                                <div class="flex justify-between py-2 border-b border-slate-100 font-semibold text-slate-800">
                                    <span>(+) Pendapatan Penjualan Kotor (Gross Sales)</span>
                                    <span>Rp {{ number_format($monthGrossSales, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-slate-100 text-rose-600 font-medium pl-4">
                                    <span>(-) Harga Pokok Penjualan (HPP / Biaya Bahan)</span>
                                    <span>- Rp {{ number_format($estimatedCogs, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between py-2.5 bg-emerald-50/70 px-3 rounded-xl font-bold text-emerald-800">
                                    <span>(=) Laba Kotor (Gross Profit) [{{ $grossMarginPct }}%]</span>
                                    <span>Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-slate-100 text-rose-600 font-medium pl-4">
                                    <span>(-) Total Biaya Operasional Toko (OPEX)</span>
                                    <span>- Rp {{ number_format($totalOpex, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between py-3.5 bg-slate-900 text-white px-4 rounded-xl font-bold text-base shadow-sm">
                                    <span>(=) Laba Bersih Toko (Net Profit)</span>
                                    <span class="{{ $netProfit >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                        Rp {{ number_format($netProfit, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Expenses (OPEX) Table -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                                <h3 class="font-bold text-slate-900 text-sm">Rincian Beban Biaya Operasional (OPEX)</h3>
                                <span class="text-xs text-slate-500">Total: Rp {{ number_format($totalOpex, 0, ',', '.') }}</span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 border-b border-slate-200/80 text-xs text-slate-500 uppercase font-semibold">
                                        <tr>
                                            <th class="py-3 px-4">Tanggal</th>
                                            <th class="py-3 px-4">Kategori Beban</th>
                                            <th class="py-3 px-4">Deskripsi Biaya</th>
                                            <th class="py-3 px-4">Metode Bayar</th>
                                            <th class="py-3 px-4 text-right">Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($expenses as $exp)
                                            <tr class="hover:bg-slate-50/60 transition">
                                                <td class="py-3 px-4 text-xs text-slate-500">{{ $exp->expense_date->format('d M Y') }}</td>
                                                <td class="py-3 px-4">
                                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">
                                                        {{ $exp->category }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 font-semibold text-slate-900">
                                                    {{ $exp->title }}
                                                    <div class="text-[11px] font-normal text-slate-400">{{ $exp->notes }}</div>
                                                </td>
                                                <td class="py-3 px-4 text-xs text-slate-600">{{ $exp->payment_method }}</td>
                                                <td class="py-3 px-4 text-right font-bold text-rose-600">
                                                    Rp {{ number_format($exp->amount, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
