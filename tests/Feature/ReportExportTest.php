<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Medication;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_sales_report_as_csv(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = Customer::create([
            'first_name' => 'Export',
            'last_name' => 'Customer',
            'date_of_birth' => '1990-01-01',
            'sex' => 'female',
        ]);
        $medication = Medication::create([
            'sku' => 'EXP-001',
            'name' => 'Export Med',
            'unit_type' => 'tablet',
            'unit_price' => 10,
            'status' => 'active',
        ]);
        $sale = Sale::create([
            'customer_id' => $customer->id,
            'user_id' => $admin->id,
            'sale_number' => 'SL-EXP-001',
            'subtotal' => 20,
            'discount' => 0,
            'tax' => 0,
            'total' => 20,
            'payment_method' => 'cash',
            'status' => 'paid',
            'sold_at' => now(),
        ]);
        SaleItem::create([
            'sale_id' => $sale->id,
            'medication_id' => $medication->id,
            'quantity' => 2,
            'unit_price' => 10,
            'line_total' => 20,
        ]);

        $response = $this->actingAs($admin)->get(route('reports.sales.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $response->assertSee('Sale Number');
        $response->assertSee('SL-EXP-001');
        $response->assertSee('Cash');
    }

    public function test_admin_can_export_stock_report_as_csv(): void
    {
        $admin = User::factory()->admin()->create();
        $medication = Medication::create([
            'sku' => 'EXP-002',
            'name' => 'Stock Export Med',
            'unit_type' => 'capsule',
            'unit_price' => 8,
            'reorder_level' => 5,
            'status' => 'active',
        ]);
        Inventory::create([
            'medication_id' => $medication->id,
            'quantity_on_hand' => 4,
            'reserved_quantity' => 1,
        ]);
        StockMovement::create([
            'medication_id' => $medication->id,
            'user_id' => $admin->id,
            'movement_type' => 'in',
            'quantity' => 5,
            'reference_type' => 'manual',
            'reference_id' => null,
            'notes' => 'Opening stock',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('reports.stock.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $response->assertSee('Medication');
        $response->assertSee('Stock Export Med');
        $response->assertSee('Low stock');
    }
}