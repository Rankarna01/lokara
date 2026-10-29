<!-- ================= MODAL: PESANAN TERTUNDA (HOLD ORDERS) ================= -->
@if ($showHoldModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">Daftar Pesanan Tertunda (Hold)</h3>
                <button wire:click="$set('showHoldModal', false)" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="space-y-2 max-h-72 overflow-y-auto text-xs">
                @forelse ($heldOrders as $held)
                    <div class="p-3 rounded-xl border border-slate-200 flex items-center justify-between hover:bg-slate-50 transition">
                        <div>
                            <div class="font-bold text-slate-900">{{ $held['table'] }} <span class="text-[10px] text-slate-400 font-normal">({{ $held['time'] }})</span></div>
                            <div class="text-[11px] text-slate-500 mt-0.5">{{ count($held['items']) }} item • Rp{{ number_format($held['total'], 0, ',', '.') }}</div>
                        </div>
                        <button wire:click="recallOrder('{{ $held['id'] }}')" class="px-3 py-1.5 rounded-lg bg-orange-500 text-white font-bold text-xs hover:bg-orange-600 transition">
                            Muat Kembali
                        </button>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400">
                        <p>Tidak ada pesanan yang sedang ditahan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endif

