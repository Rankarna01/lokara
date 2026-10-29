<!-- ================= MODAL: PEMBAYARAN & CHECKOUT ================= -->
@if ($showPaymentModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full p-6 animate-scale-up space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Konfirmasi Pembayaran</h3>
                    <p class="text-[11px] text-slate-400">{{ $orderType === 'dine_in' ? 'Dine In • ' . $selectedTable : 'Take Away' }}</p>
                </div>
                <button wire:click="$set('showPaymentModal', false)" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Big Total Display -->
            <div class="text-center py-2 bg-orange-50 rounded-xl border border-orange-100">
                <span class="text-[11px] font-semibold text-orange-600 uppercase tracking-wider">Total Tagihan</span>
                <div class="text-2xl font-extrabold text-orange-600">Rp{{ number_format($totals['total'], 0, ',', '.') }}</div>
            </div>

            <!-- Payment Method Tabs -->
            <div class="grid grid-cols-3 gap-2 text-xs">
                <button type="button" wire:click="$set('paymentMethod', 'cash')"
                        class="py-2.5 px-2 rounded-xl border text-center font-bold flex flex-col items-center gap-1 transition {{ $paymentMethod === 'cash' ? 'bg-orange-500 text-white border-orange-500 shadow-xs' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                    <i class="fa-solid fa-money-bill-wave text-sm"></i>
                    <span>Tunai</span>
                </button>

                <button type="button" wire:click="$set('paymentMethod', 'qris')"
                        class="py-2.5 px-2 rounded-xl border text-center font-bold flex flex-col items-center gap-1 transition {{ $paymentMethod === 'qris' ? 'bg-orange-500 text-white border-orange-500 shadow-xs' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                    <i class="fa-solid fa-qrcode text-sm"></i>
                    <span>QRIS</span>
                </button>

                <button type="button" wire:click="$set('paymentMethod', 'debit')"
                        class="py-2.5 px-2 rounded-xl border text-center font-bold flex flex-col items-center gap-1 transition {{ $paymentMethod === 'debit' ? 'bg-orange-500 text-white border-orange-500 shadow-xs' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                    <i class="fa-solid fa-credit-card text-sm"></i>
                    <span>Debit / EDC</span>
                </button>
            </div>

            <!-- Cash Method Controls -->
            @if ($paymentMethod === 'cash')
                <div class="space-y-3 pt-2 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Uang Tunai Diterima</label>
                        <input type="number" wire:model.live="cashTendered" class="w-full text-base font-bold px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500" />
                    </div>

                    <!-- Quick Cash Buttons -->
                    <div class="grid grid-cols-4 gap-1.5">
                        <button type="button" wire:click="setQuickCash({{ $totals['total'] }})" class="py-1.5 px-1 rounded-lg border border-slate-200 text-center font-bold hover:bg-slate-50 text-[11px]">Uang Pas</button>
                        <button type="button" wire:click="setQuickCash(100000)" class="py-1.5 px-1 rounded-lg border border-slate-200 text-center font-bold hover:bg-slate-50 text-[11px]">100.000</button>
                        <button type="button" wire:click="setQuickCash(150000)" class="py-1.5 px-1 rounded-lg border border-slate-200 text-center font-bold hover:bg-slate-50 text-[11px]">150.000</button>
                        <button type="button" wire:click="setQuickCash(200000)" class="py-1.5 px-1 rounded-lg border border-slate-200 text-center font-bold hover:bg-slate-50 text-[11px]">200.000</button>
                    </div>

                    <!-- Change Due -->
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                        <span class="font-semibold text-slate-600">Uang Kembalian:</span>
                        <span class="text-base font-extrabold text-emerald-600">Rp{{ number_format(max(0, $cashTendered - $totals['total']), 0, ',', '.') }}</span>
                    </div>
                </div>
            @elseif ($paymentMethod === 'qris')
                <!-- QRIS Dynamic Display -->
                <div class="text-center py-4 space-y-3">
                    <div class="w-44 h-44 mx-auto p-2 border border-slate-200 rounded-2xl bg-white shadow-xs flex items-center justify-center">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=00020101021226670016ID.LOKARAPOS.WWW01189360099999999999990215INV-20261029-015802ID5916Kopi Ruang Pagi6013Jakarta Selatan"
                             alt="QRIS Dinamis"
                             class="w-full h-full object-contain" />
                    </div>
                    <p class="text-xs text-slate-500">Scan QRIS menggunakan GoPay, OVO, Dana, BCA, atau Livin Mandiri</p>
                </div>
            @else
                <div class="py-4 space-y-2 text-xs">
                    <label class="block font-semibold text-slate-700">Nomor Approval / Referensi EDC</label>
                    <input type="text" placeholder="Contoh: APPR-889901" class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500" />
                </div>
            @endif

            <div class="pt-3 border-t border-slate-100 flex gap-2">
                <button type="button" wire:click="$set('showPaymentModal', false)" class="w-1/3 py-2.5 rounded-xl border border-slate-200 font-semibold text-xs text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="button" wire:click="processPayment" class="w-2/3 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 font-bold text-xs text-white shadow-sm transition">
                    Selesaikan Pembayaran
                </button>
            </div>
        </div>
    </div>
@endif
