    <!-- ================= MODAL TAMBAH TENANT BARU ================= -->
    @if ($showCreateTenantModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
             x-data
             @keydown.escape.window="$wire.closeCreateTenantModal()"
             @click.self="$wire.closeCreateTenantModal()">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 animate-scale-up space-y-4"
                 @click.outside="$wire.closeCreateTenantModal()">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-slate-900">Daftarkan Tenant Baru</h3>
                    <button type="button"
                            wire:click="closeCreateTenantModal"
                            @click.prevent="$wire.set('showCreateTenantModal', false)"
                            class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                @if ($errors->any())
                    <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3">
                        <div class="w-6 h-6 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 text-xs font-bold mt-0.5">
                            <i class="fa-solid fa-circle-exclamation"></i>
                        </div>
                        <div>
                            <div class="font-bold text-rose-900">Ada Kolom Wajib yang Belum Diisi!</div>
                            <div class="text-[11px] text-rose-600 mt-0.5">
                                Terdapat <strong>{{ $errors->count() }}</strong> kolom yang wajib diisi atau belum valid. Silakan periksa kolom bergaris merah di bawah.
                            </div>
                        </div>
                    </div>
                @endif

                <form wire:submit.prevent="createTenant" class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">
                            Nama Bisnis / Toko <span class="text-rose-500 font-bold">*</span>
                        </label>
                        <input type="text"
                               wire:model.live="newTenantName"
                               placeholder="Contoh: Kopi Ruang Pagi"
                               class="w-full border rounded-xl px-3 py-2 text-xs text-slate-800 transition focus:outline-none @error('newTenantName') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                        @error('newTenantName')
                            <div class="flex items-center gap-1.5 text-rose-600 font-medium text-[11px] mt-1">
                                <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Subdomain Slug <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <div class="flex items-center border rounded-xl overflow-hidden @error('newTenantSlug') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 @else border-slate-200 @enderror">
                                <input type="text"
                                       wire:model="newTenantSlug"
                                       placeholder="kopi-ruang-pagi"
                                       class="w-full px-3 py-2 text-xs text-slate-800 focus:outline-none bg-transparent" />
                                <span class="bg-slate-100 px-2.5 py-2 text-slate-400 text-[11px] font-mono">.lokara.id</span>
                            </div>
                            @error('newTenantSlug')
                                <div class="flex items-center gap-1.5 text-rose-600 font-medium text-[11px] mt-1">
                                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Kota Asal <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <input type="text"
                                   wire:model="newTenantCity"
                                   placeholder="Jakarta Selatan"
                                   class="w-full border rounded-xl px-3 py-2 text-xs text-slate-800 transition focus:outline-none @error('newTenantCity') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                            @error('newTenantCity')
                                <div class="flex items-center gap-1.5 text-rose-600 font-medium text-[11px] mt-1">
                                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Jenis Usaha <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <select wire:model="newTenantBusinessType"
                                    class="w-full border rounded-xl px-3 py-2 text-xs text-slate-800 transition focus:outline-none @error('newTenantBusinessType') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror">
                                <option value="">-- Pilih Jenis Usaha --</option>
                                @foreach ($businessTypes as $bt)
                                    <option value="{{ $bt->id }}">{{ $bt->name }}</option>
                                @endforeach
                            </select>
                            @error('newTenantBusinessType')
                                <div class="flex items-center gap-1.5 text-rose-600 font-medium text-[11px] mt-1">
                                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Pilihan Paket <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <select wire:model="newTenantPlan"
                                    class="w-full border rounded-xl px-3 py-2 text-xs text-slate-800 transition focus:outline-none @error('newTenantPlan') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror">
                                <option value="">-- Pilih Paket Langganan --</option>
                                @foreach ($plans as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} (Rp {{ number_format($p->price_monthly, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                            @error('newTenantPlan')
                                <div class="flex items-center gap-1.5 text-rose-600 font-medium text-[11px] mt-1">
                                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="font-semibold text-slate-700">
                                Kontak Pemilik (Owner PIC) <span class="text-rose-500 font-bold">*</span>
                            </div>
                            <span class="text-[10px] text-slate-400">Wajib diisi untuk akses login pemilik</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <div>
                                <input type="text"
                                       wire:model="newTenantOwnerName"
                                       placeholder="Nama Pemilik *"
                                       class="w-full border rounded-xl px-3 py-2 text-xs text-slate-800 transition focus:outline-none @error('newTenantOwnerName') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 placeholder-rose-300 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                                @error('newTenantOwnerName')
                                    <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <input type="email"
                                       wire:model="newTenantOwnerEmail"
                                       placeholder="Email *"
                                       class="w-full border rounded-xl px-3 py-2 text-xs text-slate-800 transition focus:outline-none @error('newTenantOwnerEmail') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 placeholder-rose-300 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                                @error('newTenantOwnerEmail')
                                    <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <input type="text"
                                       wire:model="newTenantOwnerPhone"
                                       placeholder="No. WhatsApp *"
                                       class="w-full border rounded-xl px-3 py-2 text-xs text-slate-800 transition focus:outline-none @error('newTenantOwnerPhone') border-rose-400 ring-1 ring-rose-400 bg-rose-50/30 text-rose-900 placeholder-rose-300 @else border-slate-200 focus:ring-1 focus:ring-orange-500 @enderror" />
                                @error('newTenantOwnerPhone')
                                    <div class="text-rose-600 font-medium text-[10px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button"
                                wire:click="closeCreateTenantModal"
                                @click.prevent="$wire.set('showCreateTenantModal', false)"
                                class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium text-xs transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs shadow-sm shadow-orange-500/20 transition flex items-center gap-2 cursor-pointer">
                            <span wire:loading.remove wire:target="createTenant">Daftarkan Sekarang</span>
                            <span wire:loading wire:target="createTenant" class="flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-notch fa-spin"></i>
                                <span>Memproses...</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
