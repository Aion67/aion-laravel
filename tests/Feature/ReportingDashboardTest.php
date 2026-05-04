<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Medication;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportingDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_live_operational_metrics(): void
    {
        $now = Carbon::parse('2026-05-04 10:00:00');
        Carbon::setTestNow($now);

        try {
            $staff = User::factory()->admin()->create();
            $customer = Customer::create([
                'first_name' => 'Report',
                'last_name' => 'Customer',
                'date_of_birth' => '1990-01-01',
                'sex' => 'female',
            ]);
            $lowStockMedication = Medication::create([
                'sku' => 'REP-LOW',
                'name' => 'Low Stock Med',
                'unit_type' => 'tablet',
                'unit_price' => 12,
                'reorder_level' => 10,
                'status' => 'active',
            ]);
            $healthyMedication = Medication::create([
                'sku' => 'REP-HIGH',
                'name' => 'Healthy Stock Med',
                'unit_type' => 'tablet',
                'unit_price' => 18,
                'reorder_level' => 5,
                'status' => 'active',
            ]);
            Inventory::create([
                'medication_id' => $lowStockMedication->id,
                'quantity_on_hand' => 4,
                'reserved_quantity' => 0,
            ]);
            Inventory::create([
                'medication_id' => $healthyMedication->id,
                'quantity_on_hand' => 25,
                'reserved_quantity' => 0,
            ]);

            $sale = Sale::create([
                'customer_id' => $customer->id,
                'user_id' => $staff->id,
                'sale_number' => 'SL-REPORT-001',
                'subtotal' => 120,
                'discount' => 0,
                'tax' => 0,
                'total' => 120,
                'payment_method' => 'cash',
                'status' => 'paid',
                'sold_at' => $now,
            ]);
            SaleItem::create([
                'sale_id' => $sale->id,
                'medication_id' => $lowStockMedication->id,
                'quantity' => 5,
                'unit_price' => 12,
                'line_total' => 60,
            ]);
            Prescription::create([
                'customer_id' => $customer->id,
                'user_id' => $staff->id,
                'prescription_number' => 'RX-REPORT-001',
                'status' => 'draft',
                'notes' => null,
                'prescribed_at' => $now,
            ]);
            StockMovement::create([
                'medication_id' => $lowStockMedication->id,
                'user_id' => $staff->id,
                'movement_type' => 'out',
                'quantity' => 5,
                'reference_type' => 'sale',
                'reference_id' => $sale->id,
                'notes' => 'Sale deduction',
                'created_at' => $now,
            ]);

            $response = $this->actingAs($staff)->get(route('dashboard'));

            $response->assertOk();
            $response->assertSee('Customers');
            $response->assertSee('Low Stock Alerts');
            $response->assertSee('1');
            $response->assertSee('120.00');
            $response->assertSee('Low Stock Med');
            $response->assertSee('SL-REPORT-001');
            $response->assertSee('RX-REPORT-001');
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_admin_sales_and_stock_reports_are_accessible_and_populated(): void
    {
        $now = Carbon::parse('2026-05-04 10:00:00');
        Carbon::setTestNow($now);

        try {
            $staff = User::factory()->admin()->create();
            $customer = Customer::create([
                'first_name' => 'Report',
                'last_name' => 'User',
                'date_of_birth' => '1992-01-01',
                'sex' => 'male',
            ]);
            $medication = Medication::create([
                'sku' => 'REP-SALES',
                'name' => 'Report Med',
                'unit_type' => 'syrup',
                'unit_price' => 20,
                'reorder_level' => 8,
                'status' => 'active',
            ]);
            Inventory::create([
                'medication_id' => $medication->id,
                'quantity_on_hand' => 3,
                'reserved_quantity' => 0,
            ]);
            $sale = Sale::create([
                'customer_id' => $customer->id,
                'user_id' => $staff->id,
                'sale_number' => 'SL-REPORT-002',
                'subtotal' => 40,
                'discount' => 0,
                'tax' => 0,
                'total' => 40,
                'payment_method' => 'cash',
                'status' => 'paid',
                'sold_at' => $now,
            ]);
            SaleItem::create([
                'sale_id' => $sale->id,
                'medication_id' => $medication->id,
                'quantity' => 2,
                'unit_price' => 20,
                'line_total' => 40,
            ]);
            StockMovement::create([
                'medication_id' => $medication->id,
                'user_id' => $staff->id,
                'movement_type' => 'out',
                'quantity' => 2,
                'reference_type' => 'sale',
                'reference_id' => $sale->id,
                'notes' => 'Report sale',
                'created_at' => $now,
            ]);

            $this->actingAs($staff)->get(route('reports.sales'))
                ->assertOk()
                ->assertSee('Sales Report')
                ->assertSee('Report Med')
                ->assertSee('40.00');

            $this->actingAs($staff)->get(route('reports.stock'))
                ->assertOk()
                ->assertSee('Stock Report')
                ->assertSee('Low Stock Alerts')
                ->assertSee('Report Med');
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_pharmacist_dashboard_hides_admin_only_sections(): void
    {
        $staff = User::factory()->pharmacist()->create();

        $response = $this->actingAs($staff)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Customers');
        $response->assertSee('Medications');
        $response->assertSee("Today's Prescriptions");
        $response->assertDontSee('Low Stock Alerts');
        $response->assertDontSee("Today's Sales");
        $response->assertDontSee('Recent Sales');
        $response->assertDontSee('Recent Stock Movements');
        $response->assertDontSee('Sales Report');
        $response->assertDontSee('Stock Report');
    }
}
