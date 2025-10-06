<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Route;

class UnitPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if (Route::is('unit-payments.store')) {
            return [
                'unit_id' => 'required|exists:units,id',
                'data' => 'required|array|min:1',
                'data.*.total_price' => 'required|numeric|min:0',
                'data.*.installments_count' => 'required|integer|min:1',
                'data.*.payment_type' => 'required|in:cash,installments',
                'data.*.overall_status' => 'nullable|in:pending,in_progress,completed,overdue',
            ];
        }
        return [
            'data' => 'required|array',
            'data.unit_id' => 'nullable|exists:units,id',
            'data.total_price' => 'nullable|numeric|min:0',
            'data.installments_count' => 'nullable|integer|min:1',
            'data.payment_type' => 'nullable|in:cash,installments',
            'data.overall_status' => 'nullable|in:pending,in_progress,completed,overdue',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        failedValidation($validator);
    }
}
