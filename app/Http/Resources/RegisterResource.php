<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegisterResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'country_code' => $this->country_code,
            'country_name' => $this->country_name,
            'role' => $this->getRoleNames()->first(),
            'gender' => $this->gender,
            'profile_image' => getImageassetUrl($this->profile_image),
            'token' => $this->token,
        ];
    }
}
