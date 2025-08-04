<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class UnitGalleryRequest extends FormRequest
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
        $rules = Route::is('gallery.store') ? [
            'unit_id' => 'required|exists:units,id',
            'data' => 'required|array',
            'data.*.folder_id' => [
                'required',
                Rule::exists('folders', 'id')->where(function ($query) {
                    $query->where('file_type', 'gallery');
                }),
            ],
            'data.*.image' => 'required|file|mimes:jpeg,png,jpg,svg,webp|max:6144',
            'data.*.title' => 'required|array',
            'data.*.title.*' => 'required|string',
            'data.*.date' => 'required|date',
        ] : [
            'unit_id' => 'required|exists:units,id',
            'data' => 'required|array',
            'data.*.id' => 'nullable|exists:unit_documents,id',
            'data.*.folder_id' => [
                'required',
                Rule::exists('folders', 'id')->where(function ($query) {
                    $query->where('file_type', 'gallery');
                }),
            ],
            'data.*.image' => 'nullable|file|mimes:jpeg,png,jpg,svg,webp|max:6144',
            'data.*.title' => 'nullable|array',
            'data.*.title.*' => 'required_with:data.*.title|string',
            'data.*.date' => 'nullable|date',

        ];
        return $rules;
    }
}
