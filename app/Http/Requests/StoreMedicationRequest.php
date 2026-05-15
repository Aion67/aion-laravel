<?php

namespace App\Http\Requests;

use App\Models\Medication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMedicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:64', Rule::unique(Medication::class)],
            'name' => ['required', 'string', 'max:255'],
            'unit_type' => ['required', 'string', 'max:32'],
            'dosage_form' => ['nullable', 'string', 'max:64'],
            'strength' => ['nullable', 'string', 'max:64'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'reorder_level' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
