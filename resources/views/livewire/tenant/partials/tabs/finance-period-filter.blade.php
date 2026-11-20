@php
    $periodState = $financePeriods[$periodKey] ?? [];
    $periodMode = $periodState['mode'] ?? 'month';
    $periodModes = [
        'day' => ['label' => 'Harian', 'icon' => 'fa-calendar-day'],
        'week' => ['label' => 'Mingguan', 'icon' => 'fa-calendar-week'],
        'month' => ['label' => 'Bulanan', 'icon' => 'fa-calendar'],
        'range' => ['label' => 'Rentang Waktu', 'icon' => 'fa-calendar-days'],
        'all' => ['label' => 'Semua', 'icon' => 'fa-infinity'],
    ];
    $inputClass = 'bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-700 focus:ring-1 focus:ring-orange-500 focus:bg-white focus:outline-none transition';
@endphp

<div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-3">
    <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl self-start overflow-x-auto max-w-full">
        @foreach ($periodModes as $modeKey => $modeMeta)
            <button type="button"
                wire:click="setFinancePeriodMode('{{ $periodKey }}', '{{ $modeKey }}')"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 whitespace-nowrap transition cursor-pointer {{ $periodMode === $modeKey ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                <i class="fa-solid {{ $modeMeta['icon'] }} text-[11px]"></i>
                <span>{{ $modeMeta['label'] }}</span>
            </button>
        @endforeach
    </div>

    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-600">
        @if ($periodMode === 'day')
            <span class="font-medium">Pilih tanggal:</span>
            <input type="date" wire:model.live="financePeriods.{{ $periodKey }}.date" class="{{ $inputClass }}" />
        @elseif ($periodMode === 'week')
            <span class="font-medium">Pilih tanggal dalam minggu:</span>
            <input type="date" wire:model.live="financePeriods.{{ $periodKey }}.date" class="{{ $inputClass }}" />
        @elseif ($periodMode === 'month')
            <span class="font-medium">Pilih bulan:</span>
            <input type="month" wire:model.live="financePeriods.{{ $periodKey }}.month" class="{{ $inputClass }}" />
        @elseif ($periodMode === 'range')
            <input type="date" wire:model.live="financePeriods.{{ $periodKey }}.start" class="{{ $inputClass }}" />
            <span>s/d</span>
            <input type="date" wire:model.live="financePeriods.{{ $periodKey }}.end" class="{{ $inputClass }}" />
        @else
            <span class="font-medium">Menampilkan seluruh periode</span>
        @endif

        <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-semibold">{{ $periodLabel }}</span>
    </div>
</div>
