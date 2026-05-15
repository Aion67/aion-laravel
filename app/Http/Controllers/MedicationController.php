<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicationRequest;
use App\Http\Requests\UpdateMedicationRequest;
use App\Models\Medication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MedicationController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');
        $viewMode = in_array((string) $request->query('view', 'table'), ['table', 'cards'], true)
            ? (string) $request->query('view', 'table')
            : 'table';

        $medications = Medication::query()
            ->with('inventory')
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
            'viewMode' => $viewMode,
        ]);
    }

    public function create(): View
    {
        return view('medications.create');
    }

    public function store(StoreMedicationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['image_path'] = $this->storeImage($request);

        Medication::create($validated);

        return redirect()->route('medications.index')->with('status', 'Medication created successfully.');
    }

    public function edit(Medication $medication): View
    {
        return view('medications.edit', [
            'medication' => $medication,
        ]);
    }

    public function show(Medication $medication): View
    {
        $medication->load('inventory');

        return view('medications.show', [
            'medication' => $medication,
        ]);
    }

    public function update(UpdateMedicationRequest $request, Medication $medication): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image_path'] = $this->storeImage($request, $medication);
        }

        $medication->update($validated);

        return redirect()->route('medications.index')->with('status', 'Medication updated successfully.');
    }

    public function destroy(Medication $medication): RedirectResponse
    {
        $this->deleteImage($medication);
        $medication->delete();

        return redirect()->route('medications.index')->with('status', 'Medication deleted successfully.');
    }

    private function storeImage(Request $request, ?Medication $medication = null): ?string
    {
        if (! $request->hasFile('image')) {
            return $medication?->image_path;
        }

        if ($medication?->image_path) {
            $this->deleteImage($medication);
        }

        return $request->file('image')->store('medication-images', 'public');
    }

    private function deleteImage(Medication $medication): void
    {
        if ($medication->image_path !== null && $medication->image_path !== '') {
            Storage::disk('public')->delete($medication->image_path);
        }
    }
}
