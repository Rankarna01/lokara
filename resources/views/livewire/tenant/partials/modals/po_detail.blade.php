    <!-- Modal Detail Purchase Order (PO) -->
    @if ($showPoDetailModal && $viewingPo)
        <div class="fixed inset-0 z-[75] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-slate-900 text-base">{{ $viewingPo->po_number }}</span>
                            @if($viewingPo->status === 'received')
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Diterima (GRN)</span>
                            @elseif($viewingPo->status === 'cancelled')
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">Dibatalkan</span>
                            @else
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">Dipesan</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Detail faktur pemesanan pengadaan barang</p>
                    </div>
                    <button type="button" wire:click="$set('showPoDetailModal', false)" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 space-y-4 text-xs max-h-[75vh] overflow-y-auto">
                    <!-- Supplier Info Box -->
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/70 space-y-2">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Rekanan Pemasok</div>
                        <div class="font-bold text-slate-900 text-sm">{{ $viewingPo->supplier->name ?? 'Pemasok Terhapus' }}</div>
                        <div class="grid grid-cols-2 gap-2 text-slate-600 pt-1">
                            <div><i class="fa-solid fa-user-tie text-slate-400 mr-1.5"></i>PIC: {{ $viewingPo->supplier->contact_person ?? '-' }}</div>
                            <div><i class="fa-solid fa-phone text-slate-400 mr-1.5"></i>{{ $viewingPo->supplier->phone ?? '-' }}</div>
                            <div class="col-span-2"><i class="fa-solid fa-location-dot text-slate-400 mr-1.5"></i>{{ $viewingPo->supplier->address ?? '-' }}</div>
                        </div>
                    </div>

                    <!-- PO Dates & Outlet -->
                    <div class="grid grid-cols-2 gap-3 p-3 bg-slate-50/70 rounded-xl border border-slate-200/60">
                        <div>
                            <div class="text-slate-400 text-[10px]">Tanggal Terbit Order:</div>
                            <div class="font-bold text-slate-800 mt-0.5">{{ $viewingPo->order_date ? $viewingPo->order_date->format('d M Y') : '-' }}</div>
                        </div>
                        <div>
                            <div class="text-slate-400 text-[10px]">Estimasi Kedatangan:</div>
                            <div class="font-bold text-slate-800 mt-0.5">{{ $viewingPo->expected_date ? $viewingPo->expected_date->format('d M Y') : '-' }}</div>
                        </div>
                        <div class="col-span-2 pt-1 border-t border-slate-200/50">
                            <div class="text-slate-400 text-[10px]">Outlet Tujuan:</div>
                            <div class="font-semibold text-slate-800 mt-0.5">{{ $viewingPo->outlet->name ?? 'Outlet Utama' }}</div>
                        </div>
                    </div>

                    <!-- Catatan Pengadaan -->
                    @if($viewingPo->notes)
                        <div class="p-3 bg-amber-50/60 border border-amber-200/60 rounded-xl">
                            <div class="font-semibold text-amber-900 text-[11px]">Catatan / Instruksi Pengadaan:</div>
                            <div class="text-amber-800 mt-0.5 leading-relaxed">{{ $viewingPo->notes }}</div>
                        </div>
                    @endif

                    <!-- Total Tagihan -->
                    <div class="p-4 bg-orange-50/80 rounded-xl border border-orange-200 flex items-center justify-between">
                        <div>
                            <div class="text-orange-700 text-[11px] font-semibold uppercase tracking-wider">Total Nilai Tagihan PO</div>
                            <div class="text-[10px] text-orange-600">Syarat: {{ $viewingPo->supplier?->terms_days == 0 ? 'Cash on Delivery (COD)' : 'Tempo ' . $viewingPo->supplier?->terms_days . ' Hari' }}</div>
                        </div>
                        <div class="text-xl font-bold text-slate-900 font-mono">
                            Rp {{ number_format($viewingPo->total_amount, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2">
                    @if($viewingPo->status === 'ordered')
                        <button type="button" wire:click="cancelPo({{ $viewingPo->id }})"
                            wire:confirm="Yakin ingin membatalkan Purchase Order ini?"
                            class="px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition cursor-pointer">
                            Batalkan PO
                        </button>

                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="$set('showPoDetailModal', false)"
                                class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl cursor-pointer">Tutup</button>
                            <button type="button" wire:click="markPoReceived({{ $viewingPo->id }})"
                                class="px-4 py-2 text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                                <i class="fa-solid fa-box-open"></i>
                                <span>Konfirmasi Terima Barang (GRN)</span>
                            </button>
                        </div>
                    @else
                        <div></div>
                        <button type="button" wire:click="$set('showPoDetailModal', false)"
                            class="px-4 py-2 text-xs font-semibold bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl cursor-pointer">Tutup</button>
                    @endif
                </div>
            </div>
        </div>
    @endif

