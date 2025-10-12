<?php

namespace App\Http\Requests\Api;

class LanguageRequest extends BaseApiRequest
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
        return [
            'lang_id'   => 'required|exists:languages,id',
            'type'      => 'required|in:app,panel,vendor,web,all',
        ];
    }
}
