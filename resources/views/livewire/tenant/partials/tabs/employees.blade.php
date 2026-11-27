@php
    $ed = $employeesData ?? [
        'allEmployees' => collect(),
        'employees' => collect(),
        'periodLabel' => '4 Minggu Terakhir',
        'weeklyRows' => [],
        'employeeTotals' => [],
        'detailItems' => [],
        'summary' => [
            'total_fee' => 0,
            'assigned_fee' => 0,
            'unassigned_fee' => 0,
            'total_sessions' => 0,
            'active_employees_count' => 0,
        ],
    ];
    $summary = $ed['summary'];
@endphp

<div class="space-y-6">
    <!-- 1. Header -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900 tracking-tight">Pegawai & Rekap Fee Mingguan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola daftar pegawai/terapis dan pantau total fee jasa otomatis per minggu tanpa hitung manual.</p>
                </div>
            </div>
        </div>

        <button wire:click="openNewEmployeeModal"
                class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2 shrink-0 cursor-pointer">
            <i class="fa-solid fa-user-plus"></i>
            <span>Tambah Pegawai</span>
        </button>
    </div>

    <!-- 2. Summary Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Fee Pegawai</div>
            <div class="text-2xl font-black text-amber-600 mt-2 whitespace-nowrap">
                Rp {{ number_format($summary['total_fee'], 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">Periode: {{ $ed['periodLabel'] }}</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Sesi Jasa</div>
            <div class="text-2xl font-black text-indigo-600 mt-2">
                {{ number_format($summary['total_sessions'], 0, ',', '.') }} <span class="text-sm font-semibold text-slate-400">Sesi</span>
            </div>
            <div class="text-[11px] text-slate-400 mt-1">Jasa selesai dikerjakan</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Pegawai Aktif</div>
            <div class="text-2xl font-black text-emerald-600 mt-2">
                {{ $summary['active_employees_count'] }} <span class="text-sm font-semibold text-slate-400">Orang</span>
            </div>
            <div class="text-[11px] text-slate-400 mt-1">Tersedia dipilih di POS Kasir</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Belum Pilih Pegawai</div>
            <div class="text-2xl font-black {{ $summary['unassigned_fee'] > 0 ? 'text-rose-600' : 'text-slate-400' }} mt-2 whitespace-nowrap">
                Rp {{ number_format($summary['unassigned_fee'], 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
                {{ $summary['unassigned_fee'] > 0 ? 'Fee jasa tanpa nama pegawai' : 'Semua jasa sudah ada pegawainya' }}
            </div>
        </div>
    </div>

    <!-- 3. Filter Periode & Pegawai untuk Rekap Fee -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-1.5 bg-slate-100 p-1 rounded-xl text-xs font-bold">
                <button wire:click="setEmployeeFeePeriod('this_week')"
                        class="px-3 py-1.5 rounded-lg transition cursor-pointer {{ $employeeFeePeriod === 'this_week' ? 'bg-white text-orange-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Minggu Ini
                </button>
                <button wire:click="setEmployeeFeePeriod('last_week')"
                        class="px-3 py-1.5 rounded-lg transition cursor-pointer {{ $employeeFeePeriod === 'last_week' ? 'bg-white text-orange-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Minggu Lalu
                </button>
                <button wire:click="setEmployeeFeePeriod('4_weeks')"
                        class="px-3 py-1.5 rounded-lg transition cursor-pointer {{ $employeeFeePeriod === '4_weeks' ? 'bg-white text-orange-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    4 Minggu Terakhir
                </button>
                <button wire:click="setEmployeeFeePeriod('this_month')"
                        class="px-3 py-1.5 rounded-lg transition cursor-pointer {{ $employeeFeePeriod === 'this_month' ? 'bg-white text-orange-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Bulan Ini
                </button>
                <button wire:click="setEmployeeFeePeriod('last_month')"
                        class="px-3 py-1.5 rounded-lg transition cursor-pointer {{ $employeeFeePeriod === 'last_month' ? 'bg-white text-orange-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Bulan Lalu
                </button>
                <button wire:click="setEmployeeFeePeriod('custom')"
                        class="px-3 py-1.5 rounded-lg transition cursor-pointer flex items-center gap-1 {{ $employeeFeePeriod === 'custom' ? 'bg-white text-orange-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    <i class="fa-solid fa-calendar-days text-[11px]"></i>
                    <span>Rentang Tanggal</span>
                </button>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-slate-500 whitespace-nowrap">Filter Pegawai:</span>
                <select wire:model.live="selectedFeeEmployeeId"
                        class="bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 py-2 px-3 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500">
                    <option value="all">Semua Pegawai</option>
                    @foreach ($ed['allEmployees'] as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                    @endforeach
                    <option value="unassigned">Belum Dipilih Pegawai</option>
                </select>
            </div>
        </div>

        @if ($employeeFeePeriod === 'custom')
            <div class="p-3 bg-orange-50/70 border border-orange-200 rounded-xl flex flex-wrap items-center gap-3 text-xs">
                <span class="font-bold text-orange-900 flex items-center gap-1.5">
                    <i class="fa-solid fa-calendar-week text-orange-500"></i>
                    <span>Rentang Kustom:</span>
                </span>
                <div class="flex items-center gap-2">
                    <span class="text-slate-600">Dari:</span>
                    <input type="date" wire:model.live="employeeFeeStartDate"
                           class="bg-white border border-slate-300 rounded-lg px-2.5 py-1 text-xs font-semibold text-slate-700" />
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-slate-600">Sampai:</span>
                    <input type="date" wire:model.live="employeeFeeEndDate"
                           class="bg-white border border-slate-300 rounded-lg px-2.5 py-1 text-xs font-semibold text-slate-700" />
                </div>
            </div>
        @endif
    </div>

    <!-- 4. Ringkasan Total Fee per Pegawai (Periode Terpilih) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Total Fee per Pegawai ({{ $ed['periodLabel'] }})</h3>
                <p class="text-xs text-slate-400 mt-0.5">Akumulasi otomatis seluruh fee jasa berdasarkan pegawai yang mengerjakan di POS Kasir.</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-amber-50 text-amber-700 whitespace-nowrap">
                {{ count($ed['employeeTotals']) }} Pegawai
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-[11px] text-slate-500 uppercase font-bold">
                    <tr>
                        <th class="py-3 px-4">Nama Pegawai</th>
                        <th class="py-3 px-3">Jabatan</th>
                        <th class="py-3 px-3 text-center">Jumlah Minggu</th>
                        <th class="py-3 px-3 text-center">Total Sesi Jasa</th>
                        <th class="py-3 px-3 text-right">Total Omset Jasa</th>
                        <th class="py-3 px-4 text-right">Total Fee Diterima</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($ed['employeeTotals'] as $row)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $row['employee_name'] }}</td>
                            <td class="py-3 px-3 text-slate-500">{{ $row['role_title'] }}</td>
                            <td class="py-3 px-3 text-center font-semibold text-slate-700">{{ $row['weeks_count'] }} minggu</td>
                            <td class="py-3 px-3 text-center font-bold text-indigo-600">{{ number_format($row['sessions_count'], 0, ',', '.') }} sesi</td>
                            <td class="py-3 px-3 text-right text-slate-600 font-medium">Rp {{ number_format($row['total_sales'], 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-extrabold text-amber-600 text-sm">
                                Rp {{ number_format($row['total_fee'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Belum ada transaksi jasa pada periode {{ $ed['periodLabel'] }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 5. Rekap Fee Mingguan per Pegawai (Senin - Minggu) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Rekap Fee Mingguan (Per Minggu: Senin – Minggu)</h3>
                <p class="text-xs text-slate-400 mt-0.5">Total fee masing-masing pegawai otomatis dikelompokkan per minggu agar mudah dibayarkan setiap minggu.</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 whitespace-nowrap">
                {{ count($ed['weeklyRows']) }} Baris Mingguan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-[11px] text-slate-500 uppercase font-bold">
                    <tr>
                        <th class="py-3 px-4">Periode Minggu</th>
                        <th class="py-3 px-3">Nama Pegawai</th>
                        <th class="py-3 px-3 text-center">Sesi Jasa</th>
                        <th class="py-3 px-3 text-right">Omset Jasa</th>
                        <th class="py-3 px-3 text-right">Total Fee Mingguan</th>
                        <th class="py-3 px-4 text-right">Rincian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($ed['weeklyRows'] as $wrow)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $wrow['week_label'] }}</div>
                                <div class="text-[10px] text-slate-400">Senin – Minggu</div>
                            </td>
                            <td class="py-3 px-3">
                                <div class="font-bold text-slate-800">{{ $wrow['employee_name'] }}</div>
                                <div class="text-[10px] text-slate-400">{{ $wrow['role_title'] }}</div>
                            </td>
                            <td class="py-3 px-3 text-center font-bold text-indigo-600">
                                {{ number_format($wrow['sessions_count'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-right text-slate-600 font-medium">
                                Rp {{ number_format($wrow['total_sales'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-right font-extrabold text-amber-600 text-sm">
                                Rp {{ number_format($wrow['total_fee'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <button wire:click="openEmployeeFeeDetail({{ $wrow['employee_id'] !== null ? (int) $wrow['employee_id'] : 'null' }}, '{{ $wrow['week_key'] }}', '{{ $wrow['week_label'] }}')"
                                        class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-orange-50 text-slate-700 hover:text-orange-600 font-semibold text-[11px] transition cursor-pointer">
                                    Lihat Jasa
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Belum ada catatan fee mingguan pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 6. Daftar Master Pegawai / Terapis -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Daftar Master Pegawai / Terapis</h3>
                <p class="text-xs text-slate-400 mt-0.5">Pegawai aktif akan muncul otomatis di pilihan pegawai saat penjualan jasa di layar POS Kasir.</p>
            </div>
            <div class="relative w-full sm:w-64">
                <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" wire:model.live.debounce.300ms="searchEmployee" placeholder="Cari nama pegawai..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-8 pr-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-[11px] text-slate-500 uppercase font-bold">
                    <tr>
                        <th class="py-3 px-4">Nama Pegawai</th>
                        <th class="py-3 px-3">Jabatan / Peran</th>
                        <th class="py-3 px-3">No. HP / WhatsApp</th>
                        <th class="py-3 px-3">Cabang</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($ed['employees'] as $emp)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $emp->name }}</td>
                            <td class="py-3 px-3 text-slate-600">{{ $emp->role_title }}</td>
                            <td class="py-3 px-3 text-slate-500">{{ $emp->phone ?: '-' }}</td>
                            <td class="py-3 px-3 text-slate-500">{{ $emp->outlet?->name ?? 'Semua Cabang' }}</td>
                            <td class="py-3 px-3 text-center">
                                <button wire:click="toggleEmployeeStatus({{ $emp->id }})"
                                        class="px-2.5 py-0.5 rounded-full text-[11px] font-bold cursor-pointer {{ $emp->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $emp->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <button wire:click="editEmployee({{ $emp->id }})"
                                            class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-orange-50 text-slate-700 hover:text-orange-600 font-semibold transition cursor-pointer">
                                        Edit
                                    </button>
                                    <button wire:click="deleteEmployee({{ $emp->id }})"
                                            wire:confirm="Yakin ingin menghapus pegawai '{{ $emp->name }}'? Riwayat transaksi lama tetap tersimpan."
                                            class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold transition cursor-pointer">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Belum ada data pegawai. Klik <strong>Tambah Pegawai</strong> untuk menambahkan pegawai seperti Kak Sherly, terapis, atau kapster.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
