<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Medication;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_complete_sale_and_inventory_is_deducted(): void
    {
        $staff = User::factory()->admin()->create();
        $customer = Customer::create([
            'first_name' => 'Buyer',
            'last_name' => 'One',
            'date_of_birth' => '1988-01-01',
            'sex' => 'male',
        ]);
        $medication = Medication::create([
            'sku' => 'SALE-001',
            'name' => 'Antibiotic',
            'unit_type' => 'capsule',
            'unit_price' => 15,
            'status' => 'active',
        ]);
        Inventory::create([
            'medication_id' => $medication->id,
            'quantity_on_hand' => 10,
            'reserved_quantity' => 0,
        ]);

        $response = $this->actingAs($staff)->post(route('sales.store'), [
            'customer_id' => $customer->id,
            'discount' => 5,
            'tax' => 2,
            'payment_method' => 'cash',
            'items' => [
                [
                    'medication_id' => $medication->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertRedirect();

        $sale = Sale::firstOrFail();
        $this->assertSame('paid', $sale->status);
        $this->assertDatabaseHas('sale_items', [
            'sale_id' => $sale->id,
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas('inventory', [
            'medication_id' => $medication->id,
            'quantity_on_hand' => 8,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'reference_type' => 'sale',
            'reference_id' => $sale->id,
            'quantity' => 2,
            'movement_type' => 'out',
        ]);
    }

    public function test_admin_sale_cannot_exceed_available_stock(): void
    {
        $staff = User::factory()->admin()->create();
        $medication = Medication::create([
            'sku' => 'SALE-002',
            'name' => 'Limited Sale Med',
            'unit_type' => 'tablet',
            'unit_price' => 20,
            'status' => 'active',
        ]);
        Inventory::create([
            'medication_id' => $medication->id,
            'quantity_on_hand' => 1,
            'reserved_quantity' => 0,
        ]);

        $response = $this->actingAs($staff)
            ->from(route('sales.create'))
            ->post(route('sales.store'), [
                'customer_id' => null,
                'discount' => 0,
                'tax' => 0,
                'payment_method' => 'cash',
                'items' => [
                    [
                        'medication_id' => $medication->id,
                        'quantity' => 3,
                    ],
                ],
            ]);

        $response->assertRedirect(route('sales.create'));
        $response->assertSessionHasErrors('items');
    }

    public function test_admin_can_view_sales_index_and_receipt(): void
    {
        $staff = User::factory()->admin()->create();
        $sale = Sale::create([
            'customer_id' => null,
            'user_id' => $staff->id,
            'sale_number' => 'SL-TEST-001',
            'subtotal' => 10,
            'discount' => 0,
            'tax' => 0,
            'total' => 10,
            'payment_method' => 'cash',
            'status' => 'paid',
            'sold_at' => now(),
        ]);

        $this->actingAs($staff)->get(route('sales.index'))->assertOk();
        $this->actingAs($staff)->get(route('sales.show', $sale))->assertOk();
    }
}
