<?php

namespace App\services\Consultant;

use App\Models\Consultant;
use App\Services\FileService;
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
                $image = FileService::upload($item['image'], $this->uploadFolder);

                Consultant::create([
                    'title' => $title,
                    'description' => $description,
                    'image' => $image,
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

            $consultant->update($request);
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
