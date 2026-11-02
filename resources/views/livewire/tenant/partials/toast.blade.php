    <!-- TOAST NOTIFICATION -->
    @if ($toastMessage)
        <div wire:key="toast-{{ md5($toastMessage.$toastType) }}"
             class="fixed top-5 right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg border text-sm transition-all animate-bounce {{ $toastType === 'error' ? 'bg-rose-50 border-rose-200 text-rose-800' : ($toastType === 'info' ? 'bg-sky-50 border-sky-200 text-sky-800' : 'bg-emerald-50 border-emerald-200 text-emerald-800') }}"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false; $wire.closeToast() }, 3000)">
            <i class="fa-solid {{ $toastType === 'error' ? 'fa-circle-xmark text-rose-500' : ($toastType === 'info' ? 'fa-circle-info text-sky-500' : 'fa-circle-check text-emerald-500') }}"></i>
            <span class="font-medium">{{ $toastMessage }}</span>
            <button wire:click="closeToast" class="ml-2 text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif
