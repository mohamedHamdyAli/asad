<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;



class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isConsultant = $this->getRoleNames()->first() === 'consultant';
        $consultant = $isConsultant ? $this->consultant : null;

        return [
            'id' => $this->id,
            'name' => $isConsultant && $consultant ? getLocalizedValue($consultant, 'title') : $this->name,
            'email' => $this->email,
            'phone' => $isConsultant && $consultant ? $consultant->representative_phone : $this->phone,
            'profile_image' => $isConsultant && $consultant ? getImageassetUrl($consultant->image) : getImageassetUrl($this->profile_image),
            'role' => $this->getRoleNames()->first()
        ];
    }
}
