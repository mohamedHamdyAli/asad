<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class InvoiceConfirmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => 'required|in:confirm,reject',
            'invoice_id' => 'required|exists:unit_payment_installment_invoices,id',

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        failedValidation($validator);
    }
}
