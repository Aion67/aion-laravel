<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdjustInventoryRequest;
use App\Models\Inventory;
use App\Models\Medication;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $onlyLowStock = $request->boolean('low_stock');

        $medications = Medication::query()
            ->with('inventory')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get()
            ->map(function (Medication $medication) {
                $quantity = (int) ($medication->inventory?->quantity_on_hand ?? 0);
                $reorderLevel = $medication->reorder_level;
                $isLowStock = $reorderLevel !== null && $quantity <= $reorderLevel;

                return [
                    'medication' => $medication,
                    'quantity_on_hand' => $quantity,
                    'reserved_quantity' => (int) ($medication->inventory?->reserved_quantity ?? 0),
                    'is_low_stock' => $isLowStock,
                ];
            })
            ->when($onlyLowStock, fn ($collection) => $collection->where('is_low_stock', true))
            ->values();

        return view('inventory.index', [
            'inventoryRows' => $medications,
            'search' => $search,
            'onlyLowStock' => $onlyLowStock,
        ]);
    }

    public function createAdjustment(Request $request): View
    {
        $selectedMedicationId = (string) $request->query('medication_id', '');

        return view('inventory.adjust', [
            'medications' => Medication::query()->orderBy('name')->get(['id', 'name', 'sku']),
            'selectedMedicationId' => $selectedMedicationId,
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function storeAdjustment(AdjustInventoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request): void {
            $medication = Medication::query()->findOrFail($validated['medication_id']);

            $inventory = Inventory::query()->firstOrCreate(
                ['medication_id' => $medication->id],
                ['quantity_on_hand' => 0, 'reserved_quantity' => 0]
            );

            $quantity = (int) $validated['quantity'];
            $delta = $this->resolveDelta(
                movementType: $validated['movement_type'],
                quantity: $quantity,
                direction: $validated['adjustment_direction'] ?? null,
                currentQuantity: (int) $inventory->quantity_on_hand,
            );

            $inventory->quantity_on_hand += $delta;
            $inventory->save();

            StockMovement::query()->create([
                'medication_id' => $medication->id,
                'user_id' => $request->user()->id,
                'movement_type' => $validated['movement_type'],
                'quantity' => abs($delta),
                'reference_type' => 'manual',
                'reference_id' => null,
                'notes' => $validated['notes'] ?? null,
                'created_at' => now(),
            ]);
        });

        return redirect()->route('inventory.index')->with('status', 'Inventory adjusted successfully.');
    }

    /**
     * @throws ValidationException
     */
    private function resolveDelta(string $movementType, int $quantity, ?string $direction, int $currentQuantity): int
    {
        if ($movementType === 'in') {
            return $quantity;
        }

        if ($movementType === 'out') {
            if ($currentQuantity < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Quantity exceeds stock on hand.',
                ]);
            }

            return -$quantity;
        }

        $resolvedDirection = $direction ?? 'increase';

        if ($resolvedDirection === 'decrease' && $currentQuantity < $quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'Quantity exceeds stock on hand.',
            ]);
        }

        return $resolvedDirection === 'decrease' ? -$quantity : $quantity;
    }
}
