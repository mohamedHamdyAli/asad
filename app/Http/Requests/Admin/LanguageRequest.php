<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


class LanguageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function __construct(Request $request)
    {
        $request->merge(['app_scope' => 'all']);
    }
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
        $rules = [
            'name'         => 'required',
            'name_en'      => 'required|regex:/^[\pL\s]+$/u',
            'icon'         => 'required|mimes:jpeg,png,jpg,svg|max:6144',
            'code'         => 'required|unique:languages,code',
            'country_code' => 'nullable',
            'is_rtl'       => 'nullable',
        ];

        if (Route::is('language.update')) {
            $rules['name'] = 'nullable';
            $rules['name_en'] = 'nullable|regex:/^[\pL\s]+$/u';
            $rules['icon'] = 'nullable|mimes:jpeg,png,jpg,svg|max:6144';
            $rules['code'] = 'nullable|unique:languages,code';
        }
        return $rules;
    }
}
