<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Route;

class UnitRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $rules = (Route::is('units.store')) ? [
            'name' => 'required|array',
            'name.*' => 'required|string',
            'description' => 'required|array',
            'description.*' => 'required|string',
            'location'    => 'required|string',
            'address'     => 'nullable|string',
            'lat'         => 'required|numeric|between:-90,90',
            'long'        => 'required|numeric|between:-180,180',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'cover_image' => 'required|mimes:jpeg,png,jpg,svg,webp|max:6144',
            'gallery'       => 'nullable|array',
            'gallery.*'     => 'nullable|file|mimes:jpeg,png,jpg,svg,webp|max:6144',
            'user_id'     => 'required|exists:users,id',
            'vendor_id'   => 'required|exists:users,id',
        ] : [
            'name' => 'nullable|array',
            'name.*' => 'nullable|string',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
            'location'    => 'nullable|string',
            'address'     => 'nullable|string',
            'lat'         => 'nullable|numeric|between:-90,90',
            'long'        => 'nullable|numeric|between:-180,180',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'cover_image' => 'nullable|mimes:jpeg,png,jpg,svg,webp|max:6144',
            'gallery'     => 'nullable|array',
            'gallery.*'   => 'nullable|file|mimes:jpeg,png,jpg,svg,webp|max:6144',
            'user_id'     => 'nullable|exists:users,id',
            'vendor_id'   => 'nullable|exists:users,id',
            'status'      => 'nullable|in:under_construction,completed',
            'extension_dates' => 'nullable|array',
            'extension_dates.*' => 'required|date|after_or_equal:start_date',
        ];

        return $rules;
    }

    /**
     * Handle failed validation.
     */
    protected function failedValidation(Validator $validator)
    {
        failedValidation($validator);
    }
}
