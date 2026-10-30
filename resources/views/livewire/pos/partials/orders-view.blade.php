<!-- ================= ORDERS VIEW: RIWAYAT & DAFTAR SEMUA PESANAN ================= -->
<div class="flex-1 overflow-y-auto p-6 space-y-6">
    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-clipboard-list"></i>
                </span>
                <h2 class="text-base font-bold text-slate-900">Riwayat & Daftar Semua Pesanan</h2>
            </div>
            <p class="text-xs text-slate-400 mt-1">
                Daftar transaksi kasir, pesanan tersimpan (*hold*), pesanan selesai, dan cetak ulang struk.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Search Order -->
            <div class="relative w-64">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text"
                       wire:model.live.debounce.250ms="orderSearch"
                       placeholder="Cari no order / meja..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 font-medium" />
            </div>

            <!-- Back to Catalog -->
            <button wire:click="$set('activeNav', 'katalog')"
                    class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5 shrink-0">
                <i class="fa-solid fa-shapes text-xs"></i>
                <span>Kembali ke Katalog</span>
            </button>
        </div>
    </div>

    <!-- Filter Status Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
        <button wire:click="$set('orderFilterStatus', 'all')"
                class="px-3.5 py-1.5 rounded-xl font-bold transition shrink-0 {{ $orderFilterStatus === 'all' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            Semua Pesanan ({{ $allOrders->count() }})
        </button>
        <button wire:click="$set('orderFilterStatus', 'completed')"
                class="px-3.5 py-1.5 rounded-xl font-bold transition shrink-0 {{ $orderFilterStatus === 'completed' ? 'bg-emerald-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            ● Selesai ({{ $allOrders->where('status', 'completed')->count() }})
        </button>
        <button wire:click="$set('orderFilterStatus', 'hold')"
                class="px-3.5 py-1.5 rounded-xl font-bold transition shrink-0 {{ $orderFilterStatus === 'hold' ? 'bg-amber-500 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            ⏸ Ditahan / Pending ({{ $allOrders->where('status', 'pending')->count() + count($heldOrders) }})
        </button>
        <button wire:click="$set('orderFilterStatus', 'processing')"
                class="px-3.5 py-1.5 rounded-xl font-bold transition shrink-0 {{ $orderFilterStatus === 'processing' ? 'bg-sky-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            ⏱ Diproses ({{ $allOrders->whereIn('status', ['confirmed', 'processing'])->count() }})
        </button>
        <button wire:click="$set('orderFilterStatus', 'voided')"
                class="px-3.5 py-1.5 rounded-xl font-bold transition shrink-0 {{ $orderFilterStatus === 'voided' ? 'bg-rose-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            ✕ Batal / Void ({{ $allOrders->where('status', 'voided')->count() }})
        </button>
    </div>

    <!-- Active Held Orders Banner (if any) -->
    @if (count($heldOrders) > 0 && ($orderFilterStatus === 'all' || $orderFilterStatus === 'hold'))
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 space-y-3">
            <div class="flex items-center justify-between">
                <span class="font-bold text-amber-900 text-xs flex items-center gap-2">
                    <i class="fa-solid fa-pause text-amber-600"></i>
                    Pesanan Disimpan di Memori Sesi Kasir ({{ count($heldOrders) }} Tertahan)
                </span>
                <span class="text-[10px] text-amber-700 bg-amber-100 px-2 py-0.5 rounded font-mono">Siap Dipanggil</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($heldOrders as $held)
                    <div class="bg-white p-3.5 rounded-xl border border-amber-200 shadow-2xs space-y-2 text-xs">
                        <div class="flex items-center justify-between font-bold">
                            <span class="text-slate-900">{{ $held['table'] }}</span>
                            <span class="text-orange-600">Rp{{ number_format($held['total'], 0, ',', '.') }}</span>
                        </div>
                        <div class="text-[11px] text-slate-500">
                            <div>{{ $held['customer'] }} • {{ count($held['items']) }} Item</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">Disimpan jam {{ $held['time'] }}</div>
                        </div>
                        <button wire:click="recallOrder('{{ $held['id'] }}')"
                                class="w-full py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold text-xs transition flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-arrow-turn-up text-xs"></i>
                            <span>Panggil ke Keranjang Kasir</span>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Orders Table / List -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                        <th class="py-3 px-4">No. Order & Waktu</th>
                        <th class="py-3 px-4">Tipe & Meja</th>
                        <th class="py-3 px-4">Rincian Menu / Layanan</th>
                        <th class="py-3 px-4">Total Bayar</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($allOrders as $ord)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $ord->order_number }}</div>
                                <div class="text-[11px] text-slate-400">
                                    {{ $ord->created_at->format('d/m/Y - H:i') }} • Kasir: {{ $ord->user->name ?? 'Kasir' }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-800 capitalize">
                                    {{ $ord->order_type === 'dine_in' ? 'Dine In' : 'Take Away' }}
                                </div>
                                <div class="text-[11px] text-slate-400">
                                    {{ $ord->table_number ?: 'Tanpa Meja' }}
                                    @if ($ord->customer)
                                        • {{ $ord->customer->name }}
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4 max-w-xs">
                                <div class="text-[11px] text-slate-700 truncate">
                                    @foreach ($ord->items as $idx => $it)
                                        <span>{{ (int)$it->qty }}x {{ $it->product_name }}</span>@if(!$loop->last), @endif
                                    @endforeach
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    Total {{ $ord->items->sum('qty') }} item
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">
                                    Rp{{ number_format($ord->total_amount, 0, ',', '.') }}
                                </div>
                                <div class="text-[11px] text-slate-400 uppercase">
                                    {{ $ord->payments->first()?->payment_method ?? 'Tunai' }}
                                </div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if ($ord->status === 'completed')
                                    <span class="text-[10px] font-bold bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full">
                                        ● Selesai
                                    </span>
                                @elseif ($ord->status === 'pending')
                                    <span class="text-[10px] font-bold bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full">
                                        ⏸ Ditahan
                                    </span>
                                @elseif (in_array($ord->status, ['confirmed', 'processing']))
                                    <span class="text-[10px] font-bold bg-sky-100 text-sky-700 px-2.5 py-1 rounded-full">
                                        ⏱ Diproses
                                    </span>
                                @else
                                    <span class="text-[10px] font-bold bg-rose-100 text-rose-700 px-2.5 py-1 rounded-full">
                                        ✕ Batal/Void
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right space-x-1">
                                <button type="button" wire:click="reprintReceipt({{ $ord->id }})"
                                        title="Cetak Ulang Struk Thermal"
                                        class="px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-[11px] transition inline-flex items-center gap-1">
                                    <i class="fa-solid fa-print text-orange-500 text-xs"></i>
                                    <span>Cetak</span>
                                </button>
                                @if ($ord->status === 'pending')
                                    <button type="button" wire:click="recallDbOrder({{ $ord->id }})"
                                            title="Lanjutkan Transaksi"
                                            class="px-2.5 py-1.5 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-semibold text-[11px] transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-play text-xs"></i>
                                        <span>Proses</span>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-receipt text-2xl text-slate-300 mb-2 block"></i>
                                Belum ada data pesanan yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

