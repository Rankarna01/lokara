<?php

namespace App\Livewire\Tenant\Concerns;

use App\Models\Order;
use Carbon\Carbon;

trait HasOrdersAndVoid
{
    // Transactions Filters
    public string $searchTransaction = '';

    public string $transactionTypeTab = 'all'; // all, services, products (Khusus Salon & Barbershop)

    public string $transactionStatusFilter = 'all'; // all, completed, hold, voided

    public string $transactionPaymentMethodFilter = 'all'; // all, cash, qris, transfer, debit, credit

    public string $transactionOrderTypeFilter = 'all'; // all, dine_in, take_away, delivery

    public string $transactionDateFilter = '30_days'; // 30_days, 7_days, today, yesterday, this_month, all, custom

    public string $transactionStartDate = '';

    public string $transactionStartTime = '00:00';

    public string $transactionEndDate = '';

    public string $transactionEndTime = '23:59';

    public int $transactionPerPage = 20;

    // Chart & Trend Controls
    public string $transactionTrendPeriod = '7_days'; // 7_days, 14_days, 30_days

    public string $transactionTrendMetric = 'revenue'; // revenue, count

    // Transaction Detail & Receipt Modals
    public bool $showTransactionDetailModal = false;

    public ?int $selectedTransactionId = null;

    public bool $showPrintReceiptModal = false;

    // Void Order Modal
    public bool $showVoidModal = false;

    public ?int $voidOrderId = null;

    public string $voidPin = '';

    public string $voidReason = 'Kesalahan pesanan';

    public function setTransactionTypeTab(string $tab): void
    {
        $this->transactionTypeTab = in_array($tab, ['all', 'services', 'products']) ? $tab : 'all';
        $this->resetPage('transactionsPage');
    }

    public function resetTransactionFilters(): void
    {
        $this->searchTransaction = '';
        $this->transactionTypeTab = 'all';
        $this->transactionStatusFilter = 'all';
        $this->transactionPaymentMethodFilter = 'all';
        $this->transactionOrderTypeFilter = 'all';
        $this->transactionDateFilter = '30_days';
        $this->transactionStartDate = '';
        $this->transactionEndDate = '';
        $this->resetPage('transactionsPage');
    }

    public function updatedTransactionPerPage(): void
    {
        $this->resetPage('transactionsPage');
    }

    public function updatedSearchTransaction(): void
    {
        $this->resetPage('transactionsPage');
    }

    public function updatedTransactionStatusFilter(): void
    {
        $this->resetPage('transactionsPage');
    }

    public function updatedTransactionPaymentMethodFilter(): void
    {
        $this->resetPage('transactionsPage');
    }

    public function updatedTransactionOrderTypeFilter(): void
    {
        $this->resetPage('transactionsPage');
    }

    public function updatedTransactionDateFilter(): void
    {
        $this->resetPage('transactionsPage');
    }

    public function updatedTransactionStartDate(): void
    {
        $this->resetPage('transactionsPage');
    }

    public function updatedTransactionEndDate(): void
    {
        $this->resetPage('transactionsPage');
    }

    public function viewTransactionDetail(int $orderId): void
    {
        $this->selectedTransactionId = $orderId;
        $this->showTransactionDetailModal = true;
    }

    public function closeTransactionDetailModal(): void
    {
        $this->showTransactionDetailModal = false;
    }

    public function openPrintReceiptModal(int $orderId): void
    {
        $this->selectedTransactionId = $orderId;
        $this->showPrintReceiptModal = true;
    }

    public function closePrintReceiptModal(): void
    {
        $this->showPrintReceiptModal = false;
    }

    public function setTransactionDateFilter(string $filter): void
    {
        $this->transactionDateFilter = $filter;
        if ($filter !== 'custom') {
            $this->transactionStartDate = '';
            $this->transactionEndDate = '';
        }
        $this->resetPage('transactionsPage');
    }

    public function setTransactionTrendPeriod(string $period): void
    {
        $this->transactionTrendPeriod = $period;
    }

    public function setTransactionTrendMetric(string $metric): void
    {
        $this->transactionTrendMetric = $metric;
    }

    public function openVoidModal(int $orderId): void
    {
        $this->voidOrderId = $orderId;
        $this->voidPin = '';
        $this->voidReason = 'Kesalahan pesanan';
        $this->showVoidModal = true;
    }

    public function executeVoidOrder(): void
    {
        if ($this->voidPin !== '888888' && $this->voidPin !== '123456') {
            $this->showToast('PIN Otorisasi Manajer salah!', 'error');

            return;
        }

        $order = Order::where('tenant_id', $this->tenantId)->findOrFail($this->voidOrderId);
        $order->update([
            'status' => 'voided',
            'notes' => ($order->notes ? $order->notes.' | ' : '')."VOID oleh Manajer. Alasan: {$this->voidReason}",
        ]);

        $this->showVoidModal = false;
        $this->showToast("Pesanan {$order->order_number} berhasil di-VOID.", 'info');
    }

    /**
     * Get filtered transactions list with comprehensive relation loading
     */
    protected function getFilteredTransactionsData(bool $withoutLimit = false): array
    {
        $baseQuery = Order::where('tenant_id', $this->tenantId);

        if ($this->selectedOutletId) {
            $baseQuery->where('outlet_id', $this->selectedOutletId);
        }

        if ($this->transactionStatusFilter !== 'all') {
            $baseQuery->where('status', $this->transactionStatusFilter);
        }

        if ($this->transactionPaymentMethodFilter !== 'all') {
            $baseQuery->whereHas('payments', function ($q) {
                $q->where('payment_method', $this->transactionPaymentMethodFilter);
            });
        }

        if ($this->transactionOrderTypeFilter !== 'all') {
            $baseQuery->where('order_type', $this->transactionOrderTypeFilter);
        }

        // Date Filter
        if ($this->transactionDateFilter === 'today') {
            $baseQuery->whereBetween('created_at', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()]);
        } elseif ($this->transactionDateFilter === 'yesterday') {
            $baseQuery->whereBetween('created_at', [Carbon::yesterday()->startOfDay(), Carbon::yesterday()->endOfDay()]);
        } elseif ($this->transactionDateFilter === '7_days') {
            $baseQuery->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay());
        } elseif ($this->transactionDateFilter === '30_days') {
            $baseQuery->where('created_at', '>=', Carbon::now()->subDays(29)->startOfDay());
        } elseif ($this->transactionDateFilter === 'this_month') {
            $baseQuery->where('created_at', '>=', Carbon::now()->startOfMonth()->startOfDay());
        } elseif ($this->transactionDateFilter === 'custom') {
            if (! empty($this->transactionStartDate)) {
                $start = Carbon::parse($this->transactionStartDate.' '.($this->transactionStartTime ?: '00:00:00'));
                $baseQuery->where('created_at', '>=', $start);
            }
            if (! empty($this->transactionEndDate)) {
                $end = Carbon::parse($this->transactionEndDate.' '.($this->transactionEndTime ?: '23:59:59'));
                $baseQuery->where('created_at', '<=', $end);
            }
        }

        // Search Filter
        if (! empty($this->searchTransaction)) {
            $search = trim($this->searchTransaction);
            $baseQuery->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('table_number', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('items', function ($iq) use ($search) {
                        $iq->where('product_name', 'like', "%{$search}%")
                            ->orWhere('variant_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('payments', function ($pq) use ($search) {
                        $pq->where('reference_number', 'like', "%{$search}%");
                    });
            });
        }

        // Global counts for tabs (Layanan vs Produk vs Semua)
        $allCount = (clone $baseQuery)->count();
        $serviceCount = (clone $baseQuery)->whereHas('items.product', function ($pq) {
            $pq->where('type', 'service');
        })->count();
        $productCount = (clone $baseQuery)->whereHas('items.product', function ($pq) {
            $pq->where('type', '!=', 'service');
        })->count();

        // Target Query with items eager-loaded
        $query = (clone $baseQuery)->with([
            'items.product',
            'payments',
            'customer',
            'user',
            'outlet',
            'shift',
        ]);

        // Filter Type Tab (Khusus Salon & Barbershop: Penjualan Layanan vs Penjualan Produk)
        if ($this->transactionTypeTab === 'services') {
            $query->whereHas('items.product', function ($pq) {
                $pq->where('type', 'service');
            });
        } elseif ($this->transactionTypeTab === 'products') {
            $query->whereHas('items.product', function ($pq) {
                $pq->where('type', '!=', 'service');
            });
        }

        $totalFilteredCount = (clone $query)->count();
        $sortedQuery = $query->orderBy('created_at', 'desc');
        $transactions = $withoutLimit
            ? $sortedQuery->get()
            : $sortedQuery->paginate($this->transactionPerPage, ['*'], 'transactionsPage');

        $viewingTransaction = null;
        if ($this->selectedTransactionId) {
            $viewingTransaction = Order::with([
                'items.product',
                'payments',
                'customer',
                'user',
                'outlet',
                'shift',
            ])->where('tenant_id', $this->tenantId)->find($this->selectedTransactionId);
        }

        return [
            'transactions' => $transactions,
            'totalFilteredCount' => $totalFilteredCount,
            'viewingTransaction' => $viewingTransaction,
            'allCount' => $allCount,
            'serviceCount' => $serviceCount,
            'productCount' => $productCount,
        ];
    }

    /**
     * Compute analytics metrics and trend chart data
     */
    protected function getTransactionAnalyticsData(): array
    {
        $baseQuery = Order::where('tenant_id', $this->tenantId);
        if ($this->selectedOutletId) {
            $baseQuery->where('outlet_id', $this->selectedOutletId);
        }

        // Apply same date filter for summary metrics
        $metricsQuery = clone $baseQuery;
        if ($this->transactionDateFilter === 'today') {
            $metricsQuery->whereBetween('created_at', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()]);
        } elseif ($this->transactionDateFilter === 'yesterday') {
            $metricsQuery->whereBetween('created_at', [Carbon::yesterday()->startOfDay(), Carbon::yesterday()->endOfDay()]);
        } elseif ($this->transactionDateFilter === '7_days') {
            $metricsQuery->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay());
        } elseif ($this->transactionDateFilter === '30_days') {
            $metricsQuery->where('created_at', '>=', Carbon::now()->subDays(29)->startOfDay());
        } elseif ($this->transactionDateFilter === 'this_month') {
            $metricsQuery->where('created_at', '>=', Carbon::now()->startOfMonth()->startOfDay());
        } elseif ($this->transactionDateFilter === 'custom') {
            if (! empty($this->transactionStartDate)) {
                $start = Carbon::parse($this->transactionStartDate.' '.($this->transactionStartTime ?: '00:00:00'));
                $metricsQuery->where('created_at', '>=', $start);
            }
            if (! empty($this->transactionEndDate)) {
                $end = Carbon::parse($this->transactionEndDate.' '.($this->transactionEndTime ?: '23:59:59'));
                $metricsQuery->where('created_at', '<=', $end);
            }
        }

        $allOrdersInPeriod = $metricsQuery->with('payments')->get();
        $completedOrders = $allOrdersInPeriod->where('status', 'completed');
        $voidedOrdersCount = $allOrdersInPeriod->where('status', 'voided')->count();

        $totalRevenue = (float) $completedOrders->sum('total_amount');
        $completedCount = $completedOrders->count();
        $avgTicket = $completedCount > 0 ? ($totalRevenue / $completedCount) : 0;

        // Payment method breakdown
        $paymentCounts = [];
        $paymentAmounts = [];
        foreach ($completedOrders as $order) {
            foreach ($order->payments as $payment) {
                $method = strtolower($payment->payment_method ?? 'tunai');
                $paymentCounts[$method] = ($paymentCounts[$method] ?? 0) + 1;
                $paymentAmounts[$method] = ($paymentAmounts[$method] ?? 0) + (float) $payment->amount;
            }
        }
        arsort($paymentAmounts);
        $topPaymentMethod = ! empty($paymentAmounts) ? array_key_first($paymentAmounts) : 'Belum Ada';
        $topPaymentSharePct = $totalRevenue > 0 && ! empty($paymentAmounts) ? round((reset($paymentAmounts) / $totalRevenue) * 100) : 0;

        // Order Type breakdown
        $orderTypes = [
            'dine_in' => $completedOrders->where('order_type', 'dine_in')->count(),
            'take_away' => $completedOrders->where('order_type', 'take_away')->count(),
            'delivery' => $completedOrders->where('order_type', 'delivery')->count(),
        ];

        // Build Daily Trend Chart
        $daysBack = match ($this->transactionTrendPeriod) {
            '14_days' => 14,
            '30_days' => 30,
            default => 7,
        };

        $chartDays = [];
        $maxRevenue = 0;
        $maxCount = 0;
        $trendTotalRevenue = 0;
        $trendTotalCount = 0;

        for ($i = $daysBack - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateStr = $date->toDateString();

            $dayOrders = Order::where('tenant_id', $this->tenantId)
                ->where('status', 'completed')
                ->when($this->selectedOutletId, fn ($q) => $q->where('outlet_id', $this->selectedOutletId))
                ->whereDate('created_at', $dateStr)
                ->get();

            $rev = (float) $dayOrders->sum('total_amount');
            $cnt = $dayOrders->count();

            if ($rev > $maxRevenue) {
                $maxRevenue = $rev;
            }
            if ($cnt > $maxCount) {
                $maxCount = $cnt;
            }

            $trendTotalRevenue += $rev;
            $trendTotalCount += $cnt;

            $chartDays[] = [
                'date' => $dateStr,
                'short_label' => $date->format('d/m'),
                'day_name' => $date->translatedFormat('D'),
                'full_label' => $date->translatedFormat('d M Y'),
                'is_today' => $date->isToday(),
                'revenue' => $rev,
                'tx_count' => $cnt,
            ];
        }

        // Compute bar percentages
        foreach ($chartDays as &$day) {
            if ($this->transactionTrendMetric === 'count') {
                $day['pct'] = $maxCount > 0 ? max(6, round(($day['tx_count'] / $maxCount) * 100)) : 6;
            } else {
                $day['pct'] = $maxRevenue > 0 ? max(6, round(($day['revenue'] / $maxRevenue) * 100)) : 6;
            }
        }
        unset($day);

        $avgDailyRevenue = $daysBack > 0 ? ($trendTotalRevenue / $daysBack) : 0;

        return [
            'totalRevenue' => $totalRevenue,
            'completedCount' => $completedCount,
            'voidedOrdersCount' => $voidedOrdersCount,
            'avgTicket' => $avgTicket,
            'topPaymentMethod' => $topPaymentMethod,
            'topPaymentSharePct' => $topPaymentSharePct,
            'paymentAmounts' => $paymentAmounts,
            'paymentCounts' => $paymentCounts,
            'orderTypes' => $orderTypes,
            'chartDays' => $chartDays,
            'maxRevenue' => $maxRevenue,
            'maxCount' => $maxCount,
            'trendTotalRevenue' => $trendTotalRevenue,
            'trendTotalCount' => $trendTotalCount,
            'avgDailyRevenue' => $avgDailyRevenue,
        ];
    }
}
