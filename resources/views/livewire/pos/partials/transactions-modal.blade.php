<!-- ================= MODAL: TRANSAKSI HARI INI ================= -->
@if ($showTransactionsModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-xl w-full p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Riwayat Transaksi Shift Ini</h3>
                    <p class="text-[11px] text-slate-400">Daftar transaksi yang diselesaikan kasir hari ini</p>
                </div>
                <button wire:click="$set('showTransactionsModal', false)" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="overflow-x-auto max-h-80 overflow-y-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="py-2 px-3 font-semibold">No. Order</th>
                            <th class="py-2 px-3 font-semibold">Waktu / Tipe</th>
                            <th class="py-2 px-3 font-semibold">Metode</th>
                            <th class="py-2 px-3 font-semibold">Total</th>
                            <th class="py-2 px-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($todayOrders as $to)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-2.5 px-3 font-bold text-slate-900">{{ $to->order_number }}</td>
                                <td class="py-2.5 px-3 text-slate-500">
                                    {{ $to->created_at ? $to->created_at->format('H:i') : '14:20' }} • {{ $to->order_type === 'salon_order' ? 'Layanan Salon' : ($to->order_type === 'dine_in' ? ($to->table_number ?? 'Dine In') : 'Take Away') }}
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                        {{ $to->payments->first() ? $to->payments->first()->payment_method : 'QRIS' }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 font-bold text-slate-900">Rp{{ number_format($to->total_amount, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3 text-right">
                                    <button wire:click="notify('Struk order {{ $to->order_number }} dikirim ke printer thermal')" class="px-2.5 py-1 rounded border border-slate-200 text-slate-700 hover:bg-slate-100 text-[11px] font-semibold">
                                        <i class="fa-solid fa-print mr-1"></i> Cetak Ulang
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-400">Belum ada transaksi di database untuk shift ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

