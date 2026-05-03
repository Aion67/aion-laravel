<?php

namespace Tests\Feature;

use App\Models\Medication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_adjust_inventory_and_log_stock_movement(): void
    {
        $staff = User::factory()->pharmacist()->create();
        $medication = Medication::create([
            'sku' => 'INV-001',
            'name' => 'Inventory Med',
            'unit_type' => 'tablet',
            'unit_price' => 5,
            'status' => 'active',
            'reorder_level' => 4,
        ]);

        $this->actingAs($staff)
            ->post(route('inventory.adjust.store'), [
                'medication_id' => $medication->id,
                'movement_type' => 'in',
                'quantity' => 10,
                'notes' => 'Initial stock',
            ])
            ->assertRedirect(route('inventory.index'));

        $this->assertDatabaseHas('inventory', [
            'medication_id' => $medication->id,
            'quantity_on_hand' => 10,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'medication_id' => $medication->id,
            'user_id' => $staff->id,
            'movement_type' => 'in',
            'quantity' => 10,
            'reference_type' => 'manual',
        ]);

        $this->actingAs($staff)
            ->post(route('inventory.adjust.store'), [
                'medication_id' => $medication->id,
                'movement_type' => 'out',
                'quantity' => 3,
                'notes' => 'Sold manually',
            ])
            ->assertRedirect(route('inventory.index'));

        $this->assertDatabaseHas('inventory', [
            'medication_id' => $medication->id,
            'quantity_on_hand' => 7,
        ]);
    }

    public function test_stock_out_cannot_exceed_stock_on_hand(): void
    {
        $staff = User::factory()->pharmacist()->create();
        $medication = Medication::create([
            'sku' => 'INV-002',
            'name' => 'Limited Med',
            'unit_type' => 'tablet',
            'unit_price' => 2,
            'status' => 'active',
        ]);

        $this->actingAs($staff)
            ->post(route('inventory.adjust.store'), [
                'medication_id' => $medication->id,
                'movement_type' => 'in',
                'quantity' => 2,
            ])
            ->assertRedirect(route('inventory.index'));

        $response = $this->actingAs($staff)
            ->from(route('inventory.adjust.create'))
            ->post(route('inventory.adjust.store'), [
                'medication_id' => $medication->id,
                'movement_type' => 'out',
                'quantity' => 5,
            ]);

        $response->assertRedirect(route('inventory.adjust.create'));
        $response->assertSessionHasErrors('quantity');
    }

    public function test_staff_can_filter_stock_movements(): void
    {
        $staff = User::factory()->pharmacist()->create();
        $medication = Medication::create([
            'sku' => 'INV-003',
            'name' => 'Filter Med',
            'unit_type' => 'tablet',
            'unit_price' => 2,
            'status' => 'active',
        ]);

        $this->actingAs($staff)->post(route('inventory.adjust.store'), [
            'medication_id' => $medication->id,
            'movement_type' => 'in',
            'quantity' => 5,
        ]);

        $this->actingAs($staff)->post(route('inventory.adjust.store'), [
            'medication_id' => $medication->id,
            'movement_type' => 'out',
            'quantity' => 1,
        ]);

        $response = $this->actingAs($staff)->get(route('stock-movements.index', [
            'movement_type' => 'out',
        ]));

        $response->assertOk();
        $response->assertSee('Out');
        $response->assertDontSee('Initial stock');
    }
}
