<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "unit_id" => $this->unit_id,
            "total_price" => $this->total_price,
            "installments_count" => $this->installments_count,
            "remaining_installments" => $this->remaining_installments,
            "payment_type" => $this->payment_type,
            "overall_status" => $this->overall_status,
            "installments" => UnitPaymentInstallmentResource::collection($this->installments ?? []),
        ];
    }
}
