<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class UnitTimeLineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return Route::is('timeline.store') ? [
            'unit_id' => 'required|exists:units,id',
            'data' => 'required|array',
            'data.*.file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'data.*.title' => 'required|array',
            'data.*.title.*' => 'required|string',
        ] : [
            'unit_id' => 'required|exists:units,id',
            'data' => 'required|array',
            'data.*.id' => 'required|exists:unit_time_lines,id',
            'data.*.file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'data.*.title' => 'nullable|array',
            'data.*.title.*' => 'required_with:data.*.title|string',
        ];
    }
}
