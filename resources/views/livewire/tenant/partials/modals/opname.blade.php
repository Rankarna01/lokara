    <!-- 4. Modal Stok Opname -->
    @if ($showOpnameModal)
        <div class="fixed inset-0 z-[80] bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <!-- Modal Header -->
                <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                            <i class="fa-solid fa-clipboard-check text-orange-500"></i>
                            <span>Audit & Rekonsiliasi Stok Opname</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Sinkronisasi saldo fisik dengan saldo sistem beserta pencatatan waktu audit.
                        </p>
                    </div>
                    <button type="button" wire:click="$set('showOpnameModal', false)" class="text-slate-400 hover:text-slate-600 transition p-1">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-5 space-y-4 text-sm max-h-[80vh] overflow-y-auto">
                    <!-- 1. Tipe Item Selection: Produk Langsung vs Bahan Baku -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jenis Item yang Di-Opname</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" wire:click="updatedOpnameItemType('product')"
                                class="p-2.5 rounded-xl border text-left transition flex items-center gap-2.5 {{ $opnameItemType === 'product' ? 'border-orange-500 bg-orange-50/60 ring-1 ring-orange-500 text-orange-900' : 'border-slate-200 hover:bg-slate-50 text-slate-700' }}">
                                <div class="w-7 h-7 rounded-lg {{ $opnameItemType === 'product' ? 'bg-orange-500 text-white' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center shrink-0 text-xs">
                                    <i class="fa-solid fa-spray-can-sparkles"></i>
                                </div>
                                <div>
                                    <div class="text-xs font-bold leading-tight">Produk Langsung / Ritel</div>
                                    <div class="text-[10px] text-slate-400">Pomade, toner, shampoo, serum</div>
                                </div>
                            </button>

                            <button type="button" wire:click="updatedOpnameItemType('ingredient')"
                                class="p-2.5 rounded-xl border text-left transition flex items-center gap-2.5 {{ $opnameItemType === 'ingredient' ? 'border-orange-500 bg-orange-50/60 ring-1 ring-orange-500 text-orange-900' : 'border-slate-200 hover:bg-slate-50 text-slate-700' }}">
                                <div class="w-7 h-7 rounded-lg {{ $opnameItemType === 'ingredient' ? 'bg-orange-500 text-white' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center shrink-0 text-xs">
                                    <i class="fa-solid fa-wheat-awn"></i>
                                </div>
                                <div>
                                    <div class="text-xs font-bold leading-tight">Bahan Baku</div>
                                    <div class="text-[10px] text-slate-400">Bahan dapur / backwash gram/ml</div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- 2. Pilih Item / Produk -->
                    <div>
                        @if ($opnameItemType === 'product')
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Pilih Produk Langsung <span class="text-rose-500">*</span>
                            </label>
                            <select wire:model.live="opnameProductId"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                @php
                                    $optProds = $products->where('type', 'item');
                                    if ($optProds->isEmpty()) {
                                        $optProds = $products;
                                    }
                                @endphp
                                @forelse($optProds as $prod)
                                    <option value="{{ $prod->id }}">
                                        {{ $prod->name }} (SKU: {{ $prod->sku ?? 'PRD-'.$prod->id }}) • Stok Sistem: {{ (int)($prod->stock_quantity ?? 0) }} {{ $prod->unit ?: 'pcs' }}
                                    </option>
                                @empty
                                    <option value="">Belum ada produk ritel terdaftar</option>
                                @endforelse
                            </select>
                            @error('opnameProductId') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        @else
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Pilih Bahan Baku <span class="text-rose-500">*</span>
                            </label>
                            <select wire:model.live="opnameIngredientId"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                @forelse($ingredients as $ing)
                                    <option value="{{ $ing->id }}">
                                        {{ $ing->name }} • Stok Sistem: {{ (int)$ing->current_stock }} {{ $ing->usage_unit ?: 'satuan' }}
                                    </option>
                                @empty
                                    <option value="">Belum ada bahan baku terdaftar</option>
                                @endforelse
                            </select>
                            @error('opnameIngredientId') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        @endif
                    </div>

                    <!-- 3. Rekonsiliasi Saldo: Sistem vs Fisik vs Selisih -->
                    <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <!-- Saldo Sistem -->
                            <div class="bg-white p-2.5 rounded-xl border border-slate-200/80">
                                <span class="text-[10px] font-semibold text-slate-400 block uppercase">Stok Sistem</span>
                                <span class="text-lg font-bold text-slate-800 block mt-0.5">
                                    {{ (float)$opnameCurrentStock }}
                                </span>
                                <span class="text-[10px] text-slate-400">Unit di Catatan</span>
                            </div>

                            <!-- Input Stok Fisik -->
                            <div class="bg-white p-2.5 rounded-xl border border-orange-300 ring-1 ring-orange-200">
                                <span class="text-[10px] font-bold text-orange-600 block uppercase">Hitung Fisik</span>
                                <input type="number" step="any" min="0" wire:model.live="opnamePhysicalQty"
                                    class="w-full text-center text-lg font-bold text-slate-900 border-b border-orange-400 focus:outline-none py-0.5 mt-0.5">
                                <span class="text-[10px] text-orange-500 font-semibold">Hasil Cek Nyata</span>
                            </div>

                            <!-- Selisih / Varians Live Preview -->
                            @php
                                $diff = round((float)$opnamePhysicalQty - (float)$opnameCurrentStock, 2);
                            @endphp
                            <div class="bg-white p-2.5 rounded-xl border border-slate-200/80">
                                <span class="text-[10px] font-semibold text-slate-400 block uppercase">Selisih Varians</span>
                                <span class="text-lg font-bold block mt-0.5 {{ $diff == 0 ? 'text-emerald-600' : ($diff > 0 ? 'text-blue-600' : 'text-rose-600') }}">
                                    {{ $diff >= 0 ? '+' : '' }}{{ $diff }}
                                </span>
                                <span class="text-[10px] font-semibold {{ $diff == 0 ? 'text-emerald-500' : ($diff > 0 ? 'text-blue-500' : 'text-rose-500') }}">
                                    {{ $diff == 0 ? 'Klop / Sesuai' : ($diff > 0 ? 'Surplus Fisik' : 'Defisit / Hilang') }}
                                </span>
                            </div>
                        </div>

                        @error('opnamePhysicalQty')
                            <span class="text-[11px] text-rose-500 font-medium block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- 4. Tanggal & Jam Pelaksanaan Opname (Waktu Jelas Masuknya) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1 flex items-center justify-between">
                                <span><i class="fa-regular fa-calendar-check text-orange-500 mr-1"></i> Waktu Audit Opname</span>
                                <span class="text-[10px] text-orange-600 font-bold">Wajib</span>
                            </label>
                            <input type="datetime-local" wire:model="opnameRecordedAt"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Waktu audit fisik dicatat ke kartu mutasi</span>
                            @error('opnameRecordedAt') <span class="text-[11px] text-rose-500 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Petugas Penanggung Jawab Audit -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1 flex items-center justify-between">
                                <span><i class="fa-solid fa-user-check text-orange-500 mr-1"></i> Petugas PIC Opname</span>
                            </label>
                            <select wire:model="opnameStaffId"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                @foreach($cashiers as $usr)
                                    <option value="{{ $usr->id }}">{{ $usr->name }} ({{ ucfirst($usr->role) }})</option>
                                @endforeach
                            </select>
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Penanggung jawab yang memeriksa stok</span>
                        </div>
                    </div>

                    <!-- 5. Kategori Alasan Preset Pills -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Alasan / Kategori Penyesuaian</label>
                        <div class="flex flex-wrap gap-1.5 mb-2">
                            @php
                                $presets = [
                                    ['Pemeriksaan Rutin', 'Pemeriksaan rutin berkala display & gudang'],
                                    ['Kemasan Rusak / Bocor', 'Barang rusak / kemasan bocor saat penataan di display'],
                                    ['Selisih Kasir POS', 'Selisih transaksi kasir saat jam sibuk operasional'],
                                    ['Barang Hilang / Susut', 'Barang susut atau tidak ditemukan saat audit rak fisik'],
                                    ['Tester Display Konsumen', 'Dikeluarkan untuk tester / sampel pelanggan di salon'],
                                    ['Koreksi Saldo Awal', 'Koreksi penyesuaian saldo awal sistem'],
                                ];
                            @endphp
                            @foreach($presets as $p)
                                <button type="button" wire:click="setOpnameReasonPreset('{{ $p[0] }}', '{{ $p[1] }}')"
                                    class="px-2.5 py-1 rounded-lg text-[11px] font-semibold transition border {{ $opnameReasonCategory === $p[0] ? 'bg-orange-500 text-white border-orange-500 shadow-xs' : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200' }}">
                                    {{ $p[0] }}
                                </button>
                            @endforeach
                        </div>

                        <!-- Catatan Detail Input -->
                        <textarea wire:model="opnameReason" rows="2"
                            placeholder="Tuliskan catatan detail hasil audit stok opname..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500"></textarea>
                        @error('opnameReason') <span class="text-[11px] text-rose-500 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between">
                    <div class="text-[11px] text-slate-400 flex items-center gap-1">
                        <i class="fa-solid fa-shield-halved text-emerald-500"></i>
                        <span>Audit ledger terekam permanen</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="$set('showOpnameModal', false)"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl transition">
                            Batal
                        </button>
                        <button type="button" wire:click="executeOpname"
                            class="px-4 py-2 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm flex items-center gap-1.5 transition cursor-pointer">
                            <i class="fa-solid fa-check"></i>
                            <span>Sinkronkan Saldo Stok</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
