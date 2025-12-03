<?php

namespace App\services\Consultant;

use App\Models\Consultant;
use App\services\FileService;
use Illuminate\Support\Facades\DB;

class ConsultantService
{
    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = "consultants";
    }

    public function getConsultantData($id = null)
    {
        return $id ? Consultant::where('id', $id)->first() : Consultant::all();
    }

    public function createConsultant($request): bool
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                $title = json_encode($item['title'], JSON_UNESCAPED_UNICODE);
                $description = json_encode($item['description'], JSON_UNESCAPED_UNICODE);
                $representative_name = json_encode($item['representative_name'], JSON_UNESCAPED_UNICODE);
                $company_address = json_encode($item['company_address'], JSON_UNESCAPED_UNICODE);
                $image = FileService::upload($item['image'], $this->uploadFolder);

                Consultant::create([
                    'title' => $title,
                    'description' => $description,
                    'image' => $image,
                    'email' => $item['email'] ?? null,
                    'company_address' => $company_address,
                    'company_phone' => $item['company_phone'] ?? null,
                    'representative_name' => $representative_name,
                    'representative_phone' => $item['representative_phone'] ?? null

                ]);
            }

            return true;
        });
    }

    public function updateConsultantData(array $request, int $id): bool
    {
        return DB::transaction(function () use ($request, $id) {
            if (isset($request['data']) && is_array($request['data'])) {
                $request = $request['data'];
            }

            $consultant = Consultant::find($id);
            if (!$consultant) {
                return false;
            }

            if (!empty($request['image'])) {
                $request['image'] = FileService::replace(
                    $request['image'],
                    $this->uploadFolder,
                    $consultant->getRawOriginal('image')
                );
            }
            if (!empty($request['title']) && is_array($request['title'])) {
                $request['title'] = json_encode($request['title'], JSON_UNESCAPED_UNICODE);
            }

            if (!empty($request['description']) && is_array($request['description'])) {
                $request['description'] = json_encode($request['description'], JSON_UNESCAPED_UNICODE);
            }

            if (!empty($request['company_address']) && (is_array($request['company_address']) || is_object($request['company_address']))) {
                $request['company_address'] = (string) json_encode($request['company_address'], JSON_UNESCAPED_UNICODE);
            }


            if (!empty($request['representative_name']) && (is_array($request['representative_name']) || is_object($request['representative_name']))) {
                $request['representative_name'] = (string) json_encode($request['representative_name'], JSON_UNESCAPED_UNICODE);
            }

            if (isset($request['representative_phone'])) {
                $consultant->representative_phone = $request['representative_phone'];
            }

            if (isset($request['company_phone'])) {
                $consultant->company_phone = $request['company_phone'];
            }

            if (isset($request['email'])) {
                $consultant->email = $request['email'];
            }

             $consultant->update([
                'title' => $request['title'] ?? $consultant->getRawOriginal('title'),
                'description' => $request['description'] ?? $consultant->getRawOriginal('description'),
                'company_address' => $request['company_address'] ?? $consultant->getRawOriginal('company_address'),
                'representative_name' => $request['representative_name'] ?? $consultant->getRawOriginal('representative_name'),
                'image' => $request['image'] ?? $consultant->getRawOriginal('image'),
                'company_phone' => $request['company_phone'] ?? $consultant->company_phone,
                'representative_phone' => $request['representative_phone'] ?? $consultant->representative_phone,
                'email' => $request['email'] ?? $consultant->email,
            ]);
;

            return true;
        });
    }

    public function deleteConsultant(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $consultant = Consultant::find($id);
            if (!$consultant) {
                return false;
            }

            if ($consultant->image) {
                FileService::delete($consultant->getRawOriginal('image'));
            }

            $consultant->delete();
            return true;
        });
    }
}
