<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Route;

class UnitPhaseNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if (Route::is('unit-phase-notes.store')) {
            return [
                'data' => 'required|array',
                'data.unit_id' => 'required|exists:units,id',
                'data.unit_phase_id' => 'nullable|exists:unit_phases,id',
                'data.user_id' => 'required|exists:users,id',
                'data.note'    => 'required|string',
                'data.status' => 'nullable|string|in:open,close',
            ];
        }

        return [
            'data' => 'required|array',
            'data.unit_id' => 'nullable|exists:units,id',
            'data.unit_phase_id' => 'nullable|exists:unit_phases,id',
            'data.user_id' => 'nullable|exists:users,id',
            'data.note'    => 'nullable|string',
            'data.status' => 'nullable|string|in:open,close',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        failedValidation($validator);
    }
}
