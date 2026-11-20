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

    <!-- Catatan keterbatasan data -->
    <div class="p-4 rounded-2xl border border-amber-200 bg-amber-50/70 flex gap-3">
        <i class="fa-solid fa-circle-info text-amber-600 mt-0.5"></i>
        <div class="text-xs text-amber-900 space-y-1">
            <p class="font-semibold">Komisi dihitung per layanan, belum per pegawai.</p>
            <p>Komisi = jumlah sesi layanan selesai × skema komisi master layanan. Transaksi POS belum mencatat kapster/stylist yang mengerjakan setiap layanan, sehingga rincian komisi per pegawai belum bisa ditampilkan.</p>
        </div>
    </div>

    <!-- Tabel Rincian Komisi per Layanan -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Rincian Komisi Pegawai per Layanan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Hanya layanan yang dikerjakan (sesi > 0) pada periode terpilih, diurutkan dari komisi terbesar</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-amber-50 text-amber-700 whitespace-nowrap">
                {{ count($commissionRows) }} layanan
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
    </div>
</div>
