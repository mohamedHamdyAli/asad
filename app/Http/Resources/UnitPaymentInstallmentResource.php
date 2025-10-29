<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitPaymentInstallmentResource extends JsonResource
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
            "title" => getLocalizedValue($this, 'title') ?? $this->title,
            "description" =>getLocalizedValue($this, 'description') ?? $this->description ?? null,
            "percentage" => $this->percentage,
            "amount" => $this->amount,
            "milestone_date" => $this->milestone_date,
            "submission_date" => $this->submission_date,
            "consultant_approval_date" => $this->consultant_approval_date,
            "payment_date" => $this->payment_date,
            "due_date" => $this->due_date,
            "status" => $this->status,
        ];
    }
}
