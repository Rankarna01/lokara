<div class="space-y-5 max-w-2xl">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Hapus Riwayat Transaksi</h1>
        <p class="text-sm text-slate-500 mt-0.5">Menghapus seluruh riwayat order, item, dan pembayaran milik satu tenant. Tindakan ini permanen.</p>
    </div>

    @if (! $cleanupUnlocked)
        <!-- Layar Kunci: butuh username dan password dari .env -->
        <div class="bg-white border border-slate-200/80 rounded-xl shadow-sm p-6 space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <div>
                    <div class="font-bold text-slate-900 text-sm">Menu Terkunci</div>
                    <div class="text-xs text-slate-500">Masukkan username dan password khusus menu ini.</div>
                </div>
            </div>

            <form wire:submit="unlockCleanup" class="space-y-3 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Username</label>
                    <input type="text" wire:model="cleanupLoginUsername" autocomplete="off"
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500" />
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Password</label>
                    <input type="password" wire:model="cleanupLoginPassword" autocomplete="new-password"
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-orange-500" />
                </div>
                <button type="submit" wire:loading.attr="disabled"
                        class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 disabled:opacity-60 text-white font-bold text-xs transition">
                    Buka Menu
                </button>
            </form>
        </div>
    @else
        <!-- Menu Terbuka -->
        <div class="bg-white border border-slate-200/80 rounded-xl shadow-sm p-6 space-y-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-xs font-semibold text-emerald-700">
                    <i class="fa-solid fa-lock-open"></i>
                    <span>Menu terbuka</span>
                </div>
                <button wire:click="lockCleanup" class="text-xs font-semibold text-slate-500 hover:text-slate-800">Kunci Kembali</button>
            </div>

            <div class="space-y-2 text-xs">
                <label class="block font-semibold text-slate-700">Pilih Tenant</label>
                <select wire:model.live="cleanupTenantId"
                        class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white focus:outline-none focus:ring-1 focus:ring-orange-500 @error('cleanupTenantId') border-rose-400 @enderror">
                    <option value="">-- Pilih tenant --</option>
                    @foreach ($cleanupTenants as $tn)
                        <option value="{{ $tn->id }}">{{ $tn->name }}</option>
                    @endforeach
                </select>
                @error('cleanupTenantId') <div class="text-rose-600 text-[11px]">{{ $message }}</div> @enderror
            </div>

            @if ($cleanupTenantId)
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800 space-y-3">
                    <div>
                        Akan dihapus: <span class="font-bold">{{ $cleanupOrderCount }} transaksi</span> beserta item dan pembayarannya.
                        Data stok, pelanggan, dan pengaturan tenant tidak ikut terhapus.
                    </div>

                    <label class="flex items-start gap-2 cursor-pointer">
                        <input type="checkbox" wire:model.live="cleanupConfirmed" class="mt-0.5 rounded text-rose-600 focus:ring-rose-500" />
                        <span>Saya memahami bahwa riwayat transaksi tenant ini akan terhapus permanen dan tidak bisa dikembalikan.</span>
                    </label>
                    @error('cleanupConfirmed') <div class="text-rose-700 font-semibold text-[11px]">{{ $message }}</div> @enderror

                    <button type="button"
                            wire:click="deleteTenantTransactionHistory"
                            wire:confirm="Hapus permanen seluruh riwayat transaksi tenant ini?"
                            wire:loading.attr="disabled"
                            @disabled($cleanupOrderCount === 0)
                            class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-xs transition">
                        <i class="fa-solid fa-trash-can"></i> Hapus Riwayat Transaksi
                    </button>
                    @if ($cleanupOrderCount === 0)
                        <div class="text-[11px] text-slate-500">Tenant ini belum memiliki riwayat transaksi.</div>
                    @endif
                </div>
            @endif
        </div>
    @endif
</div>
