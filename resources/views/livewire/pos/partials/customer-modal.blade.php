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

            <div class="space-y-2 text-xs">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" placeholder="Cari nama atau nomor WhatsApp pelanggan..." class="w-full pl-8 pr-3 py-2 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500" />
                </div>

                <div class="space-y-2 max-h-60 overflow-y-auto pt-1">
                    @foreach ($customersList as $cust)
                        <div class="p-3 rounded-xl border border-slate-200 flex items-center justify-between hover:bg-slate-50 transition">
                            <div>
                                <div class="font-bold text-slate-900">{{ $cust['name'] }} <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded-full font-semibold {{ $cust['tier'] === 'VIP Member' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}">{{ $cust['tier'] }}</span></div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $cust['phone'] }} • {{ $cust['points'] }} Poin</div>
                            </div>
                            <button type="button" wire:click="selectCustomer('{{ $cust['name'] }}', '{{ $cust['phone'] }}', {{ $cust['points'] }})"
                                    class="px-3 py-1.5 rounded-lg bg-orange-500 text-white font-bold text-[11px] hover:bg-orange-600 transition">
                                Pilih
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif

