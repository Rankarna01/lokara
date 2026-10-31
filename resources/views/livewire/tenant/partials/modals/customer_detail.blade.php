<!-- Modal Profil & Riwayat Transaksi Pelanggan CRM -->
@if ($showCustomerDetailModal && $viewingCustomer)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in duration-150">
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/80 flex items-start justify-between">
                <div class="flex items-center gap-3.5">
                    <!-- Avatar initial -->
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white flex items-center justify-center font-black text-lg shadow-sm">
                        {{ strtoupper(substr($viewingCustomer->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-bold text-slate-900">{{ $viewingCustomer->name }}</h3>
                            @php
                                $tier = strtolower($viewingCustomer->membership_tier ?? 'silver');
                            @endphp
                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ $tier === 'platinum' ? 'bg-purple-100 text-purple-700 border border-purple-200' : ($tier === 'gold' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                                {{ $tier === 'platinum' ? '👑 Platinum VIP' : ($tier === 'gold' ? '🥇 Gold VIP' : '🥈 Silver Member') }}
                            </span>
                        </div>
                        <div class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-3">
                            @if($viewingCustomer->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $viewingCustomer->phone) }}" target="_blank"
                                   class="text-emerald-600 hover:text-emerald-700 font-semibold flex items-center gap-1">
                                    <i class="fa-brands fa-whatsapp"></i>
                                    <span>{{ $viewingCustomer->phone }}</span>
                                </a>
                            @endif
                            @if($viewingCustomer->email)
                                <span class="flex items-center gap-1 text-slate-500">
                                    <i class="fa-regular fa-envelope text-slate-400"></i>
                                    <span>{{ $viewingCustomer->email }}</span>
                                </span>
                            @endif
                            <span class="text-slate-400">
                                Terdaftar: {{ $viewingCustomer->created_at->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                </div>
                <button wire:click="closeCustomerDetailModal" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                <!-- 4 Mini Metrics Row -->
                @php
                    $completedOrders = $viewingCustomer->orders->where('status', 'completed');
                    $totalSpent = (float) $completedOrders->sum('total_amount');
                    $orderCount = $completedOrders->count();
                    $avgSpent = $orderCount > 0 ? ($totalSpent / $orderCount) : 0;
                    $lastOrder = $completedOrders->first();
                @endphp
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/70">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Belanja (LTV)</span>
                        <div class="font-black text-sm text-slate-900 mt-1 whitespace-nowrap">
                            Rp {{ number_format($totalSpent, 0, ',', '.') }}
                        </div>
                        <span class="text-[10px] text-slate-400">Lifetime Spending</span>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/70">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Kunjungan</span>
                        <div class="font-black text-sm text-slate-900 mt-1">
                            {{ $orderCount }} <span class="text-xs font-normal text-slate-400">Kali</span>
                        </div>
                        <span class="text-[10px] text-slate-400">Transaksi Selesai</span>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/70">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Saldo Poin</span>
                        <div class="font-black text-sm text-orange-600 mt-1">
                            {{ number_format($viewingCustomer->points, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400">Pts</span>
                        </div>
                        <span class="text-[10px] text-slate-400">Loyalty Rewards</span>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/70">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Terakhir Kunjung</span>
                        <div class="font-bold text-xs text-slate-800 mt-1 truncate">
                            {{ $lastOrder ? $lastOrder->created_at->format('d M Y') : 'Belum Pernah' }}
                        </div>
                        <span class="text-[10px] text-slate-400">
                            {{ $lastOrder ? $lastOrder->created_at->diffForHumans() : '-' }}
                        </span>
                    </div>
                </div>

                <!-- Informasi Biodata & Preferensi -->
                <div class="bg-orange-50/40 p-4 rounded-xl border border-orange-100 space-y-2 text-xs">
                    <h4 class="font-bold text-slate-900 uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                        <i class="fa-solid fa-clipboard-user text-orange-500"></i>
                        <span>Biodata & Catatan Khusus Pelanggan</span>
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1 text-slate-600">
                        <div>
                            <span class="text-slate-400 text-[10px] block">Jenis Kelamin:</span>
                            <span class="font-semibold capitalize">{{ $viewingCustomer->gender ? ($viewingCustomer->gender === 'female' ? 'Wanita' : 'Pria') : '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] block">Tanggal Lahir:</span>
                            <span class="font-semibold">{{ $viewingCustomer->birth_date ? $viewingCustomer->birth_date->format('d F Y') : '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] block">Domisili / Alamat:</span>
                            <span class="font-semibold">{{ $viewingCustomer->address ?? '-' }}</span>
                        </div>
                    </div>
                    @if($viewingCustomer->notes)
                        <div class="pt-2 border-t border-orange-200/60">
                            <span class="text-slate-400 text-[10px] block mb-0.5">Catatan Preferensi / Alergi:</span>
                            <span class="bg-white p-2.5 rounded-lg border border-orange-200 block text-slate-700 font-medium">
                                {{ $viewingCustomer->notes }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Riwayat Transaksi POS Pelanggan -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-receipt text-orange-500"></i>
                            <span>Riwayat Transaksi Belanja POS ({{ $viewingCustomer->orders->count() }} Struk)</span>
                        </h4>
                    </div>

                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase text-[10px]">
                                <tr>
                                    <th class="py-2.5 px-3">No. Order & Waktu</th>
                                    <th class="py-2.5 px-3">Outlet & Tipe</th>
                                    <th class="py-2.5 px-3">Rincian Item</th>
                                    <th class="py-2.5 px-3 text-right">Total Belanja</th>
                                    <th class="py-2.5 px-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($viewingCustomer->orders as $order)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="py-2.5 px-3">
                                            <div class="font-bold text-slate-900 font-mono">{{ $order->order_number }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $order->created_at->format('d/m/Y H:i') }} WIB</div>
                                        </td>
                                        <td class="py-2.5 px-3 text-slate-600">
                                            <div class="font-medium text-slate-800">{{ $order->outlet?->name ?? 'Outlet Utama' }}</div>
                                            <div class="text-[10px] capitalize text-slate-400">{{ str_replace('_', ' ', $order->order_type) }}</div>
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <span class="text-[11px] font-semibold text-slate-700">
                                                {{ $order->items->count() }} Item:
                                            </span>
                                            <span class="text-[11px] text-slate-500 truncate block max-w-xs" title="{{ $order->items->map(fn($i) => $i->qty . 'x ' . $i->product_name)->implode(', ') }}">
                                                {{ $order->items->take(2)->map(fn($i) => ((float)$i->qty == (int)$i->qty ? (int)$i->qty : $i->qty) . 'x ' . $i->product_name)->implode(', ') }}
                                                @if($order->items->count() > 2)
                                                    <span class="text-slate-400">+{{ $order->items->count() - 2 }} lainnya</span>
                                                @endif
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3 text-right font-black text-slate-900 whitespace-nowrap">
                                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                        </td>
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $order->status === 'completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($order->status === 'voided' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-amber-50 text-amber-700 border border-amber-200') }}">
                                                {{ $order->status === 'completed' ? 'Lunas' : ($order->status === 'voided' ? 'Void' : 'Hold') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-slate-400">
                                            Belum ada transaksi POS tercatat untuk pelanggan ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/80 flex items-center justify-between">
                <button wire:click="deleteCustomer({{ $viewingCustomer->id }})"
                        wire:confirm="Yakin ingin menghapus pelanggan {{ $viewingCustomer->name }} dari CRM?"
                        class="text-xs font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1.5">
                    <i class="fa-regular fa-trash-can"></i>
                    <span>Hapus Pelanggan</span>
                </button>
                <div class="flex items-center gap-2">
                    <button wire:click="closeCustomerDetailModal"
                            class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                        Tutup
                    </button>
                    <button wire:click="editCustomer({{ $viewingCustomer->id }}); closeCustomerDetailModal();"
                            class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Edit Data Pelanggan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
