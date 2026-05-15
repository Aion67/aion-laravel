<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function sales(): View
    {
        $analyticsStart = now()->subMonthsNoOverflow(5)->startOfMonth();
        $analyticsEnd = now()->endOfMonth();

        $sales = Sale::query()
            ->with(['customer:id,first_name,last_name', 'user:id,name'])
            ->whereBetween('sold_at', [$analyticsStart, $analyticsEnd])
            ->orderByDesc('sold_at')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $salesForAnalytics = Sale::query()
            ->with(['customer:id,first_name,last_name', 'user:id,name'])
            ->whereBetween('sold_at', [$analyticsStart, $analyticsEnd])
            ->orderBy('sold_at')
            ->orderBy('id')
            ->get();

        $monthlySales = collect(range(0, 5))->map(function (int $offset) use ($analyticsStart, $salesForAnalytics): array {
            $month = $analyticsStart->copy()->addMonthsNoOverflow($offset);
            $monthKey = $month->format('Y-m');

            $monthSales = $salesForAnalytics->filter(function (Sale $sale) use ($monthKey): bool {
                return $sale->sold_at?->format('Y-m') === $monthKey;
            });

            return [
                'label' => $month->format('M Y'),
                'total' => (float) $monthSales->sum('total'),
                'count' => $monthSales->count(),
            ];
        });

        $paymentMethodSummary = $salesForAnalytics
            ->groupBy('payment_method')
            ->map(fn ($group): array => [
                'method' => Str::headline($group->first()->payment_method),
                'total' => (float) $group->sum('total'),
                'count' => $group->count(),
            ])
            ->sortByDesc('total')
            ->values();

        $salesByUser = $salesForAnalytics
            ->groupBy('user_id')
            ->map(fn ($group): array => [
                'name' => $group->first()->user?->name ?? 'Unknown',
                'total' => (float) $group->sum('total'),
                'count' => $group->count(),
            ])
            ->sortByDesc('total')
            ->values();

        $topMedications = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('medications', 'sale_items.medication_id', '=', 'medications.id')
            ->whereBetween('sales.sold_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->selectRaw('medications.name, medications.sku, SUM(sale_items.quantity) as quantity_sold, SUM(sale_items.line_total) as revenue')
            ->groupBy('medications.id', 'medications.name', 'medications.sku')
            ->orderByDesc('quantity_sold')
            ->limit(5)
            ->get();

        return view('reports.sales', [
            'sales' => $sales,
            'monthlySalesTotal' => Sale::query()->whereBetween('sold_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total'),
            'monthlySalesCount' => Sale::query()->whereBetween('sold_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'dailySalesTotal' => Sale::query()->whereDate('sold_at', now()->toDateString())->sum('total'),
            'topMedications' => $topMedications,
            'salesTrendChart' => $this->lineChart(
                chartId: 'sales-trend-chart',
                labels: $monthlySales->pluck('label')->all(),
                datasets: [
                    [
                        'label' => 'Sales total',
                        'data' => $monthlySales->pluck('total')->all(),
                        'borderColor' => 'rgb(37, 99, 235)',
                        'backgroundColor' => 'rgba(37, 99, 235, 0.18)',
                        'fill' => true,
                        'tension' => 0.35,
                    ],
                ],
            ),
            'paymentMethodChart' => $this->doughnutChart(
                chartId: 'payment-method-chart',
                labels: $paymentMethodSummary->pluck('method')->all(),
                datasets: [
                    [
                        'label' => 'Sales by payment method',
                        'data' => $paymentMethodSummary->pluck('total')->all(),
                        'backgroundColor' => [
                            'rgba(37, 99, 235, 0.82)',
                            'rgba(16, 185, 129, 0.82)',
                            'rgba(245, 158, 11, 0.82)',
                            'rgba(239, 68, 68, 0.82)',
                        ],
                    ],
                ],
            ),
            'salesByUserChart' => $this->barChart(
                chartId: 'sales-by-user-chart',
                labels: $salesByUser->pluck('name')->all(),
                datasets: [
                    [
                        'label' => 'Sales total',
                        'data' => $salesByUser->pluck('total')->all(),
                        'backgroundColor' => 'rgba(99, 102, 241, 0.82)',
                    ],
                ],
            ),
        ]);
    }

    public function exportSales(): Response
    {
        $sales = Sale::query()
            ->with(['customer:id,first_name,last_name', 'user:id,name'])
            ->whereBetween('sold_at', [now()->subMonthsNoOverflow(5)->startOfMonth(), now()->endOfMonth()])
            ->orderByDesc('sold_at')
            ->orderByDesc('id')
            ->get();

        return $this->csvDownload('sales-report.csv', [
            'Sale Number',
            'Date',
            'Customer',
            'Cashier',
            'Payment Method',
            'Status',
            'Subtotal',
            'Discount',
            'Tax',
            'Total',
        ], $sales->map(function (Sale $sale): array {
            return [
                $sale->sale_number,
                $sale->sold_at?->format('Y-m-d H:i'),
                trim(($sale->customer?->first_name ?? 'Walk-in').' '.($sale->customer?->last_name ?? '')),
                $sale->user?->name ?? 'Unknown',
                Str::headline($sale->payment_method),
                Str::headline($sale->status),
                number_format((float) $sale->subtotal, 2, '.', ''),
                number_format((float) $sale->discount, 2, '.', ''),
                number_format((float) $sale->tax, 2, '.', ''),
                number_format((float) $sale->total, 2, '.', ''),
            ];
        })->all());
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

        $currentMonthStart = now()->startOfMonth();
        $analyticsStart = now()->subMonthsNoOverflow(5)->startOfMonth();
        $analyticsEnd = now()->endOfMonth();

        $stockMovements = StockMovement::query()
            ->select('stock_movements.*')
            ->with(['medication:id,name,sku', 'user:id,name'])
            ->whereBetween('created_at', [$analyticsStart, $analyticsEnd])
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $movementTrend = collect(range(0, 5))->map(function (int $offset) use ($analyticsStart, $stockMovements): array {
            $month = $analyticsStart->copy()->addMonthsNoOverflow($offset);
            $monthKey = $month->format('Y-m');

            $monthlyMovements = $stockMovements->filter(function (StockMovement $movement) use ($monthKey): bool {
                return $movement->created_at?->format('Y-m') === $monthKey;
            });

            return [
                'label' => $month->format('M Y'),
                'in' => (int) $monthlyMovements->where('movement_type', 'in')->sum('quantity'),
                'out' => (int) $monthlyMovements->where('movement_type', 'out')->sum('quantity'),
            ];
        });

        $stockHealth = [
            'Healthy stock' => $inventoryRows->count() - $lowStockItems->count(),
            'Low stock' => $lowStockItems->count(),
        ];

        $inventoryLevels = $inventoryRows
            ->sortBy('quantity_on_hand')
            ->take(8)
            ->values()
            ->map(function (array $row): array {
                return [
                    'label' => $row['inventory']->medication?->sku ?? 'Unknown',
                    'name' => $row['inventory']->medication?->name ?? 'Unknown',
                    'quantity' => $row['inventory']->quantity_on_hand,
                ];
            });

        return view('reports.stock', [
            'inventoryRows' => $inventoryRows,
            'lowStockItems' => $lowStockItems,
            'movementSummary' => StockMovement::query()
                ->selectRaw('movement_type, SUM(quantity) as quantity_total')
                ->whereBetween('created_at', [$currentMonthStart, now()->endOfMonth()])
                ->groupBy('movement_type')
                ->orderBy('movement_type')
                ->get(),
            'recentMovements' => $stockMovements->sortByDesc('created_at')->take(10)->values(),
            'movementTrendChart' => $this->barChart(
                chartId: 'movement-trend-chart',
                labels: $movementTrend->pluck('label')->all(),
                datasets: [
                    [
                        'label' => 'Stock in',
                        'data' => $movementTrend->pluck('in')->all(),
                        'backgroundColor' => 'rgba(16, 185, 129, 0.82)',
                    ],
                    [
                        'label' => 'Stock out',
                        'data' => $movementTrend->pluck('out')->all(),
                        'backgroundColor' => 'rgba(239, 68, 68, 0.82)',
                    ],
                ],
            ),
            'stockHealthChart' => $this->doughnutChart(
                chartId: 'stock-health-chart',
                labels: array_keys($stockHealth),
                datasets: [
                    [
                        'label' => 'Inventory status',
                        'data' => array_values($stockHealth),
                        'backgroundColor' => [
                            'rgba(37, 99, 235, 0.82)',
                            'rgba(245, 158, 11, 0.82)',
                        ],
                    ],
                ],
            ),
            'inventoryLevelsChart' => $this->barChart(
                chartId: 'inventory-levels-chart',
                labels: $inventoryLevels->pluck('name')->all(),
                datasets: [
                    [
                        'label' => 'On hand',
                        'data' => $inventoryLevels->pluck('quantity')->all(),
                        'backgroundColor' => 'rgba(99, 102, 241, 0.82)',
                    ],
                ],
            ),
        ]);
    }

    public function exportStock(): Response
    {
        $inventoryRows = Inventory::query()
            ->select('inventory.*')
            ->with(['medication:id,name,sku,reorder_level'])
            ->join('medications', 'inventory.medication_id', '=', 'medications.id')
            ->orderBy('medications.name')
            ->get();

        return $this->csvDownload('stock-report.csv', [
            'Medication',
            'SKU',
            'On Hand',
            'Reserved',
            'Reorder Level',
            'Status',
        ], $inventoryRows->map(function (Inventory $inventory): array {
            $quantityOnHand = (int) $inventory->quantity_on_hand;
            $reorderLevel = $inventory->medication?->reorder_level;

            return [
                $inventory->medication?->name ?? 'Unknown',
                $inventory->medication?->sku ?? 'Unknown',
                (string) $quantityOnHand,
                (string) $inventory->reserved_quantity,
                $reorderLevel !== null ? (string) $reorderLevel : '-',
                $reorderLevel !== null && $quantityOnHand <= $reorderLevel ? 'Low stock' : 'Healthy',
            ];
        })->all());
    }

    private function barChart(string $chartId, array $labels, array $datasets): array
    {
        return $this->chartConfig($chartId, 'bar', $labels, $datasets, [
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ]);
    }

    private function lineChart(string $chartId, array $labels, array $datasets): array
    {
        return $this->chartConfig($chartId, 'line', $labels, $datasets, [
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ]);
    }

    private function doughnutChart(string $chartId, array $labels, array $datasets): array
    {
        return $this->chartConfig($chartId, 'doughnut', $labels, $datasets, [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'cutout' => '62%',
        ]);
    }

    private function chartConfig(string $chartId, string $type, array $labels, array $datasets, array $options = []): array
    {
        return [
            'type' => $type,
            'data' => [
                'labels' => $labels,
                'datasets' => $datasets,
            ],
            'options' => array_merge([
                'responsive' => true,
                'maintainAspectRatio' => false,
            ], $options),
            'id' => $chartId,
        ];
    }

    private function csvDownload(string $filename, array $headers, array $rows): Response
    {
        $handle = fopen('php://temp', 'w+');

        fputcsv($handle, $headers);

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);

        $csv = stream_get_contents($handle) ?: '';

        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
