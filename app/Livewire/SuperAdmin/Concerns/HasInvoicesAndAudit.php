<?php

namespace App\Livewire\SuperAdmin\Concerns;

use App\Models\Invoice;
use App\Models\PlatformActivityLog;
use Carbon\Carbon;

trait HasInvoicesAndAudit
{
    public string $invoiceStatusFilter = 'all';

    public function markInvoicePaid(int $invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $invoice->update([
            'status' => 'paid',
            'paid_at' => Carbon::now(),
            'payment_method' => 'Manual Transfer (Admin Verification)',
        ]);

        PlatformActivityLog::create([
            'action' => "Invoice {$invoice->invoice_number} ditandai Lunas",
            'description' => "Tenant: {$invoice->tenant->name} • Rp " . number_format($invoice->amount, 0, ',', '.'),
            'icon' => 'fa-receipt',
            'icon_color' => 'text-emerald-500 bg-emerald-50',
            'badge_text' => 'Baru saja',
        ]);

        $this->notify("Invoice {$invoice->invoice_number} berhasil ditandai LUNAS.");
    }
}

