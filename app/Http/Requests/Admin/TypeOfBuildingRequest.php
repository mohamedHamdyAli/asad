<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class TypeOfBuildingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if (Route::is('type-of-buildings.store')) {
            return [
                'data' => 'required|array',
                'data.*.title' => 'required|array',
                'data.*.title.*' => 'required|string|max:255',
                'data.*.price' => 'nullable|numeric|min:0',
            ];
        }

        return [
            'data' => 'required|array',
            'data.title' => 'nullable|array',
            'data.title.*' => 'nullable|string|max:255',
            'data.price' => 'nullable|numeric|min:0',
        ];
    }
}
