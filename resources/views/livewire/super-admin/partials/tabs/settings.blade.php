                    <div class="space-y-6">
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pengaturan Global Platform</h1>
                            <p class="text-sm text-slate-500 mt-0.5">Konfigurasi merek, payment gateway, notifikasi WhatsApp, dan nilai default</p>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Card 1: Identitas Platform -->
                            <div class="bg-white border border-slate-200/80 rounded-xl p-6 shadow-sm space-y-4">
                                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">Identitas & Brand Aplikasi</h3>
                                <div class="space-y-3 text-xs">
                                    <div>
                                        <label class="block font-semibold text-slate-700 mb-1">Nama Aplikasi / Platform</label>
                                        <input type="text" value="Lokara POS" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-slate-700 mb-1">Domain Platform Utama</label>
                                        <input type="text" value="lokara.id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-slate-700 mb-1">Mata Uang Utama</label>
                                        <input type="text" value="IDR (Rupiah Indonesia)" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                                    </div>
                                </div>
                                <div class="pt-2 text-right">
                                    <button wire:click="notify('Pengaturan identitas berhasil disimpan')" class="px-4 py-2 bg-orange-500 text-white rounded-lg text-xs font-semibold hover:bg-orange-600 transition">Simpan Perubahan</button>
                                </div>
                            </div>

                            <!-- Card 2: Gateway & Notifikasi -->
                            <div class="bg-white border border-slate-200/80 rounded-xl p-6 shadow-sm space-y-4">
                                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">Integrasi Payment & WhatsApp</h3>
                                <div class="space-y-3 text-xs">
                                    <div>
                                        <label class="block font-semibold text-slate-700 mb-1">Payment Gateway Langganan</label>
                                        <select class="w-full border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                            <option selected>Midtrans Snap API (Production)</option>
                                            <option>Xendit Invoice API</option>
                                            <option>Duitku Payment Gateway</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-slate-700 mb-1">WhatsApp Gateway Provider</label>
                                        <select class="w-full border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                            <option selected>Fonnte API Gateway</option>
                                            <option>Wablas Enterprise</option>
                                            <option>Meta WhatsApp Cloud API Official</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-slate-700 mb-1">Nomor WhatsApp Resmi Dukungan</label>
                                        <input type="text" value="081234567890" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-slate-800 focus:outline-none focus:ring-1 focus:ring-orange-500" />
                                    </div>
                                </div>
                                <div class="pt-2 text-right">
                                    <button wire:click="notify('Konfigurasi gateway berhasil diperbarui')" class="px-4 py-2 bg-orange-500 text-white rounded-lg text-xs font-semibold hover:bg-orange-600 transition">Simpan Integrasi</button>
                                </div>
                            </div>
                        </div>
                    </div>
