<?php

namespace App\services\Unit;

use App\Models\UnitPayment;
use App\Models\UnitPaymentInstallment;
use App\services\FileService;
use Illuminate\Support\Facades\DB;

class UnitPaymentInstallmentCrudService
{
    public function createInstallment($request)
    {
        return DB::transaction(function () use ($request) {
            $unitPaymentId = $request['unit_payment_id'] ?? null;
            $unitPayment = UnitPayment::find($unitPaymentId);

            if (!$unitPayment) {
                return ['status' => false, 'message' => 'Unit payment not found'];
            }

            $data = $request['data'];

            if (isset($data['title']) && is_array($data['title'])) {
                $data['title'] = json_encode($data['title'], JSON_UNESCAPED_UNICODE);
            }

            if (isset($data['description']) && is_array($data['description'])) {
                $data['description'] = json_encode($data['description'], JSON_UNESCAPED_UNICODE);
            }

            if (isset($data['invoice_file']) && $data['invoice_file'] instanceof \Illuminate\Http\UploadedFile) {
                $data['invoice_file'] = FileService::upload($data['invoice_file'], 'units/invoices');
            }

            $installment = $unitPayment->installments()->create($data);

            if ($unitPayment->overall_status === 'completed') {
                $unitPayment->overall_status = 'in_progress';
                $unitPayment->save();
            }

            return ['status' => true, 'data' => $installment];
        });
    }


    public function updateInstallment($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            $installment = UnitPaymentInstallment::find($id);
            if (!$installment) {
                return ['status' => false, 'message' => 'Installment not found'];
            }

            $data = $request['data'] ?? [];

            $unitPaymentId = $data['unit_payment_id'] ?? $installment->unit_payment_id ?? null;
            $unitPayment = UnitPayment::find($unitPaymentId);

            if (!$unitPayment) {
                return ['status' => false, 'message' => 'Unit payment not found'];
            }

            if (isset($data['title']) && is_array($data['title'])) {
                $data['title'] = json_encode($data['title'], JSON_UNESCAPED_UNICODE);
            }

            if (isset($data['description']) && is_array($data['description'])) {
                $data['description'] = json_encode($data['description'], JSON_UNESCAPED_UNICODE);
            }

            if (isset($data['invoice_file']) && $data['invoice_file'] instanceof \Illuminate\Http\UploadedFile) {
                if ($installment->invoice_file) {
                    FileService::delete($installment->invoice_file);
                }
                $data['invoice_file'] = FileService::upload($data['invoice_file'], 'units/invoices');
            }

            $installment->update($data);

            if ($unitPayment->overall_status === 'completed') {
                $unitPayment->overall_status = 'in_progress';
                $unitPayment->save();
            }

            return ['status' => true, 'data' => $installment];
        });
    }


    public function deleteInstallment($id)
    {
        return DB::transaction(function () use ($id) {
            $installment = UnitPaymentInstallment::find($id);

            if (!$installment) {
                return ['status' => false, 'message' => 'Installment not found'];
            }

            $installment->delete();

            return ['status' => true];
        });
    }


    public function getInstallmentsByUnitPayment($unitPaymentId)
    {
        $unitPayment = UnitPayment::with(['installments'])->find($unitPaymentId);

        if (!$unitPayment) {
            return ['status' => false, 'message' => 'Unit payment not found'];
        }

        return [
            'status' => true,
            'data' => $unitPayment->installments
        ];
    }



}
