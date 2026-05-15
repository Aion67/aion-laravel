<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Prescription;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ReportController extends Controller
{
    private const UGX_EXCHANGE_RATE = 3800;

    public function index(): View
    {
        [$analyticsStart, $analyticsEnd] = $this->analyticsWindow();

        $salesTrend = $this->salesTrendSeries();
        $patientGrowth = $this->customerGrowthSeries();
        $medicationPerformance = $this->medicationPerformanceSeries($analyticsStart, $analyticsEnd, 6);
        $lowStockItems = $this->lowStockInventory();

        return view('reports.index', [
            'monthlySalesTotal' => Sale::query()->whereBetween('sold_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total'),
            'patientCount' => Customer::query()->count(),
            'lowStockCount' => $lowStockItems->count(),
            'monthlyPrescriptionCount' => Prescription::query()->whereBetween('prescribed_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'salesTrendChart' => $this->lineChart(
                chartId: 'overview-sales-trend-chart',
                labels: $salesTrend->pluck('label')->all(),
                datasets: [
                    [
                        'label' => 'Revenue',
                        'data' => $salesTrend->pluck('total')->map(fn ($amount) => $this->normalizeAmount($amount))->all(),
                        'borderColor' => 'rgb(37, 99, 235)',
                        'backgroundColor' => 'rgba(37, 99, 235, 0.18)',
                        'fill' => true,
                        'tension' => 0.35,
                    ],
                ],
            ),
            'stockHealthChart' => $this->doughnutChart(
                chartId: 'overview-stock-health-chart',
                labels: ['Healthy stock', 'Low stock'],
                datasets: [
                    [
                        'label' => 'Stock health',
                        'data' => [max(0, Inventory::query()->count() - $lowStockItems->count()), $lowStockItems->count()],
                        'backgroundColor' => [
                            'rgba(37, 99, 235, 0.82)',
                            'rgba(245, 158, 11, 0.82)',
                        ],
                    ],
                ],
            ),
            'patientGrowthChart' => $this->lineChart(
                chartId: 'overview-patient-growth-chart',
                labels: $patientGrowth->pluck('label')->all(),
                datasets: [
                    [
                        'label' => 'New patients',
                        'data' => $patientGrowth->pluck('count')->all(),
                        'borderColor' => 'rgb(16, 185, 129)',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.16)',
                        'fill' => true,
                        'tension' => 0.35,
                    ],
                ],
            ),
            'medicationPerformanceChart' => $this->barChart(
                chartId: 'overview-medication-performance-chart',
                labels: $medicationPerformance->pluck('name')->all(),
                datasets: [
                    [
                        'label' => 'Revenue',
                        'data' => $medicationPerformance->pluck('revenue')->map(fn ($amount) => $this->normalizeAmount($amount))->all(),
                        'backgroundColor' => 'rgba(99, 102, 241, 0.82)',
                    ],
                ],
            ),
            'recentSales' => Sale::query()->with(['customer:id,first_name,last_name', 'user:id,name'])->orderByDesc('sold_at')->orderByDesc('id')->limit(5)->get(),
            'recentPatients' => Customer::query()->orderByDesc('created_at')->orderByDesc('id')->limit(5)->get(),
        ]);
    }

    public function patients(): View
    {
        [$analyticsStart, $analyticsEnd] = $this->analyticsWindow();

        $patientGrowth = $this->customerGrowthSeries();
        $sexDistribution = Customer::query()
            ->selectRaw('sex, COUNT(*) as total')
            ->groupBy('sex')
            ->orderBy('sex')
            ->get();

        $prescriptionActivity = collect(range(0, 5))->map(function (int $offset) use ($analyticsStart): array {
            $month = $analyticsStart->copy()->addMonthsNoOverflow($offset);

            return [
                'label' => $month->format('M Y'),
                'count' => Prescription::query()->whereBetween('prescribed_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])->count(),
            ];
        });

        $repeatPatients = Customer::query()
            ->withCount(['prescriptions', 'sales'])
            ->get()
            ->filter(fn (Customer $customer): bool => $customer->prescriptions_count > 1 || $customer->sales_count > 1)
            ->sortByDesc('sales_count')
            ->sortByDesc('prescriptions_count')
            ->take(8)
            ->values();

        return view('reports.patients', [
            'patientCount' => Customer::query()->count(),
            'newPatientsThisMonth' => Customer::query()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'prescriptionsThisMonth' => Prescription::query()->whereBetween('prescribed_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'repeatPatientsCount' => $repeatPatients->count(),
            'patientGrowthChart' => $this->lineChart(
                chartId: 'patients-growth-chart',
                labels: $patientGrowth->pluck('label')->all(),
                datasets: [
                    [
                        'label' => 'New patients',
                        'data' => $patientGrowth->pluck('count')->all(),
                        'borderColor' => 'rgb(37, 99, 235)',
                        'backgroundColor' => 'rgba(37, 99, 235, 0.18)',
                        'fill' => true,
                        'tension' => 0.35,
                    ],
                ],
            ),
            'patientSexChart' => $this->doughnutChart(
                chartId: 'patient-sex-chart',
                labels: $sexDistribution->pluck('sex')->map(fn (string $sex) => Str::headline($sex))->all(),
                datasets: [
                    [
                        'label' => 'Patients',
                        'data' => $sexDistribution->pluck('total')->all(),
                        'backgroundColor' => [
                            'rgba(99, 102, 241, 0.82)',
                            'rgba(16, 185, 129, 0.82)',
                            'rgba(245, 158, 11, 0.82)',
                        ],
                    ],
                ],
            ),
            'prescriptionActivityChart' => $this->barChart(
                chartId: 'prescription-activity-chart',
                labels: $prescriptionActivity->pluck('label')->all(),
                datasets: [
                    [
                        'label' => 'Prescriptions',
                        'data' => $prescriptionActivity->pluck('count')->all(),
                        'backgroundColor' => 'rgba(245, 158, 11, 0.82)',
                    ],
                ],
            ),
            'repeatPatients' => $repeatPatients,
        ]);
    }

    public function exportOverview(): Response
    {
        [$analyticsStart, $analyticsEnd] = $this->analyticsWindow();

        $salesTrend = $this->salesTrendSeries();
        $patientGrowth = $this->customerGrowthSeries();
        $medicationPerformance = $this->medicationPerformanceSeries($analyticsStart, $analyticsEnd, 6);
        $lowStockItems = $this->lowStockInventory();
        $recentSales = Sale::query()
            ->with(['customer:id,first_name,last_name', 'user:id,name'])
            ->orderByDesc('sold_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();
        $recentPatients = Customer::query()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $rows = [
            ['Section', 'Label', 'Value'],
            ['Summary', 'Monthly Revenue (UGX)', $this->normalizeAmount(Sale::query()->whereBetween('sold_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total'))],
            ['Summary', 'Active Patients', Customer::query()->count()],
            ['Summary', 'Low Stock Medications', $lowStockItems->count()],
            ['Summary', 'Prescriptions This Month', Prescription::query()->whereBetween('prescribed_at', [now()->startOfMonth(), now()->endOfMonth()])->count()],
        ];

        foreach ($salesTrend as $row) {
            $rows[] = ['Sales Trend', $row['label'], $this->normalizeAmount($row['total'])];
        }

        foreach ($patientGrowth as $row) {
            $rows[] = ['Patient Growth', $row['label'], $row['count']];
        }

        foreach ($medicationPerformance as $row) {
            $rows[] = ['Medication Performance', $row->name.' ('.$row->sku.')', $this->normalizeAmount($row->revenue)];
        }

        foreach ($recentSales as $sale) {
            $rows[] = [
                'Recent Sales',
                $sale->sale_number,
                $this->normalizeAmount($sale->total),
            ];
        }

        foreach ($recentPatients as $patient) {
            $rows[] = [
                'Recent Patients',
                trim($patient->first_name.' '.$patient->last_name),
                $patient->phone ?: '-',
            ];
        }

        return $this->csvDownload('reports-overview.csv', $rows[0], array_slice($rows, 1));
    }

    public function exportPatients(): Response
    {
        $patients = Customer::query()
            ->withCount(['prescriptions', 'sales'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return $this->csvDownload('patients-report.csv', [
            'Patient',
            'Phone',
            'Sex',
            'Prescriptions',
            'Sales',
            'Joined',
        ], $patients->map(function (Customer $customer): array {
            return [
                trim($customer->first_name.' '.$customer->last_name),
                $customer->phone ?: '-',
                Str::headline((string) $customer->sex),
                (string) $customer->prescriptions_count,
                (string) $customer->sales_count,
                $customer->created_at?->format('Y-m-d H:i') ?? '-',
            ];
        })->all());
    }

    public function sales(): View
    {
        [$analyticsStart, $analyticsEnd] = $this->analyticsWindow();

        $sales = Sale::query()
            ->with(['customer:id,first_name,last_name', 'user:id,name'])
            ->whereBetween('sold_at', [$analyticsStart, $analyticsEnd])
            ->orderByDesc('sold_at')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $monthlySales = $this->salesTrendSeries();

        $paymentMethodSummary = Sale::query()
            ->whereBetween('sold_at', [$analyticsStart, $analyticsEnd])
            ->selectRaw('payment_method, SUM(total) as total')
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row): array => [
                'method' => Str::headline($row->payment_method),
                'total' => (float) $row->total,
            ]);

        $topMedications = $this->medicationPerformanceSeries($analyticsStart, $analyticsEnd, 6);

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
                        'data' => $monthlySales->pluck('total')->map(fn ($amount) => $this->normalizeAmount($amount))->all(),
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
                        'data' => $paymentMethodSummary->pluck('total')->map(fn ($amount) => $this->normalizeAmount($amount))->all(),
                        'backgroundColor' => [
                            'rgba(37, 99, 235, 0.82)',
                            'rgba(16, 185, 129, 0.82)',
                            'rgba(245, 158, 11, 0.82)',
                            'rgba(239, 68, 68, 0.82)',
                        ],
                    ],
                ],
            ),
            'medicationPerformanceChart' => $this->barChart(
                chartId: 'medication-performance-chart',
                labels: $topMedications->pluck('name')->all(),
                datasets: [
                    [
                        'label' => 'Revenue',
                        'data' => $topMedications->pluck('revenue')->map(fn ($amount) => $this->normalizeAmount($amount))->all(),
                        'backgroundColor' => 'rgba(99, 102, 241, 0.82)',
                    ],
                ],
            ),
        ]);
    }

    public function exportSales(): Response
    {
        [$analyticsStart, $analyticsEnd] = $this->analyticsWindow();

        $sales = Sale::query()
            ->with(['customer:id,first_name,last_name', 'user:id,name'])
            ->whereBetween('sold_at', [$analyticsStart, $analyticsEnd])
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
            'Subtotal (UGX)',
            'Discount (UGX)',
            'Tax (UGX)',
            'Total (UGX)',
        ], $sales->map(function (Sale $sale): array {
            return [
                $sale->sale_number,
                $sale->sold_at?->format('Y-m-d H:i'),
                trim(($sale->customer?->first_name ?? 'Walk-in').' '.($sale->customer?->last_name ?? '')),
                $sale->user?->name ?? 'Unknown',
                Str::headline($sale->payment_method),
                Str::headline($sale->status),
                (string) $this->normalizeAmount($sale->subtotal),
                (string) $this->normalizeAmount($sale->discount),
                (string) $this->normalizeAmount($sale->tax),
                (string) $this->normalizeAmount($sale->total),
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
        [$analyticsStart, $analyticsEnd] = $this->analyticsWindow();

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

        $inventoryLevels = $inventoryRows
            ->sortBy('inventory.quantity_on_hand')
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
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
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
                labels: ['Healthy stock', 'Low stock'],
                datasets: [
                    [
                        'label' => 'Inventory status',
                        'data' => [max(0, $inventoryRows->count() - $lowStockItems->count()), $lowStockItems->count()],
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

    private function analyticsWindow(): array
    {
        return [now()->subMonthsNoOverflow(5)->startOfMonth(), now()->endOfMonth()];
    }

    private function normalizeAmount(float|int|string $amount): int
    {
        $exchangeRate = (float) config('currency.usd_to_ugx_rate', self::UGX_EXCHANGE_RATE);

        return (int) round(((float) $amount) * $exchangeRate, 0);
    }

    private function reportMonths(): Collection
    {
        [$analyticsStart] = $this->analyticsWindow();

        return collect(range(0, 5))->map(fn (int $offset) => $analyticsStart->copy()->addMonthsNoOverflow($offset));
    }

    private function salesTrendSeries(): Collection
    {
        return $this->reportMonths()->map(function ($month) {
            $monthSales = Sale::query()
                ->whereBetween('sold_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->get();

            return [
                'label' => $month->format('M Y'),
                'total' => (float) $monthSales->sum('total'),
                'count' => $monthSales->count(),
            ];
        });
    }

    private function customerGrowthSeries(): Collection
    {
        return $this->reportMonths()->map(function ($month) {
            return [
                'label' => $month->format('M Y'),
                'count' => Customer::query()->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])->count(),
            ];
        });
    }

    private function medicationPerformanceSeries($analyticsStart, $analyticsEnd, int $limit): Collection
    {
        return DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('medications', 'sale_items.medication_id', '=', 'medications.id')
            ->whereBetween('sales.sold_at', [$analyticsStart, $analyticsEnd])
            ->selectRaw('medications.name, medications.sku, SUM(sale_items.quantity) as quantity_sold, SUM(sale_items.line_total) as revenue')
            ->groupBy('medications.id', 'medications.name', 'medications.sku')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();
    }

    private function lowStockInventory(): Collection
    {
        return Inventory::query()
            ->select('inventory.*')
            ->with(['medication:id,name,sku,reorder_level'])
            ->join('medications', 'inventory.medication_id', '=', 'medications.id')
            ->whereNotNull('medications.reorder_level')
            ->whereColumn('inventory.quantity_on_hand', '<=', 'medications.reorder_level')
            ->orderByRaw('(medications.reorder_level - inventory.quantity_on_hand) DESC')
            ->get();
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