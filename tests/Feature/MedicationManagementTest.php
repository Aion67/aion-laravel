<?php

namespace Tests\Feature;

use App\Models\Medication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_create_update_and_delete_medication(): void
    {
        $staff = User::factory()->pharmacist()->create();

        $this->actingAs($staff)
            ->post(route('medications.store'), [
                'sku' => 'SKU-001',
                'name' => 'Amoxicillin',
                'unit_type' => 'capsule',
                'dosage_form' => 'oral',
                'strength' => '500mg',
                'unit_price' => 12.5,
                'reorder_level' => 10,
                'status' => 'active',
            ])
            ->assertRedirect(route('medications.index'));

        $medication = Medication::where('sku', 'SKU-001')->firstOrFail();

        $this->actingAs($staff)
            ->put(route('medications.update', $medication), [
                'sku' => 'SKU-001',
                'name' => 'Amoxicillin Plus',
                'unit_type' => 'capsule',
                'dosage_form' => 'oral',
                'strength' => '625mg',
                'unit_price' => 14.0,
                'reorder_level' => 8,
                'status' => 'inactive',
            ])
            ->assertRedirect(route('medications.index'));

        $this->assertDatabaseHas('medications', [
            'id' => $medication->id,
            'name' => 'Amoxicillin Plus',
            'status' => 'inactive',
        ]);

        $this->actingAs($staff)
            ->delete(route('medications.destroy', $medication))
            ->assertRedirect(route('medications.index'));

        $this->assertDatabaseMissing('medications', ['id' => $medication->id]);
    }

    public function test_staff_can_filter_medications(): void
    {
        $staff = User::factory()->pharmacist()->create();

        Medication::create([
            'sku' => 'ACT-01',
            'name' => 'Active Med',
            'unit_type' => 'tablet',
            'unit_price' => 1,
            'status' => 'active',
        ]);

        Medication::create([
            'sku' => 'INA-01',
            'name' => 'Inactive Med',
            'unit_type' => 'tablet',
            'unit_price' => 1,
            'status' => 'inactive',
        ]);

        $response = $this->actingAs($staff)->get(route('medications.index', [
            'search' => 'Med',
            'status' => 'inactive',
        ]));

        $response->assertOk();
        $response->assertSee('Inactive Med');
        $response->assertDontSee('Active Med');
    }

    public function test_sku_must_be_unique(): void
    {
        $staff = User::factory()->pharmacist()->create();

        Medication::create([
            'sku' => 'UNQ-001',
            'name' => 'Original',
            'unit_type' => 'tablet',
            'unit_price' => 2,
            'status' => 'active',
        ]);

        $response = $this->actingAs($staff)
            ->from(route('medications.create'))
            ->post(route('medications.store'), [
                'sku' => 'UNQ-001',
                'name' => 'Duplicate',
                'unit_type' => 'tablet',
                'unit_price' => 3,
                'status' => 'active',
            ]);

        $response->assertSessionHasErrors('sku');
    }
}
