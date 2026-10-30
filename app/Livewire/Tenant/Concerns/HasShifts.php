<?php

namespace App\Livewire\Tenant\Concerns;

use App\Models\Tenant;

trait HasShifts
{
    // Master Shift Management
    public bool $showShiftTemplateModal = false;
    public ?int $editingShiftTemplateId = null;
    public string $shiftTemplateName = '';
    public string $shiftTemplateStartTime = '07:00';
    public string $shiftTemplateEndTime = '15:00';
    public float $shiftTemplateDefaultCash = 200000;
    public bool $shiftTemplateIsActive = true;

    // Shift Policy Rules
    public float $policyDefaultCash = 200000;
    public bool $policyEnforceLock = true;
    public bool $policyRequireReconciliation = true;

    public function openNewShiftTemplateModal(): void
    {
        $this->editingShiftTemplateId = null;
        $this->shiftTemplateName = '';
        $this->shiftTemplateStartTime = '07:00';
        $this->shiftTemplateEndTime = '15:00';
        $this->shiftTemplateDefaultCash = 200000;
        $this->shiftTemplateIsActive = true;
        $this->showShiftTemplateModal = true;
    }

    public function editShiftTemplate(int $id): void
    {
        $templates = $this->getShiftTemplates();
        $template = collect($templates)->firstWhere('id', $id);
        if ($template) {
            $this->editingShiftTemplateId = $template['id'];
            $this->shiftTemplateName = $template['name'];
            $this->shiftTemplateStartTime = $template['start_time'];
            $this->shiftTemplateEndTime = $template['end_time'];
            $this->shiftTemplateDefaultCash = (float) $template['default_cash'];
            $this->shiftTemplateIsActive = (bool) $template['is_active'];
            $this->showShiftTemplateModal = true;
        }
    }

    public function saveShiftTemplate(): void
    {
        $this->validate([
            'shiftTemplateName' => 'required|string|max:100',
            'shiftTemplateStartTime' => 'required',
            'shiftTemplateEndTime' => 'required',
            'shiftTemplateDefaultCash' => 'required|numeric|min:0',
        ]);

        $tenant = Tenant::find($this->tenantId);
        if (!$tenant) return;

        $settings = $tenant->settings ?? [];
        $templates = $this->getShiftTemplates();

        if ($this->editingShiftTemplateId) {
            foreach ($templates as &$item) {
                if ($item['id'] === $this->editingShiftTemplateId) {
                    $item['name'] = $this->shiftTemplateName;
                    $item['start_time'] = $this->shiftTemplateStartTime;
                    $item['end_time'] = $this->shiftTemplateEndTime;
                    $item['default_cash'] = $this->shiftTemplateDefaultCash;
                    $item['is_active'] = $this->shiftTemplateIsActive;
                }
            }
        } else {
            $newId = count($templates) > 0 ? max(array_column($templates, 'id')) + 1 : 1;
            $templates[] = [
                'id' => $newId,
                'name' => $this->shiftTemplateName,
                'start_time' => $this->shiftTemplateStartTime,
                'end_time' => $this->shiftTemplateEndTime,
                'default_cash' => $this->shiftTemplateDefaultCash,
                'is_active' => $this->shiftTemplateIsActive,
            ];
        }

        $settings['shift_templates'] = $templates;
        $tenant->update(['settings' => $settings]);
        $this->showShiftTemplateModal = false;
        $this->showToast('Master template shift berhasil disimpan.');
    }

    public function toggleShiftTemplateStatus(int $id): void
    {
        $tenant = Tenant::find($this->tenantId);
        if (!$tenant) return;

        $settings = $tenant->settings ?? [];
        $templates = $this->getShiftTemplates();

        foreach ($templates as &$item) {
            if ($item['id'] === $id) {
                $item['is_active'] = !($item['is_active'] ?? true);
            }
        }

        $settings['shift_templates'] = $templates;
        $tenant->update(['settings' => $settings]);
        $this->showToast('Status template shift berhasil diubah.');
    }

    public function deleteShiftTemplate(int $id): void
    {
        $tenant = Tenant::find($this->tenantId);
        if (!$tenant) return;

        $settings = $tenant->settings ?? [];
        $templates = array_values(array_filter($this->getShiftTemplates(), fn($t) => $t['id'] !== $id));

        $settings['shift_templates'] = $templates;
        $tenant->update(['settings' => $settings]);
        $this->showToast('Template shift berhasil dihapus.', 'info');
    }

    public function saveShiftPolicies(): void
    {
        $tenant = Tenant::find($this->tenantId);
        if (!$tenant) return;

        $settings = $tenant->settings ?? [];
        $settings['shift_policies'] = [
            'default_cash' => $this->policyDefaultCash,
            'enforce_lock' => $this->policyEnforceLock,
            'require_reconciliation' => $this->policyRequireReconciliation,
        ];
        $tenant->update(['settings' => $settings]);
        $this->showToast('Kebijakan & aturan master shift berhasil disimpan.');
    }

    public function getShiftTemplates(): array
    {
        $tenant = Tenant::find($this->tenantId);
        if ($tenant && !empty($tenant->settings['shift_templates'])) {
            return $tenant->settings['shift_templates'];
        }

        return [
            ['id' => 1, 'name' => 'Shift 1 (Pagi)', 'start_time' => '07:00', 'end_time' => '15:00', 'default_cash' => 200000, 'is_active' => true],
            ['id' => 2, 'name' => 'Shift 2 (Sore - Malam)', 'start_time' => '15:00', 'end_time' => '23:00', 'default_cash' => 200000, 'is_active' => true],
            ['id' => 3, 'name' => 'Shift Full Day', 'start_time' => '08:00', 'end_time' => '22:00', 'default_cash' => 300000, 'is_active' => false],
        ];
    }
}

