                    <div class="space-y-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div>
                                <h1 class="text-xl font-bold text-slate-900">Pelanggan & CRM Loyalti</h1>
                                <p class="text-xs text-slate-500 mt-0.5">Database member toko, poin belanja yang dapat ditukarkan, dan riwayat kunjungan.</p>
                            </div>
                        </div>

                        <!-- Customer Table -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 border-b border-slate-200/80 text-xs text-slate-500 uppercase font-semibold">
                                        <tr>
                                            <th class="py-3 px-4">Nama Pelanggan</th>
                                            <th class="py-3 px-4">No. WhatsApp</th>
                                            <th class="py-3 px-4 text-center">Tier Member</th>
                                            <th class="py-3 px-4 text-right">Poin Loyalti</th>
                                            <th class="py-3 px-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($customers as $c)
                                            <tr class="hover:bg-slate-50/60 transition">
                                                <td class="py-3 px-4">
                                                    <div class="font-bold text-slate-900">{{ $c->name }}</div>
                                                    <div class="text-[11px] text-slate-400">{{ $c->email ?? '-' }}</div>
                                                </td>
                                                <td class="py-3 px-4 text-xs font-medium text-slate-600">{{ $c->phone ?? '-' }}</td>
                                                <td class="py-3 px-4 text-center">
                                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $c->points >= 500 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}">
                                                        {{ $c->points >= 500 ? '👑 Gold VIP' : 'Silver Member' }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-right font-bold text-orange-600">
                                                    {{ number_format($c->points, 0, ',', '.') }} Pts
                                                </td>
                                                <td class="py-3 px-4 text-right">
                                                    <button class="text-xs text-slate-500 hover:text-orange-600 font-medium">Lihat Riwayat</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="py-8 text-center text-slate-400 text-sm">Belum ada pelanggan terdaftar.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
