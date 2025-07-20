<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IntroResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => getLocalizedValue($this,'name'),
            'description' => getLocalizedValue($this,'description'),
            'image' => getImageassetUrl($this->image),
        ];
    }
}
