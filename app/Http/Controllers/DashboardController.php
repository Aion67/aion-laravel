<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Medication;
use App\Models\Prescription;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->toDateString();

        $cards = [
            ['label' => 'Customers', 'value' => Customer::query()->count()],
            ['label' => 'Medications', 'value' => Medication::query()->count()],
            ['label' => 'Low Stock Alerts', 'value' => $this->lowStockCount()],
            ['label' => "Today's Sales", 'value' => number_format((float) Sale::query()->whereDate('sold_at', $today)->sum('total'), 2)],
            ['label' => "Today's Prescriptions", 'value' => Prescription::query()->whereDate('prescribed_at', $today)->count()],
        ];

        return view('dashboard', [
            'cards' => $cards,
            'lowStockItems' => $this->lowStockItems(),
            'recentSales' => Sale::query()->with(['customer:id,first_name,last_name', 'user:id,name'])->orderByDesc('sold_at')->orderByDesc('id')->limit(5)->get(),
            'recentPrescriptions' => Prescription::query()->with(['customer:id,first_name,last_name', 'user:id,name'])->orderByDesc('prescribed_at')->orderByDesc('id')->limit(5)->get(),
            'recentMovements' => StockMovement::query()->with(['medication:id,name,sku', 'user:id,name'])->orderByDesc('created_at')->orderByDesc('id')->limit(5)->get(),
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
