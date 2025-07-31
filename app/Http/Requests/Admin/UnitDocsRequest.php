<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class UnitDocsRequest extends FormRequest
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
        $rules = Route::is('docs.store') ? [
            'unit_id' => 'required|exists:units,id',
            'data' => 'required|array',
            'data.*.folder_id' => [
                'required',
                Rule::exists('folders', 'id')->where(function ($query) {
                    $query->where('file_type', 'document');
                }),
            ],
            'data.*.file' => 'required|file|mimes:jpeg,png,jpg,svg,webp,pdf|max:6144',
            'data.*.title' => 'required|string',
        ] : [
            'unit_id' => 'required|exists:units,id',
            'data' => 'required|array',
            'data.*.id' => 'required|exists:unit_documents,id',
            'data.*.folder_id' => [
                'required',
                Rule::exists('folders', 'id')->where(function ($query) {
                    $query->where('file_type', 'document');
                }),
            ],
            'data.*.file' => 'nullable|file|mimes:jpeg,png,jpg,svg,webp,pdf|max:6144',
            'data.*.title' => 'nullable|string',
        ];
        return $rules;
    }
}
