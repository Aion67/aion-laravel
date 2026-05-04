<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Medication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrescriptionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_create_and_update_prescription_status(): void
    {
        $staff = User::factory()->pharmacist()->create();
        $customer = Customer::create([
            'first_name' => 'Patient',
            'last_name' => 'One',
            'date_of_birth' => '1990-01-01',
            'sex' => 'female',
        ]);
        $medication = Medication::create([
            'sku' => 'RX-001',
            'name' => 'Pain Relief',
            'unit_type' => 'tablet',
            'unit_price' => 10,
            'status' => 'active',
        ]);

        $response = $this->actingAs($staff)->post(route('prescriptions.store'), [
            'customer_id' => $customer->id,
            'notes' => 'Take after meals',
            'items' => [
                [
                    'medication_id' => $medication->id,
                    'quantity' => 2,
                    'dosage_instructions' => 'Twice daily',
                ],
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseCount('prescriptions', 1);
        $this->assertDatabaseCount('prescription_items', 1);

        $prescription = $customer->prescriptions()->firstOrFail();

        $this->actingAs($staff)
            ->patch(route('prescriptions.status.update', $prescription), [
                'status' => 'confirmed',
            ])
            ->assertRedirect(route('prescriptions.show', $prescription));

        $this->assertDatabaseHas('prescriptions', [
            'id' => $prescription->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_staff_can_filter_prescriptions_by_status(): void
    {
        $staff = User::factory()->pharmacist()->create();
        $customer = Customer::create([
            'first_name' => 'Patient',
            'last_name' => 'Two',
            'date_of_birth' => '1991-01-01',
            'sex' => 'male',
        ]);

        $prescription = $customer->prescriptions()->create([
            'user_id' => $staff->id,
            'prescription_number' => 'RX-TEST-001',
            'status' => 'draft',
            'prescribed_at' => now(),
        ]);

        $response = $this->actingAs($staff)->get(route('prescriptions.index', [
            'status' => 'draft',
        ]));

        $response->assertOk();
        $response->assertSee($prescription->prescription_number);
    }
}
