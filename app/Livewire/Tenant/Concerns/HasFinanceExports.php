<?php

namespace App\Livewire\Tenant\Concerns;

use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

trait HasFinanceExports
{
    public function exportFinanceExcel()
    {
        $data = $this->getFinanceExportViewData();
        $html = view('livewire.tenant.exports.finance-excel', $data)->render();
        $fileName = 'laba-rugi-'.$this->financeExportTenantSlug().'-'.now()->format('Ymd-His').'.xls';

        return response()->streamDownload(function () use ($html) {
            echo $html;
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    public function exportFinancePdf(): void
    {
        $data = $this->getFinanceExportViewData();
        $html = view('livewire.tenant.exports.finance-pdf', $data)->render();

        $key = Str::random(40);
        Cache::put('tenant-finance-pdf:'.$key, [
            'user_id' => Auth::id(),
            'html' => $html,
        ], now()->addMinutes(5));

        $this->dispatch('open-finance-pdf', url: route('tenant.finance.pdf', $key));
    }

    private function getFinanceExportViewData(): array
    {
        $tenant = Tenant::find($this->tenantId);
        $data = $this->getFinanceBreakdownData(forExport: true);
        $data['tenant'] = $tenant;
        $data['tenantName'] = $tenant?->name ?? 'Lokara POS';
        $data['isSalon'] = $tenant?->isSalon() || $tenant?->hasFeature('has_services');
        $data['generatedAt'] = now()->format('d M Y, H:i').' WIB';
        $data['activeSubTab'] = $this->financeSubTab;

        return $data;
    }

    private function financeExportTenantSlug(): string
    {
        return Tenant::find($this->tenantId)?->slug ?? 'tenant';
    }
}

