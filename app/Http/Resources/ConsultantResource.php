<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConsultantResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'title' => getLocalizedValue($this, 'title'),
            'email' => $this->email,
            'description' => getLocalizedValue($this, 'description'),
            'image' => getImageassetUrl($this->image),
        ];
    }
}
