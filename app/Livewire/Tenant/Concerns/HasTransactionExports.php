<?php

namespace App\Livewire\Tenant\Concerns;

use App\Models\Order;
use App\Models\Tenant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

trait HasTransactionExports
{
    public function exportTransactionsExcel()
    {
        $transactions = $this->getFilteredTransactionsData(withoutLimit: true)['transactions'];
        $html = view('livewire.tenant.exports.transactions-excel', $this->transactionExportViewData($transactions))->render();
        $fileName = 'transaksi-'.$this->transactionExportTenantSlug().'-'.now()->format('Ymd-His').'.xls';

        return response()->streamDownload(function () use ($html) {
            echo $html;
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    public function exportTransactionsPdf(): void
    {
        $transactions = $this->getFilteredTransactionsData(withoutLimit: true)['transactions'];
        $html = view('livewire.tenant.exports.transactions-pdf', $this->transactionExportViewData($transactions))->render();

        $key = Str::random(40);
        Cache::put('tenant-transactions-pdf:'.$key, [
            'user_id' => Auth::id(),
            'html' => $html,
        ], now()->addMinutes(5));

        $this->dispatch('open-transactions-pdf', url: route('tenant.transactions.pdf', $key));
    }

    /**
     * @param  Collection<int, Order>  $transactions
     * @return array{tenantName: string, periodLabel: string, transactions: Collection<int, Order>, totalAmount: float, generatedAt: string}
     */
    private function transactionExportViewData(Collection $transactions): array
    {
        return [
            'tenantName' => Tenant::find($this->tenantId)?->name ?? 'Lokara POS',
            'periodLabel' => $this->transactionExportPeriodLabel(),
            'transactions' => $transactions,
            'totalAmount' => (float) $transactions->sum('total_amount'),
            'generatedAt' => now()->format('d M Y, H:i').' WIB',
        ];
    }

    private function transactionExportTenantSlug(): string
    {
        return Tenant::find($this->tenantId)?->slug ?? 'tenant';
    }

    private function transactionExportPeriodLabel(): string
    {
        return match ($this->transactionDateFilter) {
            '7_days' => '7 Hari Terakhir',
            '30_days' => '30 Hari Terakhir',
            'today' => 'Hari Ini',
            'yesterday' => 'Kemarin',
            'this_month' => 'Bulan Ini',
            'custom' => 'Kustom: '.($this->transactionStartDate ?: '-').' s/d '.($this->transactionEndDate ?: '-'),
            default => 'Semua Waktu',
        };
    }
}
