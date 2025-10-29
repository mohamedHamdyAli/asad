<?php

namespace App\Http\Requests\Api;

class GetLiveCameraRequest extends BaseApiRequest
{
    
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'camera_id' => 'required|exists:unit_live_cameras,id',
        ];
    }
}
