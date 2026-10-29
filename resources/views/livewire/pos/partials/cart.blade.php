<!-- ================= 3. RIGHT PANEL: ORDER CART (PESANAN BARU) ================= -->
<aside class="w-80 lg:w-96 bg-white border-l border-slate-200/80 flex flex-col justify-between flex-shrink-0 h-full">

    <!-- Cart Header -->
    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <h3 class="font-bold text-sm text-slate-900">Pesanan Baru</h3>
            <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-bold">
                {{ array_sum(array_column($cart, 'qty')) }} item
            </span>
        </div>

        <!-- Dropdown Dots Menu -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-ellipsis"></i>
            </button>
            <div x-show="open" @click.outside="open = false" x-transition
                 class="absolute right-0 mt-1 w-44 bg-white border border-slate-200 rounded-xl shadow-lg py-1 z-30 text-xs">
                <button wire:click="$set('showCustomerModal', true)" @click="open = false" class="w-full text-left px-3 py-1.5 text-slate-700 hover:bg-slate-50 font-medium">Pilih Member Pelanggan</button>
                <button wire:click="holdOrder" @click="open = false" class="w-full text-left px-3 py-1.5 text-slate-700 hover:bg-slate-50 font-medium">Tahan Pesanan</button>
                <button wire:click="clearCart" @click="open = false" class="w-full text-left px-3 py-1.5 text-rose-600 hover:bg-rose-50 font-medium border-t border-slate-100 mt-1">Kosongkan Keranjang</button>
            </div>
        </div>
    </div>

    <!-- Cart Body: Order Type + Table + Items -->
    <div class="flex-1 overflow-y-auto p-4 space-y-4">

        <!-- Order Type Switcher (Dine In vs Take Away) -->
        <div class="grid grid-cols-2 gap-2">
            <button wire:click="setOrderType('dine_in')"
                    class="py-2 px-3 rounded-lg text-xs font-semibold flex items-center justify-center gap-2 transition {{ $orderType === 'dine_in' ? 'bg-orange-500 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                <i class="fa-solid fa-utensils text-xs"></i>
                <span>Dine In</span>
            </button>

            <button wire:click="setOrderType('take_away')"
                    class="py-2 px-3 rounded-lg text-xs font-semibold flex items-center justify-center gap-2 transition {{ $orderType === 'take_away' ? 'bg-orange-500 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                <i class="fa-solid fa-bag-shopping text-xs"></i>
                <span>Take Away</span>
            </button>
        </div>

        <!-- Table Selector Dropdown (Dine In only) -->
        @if ($orderType === 'dine_in')
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="w-full py-2 px-3 rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-800 flex items-center justify-between hover:border-slate-300 transition">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-chair text-slate-400 text-xs"></i>
                        <span>{{ $selectedTable }}</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                </button>

                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg p-2 z-30 grid grid-cols-4 gap-1 text-xs">
                    @foreach (['Meja 01', 'Meja 02', 'Meja 03', 'Meja 04', 'Meja 05', 'Meja 06', 'Meja 07', 'Meja 08', 'Meja 09', 'Meja 10', 'Meja 11', 'Meja 12'] as $table)
                        <button type="button"
                                wire:click="$set('selectedTable', '{{ $table }}')"
                                @click="open = false"
                                class="py-1.5 px-2 rounded-lg text-center font-medium transition {{ $selectedTable === $table ? 'bg-orange-500 text-white font-bold' : 'hover:bg-slate-100 text-slate-700' }}">
                            {{ str_replace('Meja ', 'M', $table) }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Member Badge if selected -->
        @if ($selectedCustomer)
            <div class="p-2.5 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-id-card text-emerald-600"></i>
                    <div>
                        <div class="font-bold text-emerald-900 leading-tight">{{ $selectedCustomer['name'] }}</div>
                        <div class="text-[10px] text-emerald-600">{{ $selectedCustomer['points'] }} Poin Member</div>
                    </div>
                </div>
                <button wire:click="removeSelectedCustomer" class="text-slate-400 hover:text-rose-500">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>
        @endif

        <!-- Items List -->
        <div class="space-y-3 pt-1">
            @forelse ($cart as $index => $item)
                <div class="relative p-2.5 rounded-xl border border-slate-100 bg-slate-50/50 flex items-start justify-between gap-3 group">
                    <!-- Left: Thumbnail & Details -->
                    <div class="flex items-start gap-2.5 min-w-0">
                        <img src="{{ $item['image_url'] ?? 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=150&h=150&q=80' }}"
                             alt="{{ $item['name'] }}"
                             class="w-11 h-11 rounded-lg object-cover bg-white ring-1 ring-slate-200 flex-shrink-0" />

                        <div class="min-w-0">
                            <div class="font-bold text-xs text-slate-900 truncate">{{ $item['name'] }}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5 truncate">{{ $item['variant'] }}</div>
                            <div class="text-xs font-semibold text-slate-700 mt-1">
                                Rp{{ number_format($item['price'], 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <!-- Right: Stepper & Subtotal -->
                    <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                        <!-- Remove Cross Button -->
                        <button wire:click="removeFromCart({{ $index }})"
                                class="text-slate-300 hover:text-rose-500 text-xs">
                            <i class="fa-solid fa-xmark"></i>
                        </button>

                        <!-- Stepper (- Qty +) -->
                        <div class="flex items-center border border-slate-200 rounded-lg bg-white overflow-hidden text-xs">
                            <button wire:click="decrementQty({{ $index }})"
                                    class="w-6 h-6 flex items-center justify-center text-slate-500 hover:bg-slate-100 transition">
                                −
                            </button>
                            <span class="w-6 text-center font-bold text-slate-800 text-[11px]">{{ $item['qty'] }}</span>
                            <button wire:click="incrementQty({{ $index }})"
                                    class="w-6 h-6 flex items-center justify-center text-slate-500 hover:bg-slate-100 transition">
                                +
                            </button>
                        </div>

                        <div class="text-xs font-bold text-slate-900 mt-0.5">
                            Rp{{ number_format($item['subtotal'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-slate-400 text-xs">
                    <i class="fa-solid fa-bag-shopping text-3xl mb-2 text-slate-300"></i>
                    <p class="font-medium">Belum ada item pesanan.</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Klik produk di katalog untuk menambahkan.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Cart Footer: Calculations & Pay Button -->
    <div class="p-4 border-t border-slate-100 bg-white space-y-3">
        <!-- Calculations Breakdown -->
        <div class="space-y-1.5 text-xs text-slate-600">
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span class="font-semibold text-slate-900">Rp{{ number_format($totals['subtotal'], 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Diskon</span>
                <span class="text-slate-400">Rp0</span>
            </div>
            <div class="flex justify-between">
                <span>Pajak (11%)</span>
                <span class="font-semibold text-slate-900">Rp{{ number_format($totals['tax'], 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between pt-2 border-t border-slate-100 items-baseline">
                <span class="font-bold text-slate-900">Total Pembayaran</span>
                <span class="text-lg font-extrabold text-slate-900">Rp{{ number_format($totals['total'], 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Primary Button: Lanjut Pembayaran -->
        <button wire:click="openPaymentModal"
                class="w-full py-3 px-4 rounded-xl font-bold text-xs text-white bg-orange-500 hover:bg-orange-600 shadow-sm shadow-orange-500/20 flex items-center justify-center gap-1.5 transition">
            <span>Lanjut Pembayaran</span>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
        </button>

        <!-- Secondary Button: Simpan Pesanan -->
        <button wire:click="holdOrder"
                class="w-full py-2.5 px-4 rounded-xl font-semibold text-xs text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 flex items-center justify-center gap-2 transition">
            <i class="fa-regular fa-floppy-disk text-slate-400"></i>
            <span>Simpan Pesanan</span>
        </button>
    </div>
</aside>

