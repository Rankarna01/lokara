                    <div class="space-y-6">
                        <!-- Top Header & Actions -->
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                            <div>
                                <h1 class="text-xl font-bold text-slate-900">Cabang Lokasi & Manajemen Staf Pegawai</h1>
                                <p class="text-xs text-slate-500 mt-0.5">Kelola cabang lokasi toko, akun login kasir masing-masing cabang, serta hak otorisasi PIN POS.</p>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <button wire:click="openNewOutletModal"
                                    class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center gap-2">
                                    <i class="fa-solid fa-store text-orange-500"></i>
                                    <span>+ Tambah Cabang Baru</span>
                                </button>
                                <button wire:click="openNewStaffModal"
                                    class="px-3.5 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-sm transition flex items-center gap-2">
                                    <i class="fa-solid fa-user-plus"></i>
                                    <span>+ Tambah Akun Pegawai</span>
                                </button>
                            </div>
                        </div>

                        <!-- Stats Row -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center font-bold">
                                    <i class="fa-solid fa-store"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-slate-400 font-medium">Total Cabang Lokasi</div>
                                    <div class="text-lg font-bold text-slate-900">{{ $outlets->count() }} Cabang</div>
                                </div>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-slate-400 font-medium">Total Akun Pegawai & Kasir</div>
                                    <div class="text-lg font-bold text-slate-900">{{ $cashiers->count() }} Staf</div>
                                </div>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-slate-200/80 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-slate-400 font-medium">Cabang Aktif Back Office</div>
                                    <div class="text-lg font-bold text-slate-900 truncate max-w-[180px]">{{ $selectedOutlet->name ?? 'Outlet Utama' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 1: Daftar Cabang Lokasi (Outlets) -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-store text-orange-500 text-sm"></i>
                                    <h3 class="font-bold text-slate-900 text-sm">Daftar Cabang Lokasi Toko</h3>
                                </div>
                                <span class="text-xs text-slate-400">{{ $outlets->count() }} Cabang Terdaftar</span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                                            <th class="py-3 px-4">Nama Cabang & Alamat</th>
                                            <th class="py-3 px-4">Kontak Cabang</th>
                                            <th class="py-3 px-4 text-center">Pajak & Service</th>
                                            <th class="py-3 px-4 text-center">Staf Ditugaskan</th>
                                            <th class="py-3 px-4 text-center">Status</th>
                                            <th class="py-3 px-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-xs">
                                        @forelse($outlets as $out)
                                            <tr class="hover:bg-slate-50/50 transition">
                                                <td class="py-3 px-4">
                                                    <div class="flex items-center gap-2.5">
                                                        <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-xs shrink-0">
                                                            <i class="fa-solid fa-shop"></i>
                                                        </div>
                                                        <div>
                                                            <div class="font-bold text-slate-900 flex items-center gap-2">
                                                                <span>{{ $out->name }}</span>
                                                                @if($selectedOutletId === $out->id)
                                                                    <span class="text-[10px] font-bold bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">Aktif Dipilih</span>
                                                                @endif
                                                            </div>
                                                            <div class="text-[11px] text-slate-400 truncate max-w-xs">{{ $out->address ?? 'Alamat belum diisi' }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-4 text-slate-600 font-medium">
                                                    {{ $out->phone ?? '-' }}
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    <span class="text-[11px] font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">
                                                        PPn {{ (int)$out->tax_rate }}% | Srv {{ (int)$out->service_rate }}%
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-center font-bold text-slate-700">
                                                    {{ $out->users_count ?? $out->users()->count() }} Staf
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    <button wire:click="toggleOutletStatus({{ $out->id }})"
                                                        class="text-[11px] font-semibold px-2 py-0.5 rounded-full transition {{ $out->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400' }}">
                                                        {{ $out->is_active ? '● Buka / Aktif' : '○ Tutup' }}
                                                    </button>
                                                </td>
                                                <td class="py-3 px-4 text-right space-x-1.5">
                                                    @if($selectedOutletId !== $out->id)
                                                        <button wire:click="switchOutlet({{ $out->id }})"
                                                            class="px-2.5 py-1 text-[11px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                                                            Pilih Cabang
                                                        </button>
                                                    @endif
                                                    <button wire:click="editOutlet({{ $out->id }})"
                                                        class="p-1.5 text-slate-400 hover:text-orange-600 transition" title="Edit Cabang">
                                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-8 text-center text-slate-400">Belum ada cabang outlet terdaftar.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Section 2: Daftar Akun Staf & Kasir Masing-Masing Cabang -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-users-gear text-orange-500 text-sm"></i>
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-sm">Daftar Akun Pegawai, Kasir & Hak Akses</h3>
                                        <p class="text-[11px] text-slate-400">Setiap pegawai memiliki akun login sendiri dengan batasan wewenang sesuai role dan cabang penugasan.</p>
                                    </div>
                                </div>
                                <span class="text-xs text-slate-400">{{ $cashiers->count() }} Pengguna</span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                                            <th class="py-3 px-4">Nama Pegawai & Email Login</th>
                                            <th class="py-3 px-4">Peran (Role)</th>
                                            <th class="py-3 px-4">Cabang Penugasan</th>
                                            <th class="py-3 px-4 text-center">PIN Kasir POS</th>
                                            <th class="py-3 px-4 text-center">Status</th>
                                            <th class="py-3 px-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-xs">
                                        @forelse($cashiers as $staff)
                                            <tr class="hover:bg-slate-50/50 transition">
                                                <td class="py-3 px-4">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-xs">
                                                            {{ substr($staff->name, 0, 2) }}
                                                        </div>
                                                        <div>
                                                            <div class="font-bold text-slate-900">{{ $staff->name }}</div>
                                                            <div class="text-[11px] text-slate-400 flex items-center gap-2">
                                                                <span><i class="fa-regular fa-envelope text-[10px]"></i> {{ $staff->email }}</span>
                                                                @if($staff->phone)
                                                                    <span>• <i class="fa-solid fa-phone text-[10px]"></i> {{ $staff->phone }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-4">
                                                    @if($staff->role === 'owner')
                                                        <span class="text-[11px] font-bold bg-amber-100 text-amber-800 px-2.5 py-0.5 rounded-full">👑 Owner (Pemilik)</span>
                                                    @elseif($staff->role === 'manager')
                                                        <span class="text-[11px] font-bold bg-blue-100 text-blue-800 px-2.5 py-0.5 rounded-full">👔 Manajer Cabang</span>
                                                    @elseif($staff->role === 'cashier')
                                                        <span class="text-[11px] font-bold bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-full">🖥️ Kasir POS</span>
                                                    @else
                                                        <span class="text-[11px] font-bold bg-purple-100 text-purple-800 px-2.5 py-0.5 rounded-full">✂️ Pegawai / Staf</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4">
                                                    <span class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                                                        <i class="fa-solid fa-location-dot text-slate-400 text-xs"></i>
                                                        {{ $staff->outlet->name ?? 'Semua Cabang' }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    <span class="text-xs font-mono font-bold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">
                                                        {{ $staff->pin_code ?? '------' }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    <button wire:click="toggleStaffStatus({{ $staff->id }})"
                                                        class="text-[11px] font-semibold px-2 py-0.5 rounded-full transition {{ $staff->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400' }}">
                                                        {{ $staff->is_active ? '● Aktif' : '○ Non-Aktif' }}
                                                    </button>
                                                </td>
                                                <td class="py-3 px-4 text-right space-x-1.5">
                                                    <button wire:click="editStaff({{ $staff->id }})"
                                                        class="p-1.5 text-slate-400 hover:text-orange-600 transition" title="Edit Staf">
                                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                                    </button>
                                                    @if($staff->role !== 'owner')
                                                        <button wire:click="deleteStaff({{ $staff->id }})"
                                                            wire:confirm="Yakin ingin menghapus akun staf {{ $staff->name }}?"
                                                            class="p-1.5 text-slate-400 hover:text-rose-600 transition" title="Hapus Staf">
                                                            <i class="fa-regular fa-trash-can text-xs"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-8 text-center text-slate-400">Belum ada akun pegawai terdaftar.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Section 3: Master Shift, Jadwal & Rekonsiliasi Kas Laci -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm">
                                        <i class="fa-solid fa-business-time"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-sm">Master Shift & Kebijakan Kasir</h3>
                                        <p class="text-[11px] text-slate-400">Atur template jadwal shift kerja, kebijakan buka/tutup kasir, serta modal kas awal laci.</p>
                                    </div>
                                </div>
                                <button wire:click="openNewShiftTemplateModal"
                                    class="px-3.5 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-xs transition flex items-center gap-2 shrink-0">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                    <span>+ Tambah Template Shift</span>
                                </button>
                            </div>

                            <div class="p-5 space-y-6">
                                <!-- Kebijakan & Aturan Kasir -->
                                <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl space-y-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-shield-halved text-orange-500 text-xs"></i>
                                            <span class="font-bold text-slate-900 text-xs">Kebijakan Operasional Kasir</span>
                                        </div>
                                        <button wire:click="saveShiftPolicies"
                                                class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-[11px] rounded-lg transition shadow-2xs">
                                            Simpan Kebijakan
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                                        <div class="bg-white p-3 rounded-xl border border-slate-200 space-y-1">
                                            <label class="block font-semibold text-slate-700 text-[11px]">Modal Kas Awal Default (Rp)</label>
                                            <div class="relative">
                                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs">Rp</span>
                                                <input type="number" wire:model="policyDefaultCash"
                                                       class="w-full pl-8 pr-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                                            </div>
                                            <p class="text-[10px] text-slate-400">Nominal rekomendasi saat kasir membuka shift baru.</p>
                                        </div>
                                        <div class="bg-white p-3 rounded-xl border border-slate-200 flex items-center justify-between">
                                            <div>
                                                <div class="font-semibold text-slate-800 text-[11px]">Wajib Buka Shift</div>
                                                <p class="text-[10px] text-slate-400">Kunci kasir hingga shift resmi dibuka</p>
                                            </div>
                                            <input type="checkbox" wire:model="policyEnforceLock" class="w-4 h-4 text-orange-500 rounded focus:ring-orange-500 cursor-pointer" />
                                        </div>
                                        <div class="bg-white p-3 rounded-xl border border-slate-200 flex items-center justify-between">
                                            <div>
                                                <div class="font-semibold text-slate-800 text-[11px]">Wajib Rekonsiliasi Kas</div>
                                                <p class="text-[10px] text-slate-400">Kasir wajib hitung fisik uang di laci</p>
                                            </div>
                                            <input type="checkbox" wire:model="policyRequireReconciliation" class="w-4 h-4 text-orange-500 rounded focus:ring-orange-500 cursor-pointer" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabel Template Shift -->
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-bold text-slate-800 text-xs flex items-center gap-2">
                                            <i class="fa-solid fa-clock text-slate-400"></i>
                                            <span>Master Jadwal Jam Shift</span>
                                        </h4>
                                        <span class="text-[11px] text-slate-400">{{ count($shiftTemplates) }} Template Tersedia</span>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        @foreach($shiftTemplates as $tmpl)
                                            <div class="p-4 rounded-2xl border transition {{ ($tmpl['is_active'] ?? true) ? 'bg-white border-slate-200 shadow-2xs' : 'bg-slate-50 border-slate-200/60 opacity-60' }}">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-bold text-slate-900 text-xs">{{ $tmpl['name'] }}</span>
                                                    <button wire:click="toggleShiftTemplateStatus({{ $tmpl['id'] }})"
                                                            class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ ($tmpl['is_active'] ?? true) ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">
                                                        {{ ($tmpl['is_active'] ?? true) ? 'Aktif' : 'Non-Aktif' }}
                                                    </button>
                                                </div>
                                                <div class="mt-2 space-y-1 text-xs">
                                                    <div class="flex items-center justify-between text-slate-500 text-[11px]">
                                                        <span>Jam Operasional:</span>
                                                        <span class="font-bold text-slate-800 font-mono">{{ $tmpl['start_time'] }} - {{ $tmpl['end_time'] }}</span>
                                                    </div>
                                                    <div class="flex items-center justify-between text-slate-500 text-[11px]">
                                                        <span>Modal Kas Laci:</span>
                                                        <span class="font-bold text-orange-600">Rp{{ number_format($tmpl['default_cash'] ?? 200000, 0, ',', '.') }}</span>
                                                    </div>
                                                </div>
                                                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-end gap-1.5">
                                                    <button wire:click="editShiftTemplate({{ $tmpl['id'] }})" class="p-1.5 text-slate-400 hover:text-orange-600 transition" title="Edit Template">
                                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                                    </button>
                                                    <button wire:click="deleteShiftTemplate({{ $tmpl['id'] }})" class="p-1.5 text-slate-400 hover:text-rose-600 transition" title="Hapus Template">
                                                        <i class="fa-regular fa-trash-can text-xs"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Riwayat Shift Kasir Terkini (Monitoring Log) -->
                                <div class="space-y-3 pt-2">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-bold text-slate-800 text-xs flex items-center gap-2">
                                            <i class="fa-solid fa-list-check text-slate-400"></i>
                                            <span>Monitoring Riwayat Shift Kasir Semua Cabang</span>
                                        </h4>
                                        <span class="text-[11px] text-slate-400">{{ $shiftHistory->count() }} Riwayat Terakhir</span>
                                    </div>
                                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                                        <table class="w-full text-left border-collapse text-xs">
                                            <thead>
                                                <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                                                    <th class="py-2.5 px-3">Kasir & Cabang</th>
                                                    <th class="py-2.5 px-3">Waktu Buka / Tutup</th>
                                                    <th class="py-2.5 px-3 text-right">Modal Awal</th>
                                                    <th class="py-2.5 px-3 text-right">Kas Aktual</th>
                                                    <th class="py-2.5 px-3 text-right">Selisih Kas</th>
                                                    <th class="py-2.5 px-3 text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                @forelse($shiftHistory as $sh)
                                                    <tr class="hover:bg-slate-50/50 transition">
                                                        <td class="py-2.5 px-3">
                                                            <div class="font-bold text-slate-900">{{ $sh->user->name ?? 'Kasir' }}</div>
                                                            <div class="text-[10px] text-slate-400 flex items-center gap-1">
                                                                <i class="fa-solid fa-store text-[9px]"></i>
                                                                <span>{{ $sh->outlet->name ?? 'Cabang' }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="py-2.5 px-3 text-[11px] text-slate-600">
                                                            <div>{{ $sh->opened_at ? $sh->opened_at->format('d/m/y H:i') : '-' }}</div>
                                                            <div class="text-[10px] text-slate-400">{{ $sh->closed_at ? 'Tutup: ' . $sh->closed_at->format('d/m/y H:i') : 'Masih berjalan' }}</div>
                                                        </td>
                                                        <td class="py-2.5 px-3 text-right font-semibold text-slate-800">
                                                            Rp{{ number_format($sh->opening_cash, 0, ',', '.') }}
                                                        </td>
                                                        <td class="py-2.5 px-3 text-right font-semibold text-slate-800">
                                                            {{ $sh->actual_cash !== null ? 'Rp' . number_format($sh->actual_cash, 0, ',', '.') : '-' }}
                                                        </td>
                                                        <td class="py-2.5 px-3 text-right font-bold {{ ($sh->difference ?? 0) < 0 ? 'text-rose-600' : (($sh->difference ?? 0) > 0 ? 'text-emerald-600' : 'text-slate-600') }}">
                                                            {{ $sh->difference !== null ? (($sh->difference > 0 ? '+' : '') . 'Rp' . number_format($sh->difference, 0, ',', '.')) : '-' }}
                                                        </td>
                                                        <td class="py-2.5 px-3 text-center">
                                                            @if($sh->closed_at)
                                                                <span class="text-[10px] font-bold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">Selesai</span>
                                                            @else
                                                                <span class="text-[10px] font-bold bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full animate-pulse">● Berjalan</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="py-6 text-center text-slate-400">Belum ada riwayat shift kasir tercatat.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
