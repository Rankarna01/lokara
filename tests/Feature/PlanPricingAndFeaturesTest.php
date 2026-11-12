<?php

namespace Tests\Feature;

use App\Livewire\SuperAdmin\PlatformDashboard;
use App\Models\Plan;
use App\Models\PlatformFeature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PlanPricingAndFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_superadmin_can_view_plans_and_features_tab()
    {
        Livewire::test(PlatformDashboard::class)
            ->call('setTab', 'plans')
            ->assertSet('activeTab', 'plans')
            ->assertSee('Kelola Master Fitur')
            ->assertSee('Tambah Paket Baru')
            ->assertSee('Trial 14 Hari')
            ->assertSee('Basic')
            ->assertSee('Pro')
            ->assertSee('Business')
            ->assertSee('Ubah Tarif');
    }

    public function test_superadmin_can_open_edit_plan_modal()
    {
        $plan = Plan::where('slug', 'pro')->first();

        Livewire::test(PlatformDashboard::class)
            ->call('openEditPlanModal', $plan->id)
            ->assertSet('showEditPlanModal', true)
            ->assertSet('editingPlanId', $plan->id)
            ->assertSet('editPlanName', 'Pro')
            ->assertSet('editPlanPriceMonthly', 299000.0)
            ->assertSee('Checklist Fitur Paket');
    }

    public function test_superadmin_can_update_plan_dynamic_tariff_and_limits()
    {
        $plan = Plan::where('slug', 'pro')->first();

        Livewire::test(PlatformDashboard::class)
            ->call('openEditPlanModal', $plan->id)
            ->set('editPlanPriceMonthly', 350000)
            ->set('editPlanPriceYearly', 3500000)
            ->set('editPlanMaxOutlets', 5)
            ->set('editPlanMaxUsers', 20)
            ->set('editPlanMaxProducts', 10000)
            ->set('editPlanBadge', 'Terlaris 2026')
            ->call('savePlan')
            ->assertHasNoErrors()
            ->assertSet('showEditPlanModal', false);

        $plan->refresh();
        $this->assertEquals(350000.0, (float) $plan->price_monthly);
        $this->assertEquals(3500000.0, (float) $plan->price_yearly);
        $this->assertEquals(5, $plan->limits['max_outlets']);
        $this->assertEquals(20, $plan->limits['max_users']);
        $this->assertEquals(10000, $plan->limits['max_products']);
        $this->assertEquals('Terlaris 2026', $plan->badge);
    }

    public function test_superadmin_can_toggle_features_checklist_in_plan()
    {
        $plan = Plan::where('slug', 'basic')->first();

        $component = Livewire::test(PlatformDashboard::class)
            ->call('openEditPlanModal', $plan->id);

        // Toggle out 'Kasir Cepat'
        $component->call('toggleFeatureInPlan', 'Kasir Cepat');
        $this->assertNotContains('Kasir Cepat', $component->get('editPlanFeatures'));

        // Toggle it back in
        $component->call('toggleFeatureInPlan', 'Kasir Cepat');
        $this->assertContains('Kasir Cepat', $component->get('editPlanFeatures'));

        // Select all
        $component->call('selectAllPlanFeatures');
        $this->assertCount(PlatformFeature::count(), $component->get('editPlanFeatures'));

        // Deselect all
        $component->call('deselectAllPlanFeatures');
        $this->assertEmpty($component->get('editPlanFeatures'));

        // Set specific features and save
        $component->set('editPlanFeatures', ['Kasir Cepat', 'Multi-Metode Bayar'])
            ->call('savePlan')
            ->assertHasNoErrors();

        $plan->refresh();
        $this->assertEquals(['Kasir Cepat', 'Multi-Metode Bayar'], $plan->features);
    }

    public function test_superadmin_can_add_feature_inline_and_auto_check_in_plan()
    {
        $plan = Plan::where('slug', 'pro')->first();

        Livewire::test(PlatformDashboard::class)
            ->call('openEditPlanModal', $plan->id)
            ->set('quickAddFeatureName', 'Integrasi EDC BCA & Mandiri')
            ->set('quickAddFeatureCategory', 'Kasir & Transaksi')
            ->call('addFeatureInline')
            ->assertHasNoErrors()
            ->assertSee('Integrasi EDC BCA & Mandiri')
            ->call('savePlan')
            ->assertHasNoErrors();

        // Feature added to master
        $this->assertDatabaseHas('platform_features', [
            'name' => 'Integrasi EDC BCA & Mandiri',
            'category' => 'Kasir & Transaksi',
        ]);

        // Feature included in plan
        $plan->refresh();
        $this->assertContains('Integrasi EDC BCA & Mandiri', $plan->features);
    }

    public function test_superadmin_can_manage_master_features()
    {
        // Add new master feature
        Livewire::test(PlatformDashboard::class)
            ->call('openManageFeaturesModal')
            ->assertSet('showManageFeaturesModal', true)
            ->set('newFeatureName', 'Program Loyalitas Poin Pelanggan')
            ->set('newFeatureCategory', 'Layanan & Bantuan')
            ->call('createMasterFeature')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('platform_features', [
            'name' => 'Program Loyalitas Poin Pelanggan',
            'category' => 'Layanan & Bantuan',
        ]);

        $feature = PlatformFeature::where('name', 'Program Loyalitas Poin Pelanggan')->first();
        $this->assertNotNull($feature);

        // Edit feature
        Livewire::test(PlatformDashboard::class)
            ->call('openEditFeatureModal', $feature->id)
            ->assertSet('showEditFeatureModal', true)
            ->set('editingFeatureName', 'Program Loyalitas & Poin Cashback')
            ->call('updateFeature')
            ->assertHasNoErrors()
            ->assertSet('showEditFeatureModal', false);

        $this->assertDatabaseHas('platform_features', [
            'id' => $feature->id,
            'name' => 'Program Loyalitas & Poin Cashback',
        ]);

        // Delete feature
        Livewire::test(PlatformDashboard::class)
            ->call('deleteFeature', $feature->id);

        $this->assertDatabaseMissing('platform_features', [
            'id' => $feature->id,
        ]);
    }

    public function test_editing_feature_name_synchronizes_across_plans()
    {
        $feature = PlatformFeature::where('name', 'Kasir Cepat')->first();
        $this->assertNotNull($feature);

        Livewire::test(PlatformDashboard::class)
            ->call('openEditFeatureModal', $feature->id)
            ->set('editingFeatureName', 'Kasir Kilat AI')
            ->call('updateFeature')
            ->assertHasNoErrors();

        // Check that plans with 'Kasir Cepat' were updated to 'Kasir Kilat AI'
        $planBasic = Plan::where('slug', 'basic')->first();
        $this->assertContains('Kasir Kilat AI', $planBasic->features);
        $this->assertNotContains('Kasir Cepat', $planBasic->features);
    }

    public function test_deleting_feature_removes_it_from_all_plans()
    {
        $feature = PlatformFeature::where('name', 'Struk Digital WhatsApp')->first();
        $this->assertNotNull($feature);

        Livewire::test(PlatformDashboard::class)
            ->call('deleteFeature', $feature->id);

        $planTrial = Plan::where('slug', 'trial')->first();
        $this->assertNotContains('Struk Digital WhatsApp', $planTrial->features);
    }
}
