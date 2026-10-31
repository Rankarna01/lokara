<?php

namespace App\Livewire\Tenant\Concerns;

use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;

trait HasStoreSettings
{
    // Sub-tab Navigation: general (Cabang & Staf), receipt (Struk Thermal), payments (Metode Bayar & QRIS), pwa (PWA Mobile)
    public string $settingsSubTab = 'general';

    // Struk Thermal Customization Properties
    public string $receiptStoreName = '';
    public string $receiptTagline = '';
    public string $receiptHeaderNotes = '';
    public string $receiptFooterNotes = '';
    public string $receiptWifiInfo = '';
    public string $receiptSocialMedia = '';
    public string $receiptPaperSize = '58mm'; // 58mm or 80mm
    public bool $receiptShowLogo = true;
    public bool $receiptShowCustomer = true;
    public bool $receiptShowCashier = true;
    public bool $receiptShowTable = true;
    public bool $receiptShowTax = true;
    public $storeLogoFile = null;
    public ?string $currentStoreLogoUrl = null;

    // Payment Methods & QRIS Customization Properties
    public bool $paymentQrisActive = true;
    public string $paymentQrisMerchantName = '';
    public string $paymentQrisNmid = '';
    public $qrisImageFile = null;
    public ?string $currentQrisImageUrl = null;
    public bool $paymentCashActive = true;
    public bool $paymentTransferActive = true;
    public string $paymentBankName = 'BCA';
    public string $paymentBankAccountNumber = '';
    public string $paymentBankAccountHolder = '';
    public bool $paymentCardActive = true;

    public function setSettingsSubTab(string $tab): void
    {
        $this->settingsSubTab = $tab;
    }

    public function switchSettingsSubTab(string $tab): void
    {
        $this->setSettingsSubTab($tab);
    }

    public function initStoreSettings(): void
    {
        $tenant = Tenant::find($this->tenantId);
        if (! $tenant) {
            return;
        }

        $s = $tenant->settings ?? [];

        // Receipt Settings
        $this->receiptStoreName = $s['receipt_store_name'] ?? $tenant->name ?? 'LOKARA POS';
        $this->receiptTagline = $s['receipt_tagline'] ?? ($tenant->isSalon() ? 'Hair Studio & Beauty Care' : 'Specialty Coffee & Kitchen');
        $this->receiptHeaderNotes = $s['receipt_header_notes'] ?? ($tenant->city ?? 'Jakarta Selatan');
        $this->receiptFooterNotes = $s['receipt_footer_notes'] ?? 'Terima kasih atas kunjungan Anda!';
        $this->receiptWifiInfo = $s['receipt_wifi_info'] ?? 'WiFi: Lokara-Free | Pass: lokara123';
        $this->receiptSocialMedia = $s['receipt_social_media'] ?? '@lokarapos';
        $this->receiptPaperSize = $s['receipt_paper_size'] ?? '58mm';
        $this->receiptShowLogo = (bool) ($s['receipt_show_logo'] ?? true);
        $this->receiptShowCustomer = (bool) ($s['receipt_show_customer'] ?? true);
        $this->receiptShowCashier = (bool) ($s['receipt_show_cashier'] ?? true);
        $this->receiptShowTable = (bool) ($s['receipt_show_table'] ?? true);
        $this->receiptShowTax = (bool) ($s['receipt_show_tax'] ?? true);
        $this->currentStoreLogoUrl = $s['receipt_logo_url'] ?? null;

        // Payment & QRIS Settings
        $this->paymentQrisActive = (bool) ($s['payment_qris_active'] ?? true);
        $this->paymentQrisMerchantName = $s['payment_qris_merchant_name'] ?? strtoupper($tenant->name ?? 'LOKARA POS');
        $this->paymentQrisNmid = $s['payment_qris_nmid'] ?? 'ID1020038475891';
        $this->currentQrisImageUrl = $s['payment_qris_image_url'] ?? null;
        $this->paymentCashActive = (bool) ($s['payment_cash_active'] ?? true);
        $this->paymentTransferActive = (bool) ($s['payment_transfer_active'] ?? true);
        $this->paymentBankName = $s['payment_bank_name'] ?? 'BCA';
        $this->paymentBankAccountNumber = $s['payment_bank_account_number'] ?? '';
        $this->paymentBankAccountHolder = $s['payment_bank_account_holder'] ?? ($tenant->owner_name ?? '');
        $this->paymentCardActive = (bool) ($s['payment_card_active'] ?? true);
    }

    public function saveReceiptSettings(): void
    {
        $this->validate([
            'receiptStoreName' => 'required|string|max:100',
            'receiptTagline' => 'nullable|string|max:150',
            'receiptHeaderNotes' => 'nullable|string|max:255',
            'receiptFooterNotes' => 'nullable|string|max:255',
            'receiptWifiInfo' => 'nullable|string|max:100',
            'receiptSocialMedia' => 'nullable|string|max:100',
            'receiptPaperSize' => 'required|in:58mm,80mm',
            'storeLogoFile' => 'nullable|image|max:2048',
        ]);

        $tenant = Tenant::findOrFail($this->tenantId);
        $s = $tenant->settings ?? [];

        if ($this->storeLogoFile) {
            $path = $this->storeLogoFile->store('receipts/logos', 'public');
            $s['receipt_logo_url'] = Storage::url($path);
            $this->currentStoreLogoUrl = $s['receipt_logo_url'];
            $this->storeLogoFile = null;
        }

        $s['receipt_store_name'] = trim($this->receiptStoreName);
        $s['receipt_tagline'] = trim($this->receiptTagline);
        $s['receipt_header_notes'] = trim($this->receiptHeaderNotes);
        $s['receipt_footer_notes'] = trim($this->receiptFooterNotes);
        $s['receipt_wifi_info'] = trim($this->receiptWifiInfo);
        $s['receipt_social_media'] = trim($this->receiptSocialMedia);
        $s['receipt_paper_size'] = $this->receiptPaperSize;
        $s['receipt_show_logo'] = $this->receiptShowLogo;
        $s['receipt_show_customer'] = $this->receiptShowCustomer;
        $s['receipt_show_cashier'] = $this->receiptShowCashier;
        $s['receipt_show_table'] = $this->receiptShowTable;
        $s['receipt_show_tax'] = $this->receiptShowTax;

        $tenant->settings = $s;
        $tenant->save();

        $this->showToast('Kustomisasi format struk thermal kasir berhasil disimpan!');
    }

    public function removeStoreLogo(): void
    {
        $tenant = Tenant::findOrFail($this->tenantId);
        $s = $tenant->settings ?? [];
        unset($s['receipt_logo_url']);
        $tenant->settings = $s;
        $tenant->save();

        $this->currentStoreLogoUrl = null;
        $this->storeLogoFile = null;
        $this->showToast('Logo toko berhasil dihapus dari struk.', 'info');
    }

    public function savePaymentSettings(): void
    {
        $this->validate([
            'paymentQrisMerchantName' => 'nullable|string|max:100',
            'paymentQrisNmid' => 'nullable|string|max:50',
            'qrisImageFile' => 'nullable|image|max:3072',
            'paymentBankName' => 'nullable|string|max:50',
            'paymentBankAccountNumber' => 'nullable|string|max:50',
            'paymentBankAccountHolder' => 'nullable|string|max:100',
        ]);

        $tenant = Tenant::findOrFail($this->tenantId);
        $s = $tenant->settings ?? [];

        if ($this->qrisImageFile) {
            $path = $this->qrisImageFile->store('payments/qris', 'public');
            $s['payment_qris_image_url'] = Storage::url($path);
            $this->currentQrisImageUrl = $s['payment_qris_image_url'];
            $this->qrisImageFile = null;
        }

        $s['payment_qris_active'] = $this->paymentQrisActive;
        $s['payment_qris_merchant_name'] = trim($this->paymentQrisMerchantName);
        $s['payment_qris_nmid'] = trim($this->paymentQrisNmid);
        $s['payment_cash_active'] = $this->paymentCashActive;
        $s['payment_transfer_active'] = $this->paymentTransferActive;
        $s['payment_bank_name'] = trim($this->paymentBankName);
        $s['payment_bank_account_number'] = trim($this->paymentBankAccountNumber);
        $s['payment_bank_account_holder'] = trim($this->paymentBankAccountHolder);
        $s['payment_card_active'] = $this->paymentCardActive;

        $tenant->settings = $s;
        $tenant->save();

        $this->showToast('Pengaturan saluran pembayaran & QRIS berhasil disimpan!');
    }

    public function removeQrisImage(): void
    {
        $tenant = Tenant::findOrFail($this->tenantId);
        $s = $tenant->settings ?? [];
        unset($s['payment_qris_image_url']);
        $tenant->settings = $s;
        $tenant->save();

        $this->currentQrisImageUrl = null;
        $this->qrisImageFile = null;
        $this->showToast('Gambar QRIS berhasil dihapus.', 'info');
    }
}
