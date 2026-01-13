<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class UnitPhaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return Route::is('phases.store') ? [
            'unit_id' => 'required|exists:units,id',
            'data' => 'required|array',
            'data.*.status' => 'required|in:site_handover,skeleton,finishing,handover',
            'data.*.description' => 'required|array',
            'data.*.description.*' => 'required|string',

        ] : [
            'unit_id' => 'required|exists:units,id',
            'data' => 'required|array',
            'data.*.id' => 'required|exists:unit_phases,id',
            'data.*.status' => 'nullable|in:site_handover,skeleton,finishing,handover',
            'data.*.description' => 'nullable|array',
            'data.*.description.*' => 'required_with:data.*.description|string',
        ];
    }

}
