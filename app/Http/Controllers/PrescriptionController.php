<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePrescriptionRequest;
use App\Http\Requests\UpdatePrescriptionStatusRequest;
use App\Models\Customer;
use App\Models\Medication;
use App\Models\Prescription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PrescriptionController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', '');

        $prescriptions = Prescription::query()
            ->with(['customer:id,first_name,last_name', 'user:id,name'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('prescriptions.index', [
            'prescriptions' => $prescriptions,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('prescriptions.create', [
            'customers' => Customer::query()->orderBy('last_name')->orderBy('first_name')->get(['id', 'first_name', 'last_name']),
            'medications' => Medication::query()->orderBy('name')->get(['id', 'name', 'sku', 'unit_price']),
        ]);
    }

    public function store(StorePrescriptionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $prescription = DB::transaction(function () use ($validated, $request): Prescription {
            $prescription = Prescription::query()->create([
                'customer_id' => $validated['customer_id'],
                'user_id' => $request->user()->id,
                'prescription_number' => $this->nextPrescriptionNumber(),
                'status' => 'draft',
                'notes' => $validated['notes'] ?? null,
                'prescribed_at' => now(),
            ]);

            foreach ($validated['items'] as $item) {
                $medication = Medication::query()->findOrFail($item['medication_id']);

                $prescription->items()->create([
                    'medication_id' => $medication->id,
                    'quantity' => $item['quantity'],
                    'dosage_instructions' => $item['dosage_instructions'] ?? null,
                    'unit_price' => $medication->unit_price,
                ]);
            }

            return $prescription;
        });

        return redirect()->route('prescriptions.show', $prescription)->with('status', 'Prescription created successfully.');
    }

    public function show(Prescription $prescription): View
    {
        $prescription->load([
            'customer:id,first_name,last_name',
            'user:id,name',
            'items.medication:id,name,sku',
        ]);

        return view('prescriptions.show', [
            'prescription' => $prescription,
        ]);
    }

    public function updateStatus(UpdatePrescriptionStatusRequest $request, Prescription $prescription): RedirectResponse
    {
        $prescription->update([
            'status' => $request->validated('status'),
        ]);

        return redirect()->route('prescriptions.show', $prescription)->with('status', 'Prescription status updated.');
    }

    private function nextPrescriptionNumber(): string
    {
        do {
            $number = 'RX-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Prescription::query()->where('prescription_number', $number)->exists());

        return $number;
    }
}
