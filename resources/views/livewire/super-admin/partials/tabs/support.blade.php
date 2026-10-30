                    <div class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Support & Pengumuman</h1>
                                <p class="text-sm text-slate-500 mt-0.5">Siarkan pesan banner update fitur atau pengumuman pemeliharaan ke seluruh tenant</p>
                            </div>
                            <button wire:click="notify('Form siaran broadcast baru dibuka')" class="px-4 py-2 bg-orange-500 text-white rounded-lg text-xs font-semibold hover:bg-orange-600 transition">
                                <i class="fa-solid fa-bullhorn mr-1.5"></i> Buat Siaran Baru
                            </button>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                            @foreach ($announcements as $ann)
                                <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-sm space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $ann->type === 'update' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                            {{ $ann->type }}
                                        </span>
                                        <span class="text-xs text-slate-400">{{ $ann->created_at ? $ann->created_at->diffForHumans() : 'Hari ini' }}</span>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-900">{{ $ann->title }}</h3>
                                    <p class="text-xs text-slate-600 leading-relaxed">{{ $ann->content }}</p>
                                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                                        <span class="text-emerald-600 font-medium">Tampil di Backoffice Tenant</span>
                                        <button wire:click="notify('Siaran dinonaktifkan')" class="text-rose-500 hover:text-rose-600 font-medium">Hentikan</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
