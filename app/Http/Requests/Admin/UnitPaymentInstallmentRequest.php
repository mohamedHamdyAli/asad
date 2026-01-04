<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Route;

class UnitPaymentInstallmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->isMethod('POST')) {
            return [
                'unit_payment_id' => 'required|exists:unit_payments,id',
                'data' => 'required|array',
                'data.title' => 'required|array',
                'data.title.*' => 'required|string|max:255',
                'data.description' => 'nullable|array',
                'data.description.*' => 'nullable|string|max:1000',
                'data.percentage' => 'nullable|numeric|min:0|max:100',
                'data.amount' => 'required|numeric|min:0',
                'data.milestone_date' => 'nullable|date',
                'data.submission_date' => 'nullable|date',
                'data.consultant_approval_date' => 'nullable|date',
                'data.due_date' => 'nullable|date',
            ];
        }

        return [
            'data' => 'required|array',
            'data.unit_payment_id' => 'required|exists:unit_payments,id',
            'data.title' => 'nullable|array',
            'data.title.*' => 'nullable|string|max:255',
            'data.description' => 'nullable|array',
            'data.description.*' => 'nullable|string|max:1000',
            'data.percentage' => 'nullable|numeric|min:0|max:100',
            'data.amount' => 'nullable|numeric|min:0',
            'data.status' => 'nullable|in:pending,paid,overdue',
            'data.milestone_date' => 'nullable|date',
            'data.submission_date' => 'nullable|date',
            'data.consultant_approval_date' => 'nullable|date',
            'data.due_date' => 'nullable|date',
            'data.payment_date' => 'nullable|date',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            response()->json([
                'key' => 'Invalid data sent',
                'msg' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
