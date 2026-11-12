<?php

namespace Tests\Feature;

use App\Livewire\SuperAdmin\PlatformDashboard;
use App\Models\BusinessType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BusinessTypePresetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_superadmin_can_view_template_usaha_tab()
    {
        Livewire::test(PlatformDashboard::class)
            ->call('setTab', 'business-types')
            ->assertSet('activeTab', 'business-types')
            ->assertSee('Template Jenis Usaha')
            ->assertSee('Konfigurasi modul dan preset standar saat tenant baru mendaftar')
            ->assertSee('F&B (Kafe, Restoran, Bakery)')
            ->assertSee('Retail & Toko Kelontong')
            ->assertSee('Salon & Klinik Kecantikan')
            ->assertSee('Barbershop & Pangkas Rambut')
            ->assertSee('Laundry & Jasa')
            ->assertSee('Grosir & Distributor')
            ->assertSee('Ubah Preset');
    }

    public function test_superadmin_can_open_edit_preset_modal()
    {
        $fnb = BusinessType::where('slug', 'fnb')->first();

        Livewire::test(PlatformDashboard::class)
            ->call('openEditPresetModal', $fnb->id)
            ->assertSet('showEditPresetModal', true)
            ->assertSet('editingBusinessTypeId', $fnb->id)
            ->assertSet('editBtName', $fnb->name)
            ->assertSee('Ubah Preset Template Usaha')
            ->assertSee('Preset Modul Bawaan');
    }

    public function test_superadmin_can_save_preset_changes()
    {
        $fnb = BusinessType::where('slug', 'fnb')->first();

        Livewire::test(PlatformDashboard::class)
            ->call('openEditPresetModal', $fnb->id)
            ->set('editBtName', 'F&B dan Restoran Modern')
            ->set('editBtDescription', 'Template khusus bisnis kuliner dengan modul dapur terintegrasi.')
            ->set('editBtDefaultUnits', 'gram, ml, porsi, cup, bowl')
            ->set('editBtPresetModules', [
                'Resep & Bill of Materials (BOM) Gram/ml',
                'Manajemen Meja & Cetak Dapur/Bar',
                'Reservasi & Jadwal Booking',
            ])
            ->set('editBtHasRecipes', true)
            ->set('editBtHasTables', true)
            ->call('saveBusinessTypePreset')
            ->assertHasNoErrors()
            ->assertSet('showEditPresetModal', false);

        $fnb->refresh();
        $this->assertEquals('F&B dan Restoran Modern', $fnb->name);
        $this->assertEquals('Template khusus bisnis kuliner dengan modul dapur terintegrasi.', $fnb->description);
        $this->assertEquals(['gram', 'ml', 'porsi', 'cup', 'bowl'], $fnb->default_settings['default_units']);
        $this->assertContains('Reservasi & Jadwal Booking', $fnb->preset_modules);
        $this->assertTrue($fnb->default_settings['has_recipes']);
        $this->assertTrue($fnb->default_settings['has_tables']);
    }

    public function test_superadmin_can_toggle_preset_modules_and_add_custom_module()
    {
        $retail = BusinessType::where('slug', 'retail')->first();

        $component = Livewire::test(PlatformDashboard::class)
            ->call('openEditPresetModal', $retail->id);

        // Toggle module off
        $component->call('togglePresetModule', 'Multi-Gudang & Grosir');
        $this->assertNotContains('Multi-Gudang & Grosir', $component->get('editBtPresetModules'));

        // Toggle module back on
        $component->call('togglePresetModule', 'Multi-Gudang & Grosir');
        $this->assertContains('Multi-Gudang & Grosir', $component->get('editBtPresetModules'));

        // Add custom module
        $component->set('newPresetModuleName', 'Integrasi Label Barcode RFID')
            ->call('addPresetModule')
            ->assertHasNoErrors();

        $this->assertContains('Integrasi Label Barcode RFID', $component->get('editBtPresetModules'));

        // Remove module
        $component->call('removePresetModule', 'Varian Ukuran & Warna');
        $this->assertNotContains('Varian Ukuran & Warna', $component->get('editBtPresetModules'));

        // Save
        $component->call('saveBusinessTypePreset')
            ->assertHasNoErrors();

        $retail->refresh();
        $this->assertContains('Integrasi Label Barcode RFID', $retail->preset_modules);
        $this->assertNotContains('Varian Ukuran & Warna', $retail->preset_modules);
    }

    public function test_superadmin_can_toggle_business_type_active_status()
    {
        $bt = BusinessType::first();
        $initialStatus = $bt->is_active;

        Livewire::test(PlatformDashboard::class)
            ->call('toggleBusinessTypeActive', $bt->id);

        $bt->refresh();
        $this->assertEquals(!$initialStatus, $bt->is_active);

        // Toggle back
        Livewire::test(PlatformDashboard::class)
            ->call('toggleBusinessTypeActive', $bt->id);

        $bt->refresh();
        $this->assertEquals($initialStatus, $bt->is_active);
    }

    public function test_superadmin_can_create_new_business_type_template()
    {
        Livewire::test(PlatformDashboard::class)
            ->call('openCreateBusinessTypeModal')
            ->assertSet('showCreateBusinessTypeModal', true)
            ->set('newBtName', 'Pet Shop & Klinik Hewan')
            ->set('newBtIcon', 'fa-paw')
            ->set('newBtDescription', 'Untuk pet clinic dan pet shop dengan rekam medis hewan dan penitipan.')
            ->set('newBtDefaultUnits', 'pcs, pack, dosis')
            ->set('newBtPresetModules', ['Kasir Cepat & Struk WhatsApp', 'Rekam Medis Hewan'])
            ->call('createBusinessType')
            ->assertHasNoErrors()
            ->assertSet('showCreateBusinessTypeModal', false);

        $this->assertDatabaseHas('business_types', [
            'name' => 'Pet Shop & Klinik Hewan',
            'slug' => 'pet-shop-klinik-hewan',
            'icon' => 'fa-paw',
            'is_active' => true,
        ]);

        $created = BusinessType::where('slug', 'pet-shop-klinik-hewan')->first();
        $this->assertNotNull($created);
        $this->assertEquals(['pcs', 'pack', 'dosis'], $created->default_settings['default_units']);
        $this->assertContains('Rekam Medis Hewan', $created->preset_modules);
    }
}

