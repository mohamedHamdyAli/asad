<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractorResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'title' => getLocalizedValue($this, 'title'),
            'email' => $this->email,
            'description' => getLocalizedValue($this, 'description'),
            'company_address' => getLocalizedValue($this, 'company_address'),
            'company_phone' => $this->company_phone,
            'representative_name' => getLocalizedValue($this, 'representative_name'),
            'representative_phone' => $this->representative_phone,
            'image' => getImageassetUrl($this->image),
        ];
    }
}
