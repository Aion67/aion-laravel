<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Medication;
use App\Models\Prescription;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->toDateString();
        $canManageSales = Gate::allows('manage-sales');
        $canViewStockMovements = Gate::allows('view-stock-movements');
        $canViewReports = Gate::allows('view-reports');

        $cards = [
            ['label' => 'Customers', 'value' => Customer::query()->count()],
            ['label' => 'Medications', 'value' => Medication::query()->count()],
            ['label' => "Today's Prescriptions", 'value' => Prescription::query()->whereDate('prescribed_at', $today)->count()],
        ];

        if ($canManageSales) {
            $cards[] = ['label' => 'Low Stock Alerts', 'value' => $this->lowStockCount()];
            $cards[] = ['label' => "Today's Sales", 'value' => number_format((float) Sale::query()->whereDate('sold_at', $today)->sum('total'), 2)];
        }

        return view('dashboard', [
            'cards' => $cards,
            'canManageSales' => $canManageSales,
            'canViewStockMovements' => $canViewStockMovements,
            'canViewReports' => $canViewReports,
            'lowStockItems' => $canManageSales ? $this->lowStockItems() : collect(),
            'recentSales' => $canManageSales
                ? Sale::query()->with(['customer:id,first_name,last_name', 'user:id,name'])->orderByDesc('sold_at')->orderByDesc('id')->limit(5)->get()
                : collect(),
            'recentPrescriptions' => Prescription::query()->with(['customer:id,first_name,last_name', 'user:id,name'])->orderByDesc('prescribed_at')->orderByDesc('id')->limit(5)->get(),
            'recentMovements' => $canViewStockMovements
                ? StockMovement::query()->with(['medication:id,name,sku', 'user:id,name'])->orderByDesc('created_at')->orderByDesc('id')->limit(5)->get()
                : collect(),
        ]);
    }

    private function lowStockCount(): int
    {
        return Inventory::query()
            ->join('medications', 'inventory.medication_id', '=', 'medications.id')
            ->whereNotNull('medications.reorder_level')
            ->whereColumn('inventory.quantity_on_hand', '<=', 'medications.reorder_level')
            ->count();
    }

    private function lowStockItems()
    {
        return Inventory::query()
            ->select('inventory.*')
            ->with(['medication:id,name,sku,reorder_level'])
            ->join('medications', 'inventory.medication_id', '=', 'medications.id')
            ->whereNotNull('medications.reorder_level')
            ->whereColumn('inventory.quantity_on_hand', '<=', 'medications.reorder_level')
            ->orderByRaw('(medications.reorder_level - inventory.quantity_on_hand) DESC')
            ->limit(5)
            ->get();
    }
}
