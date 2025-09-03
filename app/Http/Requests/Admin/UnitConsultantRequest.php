<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class UnitConsultantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if (Route::is('unit.consultants.store')) {
            return [
                'unit_id' => 'required|exists:units,id',
                'data' => 'required|array',
                'data.*.consultant_id' => 'required|exists:consultants,id',
            ];
        }

        return [
            'unit_id' => 'nullable|exists:units,id',
            'data' => 'required|array',
            'data.*.id' => 'required|exists:unit_constulants,id',
            'data.*.consultant_id' => 'nullable|exists:consultants,id',
        ];
    }
}
