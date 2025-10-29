<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitLiveCameraResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'unit_id'     => $this->unit_id,
            'ip_address'  => $this->ip_address,
            'camera_link' =>  $this->camera_link,
        ];
    }
}
