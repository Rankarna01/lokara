                    <div class="space-y-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div>
                                <h1 class="text-xl font-bold text-slate-900">Riwayat Transaksi Penjualan</h1>
                                <p class="text-xs text-slate-500 mt-0.5">Audit log lengkap struk belanja, status order, metode pembayaran, dan otorisasi void.</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <select wire:model.live="transactionStatusFilter"
                                    class="bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 py-1.5 px-3 rounded-lg focus:outline-none focus:ring-1 focus:ring-orange-500">
                                    <option value="all">Semua Status</option>
                                    <option value="completed">Selesai</option>
                                    <option value="hold">Disimpan (Hold)</option>
                                    <option value="voided">Dibatalkan (Void)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Transactions Table -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 border-b border-slate-200/80 text-xs text-slate-500 uppercase font-semibold">
                                        <tr>
                                            <th class="py-3 px-4">No. Order</th>
                                            <th class="py-3 px-4">Waktu</th>
                                            <th class="py-3 px-4">Tipe & Meja</th>
                                            <th class="py-3 px-4 text-right">Total Transaksi</th>
                                            <th class="py-3 px-4 text-center">Status</th>
                                            <th class="py-3 px-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($transactions as $tx)
                                            <tr class="hover:bg-slate-50/60 transition">
                                                <td class="py-3 px-4 font-bold text-slate-900">
                                                    {{ $tx->order_number }}
                                                    <div class="text-[11px] font-normal text-slate-400">
                                                        {{ $tx->items->count() }} item • Kasir: Dina
                                                    </div>
                                                </td>
                                                <td class="py-3 px-4 text-xs text-slate-600">
                                                    {{ $tx->created_at->format('d M Y, H:i') }}
                                                </td>
                                                <td class="py-3 px-4 text-xs text-slate-600">
                                                    <span class="capitalize font-medium">{{ str_replace('_', ' ', $tx->order_type) }}</span>
                                                    @if($tx->table_number)
                                                        <span class="text-orange-600 font-semibold">• {{ $tx->table_number }}</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4 text-right font-bold text-slate-900">
                                                    Rp {{ number_format($tx->total_amount, 0, ',', '.') }}
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $tx->status === 'completed' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : ($tx->status === 'voided' ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-amber-50 text-amber-600 border border-amber-200') }}">
                                                        {{ $tx->status === 'completed' ? 'Lunas' : ($tx->status === 'voided' ? 'Void' : 'Hold') }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-right">
                                                    @if($tx->status === 'completed')
                                                        <button wire:click="openVoidModal({{ $tx->id }})"
                                                            title="Otorisasi Void Pesanan"
                                                            class="text-xs font-semibold px-2.5 py-1 text-rose-600 hover:bg-rose-50 border border-rose-200 rounded-lg">
                                                            Void Order
                                                        </button>
                                                    @else
                                                        <span class="text-xs text-slate-400">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                                                    Belum ada transaksi pada kriteria filter ini.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
