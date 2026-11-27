<?php

namespace App\Livewire\Tenant\Concerns;

use App\Models\Employee;
use App\Models\OrderItem;
use Carbon\Carbon;

trait HasEmployees
{
    // Employee CRUD Modal
    public bool $showEmployeeModal = false;

    public ?int $editingEmployeeId = null;

    public string $employeeName = '';

    public string $employeeRoleTitle = 'Terapis / Stylist';

    public string $employeePhone = '';

    public ?int $employeeOutletId = null;

    public bool $employeeIsActive = true;

    public string $searchEmployee = '';

    // Weekly Fee Recap Filters
    public string $employeeFeePeriod = '4_weeks'; // this_week, last_week, 4_weeks, this_month, last_month, custom

    public string $employeeFeeStartDate = '';

    public string $employeeFeeEndDate = '';

    public string $selectedFeeEmployeeId = 'all';

    // Detail Modal
    public bool $showEmployeeFeeDetailModal = false;

    public ?int $detailFeeEmployeeId = null;

    public string $detailFeeWeekStart = '';

    public string $detailFeeWeekLabel = '';

    public function openNewEmployeeModal(): void
    {
        $this->editingEmployeeId = null;
        $this->employeeName = '';
        $this->employeeRoleTitle = 'Terapis / Stylist';
        $this->employeePhone = '';
        $this->employeeOutletId = $this->selectedOutletId;
        $this->employeeIsActive = true;
        $this->showEmployeeModal = true;
    }

    public function editEmployee(int $id): void
    {
        $employee = Employee::where('tenant_id', $this->tenantId)->findOrFail($id);
        $this->editingEmployeeId = $employee->id;
        $this->employeeName = $employee->name;
        $this->employeeRoleTitle = $employee->role_title ?: 'Terapis / Stylist';
        $this->employeePhone = $employee->phone ?? '';
        $this->employeeOutletId = $employee->outlet_id;
        $this->employeeIsActive = (bool) $employee->is_active;
        $this->showEmployeeModal = true;
    }

    public function saveEmployee(): void
    {
        $this->validate([
            'employeeName' => 'required|min:2|max:100',
            'employeeRoleTitle' => 'required|min:2|max:80',
            'employeePhone' => 'nullable|max:30',
        ], [
            'employeeName.required' => 'Nama pegawai wajib diisi.',
            'employeeName.min' => 'Nama pegawai minimal 2 karakter.',
            'employeeRoleTitle.required' => 'Jabatan / peran pegawai wajib diisi.',
        ]);

        $data = [
            'tenant_id' => $this->tenantId,
            'outlet_id' => $this->employeeOutletId ?: $this->selectedOutletId,
            'name' => trim($this->employeeName),
            'role_title' => trim($this->employeeRoleTitle),
            'phone' => trim($this->employeePhone) ?: null,
            'is_active' => $this->employeeIsActive,
        ];

        if ($this->editingEmployeeId) {
            $employee = Employee::where('tenant_id', $this->tenantId)->findOrFail($this->editingEmployeeId);
            $employee->update($data);
            $this->showToast("Data pegawai '{$employee->name}' berhasil diperbarui.");
        } else {
            $employee = Employee::create($data);
            $this->showToast("Pegawai baru '{$employee->name}' berhasil ditambahkan.");
        }

        $this->showEmployeeModal = false;
    }

    public function toggleEmployeeStatus(int $id): void
    {
        $employee = Employee::where('tenant_id', $this->tenantId)->findOrFail($id);
        $employee->update(['is_active' => ! $employee->is_active]);
        $status = $employee->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $this->showToast("Pegawai '{$employee->name}' {$status}.");
    }

    public function deleteEmployee(int $id): void
    {
        $employee = Employee::where('tenant_id', $this->tenantId)->findOrFail($id);
        $name = $employee->name;
        $employee->delete();
        $this->showToast("Pegawai '{$name}' berhasil dihapus.");
    }

    public function setEmployeeFeePeriod(string $period): void
    {
        $this->employeeFeePeriod = $period;
        if ($period === 'custom') {
            if (empty($this->employeeFeeStartDate)) {
                $this->employeeFeeStartDate = Carbon::now()->subWeeks(3)->startOfWeek(Carbon::MONDAY)->toDateString();
            }
            if (empty($this->employeeFeeEndDate)) {
                $this->employeeFeeEndDate = Carbon::now()->endOfWeek(Carbon::SUNDAY)->toDateString();
            }
        }
    }

    public function updatedEmployeeFeeStartDate(): void
    {
        $this->employeeFeePeriod = 'custom';
    }

    public function updatedEmployeeFeeEndDate(): void
    {
        $this->employeeFeePeriod = 'custom';
    }

    public function openEmployeeFeeDetail(?int $employeeId, string $weekStart = '', string $weekLabel = ''): void
    {
        $this->detailFeeEmployeeId = $employeeId;
        $this->detailFeeWeekStart = $weekStart;
        $this->detailFeeWeekLabel = $weekLabel;
        $this->showEmployeeFeeDetailModal = true;
    }

    public function closeEmployeeFeeDetail(): void
    {
        $this->showEmployeeFeeDetailModal = false;
    }

    /**
     * @return array{0: Carbon, 1: Carbon, 2: string}
     */
    private function resolveEmployeeFeeWindow(): array
    {
        $now = Carbon::now();

        return match ($this->employeeFeePeriod) {
            'this_week' => [
                $now->copy()->startOfWeek(Carbon::MONDAY)->startOfDay(),
                $now->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay(),
                'Minggu Ini ('.$now->copy()->startOfWeek(Carbon::MONDAY)->translatedFormat('d M').' – '.$now->copy()->endOfWeek(Carbon::SUNDAY)->translatedFormat('d M Y').')',
            ],
            'last_week' => [
                $now->copy()->subWeek()->startOfWeek(Carbon::MONDAY)->startOfDay(),
                $now->copy()->subWeek()->endOfWeek(Carbon::SUNDAY)->endOfDay(),
                'Minggu Lalu ('.$now->copy()->subWeek()->startOfWeek(Carbon::MONDAY)->translatedFormat('d M').' – '.$now->copy()->subWeek()->endOfWeek(Carbon::SUNDAY)->translatedFormat('d M Y').')',
            ],
            'this_month' => [
                $now->copy()->startOfMonth()->startOfDay(),
                $now->copy()->endOfMonth()->endOfDay(),
                'Bulan Ini ('.$now->copy()->translatedFormat('F Y').')',
            ],
            'last_month' => [
                $now->copy()->subMonth()->startOfMonth()->startOfDay(),
                $now->copy()->subMonth()->endOfMonth()->endOfDay(),
                'Bulan Lalu ('.$now->copy()->subMonth()->translatedFormat('F Y').')',
            ],
            'custom' => (function () use ($now) {
                $start = ! empty($this->employeeFeeStartDate)
                    ? Carbon::parse($this->employeeFeeStartDate)->startOfDay()
                    : $now->copy()->subWeeks(3)->startOfWeek(Carbon::MONDAY)->startOfDay();
                $end = ! empty($this->employeeFeeEndDate)
                    ? Carbon::parse($this->employeeFeeEndDate)->endOfDay()
                    : $now->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
                if ($start->gt($end)) {
                    [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
                }

                return [$start, $end, $start->translatedFormat('d M Y').' – '.$end->translatedFormat('d M Y')];
            })(),
            default => [ // '4_weeks'
                $now->copy()->subWeeks(3)->startOfWeek(Carbon::MONDAY)->startOfDay(),
                $now->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay(),
                '4 Minggu Terakhir',
            ],
        };
    }

    /**
     * Compute employees master list and weekly fee recap data.
     *
     * @return array<string, mixed>
     */
    protected function getEmployeesAndFeeData(): array
    {
        $allEmployees = Employee::with('outlet')
            ->where('tenant_id', $this->tenantId)
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        $employees = $allEmployees;
        if (! empty($this->searchEmployee)) {
            $needle = mb_strtolower(trim($this->searchEmployee));
            $employees = $allEmployees->filter(function (Employee $emp) use ($needle) {
                return str_contains(mb_strtolower($emp->name), $needle)
                    || str_contains(mb_strtolower($emp->role_title ?? ''), $needle)
                    || str_contains(mb_strtolower($emp->phone ?? ''), $needle);
            })->values();
        }

        [$windowStart, $windowEnd, $periodLabel] = $this->resolveEmployeeFeeWindow();

        $serviceItems = OrderItem::with(['order', 'product', 'employee'])
            ->whereHas('order', function ($q) use ($windowStart, $windowEnd) {
                $q->where('tenant_id', $this->tenantId)
                    ->where('status', 'completed')
                    ->when($this->selectedOutletId, fn ($oq) => $oq->where('outlet_id', $this->selectedOutletId))
                    ->whereBetween('created_at', [$windowStart, $windowEnd]);
            })
            ->where(function ($q) {
                $q->whereHas('product', fn ($pq) => $pq->where('type', 'service'))
                    ->orWhere('commission_amount', '>', 0)
                    ->orWhereNotNull('employee_id');
            })
            ->get();

        $weeklyEmployeeMap = [];
        $employeeSummaryMap = [];
        $totalFeeAll = 0.0;
        $totalAssignedFee = 0.0;
        $totalUnassignedFee = 0.0;
        $totalSessions = 0.0;
        $detailItems = [];

        foreach ($serviceItems as $item) {
            $order = $item->order;
            if (! $order) {
                continue;
            }

            $qty = (float) $item->qty;
            $subtotal = (float) $item->subtotal;
            $fee = (float) $item->commission_amount;

            // Fallback for historical items created before commission_amount snapshot
            if ($fee <= 0 && $item->product && $item->product->type === 'service') {
                $unitFee = $item->product->commission_type === 'percentage'
                    ? round(((float) $item->unit_price * (float) $item->product->commission_value) / 100)
                    : (float) $item->product->commission_value;
                $fee = $unitFee * $qty;
            }

            $empId = $item->employee_id;
            $empName = $item->employee?->name ?? $item->employee_name ?? 'Belum Dipilih Pegawai';
            $empRole = $item->employee?->role_title ?? ($empId ? 'Pegawai' : 'Perlu Konfirmasi');

            if ($this->selectedFeeEmployeeId !== 'all') {
                if ($this->selectedFeeEmployeeId === 'unassigned' && $empId !== null) {
                    continue;
                }
                if ($this->selectedFeeEmployeeId !== 'unassigned' && (int) $this->selectedFeeEmployeeId !== (int) $empId) {
                    continue;
                }
            }

            $createdAt = Carbon::parse($order->created_at);
            $weekStart = $createdAt->copy()->startOfWeek(Carbon::MONDAY);
            $weekEnd = $createdAt->copy()->endOfWeek(Carbon::SUNDAY);
            $weekKey = $weekStart->toDateString();
            $weekLabel = $weekStart->translatedFormat('d M').' – '.$weekEnd->translatedFormat('d M Y');

            $totalFeeAll += $fee;
            $totalSessions += $qty;
            if ($empId !== null) {
                $totalAssignedFee += $fee;
            } else {
                $totalUnassignedFee += $fee;
            }

            $rowKey = $weekKey.'|'.($empId ?? 'none');
            if (! isset($weeklyEmployeeMap[$rowKey])) {
                $weeklyEmployeeMap[$rowKey] = [
                    'week_key' => $weekKey,
                    'week_label' => $weekLabel,
                    'week_start' => $weekStart->toDateString(),
                    'week_end' => $weekEnd->toDateString(),
                    'employee_id' => $empId,
                    'employee_name' => $empName,
                    'role_title' => $empRole,
                    'sessions_count' => 0.0,
                    'total_sales' => 0.0,
                    'total_fee' => 0.0,
                ];
            }
            $weeklyEmployeeMap[$rowKey]['sessions_count'] += $qty;
            $weeklyEmployeeMap[$rowKey]['total_sales'] += $subtotal;
            $weeklyEmployeeMap[$rowKey]['total_fee'] += $fee;

            $sumKey = $empId ?? 'none';
            if (! isset($employeeSummaryMap[$sumKey])) {
                $employeeSummaryMap[$sumKey] = [
                    'employee_id' => $empId,
                    'employee_name' => $empName,
                    'role_title' => $empRole,
                    'sessions_count' => 0.0,
                    'total_sales' => 0.0,
                    'total_fee' => 0.0,
                    'weeks_active' => [],
                ];
            }
            $employeeSummaryMap[$sumKey]['sessions_count'] += $qty;
            $employeeSummaryMap[$sumKey]['total_sales'] += $subtotal;
            $employeeSummaryMap[$sumKey]['total_fee'] += $fee;
            $employeeSummaryMap[$sumKey]['weeks_active'][$weekKey] = true;

            if ($this->showEmployeeFeeDetailModal) {
                $matchEmp = $this->detailFeeEmployeeId === null ? ($empId === null) : ((int) $empId === $this->detailFeeEmployeeId);
                $matchWeek = empty($this->detailFeeWeekStart) || $weekKey === $this->detailFeeWeekStart;
                if ($matchEmp && $matchWeek) {
                    $detailItems[] = [
                        'order_number' => $order->order_number,
                        'created_at' => $createdAt->translatedFormat('d M Y, H:i'),
                        'service_name' => $item->product_name,
                        'qty' => $qty,
                        'subtotal' => $subtotal,
                        'fee' => $fee,
                    ];
                }
            }
        }

        $weeklyRows = array_values($weeklyEmployeeMap);
        usort($weeklyRows, function ($a, $b) {
            $cmp = strcmp($b['week_key'], $a['week_key']);

            return $cmp !== 0 ? $cmp : ($b['total_fee'] <=> $a['total_fee']);
        });

        $employeeTotals = array_map(function ($row) {
            $row['weeks_count'] = count($row['weeks_active']);
            unset($row['weeks_active']);

            return $row;
        }, array_values($employeeSummaryMap));
        usort($employeeTotals, fn ($a, $b) => $b['total_fee'] <=> $a['total_fee']);

        return [
            'allEmployees' => $allEmployees,
            'employees' => $employees,
            'periodLabel' => $periodLabel,
            'weeklyRows' => $weeklyRows,
            'employeeTotals' => $employeeTotals,
            'detailItems' => $detailItems,
            'summary' => [
                'total_fee' => $totalFeeAll,
                'assigned_fee' => $totalAssignedFee,
                'unassigned_fee' => $totalUnassignedFee,
                'total_sessions' => $totalSessions,
                'active_employees_count' => $allEmployees->where('is_active', true)->count(),
            ],
        ];
    }
}
