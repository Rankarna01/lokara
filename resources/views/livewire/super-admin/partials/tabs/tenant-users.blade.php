                    <div class="space-y-5">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Akun Pengguna Tenant</h1>
                                <p class="text-sm text-slate-500 mt-0.5">Daftar pemilik dan staf lintas seluruh bisnis yang aktif di platform</p>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <button type="button"
                                        wire:click="openCreateUserModal"
                                        class="px-3.5 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold shadow-sm shadow-orange-500/20 flex items-center gap-2 transition cursor-pointer">
                                    <i class="fa-solid fa-user-plus"></i>
                                    <span>Buat Akun Pengguna</span>
                                </button>
                            </div>
                        </div>

                        <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-sm">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="p-4 rounded-lg bg-slate-50 border border-slate-200/60">
                                    <div class="text-xs font-semibold text-slate-400 uppercase">Total User Terdaftar</div>
                                    <div class="text-2xl font-bold text-slate-900 mt-1">3.412</div>
                                </div>
                                <div class="p-4 rounded-lg bg-slate-50 border border-slate-200/60">
                                    <div class="text-xs font-semibold text-slate-400 uppercase">User Aktif Hari Ini</div>
                                    <div class="text-2xl font-bold text-emerald-600 mt-1">1.840</div>
                                </div>
                                <div class="p-4 rounded-lg bg-slate-50 border border-slate-200/60">
                                    <div class="text-xs font-semibold text-slate-400 uppercase">Role Terbanyak</div>
                                    <div class="text-2xl font-bold text-orange-500 mt-1">Kasir (64%)</div>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-slate-500">
                                        <tr>
                                            <th class="py-3 px-4 font-semibold">Nama Akun</th>
                                            <th class="py-3 px-4 font-semibold">Tenant Asal</th>
                                            <th class="py-3 px-4 font-semibold">Peran (Role)</th>
                                            <th class="py-3 px-4 font-semibold">Login Terakhir</th>
                                            <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach ($allTenants as $t)
                                            <tr class="hover:bg-slate-50/50 transition">
                                                <td class="py-3 px-4 font-semibold text-slate-900">
                                                    {{ $t->owner_name }}
                                                    <span class="block text-[11px] font-normal text-slate-400">{{ $t->owner_email }}</span>
                                                </td>
                                                <td class="py-3 px-4 text-slate-700 font-medium">{{ $t->name }}</td>
                                                <td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold text-[10px]">Owner</span></td>
                                                <td class="py-3 px-4 text-slate-500">Hari ini, 14:20</td>
                                                <td class="py-3 px-4 text-right">
                                                    <button type="button"
                                                            wire:click="openResetPasswordModal('{{ $t->owner_email }}', '{{ addslashes($t->owner_name) }}', '{{ addslashes($t->name) }}', 'Owner')"
                                                            class="px-2.5 py-1 text-[11px] rounded-lg border border-slate-200 text-slate-700 hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 font-medium transition cursor-pointer">
                                                        Reset Password
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
