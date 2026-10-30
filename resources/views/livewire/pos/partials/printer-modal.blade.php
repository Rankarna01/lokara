<!-- ================= MODAL: PENGATURAN MESIN PRINTER THERMAL ================= -->
@if ($showPrinterModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-in fade-in duration-150">
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xl max-w-xl w-full overflow-hidden">
            <!-- Header -->
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-lg">
                        <i class="fa-solid fa-print"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Pengaturan Mesin Printer Thermal</h3>
                        <p class="text-[11px] text-slate-400">Hubungkan printer struk melalui Bluetooth, USB Driver, atau aplikasi pihak ketiga (RawBT)</p>
                    </div>
                </div>
                <button wire:click="$set('showPrinterModal', false)" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto text-xs">
                <!-- 1. Pilihan Jenis Koneksi Printer -->
                <div class="space-y-2">
                    <label class="block font-bold text-slate-800">1. Jenis Koneksi Printer Thermal</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <!-- Bluetooth -->
                        <button type="button" wire:click="$set('printerConnectionType', 'bluetooth')"
                                class="p-3 rounded-2xl border text-left transition flex flex-col items-center justify-center text-center gap-2 {{ $printerConnectionType === 'bluetooth' ? 'bg-orange-50 border-orange-500 ring-1 ring-orange-500 text-orange-600' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-white' }}">
                            <i class="fa-brands fa-bluetooth-b text-xl"></i>
                            <div>
                                <div class="font-bold text-[11px]">Bluetooth</div>
                                <div class="text-[9px] text-slate-400">Printer Mini Portable</div>
                            </div>
                        </button>

                        <!-- USB / Desktop Driver -->
                        <button type="button" wire:click="$set('printerConnectionType', 'usb')"
                                class="p-3 rounded-2xl border text-left transition flex flex-col items-center justify-center text-center gap-2 {{ $printerConnectionType === 'usb' ? 'bg-orange-50 border-orange-500 ring-1 ring-orange-500 text-orange-600' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-white' }}">
                            <i class="fa-brands fa-usb text-xl"></i>
                            <div>
                                <div class="font-bold text-[11px]">USB Driver</div>
                                <div class="text-[9px] text-slate-400">Desktop Windows/Mac</div>
                            </div>
                        </button>

                        <!-- RawBT / 3rd Party App -->
                        <button type="button" wire:click="$set('printerConnectionType', 'rawbt')"
                                class="p-3 rounded-2xl border text-left transition flex flex-col items-center justify-center text-center gap-2 {{ $printerConnectionType === 'rawbt' ? 'bg-orange-50 border-orange-500 ring-1 ring-orange-500 text-orange-600' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-white' }}">
                            <i class="fa-solid fa-mobile-screen-button text-xl"></i>
                            <div>
                                <div class="font-bold text-[11px]">RawBT App</div>
                                <div class="text-[9px] text-slate-400">Bridge POS Android</div>
                            </div>
                        </button>

                        <!-- LAN / Wi-Fi Network -->
                        <button type="button" wire:click="$set('printerConnectionType', 'network')"
                                class="p-3 rounded-2xl border text-left transition flex flex-col items-center justify-center text-center gap-2 {{ $printerConnectionType === 'network' ? 'bg-orange-50 border-orange-500 ring-1 ring-orange-500 text-orange-600' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-white' }}">
                            <i class="fa-solid fa-network-wired text-xl"></i>
                            <div>
                                <div class="font-bold text-[11px]">Ethernet / Wi-Fi</div>
                                <div class="text-[9px] text-slate-400">IP Kitchen / Bar</div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- 2. Parameter Detail Sesuai Koneksi -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-3">
                    @if ($printerConnectionType === 'bluetooth')
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800">Nama Perangkat Bluetooth (ESC/POS)</span>
                                <button type="button" wire:click="notify('Memindai perangkat Bluetooth terdekat...', 'info')"
                                        class="text-[11px] font-semibold text-orange-600 hover:text-orange-700 flex items-center gap-1">
                                    <i class="fa-solid fa-rotate text-[10px]"></i> Pindai Bluetooth
                                </button>
                            </div>
                            <input type="text" wire:model="printerDeviceName" placeholder="Contoh: Panda POS-58BT / PT-210"
                                   class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-orange-500" />
                            <p class="text-[10px] text-slate-400">Pastikan Bluetooth pada perangkat HP/Tablet/Komputer Anda sudah aktif dan terpasang (*paired*).</p>
                        </div>
                    @elseif ($printerConnectionType === 'usb')
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Nama Driver Printer OS</label>
                                <input type="text" wire:model="printerDeviceName" placeholder="Contoh: EPSON TM-T82 Receipt"
                                       class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-orange-500" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Port / Interface</label>
                                <input type="text" wire:model="printerPortOrIp" placeholder="USB001 / COM1 / LPT1"
                                       class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-orange-500" />
                            </div>
                        </div>
                    @elseif ($printerConnectionType === 'rawbt')
                        <div class="space-y-2">
                            <label class="block font-semibold text-slate-700">Aplikasi Jembatan Cetak Android</label>
                            <select wire:model="thirdPartyApp"
                                    class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <option value="rawbt">RawBT Print Service (Rekomendasi Android POS)</option>
                                <option value="escpos_service">ESC/POS Bluetooth Print Service</option>
                                <option value="sunmi_sdk">Sunmi / iMin Built-in Thermal SDK</option>
                            </select>
                            <p class="text-[10px] text-slate-500">
                                Lokara POS secara otomatis mengirimkan perintah cetak thermal menggunakan URL Intent <span class="font-mono text-orange-600">rawbt:data</span> tanpa perlu instal driver rumit.
                            </p>
                        </div>
                    @else
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-2">
                                <label class="block font-semibold text-slate-700 mb-1">Alamat IP Printer (TCP/IP)</label>
                                <input type="text" wire:model="printerPortOrIp" placeholder="192.168.1.200"
                                       class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-orange-500 font-mono" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Port RAW</label>
                                <input type="number" value="9100" readonly
                                       class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold font-mono" />
                            </div>
                        </div>
                    @endif
                </div>

                <!-- 3. Ukuran Kertas & Format Struk -->
                <div class="space-y-2">
                    <label class="block font-bold text-slate-800">2. Ukuran Kertas Thermal Struk</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3 rounded-2xl border cursor-pointer transition {{ $printerPaperWidth === '58mm' ? 'bg-orange-50 border-orange-500 text-orange-900 ring-1 ring-orange-500' : 'bg-slate-50 border-slate-200 text-slate-700' }}">
                            <input type="radio" wire:model="printerPaperWidth" value="58mm" class="text-orange-500 focus:ring-orange-500" />
                            <div>
                                <div class="font-bold text-xs">58 mm (Standar Mini)</div>
                                <div class="text-[10px] text-slate-400">Lebar 32 Karakter (Cocok printer Bluetooth)</div>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3 rounded-2xl border cursor-pointer transition {{ $printerPaperWidth === '80mm' ? 'bg-orange-50 border-orange-500 text-orange-900 ring-1 ring-orange-500' : 'bg-slate-50 border-slate-200 text-slate-700' }}">
                            <input type="radio" wire:model="printerPaperWidth" value="80mm" class="text-orange-500 focus:ring-orange-500" />
                            <div>
                                <div class="font-bold text-xs">80 mm (Kertas Lebar)</div>
                                <div class="text-[10px] text-slate-400">Lebar 48 Karakter (Cocok Epson TM-T82)</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- 4. Fitur Hardware Tambahan -->
                <div class="space-y-2 pt-1">
                    <label class="block font-bold text-slate-800">3. Fitur Hardware & Otomasi</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-slate-700">
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer">
                            <input type="checkbox" wire:model="printerKickDrawer" class="rounded text-orange-500 focus:ring-orange-500" />
                            <span class="font-medium">Buka Laci Uang (RJ11 Kick)</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer">
                            <input type="checkbox" wire:model="printerAutoCut" class="rounded text-orange-500 focus:ring-orange-500" />
                            <span class="font-medium">Potong Kertas Otomatis (Auto-Cut)</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer">
                            <input type="checkbox" wire:model="printerPrintLogo" class="rounded text-orange-500 focus:ring-orange-500" />
                            <span class="font-medium">Cetak Logo Toko di Kepala Struk</span>
                        </label>
                        <div class="flex items-center justify-between p-2.5 rounded-xl border border-slate-200 bg-slate-50">
                            <span class="font-medium">Jumlah Salinan:</span>
                            <select wire:model="printerCopies" class="border border-slate-200 rounded-lg px-2 py-0.5 bg-white text-xs font-bold">
                                <option value="1">1 Lembar (Kasir)</option>
                                <option value="2">2 Lembar (Kasir + Dapur)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 5. Preview Struk Percobaan (Jika tombol test diklik) -->
                @if ($showTestPrintPreview)
                    <div class="p-3.5 bg-amber-50/80 border border-amber-200 rounded-2xl space-y-2 text-amber-900 animate-in fade-in">
                        <div class="flex items-center justify-between">
                            <div class="font-bold flex items-center gap-1.5 text-xs text-amber-800">
                                <i class="fa-solid fa-check text-emerald-600"></i> Tes Cetak Berhasil Dikirim!
                            </div>
                            <span class="text-[10px] font-mono bg-amber-100 text-amber-800 px-2 py-0.5 rounded">Thermal {{ $printerPaperWidth }}</span>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-dashed border-slate-300 font-mono text-[10px] space-y-1 text-slate-800">
                            <div class="text-center font-bold text-xs">{{ $tenant->name ?? 'LOKARA POS' }}</div>
                            <div class="text-center text-[9px] text-slate-500">{{ $currentOutlet->address ?? 'Jl. Panglima Polim No. 12' }}</div>
                            <div class="border-b border-dashed border-slate-300 my-1"></div>
                            <div class="flex justify-between"><span>No: #TES-PRINT</span><span>{{ now()->format('H:i') }}</span></div>
                            <div class="flex justify-between"><span>Kasir: {{ auth()->user()->name ?? 'Dina Putri' }}</span><span>1x</span></div>
                            <div class="border-b border-dashed border-slate-300 my-1"></div>
                            <div class="flex justify-between"><span>1x Es Kopi Susu Aren</span><span>22.000</span></div>
                            <div class="flex justify-between font-bold"><span>TOTAL (TUNAI)</span><span>Rp 22.000</span></div>
                            <div class="border-b border-dashed border-slate-300 my-1"></div>
                            <div class="text-center text-[9px] text-slate-400">=== PRINTER THERMAL READY ===</div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Footer Action -->
            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2">
                <button type="button" wire:click="testPrintThermal"
                        class="px-3.5 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 font-bold text-xs text-slate-700 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-receipt text-orange-500"></i>
                    <span>Tes Cetak Struk</span>
                </button>

                <div class="flex items-center gap-2">
                    <button type="button" wire:click="$set('showPrinterModal', false)"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl">
                        Batal
                    </button>
                    <button type="button" wire:click="savePrinterSettings"
                            class="px-4 py-2 text-xs font-bold bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i>
                        <span>Simpan Pengaturan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

