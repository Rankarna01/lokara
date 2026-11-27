    <!-- Modal Tambah / Edit Pegawai -->
    @if ($showEmployeeModal)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">{{ $editingEmployeeId ? 'Edit Data Pegawai' : 'Tambah Pegawai Baru' }}</h3>
                    </div>
                    <button wire:click="$set('showEmployeeModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 space-y-4 text-sm">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Pegawai *</label>
                        <input type="text" wire:model="employeeName" placeholder="Contoh: Kak Sherly"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        @error('employeeName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jabatan / Peran *</label>
                        <input type="text" wire:model="employeeRoleTitle" placeholder="Contoh: Terapis / Stylist / Kapster"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        @error('employeeRoleTitle') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">No. HP / WhatsApp</label>
                            <input type="text" wire:model="employeePhone" placeholder="08123456789"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Cabang Penugasan</label>
                            <select wire:model="employeeOutletId"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-500 font-medium">
                                <option value="">Semua Cabang</option>
                                @foreach($outlets as $out)
                                    <option value="{{ $out->id }}">{{ $out->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700 pt-1">
                        <input type="checkbox" wire:model="employeeIsActive" class="rounded text-orange-500 focus:ring-orange-500">
                        <span>Pegawai aktif (tampil di pilihan pegawai POS Kasir)</span>
                    </label>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" wire:click="$set('showEmployeeModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">Batal</button>
                    <button type="button" wire:click="saveEmployee"
                        class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i>
                        <span>{{ $editingEmployeeId ? 'Simpan Perubahan' : 'Simpan Pegawai' }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Rincian Jasa & Fee Mingguan Pegawai -->
    @if ($showEmployeeFeeDetailModal)
        @php
            $detailList = $employeesData['detailItems'] ?? [];
            $detailEmp = $detailFeeEmployeeId ? ($employeesData['allEmployees']?->firstWhere('id', $detailFeeEmployeeId)?->name ?? 'Pegawai') : 'Belum Dipilih Pegawai';
        @endphp
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-2xl w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Rincian Fee Jasa — {{ $detailEmp }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Periode Minggu: {{ $detailFeeWeekLabel ?: '-' }}</p>
                    </div>
                    <button wire:click="closeEmployeeFeeDetail" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 max-h-[65vh] overflow-y-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-[11px] text-slate-500 uppercase font-bold">
                            <tr>
                                <th class="py-2.5 px-3">Tanggal & Struk</th>
                                <th class="py-2.5 px-3">Nama Jasa</th>
                                <th class="py-2.5 px-3 text-center">Qty</th>
                                <th class="py-2.5 px-3 text-right">Tarif</th>
                                <th class="py-2.5 px-3 text-right">Fee Pegawai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($detailList as $ditem)
                                <tr>
                                    <td class="py-2.5 px-3">
                                        <div class="font-mono font-bold text-slate-800">{{ $ditem['order_number'] }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $ditem['created_at'] }}</div>
                                    </td>
                                    <td class="py-2.5 px-3 font-semibold text-slate-800">{{ $ditem['service_name'] }}</td>
                                    <td class="py-2.5 px-3 text-center font-bold">{{ (int) $ditem['qty'] }}</td>
                                    <td class="py-2.5 px-3 text-right text-slate-600">Rp {{ number_format($ditem['subtotal'], 0, ',', '.') }}</td>
                                    <td class="py-2.5 px-3 text-right font-bold text-amber-600">Rp {{ number_format($ditem['fee'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400">Tidak ada rincian item jasa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if (count($detailList) > 0)
                            <tfoot class="bg-amber-50/70 border-t border-amber-200 font-bold text-xs">
                                <tr>
                                    <td colspan="4" class="py-2.5 px-3 text-right text-amber-900">Total Fee Minggu Ini:</td>
                                    <td class="py-2.5 px-3 text-right text-amber-700 text-sm">
                                        Rp {{ number_format(array_sum(array_column($detailList, 'fee')), 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                    <button type="button" wire:click="closeEmployeeFeeDetail"
                        class="px-4 py-2 text-xs font-semibold bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl">Tutup</button>
                </div>
            </div>
        </div>
    @endif
