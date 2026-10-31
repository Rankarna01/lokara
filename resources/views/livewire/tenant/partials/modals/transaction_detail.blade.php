<!-- Modal Detail Transaksi & Struk POS -->
@if ($showTransactionDetailModal && $viewingTransaction)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in duration-150">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-lg shadow-xs">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-bold text-slate-900 font-mono tracking-tight">{{ $viewingTransaction->order_number }}</h3>
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $viewingTransaction->status === 'completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($viewingTransaction->status === 'voided' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-amber-50 text-amber-700 border border-amber-200') }}">
                                {{ $viewingTransaction->status === 'completed' ? '● Selesai (Lunas)' : ($viewingTransaction->status === 'voided' ? '✕ Dibatalkan (Void)' : '⏸ Hold / Pending') }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $viewingTransaction->created_at->translatedFormat('l, d F Y - H:i:s') }} WIB
                            • {{ $viewingTransaction->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                <button wire:click="closeTransactionDetailModal" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                <!-- Info Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- Kasir & Outlet -->
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/70">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Outlet & Kasir</span>
                        <div class="font-bold text-xs text-slate-800 mt-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-store text-orange-500 text-[11px]"></i>
                            <span>{{ $viewingTransaction->outlet?->name ?? 'Outlet Utama' }}</span>
                        </div>
                        <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1">
                            <i class="fa-solid fa-user-circle text-slate-400 text-[10px]"></i>
                            <span>Kasir: {{ $viewingTransaction->user?->name ?? 'Kasir Lokara' }}</span>
                        </div>
                    </div>

                    <!-- Pelanggan & Meja -->
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/70">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pelanggan & Meja</span>
                        <div class="font-bold text-xs text-slate-800 mt-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-user text-sky-500 text-[11px]"></i>
                            <span>{{ $viewingTransaction->customer?->name ?? 'Pelanggan Walk-in / Reguler' }}</span>
                        </div>
                        <div class="text-[11px] text-slate-500 mt-0.5">
                            <span class="font-medium capitalize">{{ str_replace('_', ' ', $viewingTransaction->order_type) }}</span>
                            @if($viewingTransaction->table_number)
                                • <span class="font-bold text-orange-600">Meja {{ $viewingTransaction->table_number }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/70">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Metode Pembayaran</span>
                        @php
                            $firstPayment = $viewingTransaction->payments->first();
                            $methodName = $firstPayment ? strtoupper($firstPayment->payment_method) : 'TUNAI';
                        @endphp
                        <div class="font-bold text-xs text-slate-800 mt-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-wallet text-emerald-500 text-[11px]"></i>
                            <span>{{ $methodName }}</span>
                        </div>
                        <div class="text-[11px] text-slate-500 mt-0.5">
                            @if($firstPayment && $firstPayment->payment_method === 'cash')
                                Diterima: Rp {{ number_format($firstPayment->cash_tendered, 0, ',', '.') }}
                            @else
                                Ref: {{ $firstPayment?->reference_number ?? '-' }}
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Itemized Breakdown Table -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                            Rincian Item Pesanan ({{ $viewingTransaction->items->count() }} Macam)
                        </h4>
                        <span class="text-[11px] text-slate-500">
                            Total Qty: {{ $viewingTransaction->items->sum('qty') }} item
                        </span>
                    </div>

                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase text-[10px]">
                                <tr>
                                    <th class="py-2.5 px-3">Item / Layanan</th>
                                    <th class="py-2.5 px-3 text-center">Qty</th>
                                    <th class="py-2.5 px-3 text-right">Harga Satuan</th>
                                    <th class="py-2.5 px-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($viewingTransaction->items as $item)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="py-2.5 px-3 font-semibold text-slate-900">
                                            {{ $item->product_name }}
                                            @if($item->variant_name)
                                                <span class="block text-[11px] font-normal text-slate-500">
                                                    {{ $item->variant_name }}
                                                </span>
                                            @endif
                                            @if($item->notes)
                                                <span class="block text-[10px] text-amber-600 italic">
                                                    Catatan: {{ $item->notes }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-3 text-center font-bold text-slate-800">
                                            {{ (float)$item->qty == (int)$item->qty ? (int)$item->qty : $item->qty }}
                                        </td>
                                        <td class="py-2.5 px-3 text-right text-slate-600">
                                            Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                        </td>
                                        <td class="py-2.5 px-3 text-right font-bold text-slate-900">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-slate-400">Tidak ada item tercatat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Calculation Summary & Payment Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <!-- Left: Shift & Audit Trail -->
                    <div class="space-y-3 bg-slate-50/70 p-3.5 rounded-xl border border-slate-200/70 text-xs">
                        <div class="font-bold text-slate-800 flex items-center gap-1.5">
                            <i class="fa-solid fa-clock-rotate-left text-orange-500"></i>
                            <span>Audit Trail POS</span>
                        </div>
                        <div class="space-y-1.5 text-[11px] text-slate-600">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Shift Kasir:</span>
                                <span class="font-semibold">{{ $viewingTransaction->shift_id ? 'Shift #' . $viewingTransaction->shift_id : 'Sesi Umum' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Waktu Pembayaran:</span>
                                <span class="font-semibold">{{ $viewingTransaction->paid_at ? $viewingTransaction->paid_at->format('d/m/Y H:i:s') : '-' }}</span>
                            </div>
                            @if($viewingTransaction->notes)
                                <div class="pt-2 border-t border-slate-200">
                                    <span class="text-slate-400 block mb-0.5">Catatan / Alasan:</span>
                                    <span class="text-slate-700 bg-white p-2 rounded border border-slate-200 block text-[11px]">
                                        {{ $viewingTransaction->notes }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right: Financial Breakdown -->
                    <div class="space-y-2 bg-slate-50/70 p-3.5 rounded-xl border border-slate-200/70 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal Item</span>
                            <span class="font-semibold">Rp {{ number_format($viewingTransaction->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($viewingTransaction->discount_amount > 0)
                            <div class="flex justify-between text-emerald-600">
                                <span>Diskon / Promo</span>
                                <span class="font-semibold">-Rp {{ number_format($viewingTransaction->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($viewingTransaction->tax_amount > 0)
                            <div class="flex justify-between text-slate-600">
                                <span>Pajak (PB1 / PPN)</span>
                                <span class="font-semibold">Rp {{ number_format($viewingTransaction->tax_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($viewingTransaction->service_amount > 0)
                            <div class="flex justify-between text-slate-600">
                                <span>Biaya Layanan (Service)</span>
                                <span class="font-semibold">Rp {{ number_format($viewingTransaction->service_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="border-t border-slate-200 pt-2 flex justify-between items-baseline font-bold text-slate-900 text-sm">
                            <span>Total Tagihan</span>
                            <span class="text-base text-orange-600 font-black">
                                Rp {{ number_format($viewingTransaction->total_amount, 0, ',', '.') }}
                            </span>
                        </div>

                        @if($firstPayment)
                            <div class="border-t border-dashed border-slate-200 pt-2 text-[11px] space-y-1 text-slate-600">
                                <div class="flex justify-between">
                                    <span>Nominal Diterima:</span>
                                    <span class="font-semibold">Rp {{ number_format($firstPayment->cash_tendered ?? $firstPayment->amount, 0, ',', '.') }}</span>
                                </div>
                                @if(($firstPayment->change_due ?? 0) > 0)
                                    <div class="flex justify-between text-slate-800 font-medium">
                                        <span>Kembalian:</span>
                                        <span class="font-bold text-emerald-600">Rp {{ number_format($firstPayment->change_due, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/80 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div>
                    @if($viewingTransaction->status === 'completed')
                        <button wire:click="openVoidModal({{ $viewingTransaction->id }})"
                                class="px-3.5 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 border border-rose-200 rounded-xl transition flex items-center gap-1.5">
                            <i class="fa-solid fa-ban"></i>
                            <span>Otorisasi Void Transaksi</span>
                        </button>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="closeTransactionDetailModal"
                            class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                        Tutup
                    </button>
                    <button wire:click="openPrintReceiptModal({{ $viewingTransaction->id }})"
                            class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                        <i class="fa-solid fa-print"></i>
                        <span>Cetak Struk Thermal</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
