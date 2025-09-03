<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'country'       => $this->country,
            'telephone'     => $this->telephone,
            'account_name'  => $this->account_name,
            'bank_name'     => $this->bank_name,
            'iban'          => $this->iban,
            'currency'      => $this->currency,
            'swift_code'    => $this->swift_code,
            'lat'           => $this->lat,
            'long'          => $this->long,
            'location'      => $this->location,
        ];
    }
}
