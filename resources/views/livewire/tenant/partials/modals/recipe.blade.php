    <!-- 3. Modal Resep BOM Builder -->
    @if ($showRecipeModal && $selectedRecipeProduct)
        <div class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-xl w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Resep BOM: {{ $selectedRecipeProduct->name }}</h3>
                        <p class="text-xs text-slate-500">Komposisi bahan per 1 porsi saji & rekalkulasi HPP otomatis</p>
                    </div>
                    <button wire:click="$set('showRecipeModal', false)" class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto text-sm">
                    <!-- Current Ingredients in Recipe -->
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-slate-700">Komposisi Bahan Saat Ini</label>
                        <div class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden bg-slate-50">
                            @forelse($selectedRecipeProduct->recipes as $rc)
                                <div class="p-3 flex items-center justify-between gap-3 text-xs">
                                    <div>
                                        <div class="font-bold text-slate-800">{{ $rc->ingredient->name ?? '-' }}</div>
                                        <div class="text-slate-400">
                                            {{ (int)$rc->quantity }} {{ $rc->ingredient->usage_unit ?? '' }} x Rp {{ number_format($rc->ingredient->cost_per_usage_unit ?? 0, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="font-bold text-slate-900">
                                            Rp {{ number_format($rc->quantity * ($rc->ingredient->cost_per_usage_unit ?? 0), 0, ',', '.') }}
                                        </span>
                                        <button wire:click="removeRecipeItem({{ $rc->id }})" class="text-slate-400 hover:text-rose-600">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-slate-400 text-xs">Belum ada bahan dalam resep ini.</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Add new ingredient row -->
                    <div class="p-3 bg-orange-50/50 rounded-xl border border-orange-100 space-y-3">
                        <label class="block text-xs font-semibold text-orange-900">+ Tambah Bahan ke Resep</label>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="col-span-2">
                                <select wire:model="selectedRecipeIngredientId"
                                    class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-orange-500">
                                    @foreach($ingredients as $ing)
                                        <option value="{{ $ing->id }}">{{ $ing->name }} ({{ $ing->usage_unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <input type="number" wire:model="recipeQuantity" placeholder="Jumlah"
                                    class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-orange-500 font-bold">
                            </div>
                        </div>
                        <button wire:click="addRecipeItem"
                            class="w-full py-1.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-lg shadow-2xs">
                            Tambahkan Bahan
                        </button>
                    </div>

                    <!-- Auto HPP Calculation Result -->
                    <div class="p-3 bg-slate-900 text-white rounded-xl flex items-center justify-between text-xs">
                        <span>Estimasi Total HPP Modal:</span>
                        <span class="text-sm font-bold text-orange-400">Rp {{ number_format($selectedRecipeProduct->cost_price, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                    <button wire:click="$set('showRecipeModal', false)"
                        class="px-4 py-2 text-xs font-semibold bg-slate-800 text-white rounded-xl">Selesai</button>
                </div>
            </div>
        </div>
    @endif

