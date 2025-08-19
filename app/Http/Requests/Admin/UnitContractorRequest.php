<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class UnitContractorRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = Route::is('unit.contractors.store') ? [
            'unit_id' => 'required|exists:units,id',
            'data' => 'required|array',
            'data.*.contractor_id' => 'required|exists:contractors,id',
        ] : [
            'unit_id' => 'required|exists:units,id',
            'data' => 'required|array',
            'data.*.id' => 'required|exists:unit_contractors,id',
            'data.*.contractor_id' => 'required|exists:contractors,id',
        ];

        return $rules;
    }
}
