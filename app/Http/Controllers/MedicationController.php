<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicationRequest;
use App\Http\Requests\UpdateMedicationRequest;
use App\Models\Medication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MedicationController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');

        $medications = Medication::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('medications.index', [
            'medications' => $medications,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('medications.create');
    }

    public function store(StoreMedicationRequest $request): RedirectResponse
    {
        Medication::create($request->validated());

        return redirect()->route('medications.index')->with('status', 'Medication created successfully.');
    }

    public function edit(Medication $medication): View
    {
        return view('medications.edit', [
            'medication' => $medication,
        ]);
    }

    public function update(UpdateMedicationRequest $request, Medication $medication): RedirectResponse
    {
        $medication->update($request->validated());

        return redirect()->route('medications.index')->with('status', 'Medication updated successfully.');
    }

    public function destroy(Medication $medication): RedirectResponse
    {
        $medication->delete();

        return redirect()->route('medications.index')->with('status', 'Medication deleted successfully.');
    }
}
