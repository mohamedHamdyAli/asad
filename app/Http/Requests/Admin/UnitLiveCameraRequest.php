<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class UnitLiveCameraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return Route::is('unit.live_cameras.store') ? [
            'unit_id' => 'required|exists:units,id',
            'data' => 'required|array',
            'data.*.ip_address' => 'required|ip',
            'data.*.camera_link' => 'required|url',
        ] : [
            'unit_id' => 'required|exists:units,id',
            'data' => 'required|array',
            'data.*.id' => 'required|exists:unit_live_cameras,id',
            'data.*.ip_address' => 'nullable|ip',
            'data.*.camera_link' => 'nullable|url',
        ];
    }
}
