<!-- ================= MODAL: PEMBAYARAN BERHASIL (SUCCESS SCREEN) ================= -->
@if ($showSuccessModal && $completedOrderData)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-sm w-full p-6 text-center animate-scale-up space-y-4">
            <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-2xl shadow-sm">
                <i class="fa-solid fa-check"></i>
            </div>

            <div>
                <h3 class="text-lg font-bold text-slate-900">Pembayaran Berhasil!</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $completedOrderData['order_number'] }}</p>
            </div>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 text-xs text-left space-y-2">
                <div class="flex justify-between">
                    <span class="text-slate-500">Tipe Pesanan:</span>
                    <span class="font-semibold text-slate-800">{{ $completedOrderData['order_type'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Pelanggan:</span>
                    <span class="font-semibold text-slate-800">{{ $completedOrderData['customer_name'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Metode Bayar:</span>
                    <span class="font-semibold text-slate-800">{{ $completedOrderData['payment_method'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Total Transaksi:</span>
                    <span class="font-bold text-slate-900">Rp{{ number_format($completedOrderData['total'], 0, ',', '.') }}</span>
                </div>
                @if ($completedOrderData['payment_method'] === 'CASH')
                    <div class="flex justify-between pt-1 border-t border-slate-200/60">
                        <span class="text-slate-500">Uang Diterima:</span>
                        <span class="font-semibold text-slate-800">Rp{{ number_format($completedOrderData['cash_tendered'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Uang Kembalian:</span>
                        <span class="font-bold text-emerald-600">Rp{{ number_format($completedOrderData['change_due'], 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>

            <div class="space-y-2 pt-2">
                <button type="button" wire:click="notify('Perintah byte ESC/POS dikirim ke printer thermal')" class="w-full py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-print text-slate-500"></i>
                    <span>Cetak Struk Thermal</span>
                </button>
                <button type="button" wire:click="notify('Struk digital dikirim ke WhatsApp')" class="w-full py-2.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 text-xs font-bold flex items-center justify-center gap-2">
                    <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                    <span>Kirim Struk via WhatsApp</span>
                </button>
                <button type="button" wire:click="newTransaction" class="w-full py-3 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold shadow-sm transition">
                    Pesanan Baru
                </button>
            </div>
        </div>
    </div>
@endif
