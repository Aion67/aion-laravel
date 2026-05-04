<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Medication;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', '');

        $sales = Sale::query()
            ->with(['customer:id,first_name,last_name', 'user:id,name'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('sales.index', [
            'sales' => $sales,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('sales.create', [
            'customers' => Customer::query()->orderBy('last_name')->orderBy('first_name')->get(['id', 'first_name', 'last_name']),
            'medications' => Medication::query()->with('inventory')->orderBy('name')->get(['id', 'name', 'sku', 'unit_price']),
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $sale = DB::transaction(function () use ($validated, $request): Sale {
            $lineItems = [];
            $subtotal = 0.0;

            foreach ($validated['items'] as $item) {
                $medication = Medication::query()->findOrFail($item['medication_id']);
                $inventory = Inventory::query()->firstOrCreate(
                    ['medication_id' => $medication->id],
                    ['quantity_on_hand' => 0, 'reserved_quantity' => 0]
                );

                $quantity = (int) $item['quantity'];
                if ((int) $inventory->quantity_on_hand < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Insufficient stock for {$medication->name}.",
                    ]);
                }

                $lineTotal = (float) $medication->unit_price * $quantity;
                $subtotal += $lineTotal;

                $lineItems[] = [
                    'medication' => $medication,
                    'inventory' => $inventory,
                    'quantity' => $quantity,
                    'unit_price' => (float) $medication->unit_price,
                    'line_total' => $lineTotal,
                ];
            }

            $discount = (float) ($validated['discount'] ?? 0);
            $tax = (float) ($validated['tax'] ?? 0);
            $total = max($subtotal - $discount + $tax, 0);

            $sale = Sale::query()->create([
                'customer_id' => $validated['customer_id'] ?? null,
                'user_id' => $request->user()->id,
                'sale_number' => $this->nextSaleNumber(),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'status' => 'paid',
                'sold_at' => now(),
            ]);

            foreach ($lineItems as $lineItem) {
                $sale->items()->create([
                    'medication_id' => $lineItem['medication']->id,
                    'quantity' => $lineItem['quantity'],
                    'unit_price' => $lineItem['unit_price'],
                    'line_total' => $lineItem['line_total'],
                ]);

                $lineItem['inventory']->quantity_on_hand -= $lineItem['quantity'];
                $lineItem['inventory']->save();

                StockMovement::query()->create([
                    'medication_id' => $lineItem['medication']->id,
                    'user_id' => $request->user()->id,
                    'movement_type' => 'out',
                    'quantity' => $lineItem['quantity'],
                    'reference_type' => 'sale',
                    'reference_id' => $sale->id,
                    'notes' => 'Auto deduction from sale '.$sale->sale_number,
                    'created_at' => now(),
                ]);
            }

            return $sale;
        });

        return redirect()->route('sales.show', $sale)->with('status', 'Sale completed successfully.');
    }

    public function show(Sale $sale): View
    {
        $sale->load([
            'customer:id,first_name,last_name',
            'user:id,name',
            'items.medication:id,name,sku',
        ]);

        return view('sales.show', [
            'sale' => $sale,
        ]);
    }

    private function nextSaleNumber(): string
    {
        do {
            $number = 'SL-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Sale::query()->where('sale_number', $number)->exists());

        return $number;
    }
}
