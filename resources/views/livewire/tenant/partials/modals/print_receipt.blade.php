<!-- Modal Cetak Struk Thermal POS -->
@if ($showPrintReceiptModal && $viewingTransaction)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in duration-150">
            <!-- Header Bar -->
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 bg-slate-50/80">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-orange-500"></i>
                    <h3 class="text-xs font-bold text-slate-800">Preview Struk Kasir</h3>
                </div>
                <button wire:click="closePrintReceiptModal" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Receipt Paper View (Thermal 58mm/80mm style) -->
            @php
                $tSettings = $tenant->settings ?? [];
                $paperSize = $tSettings['receipt_paper_size'] ?? '58mm';
                $showLogo = $tSettings['receipt_show_logo'] ?? true;
                $logoUrl = $tSettings['receipt_logo_url'] ?? null;
                $storeName = $tSettings['receipt_store_name'] ?? $tenant->name ?? 'LOKARA POS';
                $tagline = $tSettings['receipt_tagline'] ?? '';
                $headerNotes = $tSettings['receipt_header_notes'] ?? '';
                $footerNotes = $tSettings['receipt_footer_notes'] ?? 'Terima Kasih Atas Kunjungan Anda!';
                $wifiInfo = $tSettings['receipt_wifi_info'] ?? '';
                $socialMedia = $tSettings['receipt_social_media'] ?? '';
                $showCustomer = $tSettings['receipt_show_customer'] ?? true;
                $showCashier = $tSettings['receipt_show_cashier'] ?? true;
                $showTable = $tSettings['receipt_show_table'] ?? true;
            @endphp
            <div class="p-6 bg-slate-100 flex justify-center">
                <div id="printableThermalReceipt" class="bg-white w-full {{ $paperSize === '80mm' ? 'max-w-[340px]' : 'max-w-[280px]' }} p-5 shadow-md border border-slate-200/80 rounded-lg text-slate-800 font-mono text-[11px] leading-relaxed">
                    <!-- Store Header -->
                    <div class="text-center space-y-1 mb-4 pb-3 border-b border-dashed border-slate-300">
                        @if($showLogo && $logoUrl)
                            <div class="flex justify-center mb-1.5">
                                <img src="{{ $logoUrl }}" alt="{{ $storeName }}" class="w-12 h-12 object-contain filter grayscale contrast-125 rounded-md" />
                            </div>
                        @endif
                        <div class="font-black text-sm text-slate-900 uppercase tracking-wider">
                            {{ $storeName }}
                        </div>
                        @if($tagline)
                            <div class="text-[10px] text-slate-500 italic">
                                {{ $tagline }}
                            </div>
                        @endif
                        <div class="text-[10px] text-slate-500">
                            {{ $viewingTransaction->outlet?->name ?? 'Outlet Utama' }}
                        </div>
                        @if($viewingTransaction->outlet?->address)
                            <div class="text-[9px] text-slate-400 leading-tight">
                                {{ $viewingTransaction->outlet->address }}
                            </div>
                        @endif
                        @if($viewingTransaction->outlet?->phone)
                            <div class="text-[9px] text-slate-400">
                                Telp: {{ $viewingTransaction->outlet->phone }}
                            </div>
                        @endif
                        @if($headerNotes)
                            <div class="text-[9px] text-slate-500 pt-1">
                                {{ $headerNotes }}
                            </div>
                        @endif
                    </div>

                    <!-- Order Meta Info -->
                    <div class="space-y-0.5 text-[10px] pb-3 border-b border-dashed border-slate-300">
                        <div class="flex justify-between">
                            <span>No:</span>
                            <span class="font-bold">{{ $viewingTransaction->order_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Waktu:</span>
                            <span>{{ $viewingTransaction->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        @if($showCashier)
                            <div class="flex justify-between">
                                <span>Kasir:</span>
                                <span>{{ $viewingTransaction->user?->name ?? 'Kasir' }}</span>
                            </div>
                        @endif
                        @if($showCustomer)
                            <div class="flex justify-between">
                                <span>Pelanggan:</span>
                                <span>{{ $viewingTransaction->customer?->name ?? 'Walk-in' }}</span>
                            </div>
                        @endif
                        @if($showTable)
                            <div class="flex justify-between">
                                <span>Tipe:</span>
                                <span class="capitalize">{{ str_replace('_', ' ', $viewingTransaction->order_type) }} {{ $viewingTransaction->table_number ? '• Meja ' . $viewingTransaction->table_number : '' }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Items List -->
                    <div class="py-3 border-b border-dashed border-slate-300 space-y-2">
                        @foreach($viewingTransaction->items as $item)
                            <div>
                                <div class="font-bold text-slate-900 flex justify-between">
                                    <span class="truncate pr-2">{{ $item->product_name }}</span>
                                    <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="text-[10px] text-slate-500 flex justify-between">
                                    <span>
                                        {{ (float)$item->qty == (int)$item->qty ? (int)$item->qty : $item->qty }}x
                                        @ {{ number_format($item->unit_price, 0, ',', '.') }}
                                        @if($item->variant_name)
                                            ({{ $item->variant_name }})
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Totals Breakdown -->
                    <div class="py-3 border-b border-dashed border-slate-300 space-y-1 text-[10px]">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span>{{ number_format($viewingTransaction->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($viewingTransaction->discount_amount > 0)
                            <div class="flex justify-between text-emerald-600">
                                <span>Diskon:</span>
                                <span>-{{ number_format($viewingTransaction->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($viewingTransaction->tax_amount > 0)
                            <div class="flex justify-between">
                                <span>Pajak (PB1):</span>
                                <span>{{ number_format($viewingTransaction->tax_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($viewingTransaction->service_amount > 0)
                            <div class="flex justify-between">
                                <span>Service:</span>
                                <span>{{ number_format($viewingTransaction->service_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-xs font-black text-slate-900 pt-1 border-t border-slate-200">
                            <span>TOTAL:</span>
                            <span>Rp {{ number_format($viewingTransaction->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    @php
                        $payment = $viewingTransaction->payments->first();
                    @endphp
                    @if($payment)
                        <div class="py-3 border-b border-dashed border-slate-300 space-y-1 text-[10px]">
                            <div class="flex justify-between">
                                <span>Metode:</span>
                                <span class="font-bold uppercase">{{ $payment->payment_method }}</span>
                            </div>
                            @if($payment->payment_method === 'cash')
                                <div class="flex justify-between">
                                    <span>Tunai Diterima:</span>
                                    <span>{{ number_format($payment->cash_tendered, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between font-bold">
                                    <span>Kembalian:</span>
                                    <span>{{ number_format($payment->change_due, 0, ',', '.') }}</span>
                                </div>
                            @else
                                <div class="flex justify-between">
                                    <span>No. Ref:</span>
                                    <span>{{ $payment->reference_number ?? '-' }}</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Footer Message -->
                    <div class="text-center pt-4 text-[9px] text-slate-500 space-y-1">
                        <p class="font-bold text-slate-700 whitespace-pre-line">{{ $footerNotes }}</p>
                        @if($wifiInfo)
                            <p class="text-[9px] text-slate-500"><i class="fa-solid fa-wifi mr-1"></i> WiFi: <span class="font-bold">{{ $wifiInfo }}</span></p>
                        @endif
                        @if($socialMedia)
                            <p class="text-[9px] text-slate-500"><i class="fa-brands fa-instagram mr-1"></i> {{ $socialMedia }}</p>
                        @endif
                        <p class="pt-1.5 text-[8px] tracking-wider uppercase text-slate-400">Powered by Lokara POS</p>
                    </div>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-3">
                <button wire:click="closePrintReceiptModal"
                        class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                    Kembali
                </button>
                <button onclick="
                        var printContents = document.getElementById('printableThermalReceipt').innerHTML;
                        var originalContents = document.body.innerHTML;
                        var printWindow = window.open('', '', 'height=600,width=400');
                        printWindow.document.write('<html><head><title>Struk Transaksi</title>');
                        printWindow.document.write('<style>body{font-family:monospace;font-size:12px;padding:15px;color:#000;} .border-b{border-bottom:1px dashed #999;} .flex{display:flex;justify-content:space-between;} .text-center{text-align:center;} .font-bold{font-weight:bold;}</style>');
                        printWindow.document.write('</head><body>');
                        printWindow.document.write(printContents);
                        printWindow.document.write('</body></html>');
                        printWindow.document.close();
                        printWindow.focus();
                        printWindow.print();
                        printWindow.close();
                    "
                    class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak Sekarang</span>
                </button>
            </div>
        </div>
    </div>
@endif

