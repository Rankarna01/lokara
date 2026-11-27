@php
    $commissionSummary = $fd['commissionSummary'];
    $commissionRows = $fd['commissionRows'];
    $totalCommissionAll = max(1, (float) $commissionSummary['total_commission']);
    $showCommissionSchemeColumn = $tenant?->businessType?->slug !== 'salon';
@endphp

<div class="space-y-4">
    @include('livewire.tenant.partials.tabs.finance-period-filter', ['periodKey' => 'commission', 'periodLabel' => $fd['commissionPeriodLabel']])

    <!-- Statistik Komisi -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 shadow-sm">
            <div class="text-[11px] text-slate-500 font-medium">Total Komisi Periode Ini</div>
            <div class="text-base font-bold text-amber-600 mt-1">Rp {{ number_format($commissionSummary['total_commission'], 0, ',', '.') }}</div>
        </div>
        <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 shadow-sm">
            <div class="text-[11px] text-slate-500 font-medium">Total Sesi Dikerjakan</div>
            <div class="text-base font-bold text-indigo-600 mt-1">{{ number_format($commissionSummary['total_sessions'], 0, ',', '.') }} sesi</div>
        </div>
        <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 shadow-sm">
            <div class="text-[11px] text-slate-500 font-medium">Rata-rata Komisi / Sesi</div>
            <div class="text-base font-bold text-slate-900 mt-1">Rp {{ number_format($commissionSummary['avg_commission'], 0, ',', '.') }}</div>
        </div>
        <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 shadow-sm">
            <div class="text-[11px] text-slate-500 font-medium">Layanan Berkomisi</div>
            <div class="text-base font-bold text-slate-900 mt-1">{{ $commissionSummary['service_count'] }} layanan</div>
        </div>
    </div>

    <!-- Banner Rekap Fee Mingguan per Pegawai -->
    <div class="p-4 rounded-2xl border border-orange-200 bg-orange-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex gap-3">
            <i class="fa-solid fa-user-tie text-orange-600 mt-0.5"></i>
            <div class="text-xs text-orange-950 space-y-0.5">
                <p class="font-bold">Ingin melihat total fee per pegawai setiap minggu (misal Kak Sherly per minggu)?</p>
                <p class="text-orange-800">Buka menu <strong>Pegawai & Fee</strong> untuk melihat total fee otomatis per pegawai dan rekap mingguannya.</p>
            </div>
        </div>
        <button wire:click="switchTab('employees')"
                class="px-3.5 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold shadow-xs transition shrink-0 cursor-pointer">
            Buka Rekap Fee Pegawai
        </button>
    </div>

    <!-- Tabel Rincian Komisi per Layanan -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Rincian Komisi Pegawai per Layanan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Hanya layanan yang dikerjakan (sesi > 0) pada periode terpilih, diurutkan dari komisi terbesar</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-amber-50 text-amber-700 whitespace-nowrap">
                {{ $fd['totalCommissionRows'] }} layanan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-[11px] text-slate-500 uppercase font-semibold">
                    <tr>
                        <th class="py-3 px-4">Nama Layanan</th>
                        <th class="py-3 px-3">Tag Kategori</th>
                        @if ($showCommissionSchemeColumn)
                            <th class="py-3 px-3 text-center">Skema Komisi</th>
                        @endif
                        <th class="py-3 px-3 text-center">Sesi Selesai</th>
                        <th class="py-3 px-3 text-right">Komisi / Sesi</th>
                        <th class="py-3 px-3 text-right">Total Komisi</th>
                        <th class="py-3 px-4 text-right">Porsi Komisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($commissionRows as $row)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 text-xs">{{ $row['name'] }}</div>
                                <div class="text-[11px] text-slate-400">{{ $row['category_name'] }}</div>
                            </td>
                            <td class="py-3 px-3">
                                <span class="text-[11px] font-medium px-2 py-0.5 rounded-md bg-orange-50 text-orange-700">
                                    {{ $row['category_tag'] }}
                                </span>
                            </td>
                            @if ($showCommissionSchemeColumn)
                                <td class="py-3 px-3 text-center text-[11px] text-amber-700 font-semibold">
                                    {{ $row['commission_scheme'] }}
                                </td>
                            @endif
                            <td class="py-3 px-3 text-center font-bold text-xs text-indigo-600">
                                {{ number_format($row['sessions_count'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-right text-xs text-slate-500 font-medium">
                                Rp {{ number_format($row['commission_per_session'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-right font-bold text-xs text-amber-600">
                                Rp {{ number_format($row['total_commission'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right text-xs font-semibold text-slate-700">
                                {{ number_format(($row['total_commission'] / $totalCommissionAll) * 100, 1, ',', '.') }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $showCommissionSchemeColumn ? 7 : 6 }}" class="py-8 text-center text-xs text-slate-400">
                                Belum ada layanan yang dikerjakan pada periode terpilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer: Count & Per Page Selector -->
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/70 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
            <div>
                @if ($commissionRows instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    Menampilkan <span class="font-bold text-slate-800">{{ $commissionRows->firstItem() ?? 0 }}</span> - <span class="font-bold text-slate-800">{{ $commissionRows->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-800">{{ $commissionRows->total() }}</span> layanan berkomisi
                @else
                    Menampilkan <span class="font-bold text-slate-800">{{ count($commissionRows) }}</span> layanan berkomisi
                @endif
            </div>

            <div class="flex items-center gap-2">
                <span>Tampilkan:</span>
                <select wire:model.live="financePerPage"
                        class="bg-white border border-slate-200 text-xs font-semibold text-slate-700 py-1 px-2.5 rounded-lg focus:outline-none focus:ring-1 focus:ring-orange-500">
                    <option value="10">10 Baris</option>
                    <option value="25">25 Baris</option>
                    <option value="50">50 Baris</option>
                    <option value="100">100 Baris</option>
                </select>
            </div>
        </div>

        @if ($commissionRows instanceof \Illuminate\Pagination\LengthAwarePaginator && $commissionRows->hasPages())
            <div class="px-4 py-3 border-t border-slate-100 bg-white">
                {{ $commissionRows->links() }}
            </div>
        @endif
    </div>
</div>
