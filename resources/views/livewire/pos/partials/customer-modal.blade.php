<!-- ================= MODAL: PELANGGAN (CRM / MEMBER) ================= -->
@if ($showCustomerModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Pilih Pelanggan / Member</h3>
                    <p class="text-[11px] text-slate-400">Hubungkan transaksi ke akun member untuk menambah poin</p>
                </div>
                <button wire:click="$set('showCustomerModal', false)" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            @if ($selectedCustomer)
                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-xs">
                    <div>
                        <span class="text-[10px] text-emerald-600 uppercase font-bold">Member Terpilih:</span>
                        <div class="font-bold text-emerald-900 text-sm">{{ $selectedCustomer['name'] }} ({{ $selectedCustomer['phone'] }})</div>
                        <div class="text-[11px] text-emerald-700">{{ $selectedCustomer['points'] }} Poin Tersedia</div>
                    </div>
                    <button wire:click="removeSelectedCustomer" class="text-xs text-rose-600 font-semibold hover:underline">Lepas</button>
                </div>
            @endif

            @if ($showNewMemberForm)
                <!-- Form Tambah Member Baru -->
                <form wire:submit="saveNewMember" class="p-4 bg-orange-50/60 border border-orange-200 rounded-xl space-y-3 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-orange-900">Tambah Member Baru</span>
                        <button type="button" wire:click="$set('showNewMemberForm', false)" class="text-slate-400 hover:text-slate-600 text-[11px] font-semibold">Batal</button>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="newMemberName" placeholder="Contoh: Budi Santoso"
                               class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500 @error('newMemberName') border-rose-400 @enderror" />
                        @error('newMemberName') <div class="text-rose-600 text-[11px] mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor WhatsApp <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="newMemberPhone" placeholder="08xxxxxxxxxx" inputmode="tel"
                               class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500 @error('newMemberPhone') border-rose-400 @enderror" />
                        @error('newMemberPhone') <div class="text-rose-600 text-[11px] mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Level Member</label>
                        <select wire:model="newMemberTier" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <option value="silver">Silver Member</option>
                            <option value="gold">Gold Member</option>
                            <option value="platinum">Platinum Member</option>
                        </select>
                    </div>

                    <button type="submit" wire:loading.attr="disabled"
                            class="w-full py-2 rounded-xl bg-orange-500 hover:bg-orange-600 disabled:opacity-60 text-white font-bold text-xs transition">
                        Simpan & Pilih Member
                    </button>
                </form>
            @else
                <button type="button" wire:click="openNewMemberForm"
                        class="w-full py-2 rounded-xl border border-dashed border-orange-300 text-orange-600 hover:bg-orange-50 font-bold text-xs transition">
                    <i class="fa-solid fa-user-plus"></i> Tambah Member Baru
                </button>
            @endif

            <div class="space-y-2 text-xs">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" wire:model.live.debounce.300ms="customerSearch"
                           placeholder="Cari nama atau nomor WhatsApp pelanggan..."
                           class="w-full pl-8 pr-3 py-2 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500" />
                </div>

                <div class="space-y-2 max-h-60 overflow-y-auto pt-1">
                    @forelse ($customersList as $cust)
                        <div class="p-3 rounded-xl border border-slate-200 flex items-center justify-between hover:bg-slate-50 transition">
                            <div>
                                <div class="font-bold text-slate-900">{{ $cust['name'] }} <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded-full font-semibold {{ $cust['tier'] === 'Platinum Member' || $cust['tier'] === 'Gold Member' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}">{{ $cust['tier'] }}</span></div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $cust['phone'] }} • {{ $cust['points'] }} Poin</div>
                            </div>
                            <button type="button" wire:click="selectCustomer({{ $cust['id'] }})"
                                    class="px-3 py-1.5 rounded-lg bg-orange-500 text-white font-bold text-[11px] hover:bg-orange-600 transition">
                                Pilih
                            </button>
                        </div>
                    @empty
                        <div class="py-6 text-center text-slate-400">Belum ada member yang cocok. Tambahkan member baru di atas.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endif
