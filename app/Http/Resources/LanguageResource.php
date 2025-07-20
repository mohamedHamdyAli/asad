<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LanguageResource extends JsonResource
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
            'name' => $this->name,
            'name_en' => $this->name_en,
            'icon' => getImageassetUrl($this->icon),
            'code' => $this->code,
            'country_code' => $this->country_code,
            'is_rtl' => $this->is_rtl,
        ];
    }
}
