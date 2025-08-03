<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class UnitDrawingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = Route::is('drawing.store') ? [
            'unit_id' => 'required|exists:units,id',
            'data' => 'required|array',
            'data.*.folder_id' => [
                'required',
                Rule::exists('folders', 'id')->where(function ($query) {
                    $query->where('file_type', 'drawing');
                }),
            ],
            'data.*.image' => 'required|file|mimes:jpeg,png,jpg,svg,webp|max:6144',
            'data.*.title' => 'required|string',
            'data.*.date' => 'required|date',
        ] : [
            'unit_id' => 'required|exists:units,id',
            'data' => 'required|array',
            'data.*.id' => 'nullable|exists:unit_drawings,id',
            'data.*.folder_id' => [
                'required',
                Rule::exists('folders', 'id')->where(function ($query) {
                    $query->where('file_type', 'drawing');
                }),
            ],
            'data.*.image' => 'nullable|file|mimes:jpeg,png,jpg,svg,webp|max:6144',
            'data.*.title' => 'nullable|string',
            'data.*.date' => 'nullable|date',
        ];

        return $rules;
    }
}
