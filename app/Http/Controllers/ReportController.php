<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function sales(): View
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $sales = Sale::query()
            ->with(['customer:id,first_name,last_name', 'user:id,name'])
            ->orderByDesc('sold_at')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $topMedications = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('medications', 'sale_items.medication_id', '=', 'medications.id')
            ->whereBetween('sales.sold_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('medications.name, medications.sku, SUM(sale_items.quantity) as quantity_sold, SUM(sale_items.line_total) as revenue')
            ->groupBy('medications.id', 'medications.name', 'medications.sku')
            ->orderByDesc('quantity_sold')
            ->limit(5)
            ->get();

        return view('reports.sales', [
            'sales' => $sales,
            'monthlySalesTotal' => Sale::query()->whereBetween('sold_at', [$startOfMonth, $endOfMonth])->sum('total'),
            'monthlySalesCount' => Sale::query()->whereBetween('sold_at', [$startOfMonth, $endOfMonth])->count(),
            'dailySalesTotal' => Sale::query()->whereDate('sold_at', now()->toDateString())->sum('total'),
            'topMedications' => $topMedications,
        ]);
    }

    public function stock(): View
    {
        $inventoryRows = Inventory::query()
            ->select('inventory.*')
            ->with(['medication:id,name,sku,reorder_level'])
            ->join('medications', 'inventory.medication_id', '=', 'medications.id')
            ->orderBy('medications.name')
            ->get()
            ->map(function (Inventory $inventory): array {
                $reorderLevel = $inventory->medication?->reorder_level;

                return [
                    'inventory' => $inventory,
                    'is_low_stock' => $reorderLevel !== null && $inventory->quantity_on_hand <= $reorderLevel,
                ];
            });

        $lowStockItems = $inventoryRows->filter(fn (array $row) => $row['is_low_stock'])->values();

        return view('reports.stock', [
            'inventoryRows' => $inventoryRows,
            'lowStockItems' => $lowStockItems,
            'movementSummary' => StockMovement::query()
                ->selectRaw('movement_type, SUM(quantity) as quantity_total')
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->groupBy('movement_type')
                ->orderBy('movement_type')
                ->get(),
            'recentMovements' => StockMovement::query()->with(['medication:id,name,sku', 'user:id,name'])->orderByDesc('created_at')->orderByDesc('id')->limit(10)->get(),
        ]);
    }
}
