                    <div class="space-y-5">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Tagihan & Invoice Platform</h1>
                                <p class="text-sm text-slate-500 mt-0.5">Riwayat penagihan biaya langganan bulanan dan tahunan seluruh tenant</p>
                            </div>
                            <button wire:click="notify('Proses penerbitan invoice H-7 otomatis berjalan via scheduler')" class="px-3.5 py-2 rounded-lg bg-white border border-slate-200 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                                <i class="fa-solid fa-arrows-rotate mr-1 text-slate-400"></i> Sinkronisasi Tagihan
                            </button>
                        </div>

                        <!-- Filter Status Invoices Pills -->
                        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                            <button wire:click="$set('invoiceStatusFilter', 'all')" class="px-3.5 py-1.5 rounded-lg font-medium transition {{ $invoiceStatusFilter === 'all' ? 'bg-orange-500 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Semua Tagihan ({{ $invoiceTotalCount }})</button>
                            <button wire:click="$set('invoiceStatusFilter', 'paid')" class="px-3.5 py-1.5 rounded-lg font-medium transition {{ $invoiceStatusFilter === 'paid' ? 'bg-orange-500 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Lunas ({{ $invoicePaidCount }})</button>
                            <button wire:click="$set('invoiceStatusFilter', 'unpaid')" class="px-3.5 py-1.5 rounded-lg font-medium transition {{ $invoiceStatusFilter === 'unpaid' ? 'bg-orange-500 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Belum Bayar ({{ $invoiceUnpaidCount }})</button>
                            <button wire:click="$set('invoiceStatusFilter', 'overdue')" class="px-3.5 py-1.5 rounded-lg font-medium transition {{ $invoiceStatusFilter === 'overdue' ? 'bg-orange-500 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Terlambat ({{ $invoiceOverdueCount }})</button>
                        </div>

                        <div class="bg-white border border-slate-200/80 rounded-xl shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-slate-500">
                                        <tr>
                                            <th class="py-3 px-4 font-semibold">Nomor Invoice</th>
                                            <th class="py-3 px-4 font-semibold">Nama Tenant</th>
                                            <th class="py-3 px-4 font-semibold">Total Tagihan</th>
                                            <th class="py-3 px-4 font-semibold">Jatuh Tempo</th>
                                            <th class="py-3 px-4 font-semibold">Metode Bayar</th>
                                            <th class="py-3 px-4 font-semibold">Status</th>
                                            <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse ($invoices as $inv)
                                            <tr class="hover:bg-slate-50/50 transition">
                                                <td class="py-3 px-4 font-bold text-slate-900">{{ $inv->invoice_number }}</td>
                                                <td class="py-3 px-4 font-medium text-slate-800">{{ $inv->tenant ? $inv->tenant->name : '-' }}</td>
                                                <td class="py-3 px-4 font-bold text-slate-900">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                                                <td class="py-3 px-4 text-slate-500">{{ $inv->due_date ? $inv->due_date->translatedFormat('d M Y') : '-' }}</td>
                                                <td class="py-3 px-4 text-slate-600">{{ $inv->payment_method ?? 'Menunggu Pembayaran' }}</td>
                                                <td class="py-3 px-4">
                                                    @if ($inv->status === 'paid')
                                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-600">Lunas</span>
                                                    @elseif ($inv->status === 'overdue')
                                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-600">Terlambat</span>
                                                    @else
                                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-600">Belum Bayar</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4 text-right">
                                                    @if ($inv->status !== 'paid')
                                                        <button wire:click="markInvoicePaid({{ $inv->id }})" class="px-2.5 py-1 text-[11px] font-medium rounded bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition">Tandai Lunas</button>
                                                    @else
                                                        <span class="text-slate-400 text-[11px]">Terverifikasi</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="py-6 text-center text-slate-400">Belum ada data invoice.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3 border-t border-slate-100">
                                {{ $invoices->links() }}
                            </div>
                        </div>
                    </div>
