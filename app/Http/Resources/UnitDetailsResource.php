<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class UnitDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
  public function toArray(Request $request): array
{
    $this->resource->loadMissing('extensionDates');

    return [
        "id" => $this->id,
        'name' => getLocalizedValue($this, 'name'),
        'description' => getLocalizedValue($this, 'description'),
        'cover_image' => getImageassetUrl($this->cover_image),
        "location" => $this->location,
        "lat" => $this->lat,
        "long" => $this->long,
        "start_date" => Carbon::parse($this->start_date)->format('d M Y, h:i A'),
        "end_date" => Carbon::parse($this->end_date)->format('d M Y, h:i A'),
        "status" => $this->status,
        "galleryHome" => getImageassetUrl($this->homeUnitGallery()->pluck('image')->toArray()),
        'extension_dates' => $this->extensionDates->map(function ($item) {
            return [
                'id' => $item->id,
                'extended_date' => \Carbon\Carbon::parse($item->extended_date)->format('d M Y, h:i A'),
            ];
        })->toArray(),
    ];
}

}
