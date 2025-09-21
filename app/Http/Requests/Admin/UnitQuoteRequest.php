<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Validation\Validator;


class UnitQuoteRequest extends FormRequest
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
     */
    public function rules(): array
    {
        if (Route::is('unit_quotes.store')) {
            return [
                'title'               => 'required|string|max:255',
                'other_title'         => 'nullable|string|max:255',
                'user_id'             => 'required|exists:users,id',
                'type_of_building_id' => 'required|exists:type_of_buildings,id',
                'type_of_price_id'    => 'required|exists:type_of_prices,id',
                'pay_image'           => 'required|image|mimes:jpeg,png,jpg,webp,pdf|max:6144',
                'gallery'             => 'required|array',
                'gallery.*'           => 'required|image|mimes:jpeg,png,jpg,webp,pdf|max:6144',
            ];
        }
        return [
            'title'               => 'nullable|string|max:255',
            'other_title'         => 'nullable|string|max:255',
            'user_id'             => 'nullable|exists:users,id',
            'type_of_building_id' => 'nullable|exists:type_of_buildings,id',
            'type_of_price_id'    => 'nullable|exists:type_of_prices,id',
            'pay_image'           => 'nullable|image|mimes:jpeg,png,jpg,webp,pdf|max:6144',
            'gallery'             => 'nullable|array',
            'gallery.*'           => 'nullable|image|mimes:jpeg,png,jpg,webp,pdf|max:6144',
        ];
    }

       protected function failedValidation(Validator $validator)
    {
        failedValidation($validator);
    }
}
