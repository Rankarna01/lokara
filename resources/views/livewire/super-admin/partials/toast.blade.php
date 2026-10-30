    <!-- Toast Notification -->
    @if ($toastMessage)
        <div class="fixed top-5 right-5 z-50 flex items-center gap-3 bg-white border border-slate-200 px-4 py-3 rounded-xl shadow-lg animate-bounce"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false; $wire.clearToast() }, 4000)">
            <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-check text-sm"></i>
            </div>
            <div class="text-sm font-medium text-slate-800">{{ $toastMessage }}</div>
            <button wire:click="clearToast" class="text-slate-400 hover:text-slate-600 ml-2">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    @endif
