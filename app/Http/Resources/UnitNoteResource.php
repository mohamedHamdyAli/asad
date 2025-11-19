<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class UnitNoteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'note'        => $this->note,
            'status'      => $this->status,
        ];
    }
}
