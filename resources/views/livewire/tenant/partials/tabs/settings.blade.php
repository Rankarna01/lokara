<div class="space-y-6">
    <!-- Top Header & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-sliders"></i>
                </span>
                <h1 class="text-xl font-bold text-slate-900">
                    {{ $isSalon ? 'Pengaturan Toko, Cabang & Staf' : 'Pengaturan Toko, Cabang, Staf & Kasir' }}
                </h1>
                <span class="text-[11px] font-semibold text-orange-700 bg-orange-50 border border-orange-200/80 px-2 py-0.5 rounded-full flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                    Multi-Tenant System
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Kelola cabang toko, akun staf, kustomisasi struk thermal & logo, upload QRIS pembayaran, dan instalasi PWA mobile.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            @if($settingsSubTab === 'general')
                <button wire:click="openNewOutletModal"
                        class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center gap-2">
                    <i class="fa-solid fa-store text-orange-500"></i>
                    <span>+ Tambah Cabang</span>
                </button>
                <button wire:click="openNewStaffModal"
                        class="px-3.5 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-xs transition flex items-center gap-2">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>+ Tambah Akun Pegawai</span>
                </button>
            @elseif($settingsSubTab === 'receipt')
                <button wire:click="saveReceiptSettings"
                        class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Kustomisasi Struk</span>
                </button>
            @elseif($settingsSubTab === 'payments')
                <button wire:click="savePaymentSettings"
                        class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Pengaturan Pembayaran</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Sub-Tab Navigation Bar -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs font-bold border-b border-slate-200/80">
        <button wire:click="setSettingsSubTab('general')"
                class="px-4 py-2.5 rounded-xl transition flex items-center gap-2 whitespace-nowrap {{ $settingsSubTab === 'general' ? 'bg-orange-500 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/70' }}">
            <i class="fa-solid fa-store text-xs"></i>
            <span>{{ $isSalon ? '1. Cabang & Staf Pegawai' : '1. Cabang, Staf & Shift' }}</span>
        </button>

        <button wire:click="setSettingsSubTab('receipt')"
                class="px-4 py-2.5 rounded-xl transition flex items-center gap-2 whitespace-nowrap {{ $settingsSubTab === 'receipt' ? 'bg-orange-500 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/70' }}">
            <i class="fa-solid fa-receipt text-xs"></i>
            <span>2. Kustomisasi Struk Thermal & Logo</span>
        </button>

        <button wire:click="setSettingsSubTab('payments')"
                class="px-4 py-2.5 rounded-xl transition flex items-center gap-2 whitespace-nowrap {{ $settingsSubTab === 'payments' ? 'bg-orange-500 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/70' }}">
            <i class="fa-solid fa-qrcode text-xs"></i>
            <span>3. Metode Bayar & Upload QRIS</span>
        </button>

        <button wire:click="setSettingsSubTab('pwa')"
                class="px-4 py-2.5 rounded-xl transition flex items-center gap-2 whitespace-nowrap {{ $settingsSubTab === 'pwa' ? 'bg-orange-500 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/70' }}">
            <i class="fa-solid fa-mobile-screen-button text-xs"></i>
            <span>4. PWA Mobile App</span>
            <span class="px-1.5 py-0.2 rounded-full text-[9px] bg-emerald-100 text-emerald-700 font-black">Aktif</span>
        </button>
    </div>

    <!-- ========================================== -->
    <!-- SUB-TAB 1: CABANG & STAF                   -->
    <!-- ========================================== -->
    @if ($settingsSubTab === 'general')
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
                                <td class="py-3 px-4 text-center font-bold text-slate-800">
                                    {{ $out->users->count() }} Staf
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $out->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400' }}">
                                        {{ $out->is_active ? '● Aktif' : '○ Tutup' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right space-x-1.5">
                                    <button wire:click="editOutlet({{ $out->id }})"
                                        class="p-1.5 text-slate-400 hover:text-orange-600 transition" title="Edit Cabang">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    @if($outlets->count() > 1)
                                        <button wire:click="deleteOutlet({{ $out->id }})"
                                            wire:confirm="Yakin ingin menghapus cabang {{ $out->name }}?"
                                            class="p-1.5 text-slate-400 hover:text-rose-600 transition" title="Hapus Cabang">
                                            <i class="fa-regular fa-trash-can text-xs"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">Belum ada cabang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 2: Akun Pegawai & Kasir (Staff) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-users text-orange-500 text-sm"></i>
                    <h3 class="font-bold text-slate-900 text-sm">Daftar Akun Pegawai & Kasir POS</h3>
                </div>
                <span class="text-xs text-slate-400">{{ $cashiers->count() }} Akun Aktif</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                            <th class="py-3 px-4">Nama Pegawai & Email</th>
                            <th class="py-3 px-4 text-center">Peran (Role)</th>
                            <th class="py-3 px-4">Penempatan Cabang</th>
                            <th class="py-3 px-4 text-center">PIN Kasir POS</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($cashiers as $staff)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold flex items-center justify-center text-xs">
                                            {{ strtoupper(substr($staff->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $staff->name }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $staff->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center">
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
        @if($isSalon && !$showSalonShiftSection)
            <!-- Khusus Modul Salon & Barbershop: Master Shift Disembunyikan secara Default -->
            <div class="bg-gradient-to-r from-orange-50/70 to-amber-50/50 p-5 rounded-2xl border border-orange-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-base shadow-xs shrink-0">
                        <i class="fa-solid fa-scissors"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-slate-900 text-sm">Modul Salon & Barbershop (Operasional Fleksibel)</h3>
                            <span class="text-[10px] font-bold bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">Khusus Salon</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Layanan jasa salon beroperasi tanpa sistem shift kasir bergilir (komisi kapster / stylist dihitung langsung per transaksi layanan).
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button wire:click="toggleSalonShiftSection"
                        class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-semibold text-xs rounded-xl shadow-xs transition flex items-center gap-2">
                        <i class="fa-solid fa-business-time text-orange-500"></i>
                        <span>Tampilkan Master Shift</span>
                    </button>
                    <button wire:click="openNewShiftTemplateModal"
                        class="px-3.5 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-xs transition flex items-center gap-2">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>+ Tambah Template Shift</span>
                    </button>
                </div>
            </div>
        @else
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
                    <div class="flex items-center gap-2">
                        @if($isSalon)
                            <button wire:click="toggleSalonShiftSection"
                                class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs rounded-xl transition flex items-center gap-1.5">
                                <i class="fa-solid fa-eye-slash text-xs"></i>
                                <span>Sembunyikan Shift</span>
                            </button>
                        @endif
                        <button wire:click="openNewShiftTemplateModal"
                            class="px-3.5 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-xl shadow-xs transition flex items-center gap-2 shrink-0">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span>+ Tambah Template Shift</span>
                        </button>
                    </div>
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
                                            <span class="font-bold text-slate-800">{{ $tmpl['start_time'] }} - {{ $tmpl['end_time'] }}</span>
                                        </div>
                                        <div class="flex items-center justify-between text-slate-500 text-[11px]">
                                            <span>Kas Awal:</span>
                                            <span class="font-semibold text-orange-600">Rp{{ number_format($tmpl['default_cash'], 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
                                        <button wire:click="editShiftTemplate({{ $tmpl['id'] }})"
                                                class="text-[11px] text-slate-500 hover:text-orange-600 font-semibold">Edit</button>
                                        <button wire:click="deleteShiftTemplate({{ $tmpl['id'] }})"
                                                wire:confirm="Hapus template shift ini?"
                                                class="text-[11px] text-slate-400 hover:text-rose-600">Hapus</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Riwayat Sesi Shift Terakhir -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-800 text-xs flex items-center gap-2">
                                <i class="fa-solid fa-clock-rotate-left text-slate-400"></i>
                                <span>Audit Log Sesi Buka/Tutup Kasir Terakhir</span>
                            </h4>
                        </div>
                        <div class="border border-slate-200 rounded-xl overflow-hidden">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead class="bg-slate-50 border-b border-slate-200 text-[10px] uppercase font-bold text-slate-400">
                                    <tr>
                                        <th class="py-2.5 px-3">Kasir & Cabang</th>
                                        <th class="py-2.5 px-3">Waktu Buka / Tutup</th>
                                        <th class="py-2.5 px-3 text-right">Modal Awal</th>
                                        <th class="py-2.5 px-3 text-right">Fisik Laci</th>
                                        <th class="py-2.5 px-3 text-right">Selisih</th>
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
        @endif

    <!-- ========================================== -->
    <!-- SUB-TAB 2: KUSTOMISASI STRUK & LOGO TOKO  -->
    <!-- ========================================== -->
    @elseif ($settingsSubTab === 'receipt')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Form Pengaturan Struk (7 cols) -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Card 1: Logo Toko -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-image text-orange-500"></i>
                            <h3 class="text-sm font-bold text-slate-900">Logo Toko pada Struk Thermal</h3>
                        </div>
                        <span class="text-[11px] text-slate-400">Format PNG/JPG Max 2MB</span>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <!-- Preview Logo Box -->
                        <div class="w-24 h-24 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden shrink-0 relative group">
                            @if ($storeLogoFile)
                                <img src="{{ $storeLogoFile->temporaryUrl() }}" class="w-full h-full object-contain p-1" />
                            @elseif ($currentStoreLogoUrl)
                                <img src="{{ $currentStoreLogoUrl }}" class="w-full h-full object-contain p-1" />
                            @else
                                <div class="text-center p-2">
                                    <i class="fa-solid fa-store text-2xl text-slate-300"></i>
                                    <span class="text-[9px] text-slate-400 block mt-1 font-semibold">Belum Ada</span>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-2 flex-1 w-full">
                            <label class="block text-xs font-bold text-slate-700">Pilih File Logo Baru</label>
                            <input type="file"
                                   wire:model="storeLogoFile"
                                   accept="image/png,image/jpeg,image/webp"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer" />
                            <div wire:loading wire:target="storeLogoFile" class="text-[11px] text-orange-600 font-semibold">
                                <i class="fa-solid fa-spinner fa-spin mr-1"></i> Mengunggah logo toko...
                            </div>
                            @error('storeLogoFile')
                                <span class="text-[11px] text-rose-500 font-medium block">{{ $message }}</span>
                            @enderror
                            <div class="flex items-center gap-2 pt-1">
                                @if($currentStoreLogoUrl || $storeLogoFile)
                                    <button wire:click="removeStoreLogo"
                                            wire:confirm="Hapus logo toko dari struk?"
                                            type="button"
                                            class="text-xs text-rose-600 hover:text-rose-700 font-semibold flex items-center gap-1">
                                        <i class="fa-regular fa-trash-can"></i>
                                        <span>Hapus Logo</span>
                                    </button>
                                @endif
                                <label class="flex items-center gap-2 text-xs font-medium text-slate-600 cursor-pointer ml-auto">
                                    <input type="checkbox" wire:model.live="receiptShowLogo" class="rounded text-orange-500 focus:ring-orange-500" />
                                    <span>Tampilkan Logo di Header Struk</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Informasi Teks Struk -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                        <i class="fa-solid fa-align-left text-orange-500"></i>
                        <h3 class="text-sm font-bold text-slate-900">Format Teks & Informasi Toko</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Toko di Struk <span class="text-rose-500">*</span></label>
                            <input type="text"
                                   wire:model.live="receiptStoreName"
                                   placeholder="Contoh: Kopi Ruang Pagi"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-900 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tagline / Slogan</label>
                            <input type="text"
                                   wire:model.live="receiptTagline"
                                   placeholder="Contoh: Artisanal Coffee & Bakery"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Alamat / Informasi Header Tambahan</label>
                        <input type="text"
                               wire:model.live="receiptHeaderNotes"
                               placeholder="Contoh: Jl. Senopati No. 45 • Telp: 0812-3456-7890"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pesan Penutup / Catatan Footer</label>
                        <textarea wire:model.live="receiptFooterNotes"
                                  rows="2"
                                  placeholder="Contoh: Terima kasih atas kunjungan Anda! Barang yang dibeli tidak dapat ditukar."
                                  class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-medium text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Info WiFi Pelanggan</label>
                            <input type="text"
                                   wire:model.live="receiptWifiInfo"
                                   placeholder="SSID: Kopi-Free | Pass: ngopi123"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Instagram / Website</label>
                            <input type="text"
                                   wire:model.live="receiptSocialMedia"
                                   placeholder="@kopiruangpagi"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white" />
                        </div>
                    </div>
                </div>

                <!-- Card 3: Ukuran Kertas & Komponen Tampilan -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                        <i class="fa-solid fa-print text-orange-500"></i>
                        <h3 class="text-sm font-bold text-slate-900">Format Kertas Thermal & Visibilitas Baris</h3>
                    </div>

                    <!-- Paper Size Selector -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Ukuran Lebar Kertas Printer Thermal</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="p-3 rounded-xl border cursor-pointer transition flex items-center justify-between {{ $receiptPaperSize === '58mm' ? 'bg-orange-50/70 border-orange-500 text-orange-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-700 font-medium hover:bg-slate-100' }}">
                                <div class="flex items-center gap-2.5">
                                    <input type="radio" wire:model.live="receiptPaperSize" value="58mm" class="text-orange-500 focus:ring-orange-500" />
                                    <div>
                                        <div class="text-xs">58 mm (Kecil / Mobile)</div>
                                        <div class="text-[10px] text-slate-400 font-normal">Standar Printer Bluetooth Portabel</div>
                                    </div>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded bg-white border border-slate-200">32 Karakter</span>
                            </label>

                            <label class="p-3 rounded-xl border cursor-pointer transition flex items-center justify-between {{ $receiptPaperSize === '80mm' ? 'bg-orange-50/70 border-orange-500 text-orange-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-700 font-medium hover:bg-slate-100' }}">
                                <div class="flex items-center gap-2.5">
                                    <input type="radio" wire:model.live="receiptPaperSize" value="80mm" class="text-orange-500 focus:ring-orange-500" />
                                    <div>
                                        <div class="text-xs">80 mm (Lebar / Desktop)</div>
                                        <div class="text-[10px] text-slate-400 font-normal">Standar Printer USB / LAN Kasir</div>
                                    </div>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded bg-white border border-slate-200">48 Karakter</span>
                            </label>
                        </div>
                    </div>

                    <!-- Toggles Grid -->
                    <div class="pt-2 border-t border-slate-100 space-y-2">
                        <span class="block text-xs font-bold text-slate-700 mb-1">Visibilitas Data Tambahan:</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            <label class="flex items-center gap-2 p-2 rounded-lg bg-slate-50 border border-slate-100 cursor-pointer">
                                <input type="checkbox" wire:model.live="receiptShowCustomer" class="rounded text-orange-500 focus:ring-orange-500" />
                                <span>Nama Pelanggan</span>
                            </label>

                            <label class="flex items-center gap-2 p-2 rounded-lg bg-slate-50 border border-slate-100 cursor-pointer">
                                <input type="checkbox" wire:model.live="receiptShowCashier" class="rounded text-orange-500 focus:ring-orange-500" />
                                <span>Nama Kasir PIC</span>
                            </label>

                            <label class="flex items-center gap-2 p-2 rounded-lg bg-slate-50 border border-slate-100 cursor-pointer">
                                <input type="checkbox" wire:model.live="receiptShowTable" class="rounded text-orange-500 focus:ring-orange-500" />
                                <span>Tipe Order & Nomor Meja</span>
                            </label>

                            <label class="flex items-center gap-2 p-2 rounded-lg bg-slate-50 border border-slate-100 cursor-pointer">
                                <input type="checkbox" wire:model.live="receiptShowTax" class="rounded text-orange-500 focus:ring-orange-500" />
                                <span>Rincian Pajak PB1 & Diskon</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-3">
                        <button wire:click="saveReceiptSettings"
                                class="w-full py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Format Struk Thermal</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Preview Live Struk Thermal Kasir (5 cols) -->
            <div class="lg:col-span-5 flex flex-col items-center">
                <div class="sticky top-6 w-full space-y-3">
                    <div class="flex items-center justify-between px-1">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Live Mockup Struk Thermal</span>
                        </div>
                        <span class="text-[10px] font-mono font-bold bg-slate-100 text-slate-700 px-2 py-0.5 rounded">
                            {{ $receiptPaperSize }}
                        </span>
                    </div>

                    <!-- Thermal Paper Container -->
                    <div class="p-6 bg-slate-100 rounded-2xl border border-slate-200/80 flex justify-center shadow-inner">
                        <div id="liveSampleReceipt"
                             class="bg-white p-5 shadow-lg border border-slate-300 rounded-lg text-slate-800 font-mono text-[11px] leading-relaxed transition-all {{ $receiptPaperSize === '80mm' ? 'w-[360px]' : 'w-[290px]' }}">
                            <!-- Store Header -->
                            <div class="text-center space-y-1 mb-3 pb-3 border-b border-dashed border-slate-300">
                                @if($receiptShowLogo && ($storeLogoFile || $currentStoreLogoUrl))
                                    <div class="flex justify-center mb-1">
                                        <img src="{{ $storeLogoFile ? $storeLogoFile->temporaryUrl() : $currentStoreLogoUrl }}"
                                             class="w-12 h-12 object-contain" />
                                    </div>
                                @endif
                                <div class="font-black text-sm text-slate-900 uppercase tracking-wider">
                                    {{ $receiptStoreName ?: 'NAMA TOKO ANDA' }}
                                </div>
                                @if($receiptTagline)
                                    <div class="text-[10px] text-slate-500 font-semibold">
                                        {{ $receiptTagline }}
                                    </div>
                                @endif
                                @if($receiptHeaderNotes)
                                    <div class="text-[9px] text-slate-400 leading-tight">
                                        {{ $receiptHeaderNotes }}
                                    </div>
                                @endif
                                <div class="text-[9px] text-slate-400">
                                    {{ $selectedOutlet->name ?? 'Outlet Utama' }} • Telp: {{ $selectedOutlet->phone ?? '0812-3456-7890' }}
                                </div>
                            </div>

                            <!-- Order Meta Info -->
                            <div class="space-y-0.5 text-[10px] pb-3 border-b border-dashed border-slate-300">
                                <div class="flex justify-between">
                                    <span>No. Order:</span>
                                    <span class="font-bold">ORD-20261031-DEMO</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Waktu:</span>
                                    <span>{{ now()->format('d/m/Y H:i:s') }}</span>
                                </div>
                                @if($receiptShowCashier)
                                    <div class="flex justify-between">
                                        <span>Kasir:</span>
                                        <span>{{ auth()->user()->name ?? 'Kasir Lokara' }}</span>
                                    </div>
                                @endif
                                @if($receiptShowCustomer)
                                    <div class="flex justify-between">
                                        <span>Pelanggan:</span>
                                        <span>Budi Santoso (Member)</span>
                                    </div>
                                @endif
                                @if($receiptShowTable)
                                    <div class="flex justify-between">
                                        <span>Tipe:</span>
                                        <span>Dine In • Meja 05</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Sample Items -->
                            <div class="py-3 border-b border-dashed border-slate-300 space-y-2">
                                <div>
                                    <div class="font-bold text-slate-900 flex justify-between">
                                        <span>2x Es Kopi Susu Aren</span>
                                        <span>44.000</span>
                                    </div>
                                    <div class="text-[9px] text-slate-400 flex justify-between">
                                        <span>@ 22.000 (Regular • Less Sugar)</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 flex justify-between">
                                        <span>1x Croissant Butter</span>
                                        <span>18.000</span>
                                    </div>
                                    <div class="text-[9px] text-slate-400 flex justify-between">
                                        <span>@ 18.000 (Hangat)</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Totals Breakdown -->
                            <div class="py-3 border-b border-dashed border-slate-300 space-y-1 text-[10px]">
                                <div class="flex justify-between">
                                    <span>Subtotal:</span>
                                    <span>62.000</span>
                                </div>
                                @if($receiptShowTax)
                                    <div class="flex justify-between text-emerald-600">
                                        <span>Diskon Promo:</span>
                                        <span>-5.000</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Pajak (PB1 10%):</span>
                                        <span>5.700</span>
                                    </div>
                                @endif
                                <div class="flex justify-between text-xs font-black text-slate-900 pt-1 border-t border-slate-200">
                                    <span>TOTAL:</span>
                                    <span>Rp 62.700</span>
                                </div>
                            </div>

                            <!-- Payment Method Detail -->
                            <div class="py-2.5 border-b border-dashed border-slate-300 space-y-0.5 text-[10px]">
                                <div class="flex justify-between">
                                    <span>Pembayaran:</span>
                                    <span class="font-bold">TUNAI (CASH)</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Diterima:</span>
                                    <span>70.000</span>
                                </div>
                                <div class="flex justify-between font-bold text-emerald-700">
                                    <span>Kembalian:</span>
                                    <span>7.300</span>
                                </div>
                            </div>

                            <!-- Footer Section -->
                            <div class="text-center pt-3 text-[9px] text-slate-500 space-y-1">
                                <p class="font-bold text-slate-700">{{ $receiptFooterNotes ?: 'Terima kasih atas kunjungan Anda!' }}</p>
                                @if($receiptWifiInfo)
                                    <p class="text-slate-400 font-sans">{{ $receiptWifiInfo }}</p>
                                @endif
                                @if($receiptSocialMedia)
                                    <p class="text-slate-400 font-sans">{{ $receiptSocialMedia }}</p>
                                @endif
                                <p class="pt-1 text-[8px] uppercase tracking-wider text-slate-300">Powered by Lokara POS</p>
                            </div>
                        </div>
                    </div>

                    <!-- Print Sample Action Button -->
                    <button onclick="
                            var printContents = document.getElementById('liveSampleReceipt').innerHTML;
                            var printWindow = window.open('', '', 'height=600,width=400');
                            printWindow.document.write('<html><head><title>Sample Struk Thermal</title>');
                            printWindow.document.write('<style>body{font-family:monospace;font-size:12px;padding:15px;color:#000;} .border-b{border-bottom:1px dashed #999;} .flex{display:flex;justify-content:space-between;} .text-center{text-align:center;} .font-bold{font-weight:bold;}</style>');
                            printWindow.document.write('</head><body>');
                            printWindow.document.write(printContents);
                            printWindow.document.write('</body></html>');
                            printWindow.document.close();
                            printWindow.focus();
                            printWindow.print();
                            printWindow.close();
                        "
                        class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-print"></i>
                        <span>Cetak Sample Struk Thermal Sekarang</span>
                    </button>
                </div>
            </div>
        </div>

    <!-- ========================================== -->
    <!-- SUB-TAB 3: METODE PEMBAYARAN & UPLOAD QRIS-->
    <!-- ========================================== -->
    @elseif ($settingsSubTab === 'payments')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Form Pengaturan Pembayaran (7 cols) -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Section 1: Upload QRIS Pembayaran -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-qrcode text-orange-500"></i>
                            <h3 class="text-sm font-bold text-slate-900">Upload Barcode QRIS Resmi Toko</h3>
                        </div>
                        <label class="flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full cursor-pointer">
                            <input type="checkbox" wire:model.live="paymentQrisActive" class="rounded text-emerald-600 focus:ring-emerald-500" />
                            <span>{{ $paymentQrisActive ? 'QRIS Aktif di POS' : 'QRIS Non-Aktif' }}</span>
                        </label>
                    </div>

                    <p class="text-xs text-slate-500">
                        Unggah gambar standee / QRIS statis dari bank atau e-wallet (BCA, Mandiri, BRI, GoPay, ShopeePay, DANA) agar tampil langsung di layar pembayaran kasir POS.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <!-- Preview QRIS Box -->
                        <div class="w-32 h-32 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden shrink-0 relative">
                            @if ($qrisImageFile)
                                <img src="{{ $qrisImageFile->temporaryUrl() }}" class="w-full h-full object-contain p-1" />
                            @elseif ($currentQrisImageUrl)
                                <img src="{{ $currentQrisImageUrl }}" class="w-full h-full object-contain p-1" />
                            @else
                                <div class="text-center p-2">
                                    <i class="fa-solid fa-qrcode text-3xl text-slate-300"></i>
                                    <span class="text-[10px] text-slate-400 block mt-1 font-semibold">Belum Ada QRIS</span>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-3 flex-1 w-full">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">File Gambar Barcode QRIS</label>
                                <input type="file"
                                       wire:model="qrisImageFile"
                                       accept="image/png,image/jpeg,image/webp"
                                       class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer" />
                                <div wire:loading wire:target="qrisImageFile" class="text-[11px] text-orange-600 font-semibold mt-1">
                                    <i class="fa-solid fa-spinner fa-spin mr-1"></i> Mengunggah gambar QRIS...
                                </div>
                                @error('qrisImageFile')
                                    <span class="text-[11px] text-rose-500 font-medium block mt-0.5">{{ $message }}</span>
                                @enderror
                            </div>

                            @if($currentQrisImageUrl || $qrisImageFile)
                                <button wire:click="removeQrisImage"
                                        wire:confirm="Hapus gambar QRIS toko?"
                                        type="button"
                                        class="text-xs text-rose-600 hover:text-rose-700 font-semibold flex items-center gap-1">
                                    <i class="fa-regular fa-trash-can"></i>
                                    <span>Hapus Gambar QRIS</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Merchant QRIS</label>
                            <input type="text"
                                   wire:model.live="paymentQrisMerchantName"
                                   placeholder="Contoh: KOPI RUANG PAGI JAKARTA"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-900 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">National Merchant ID (NMID)</label>
                            <input type="text"
                                   wire:model.live="paymentQrisNmid"
                                   placeholder="Contoh: ID1020038475891"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white" />
                        </div>
                    </div>
                </div>

                <!-- Section 2: Rekening Transfer Bank -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-building-columns text-orange-500"></i>
                            <h3 class="text-sm font-bold text-slate-900">Rekening Tujuan Transfer Bank</h3>
                        </div>
                        <label class="flex items-center gap-1.5 text-xs font-bold text-sky-700 bg-sky-50 border border-sky-200 px-2.5 py-1 rounded-full cursor-pointer">
                            <input type="checkbox" wire:model.live="paymentTransferActive" class="rounded text-sky-600 focus:ring-sky-500" />
                            <span>{{ $paymentTransferActive ? 'Transfer Aktif di POS' : 'Transfer Non-Aktif' }}</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Pilihan Bank</label>
                            <select wire:model.live="paymentBankName"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <option value="BCA">Bank BCA</option>
                                <option value="Mandiri">Bank Mandiri</option>
                                <option value="BRI">Bank BRI</option>
                                <option value="BNI">Bank BNI</option>
                                <option value="BSI">Bank Syariah Indonesia (BSI)</option>
                                <option value="CIMB">Bank CIMB Niaga</option>
                                <option value="Permata">Bank Permata</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Rekening</label>
                            <input type="text"
                                   wire:model.live="paymentBankAccountNumber"
                                   placeholder="Contoh: 8271039482"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Atas Nama Pemilik</label>
                            <input type="text"
                                   wire:model.live="paymentBankAccountHolder"
                                   placeholder="Contoh: PT Kopi Ruang Pagi"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white" />
                        </div>
                    </div>
                </div>

                <!-- Section 3: Kanal Pembayaran Lainnya (Tunai & EDC) -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-2">Kanal Pembayaran Lainnya</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="p-3 rounded-xl border flex items-center justify-between cursor-pointer transition {{ $paymentCashActive ? 'bg-orange-50/60 border-orange-200' : 'bg-slate-50 border-slate-200' }}">
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-money-bill-wave text-amber-500"></i>
                                <div>
                                    <div class="text-xs font-bold text-slate-800">Tunai (Cash Laci)</div>
                                    <div class="text-[10px] text-slate-400">Pembayaran uang fisik di kasir</div>
                                </div>
                            </div>
                            <input type="checkbox" wire:model.live="paymentCashActive" class="rounded text-orange-500 focus:ring-orange-500" />
                        </label>

                        <label class="p-3 rounded-xl border flex items-center justify-between cursor-pointer transition {{ $paymentCardActive ? 'bg-orange-50/60 border-orange-200' : 'bg-slate-50 border-slate-200' }}">
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-credit-card text-violet-500"></i>
                                <div>
                                    <div class="text-xs font-bold text-slate-800">Kartu Debit / EDC Mesin</div>
                                    <div class="text-[10px] text-slate-400">Gesek kartu bank di mesin EDC</div>
                                </div>
                            </div>
                            <input type="checkbox" wire:model.live="paymentCardActive" class="rounded text-orange-500 focus:ring-orange-500" />
                        </label>
                    </div>

                    <div class="pt-3 border-t border-slate-100">
                        <button wire:click="savePaymentSettings"
                                class="w-full py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Pengaturan Pembayaran & QRIS</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Preview Tampilan Layar Kasir POS (5 cols) -->
            <div class="lg:col-span-5 flex flex-col">
                <div class="sticky top-6 space-y-4">
                    <div class="flex items-center justify-between px-1">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Preview Modal POS Kasir</span>
                        </div>
                        <span class="text-[10px] font-semibold text-slate-500">Tampilan ke Pelanggan</span>
                    </div>

                    <!-- Modal POS Screen Mockup -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-md space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Layar Pembayaran</span>
                                <div class="font-bold text-xs text-slate-900">Total: Rp 62.700</div>
                            </div>
                            <span class="text-[10px] font-bold bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">QRIS Aktif</span>
                        </div>

                        <!-- QRIS Mockup Display -->
                        <div class="text-center p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                            <div class="w-40 h-40 mx-auto p-2 bg-white rounded-xl border border-slate-200 shadow-xs flex items-center justify-center overflow-hidden">
                                @if($qrisImageFile)
                                    <img src="{{ $qrisImageFile->temporaryUrl() }}" class="w-full h-full object-contain" />
                                @elseif($currentQrisImageUrl)
                                    <img src="{{ $currentQrisImageUrl }}" class="w-full h-full object-contain" />
                                @else
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=LOKARA-QRIS-DEMO" class="w-full h-full object-contain opacity-50" />
                                @endif
                            </div>

                            <div class="font-black text-xs text-slate-900 uppercase">
                                {{ $paymentQrisMerchantName ?: 'NAMA MERCHANT QRIS' }}
                            </div>
                            <div class="text-[10px] text-slate-400 font-mono">
                                NMID: {{ $paymentQrisNmid ?: 'ID1020038475891' }}
                            </div>
                            <p class="text-[10px] text-slate-500 pt-1">
                                Scan QRIS menggunakan GoPay, OVO, Dana, ShopeePay, BCA, atau Livin Mandiri
                            </p>
                        </div>

                        <!-- Bank Transfer Mockup Display -->
                        @if($paymentTransferActive && $paymentBankAccountNumber)
                            <div class="p-3 bg-sky-50 rounded-xl border border-sky-200 text-xs space-y-1">
                                <div class="font-bold text-sky-900 flex items-center gap-1.5">
                                    <i class="fa-solid fa-building-columns text-sky-600"></i>
                                    <span>Transfer Bank {{ $paymentBankName }}</span>
                                </div>
                                <div class="flex items-center justify-between font-mono font-bold text-slate-800 text-sm pt-0.5">
                                    <span>{{ $paymentBankAccountNumber }}</span>
                                    <span class="text-[10px] text-sky-600 font-sans font-semibold">Salin</span>
                                </div>
                                <div class="text-[10px] text-slate-500">
                                    a.n. {{ $paymentBankAccountHolder ?: 'Pemilik Toko' }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    <!-- ========================================== -->
    <!-- SUB-TAB 4: PWA MOBILE APP                  -->
    <!-- ========================================== -->
    @elseif ($settingsSubTab === 'pwa')
        <div class="space-y-6">
            <!-- PWA Hero Banner -->
            <div class="bg-gradient-to-r from-orange-500 via-orange-600 to-amber-500 p-6 sm:p-8 rounded-3xl text-white shadow-lg space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-white/20 backdrop-blur-xs text-white">
                            <i class="fa-solid fa-mobile-screen-button"></i> Progressive Web App (PWA) Ready
                        </span>
                        <h2 class="text-xl sm:text-2xl font-black tracking-tight">
                            Jalankan Lokara POS Seperti Aplikasi Native di HP & Tablet
                        </h2>
                        <p class="text-xs sm:text-sm text-orange-100 max-w-2xl leading-relaxed">
                            Aplikasi kasir POS dan Back Office dapat diinstal langsung ke Layar Utama (Home Screen) smartphone Android, iPhone, dan iPad tanpa perlu melalui Google Play Store atau Apple App Store.
                        </p>
                    </div>

                    <div class="shrink-0 flex sm:flex-col items-center gap-2">
                        <a href="{{ route('pos.terminal') }}" target="_blank"
                           class="px-5 py-3 bg-white text-orange-600 hover:bg-orange-50 font-black text-xs rounded-2xl shadow-md transition flex items-center gap-2">
                            <i class="fa-solid fa-cash-register"></i>
                            <span>Buka POS di Layar Baru</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3 PWA Benefit Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-lg font-bold">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm">Instalasi Instan & Ringan</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Hanya butuh beberapa detik tanpa unduhan ratusan megabyte. Cukup klik "Install" atau "Tambah ke Layar Utama".
                    </p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg font-bold">
                        <i class="fa-solid fa-expand"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm">Layar Penuh (Standalone Mode)</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Bebas dari bilah URL browser (*address bar*). Memberikan kenyamanan transaksi kasir maksimal dan tampilan profesional.
                    </p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center text-lg font-bold">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm">Service Worker & Caching</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Pemuatan aset instan melalui background Service Worker yang secara otomatis meng-cache shell antarmuka sistem.
                    </p>
                </div>
            </div>

            <!-- Step by Step Install Guide: Android vs iOS -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Android Guide -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg">
                            <i class="fa-brands fa-android"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">Cara Instal di Android (Google Chrome)</h3>
                            <p class="text-xs text-slate-400">Kompatibel di semua smartphone dan tablet Android</p>
                        </div>
                    </div>

                    <ol class="space-y-3 text-xs text-slate-600 list-decimal list-inside leading-relaxed font-medium">
                        <li class="p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                            Buka browser <strong class="text-slate-900">Google Chrome</strong> di HP dan kunjungi alamat URL Lokara POS Anda.
                        </li>
                        <li class="p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                            Ketuk ikon <strong class="text-slate-900">Titik Tiga (⋮)</strong> di sudut kanan atas browser Chrome.
                        </li>
                        <li class="p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                            Pilih menu <strong class="text-orange-600 font-bold">"Install App"</strong> atau <strong class="text-orange-600 font-bold">"Tambahkan ke Layar Utama"</strong>.
                        </li>
                        <li class="p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                            Ikon <strong class="text-slate-900">Lokara POS</strong> akan otomatis terpasang di Home Screen HP Anda.
                        </li>
                    </ol>
                </div>

                <!-- iOS Guide -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center text-lg">
                            <i class="fa-brands fa-apple"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">Cara Instal di iPhone & iPad (Safari)</h3>
                            <p class="text-xs text-slate-400">Kompatibel di iOS Safari & iPadOS</p>
                        </div>
                    </div>

                    <ol class="space-y-3 text-xs text-slate-600 list-decimal list-inside leading-relaxed font-medium">
                        <li class="p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                            Buka browser resmi <strong class="text-slate-900">Safari</strong> di iPhone atau iPad Anda.
                        </li>
                        <li class="p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                            Ketuk tombol <strong class="text-slate-900">Bagikan (Share / 📤)</strong> di bar bawah layar Safari.
                        </li>
                        <li class="p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                            Gulir ke bawah dan pilih opsi <strong class="text-orange-600 font-bold">"Add to Home Screen" (Tambah ke Layar Utama)</strong>.
                        </li>
                        <li class="p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                            Ketuk <strong class="text-slate-900">"Add / Tambahkan"</strong> di pojok kanan atas. Aplikasi siap dibuka kapan saja.
                        </li>
                    </ol>
                </div>
            </div>

            <!-- PWA Manifest & Service Worker Diagnostic Status Card -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500"></i>
                        <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Status Konfigurasi Teknis PWA</h4>
                    </div>
                    <span class="text-[10px] font-bold bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">Semua Sistem Valid</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-[10px] text-slate-400 font-semibold">Web App Manifest:</div>
                        <div class="font-bold text-emerald-600 mt-0.5 flex items-center gap-1">
                            <i class="fa-solid fa-check text-[10px]"></i> /manifest.json (OK)
                        </div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-[10px] text-slate-400 font-semibold">Service Worker:</div>
                        <div class="font-bold text-emerald-600 mt-0.5 flex items-center gap-1">
                            <i class="fa-solid fa-check text-[10px]"></i> /sw.js (Terdaftar)
                        </div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-[10px] text-slate-400 font-semibold">Display Mode:</div>
                        <div class="font-bold text-slate-800 mt-0.5">Standalone (Layar Penuh)</div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="text-[10px] text-slate-400 font-semibold">Icon Maskable:</div>
                        <div class="font-bold text-slate-800 mt-0.5">192px & 512px High-Res</div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
