<?php

namespace App\services\Unit;

use App\Models\UnitPayment;
use Illuminate\Support\Facades\DB;

class UnitPaymentCrudService
{
    public function getUnitPayments($unitId)
    {
        return UnitPayment::with('installments.invoices')
            ->where('unit_id', $unitId)
            ->get();
    }

    public function createUnitPayments($request)
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                UnitPayment::create([
                    'unit_id' => $request['unit_id'],
                    'total_price' => $item['total_price'],
                    'installments_count' => $item['installments_count'],
                    'remaining_installments' => $item['installments_count'],
                    'payment_type' => $item['payment_type'] ?? 'cash',
                    'overall_status' => $item['overall_status'] ?? 'pending',
                ]);
            }

            return true;
        });
    }


    public function updateUnitPayments($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            $record = UnitPayment::find($id);
            if (!$record) {
                return false;
            }
            $data = $request['data'];
            $updateData = [];

            if (isset($data['unit_id'])) {
                $updateData['unit_id'] = $data['unit_id'];
            }

            if (isset($data['total_price'])) {
                $updateData['total_price'] = $data['total_price'];
            }

            if (isset($data['installments_count'])) {
                $updateData['installments_count'] = $data['installments_count'];
            }

            if (isset($data['payment_type'])) {
                $updateData['payment_type'] = $data['payment_type'];
            }

            if (isset($data['overall_status'])) {
                $updateData['overall_status'] = $data['overall_status'];
            }

            if (!empty($updateData)) {
                $record->update($updateData);
            }

            return true;
        });
    }



    public function deleteUnitPayment($id)
    {
        return DB::transaction(function () use ($id) {
            $record = UnitPayment::find($id);
            if (!$record) {
                return false;
            }
            $record->delete();
            return true;
        });
    }
}
