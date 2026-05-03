<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function index(Request $request): View
    {
        $movementType = (string) $request->query('movement_type', '');

        $movements = StockMovement::query()
            ->with(['medication:id,name,sku', 'user:id,name'])
            ->when($movementType !== '', fn ($query) => $query->where('movement_type', $movementType))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('stock-movements.index', [
            'movements' => $movements,
            'movementType' => $movementType,
        ]);
    }
}
