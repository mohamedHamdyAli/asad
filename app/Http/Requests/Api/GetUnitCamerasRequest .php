<?php

namespace App\Http\Requests\Api;

class GetUnitCamerasRequest extends BaseApiRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'unit_id' => 'required|exists:units,id',
        ];
    }
}
