<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LanguageRequest extends FormRequest
{
    public function __construct(Request $request)
    {
        $request->merge(['app_scope' => 'all']);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('language') ?? null;

        $rules = [
            'name'         => 'required|string|max:255',
            'name_en'      => 'required|regex:/^[\pL\s]+$/u',
            'icon'         => 'required|mimes:jpeg,png,jpg,svg|max:6144',
            'code'         => ['required', 'string', Rule::unique('languages', 'code')],
            'country_code' => ['nullable', 'string', Rule::unique('languages', 'country_code')],
            'is_rtl'       => 'nullable|boolean',
        ];

        if (Route::is('language.update')) {
            $rules = [
                'name'         => 'nullable|string|max:255',
                'name_en'      => 'nullable|regex:/^[\pL\s]+$/u',
                'icon'         => 'nullable|mimes:jpeg,png,jpg,svg|max:6144',
                'code'         => [
                    'nullable',
                    'string',
                    Rule::unique('languages', 'code')->ignore($id),
                ],
                'country_code' => [
                    'nullable',
                    'string',
                    Rule::unique('languages', 'country_code')->ignore($id),
                ],
                'is_rtl'       => 'nullable|boolean',
            ];
        }

        return $rules;
    }
}
