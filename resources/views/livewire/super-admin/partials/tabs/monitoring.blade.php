                    <div class="space-y-6">
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Monitoring & Audit Log</h1>
                            <p class="text-sm text-slate-500 mt-0.5">Pantau kesehatan antrean sistem, pemakaian database, dan audit jejak operasi platform</p>
                        </div>

                        <!-- System Health Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="p-4 bg-white border border-slate-200/80 rounded-xl shadow-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-slate-500">Database Engine</span>
                                    <i class="fa-solid fa-database text-emerald-500"></i>
                                </div>
                                <div class="text-lg font-bold text-slate-900 mt-2">Normal & Sehat</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Shared Hosting Compatible</div>
                            </div>
                            <div class="p-4 bg-white border border-slate-200/80 rounded-xl shadow-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-slate-500">Queue & Jobs</span>
                                    <i class="fa-solid fa-bolt text-emerald-500"></i>
                                </div>
                                <div class="text-lg font-bold text-slate-900 mt-2">0 Antrean Tertunda</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Driver: Database / Sync</div>
                            </div>
                            <div class="p-4 bg-white border border-slate-200/80 rounded-xl shadow-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-slate-500">Penyimpanan Cache</span>
                                    <i class="fa-solid fa-server text-emerald-500"></i>
                                </div>
                                <div class="text-lg font-bold text-slate-900 mt-2">File / DB Cache</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Responsif < 20ms</div>
                            </div>
                            <div class="p-4 bg-white border border-slate-200/80 rounded-xl shadow-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-slate-500">Backup Terakhir</span>
                                    <i class="fa-solid fa-shield-check text-emerald-500"></i>
                                </div>
                                <div class="text-lg font-bold text-slate-900 mt-2">Otomatis Hari Ini</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">02:00 WIB Sukses</div>
                            </div>
                        </div>

                        <!-- Audit Logs List -->
                        <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-sm">
                            <h3 class="text-sm font-bold text-slate-900 mb-4">Catatan Aktivitas Operasional (Audit Trail)</h3>
                            <div class="space-y-3">
                                @foreach ($allLogs as $log)
                                    <div class="flex items-center justify-between p-3 rounded-lg hover:bg-slate-50 border border-slate-100 text-xs transition">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg {{ $log->icon_color }} flex items-center justify-center flex-shrink-0">
                                                <i class="fa-solid {{ $log->icon }} text-xs"></i>
                                            </div>
                                            <div>
                                                <span class="font-bold text-slate-800">{{ $log->action }}</span>
                                                <span class="text-slate-400 ml-2">({{ $log->description }})</span>
                                            </div>
                                        </div>
                                        <span class="text-slate-400 text-[11px]">{{ $log->created_at ? $log->created_at->diffForHumans() : $log->badge_text }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
