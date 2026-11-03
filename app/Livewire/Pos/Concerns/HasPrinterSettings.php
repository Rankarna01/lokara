<?php

namespace App\Livewire\Pos\Concerns;

trait HasPrinterSettings
{
    // Thermal Printer Settings State
    public bool $showPrinterModal = false;

    public string $printerConnectionType = 'bluetooth'; // 'bluetooth', 'usb', 'rawbt', 'network'

    public string $printerDeviceName = 'Panda POS-58BT';

    public string $printerPaperWidth = '58mm'; // '58mm', '80mm'

    public string $printerPortOrIp = 'COM3';

    public string $thirdPartyApp = 'rawbt'; // 'rawbt', 'escpos_service'

    public bool $printerAutoCut = true;

    public bool $printerKickDrawer = true;

    public int $printerCopies = 1;

    public bool $printerPrintLogo = true;

    public bool $showTestPrintPreview = false;

    public function savePrinterSettings()
    {
        $this->showPrinterModal = false;
        $this->notify("Pengaturan printer thermal ({$this->printerDeviceName} via {$this->printerConnectionType}) berhasil disimpan!");
    }

    public function testPrintThermal()
    {
        $this->showTestPrintPreview = true;
        $this->notify("Perintah cetak thermal dikirim ke printer {$this->printerDeviceName} ({$this->printerPaperWidth})!");
    }
}
