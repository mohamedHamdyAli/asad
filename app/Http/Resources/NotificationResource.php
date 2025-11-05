<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'title'                => $this->title,
            'body'            => $this->body,
            'is_seen'            => $this->is_seen,
            'created_at' => Carbon::parse($this->created_at)->translatedFormat('j M Y - g:ia'),
        ];
    }
}
