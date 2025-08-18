<?php

namespace App\services\Contractor;

use App\Models\Contractor;
use App\services\FileService;
use Illuminate\Support\Facades\DB;

class ContractorService
{
    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = "contractors";
    }

    public function getContractorData($id = null)
    {
        return $id ? Contractor::where('id', $id)->first() : Contractor::all();
    }

    public function createContractor($request): bool
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                $title = json_encode($item['title'], JSON_UNESCAPED_UNICODE);
                $description = json_encode($item['description'], JSON_UNESCAPED_UNICODE);
                $image = FileService::upload($item['image'], $this->uploadFolder);

                Contractor::create([
                    'title' => $title,
                    'description' => $description,
                    'image' => $image,
                ]);
            }

            return true;
        });
    }


    public function updateContractorData(array $request): bool
    {
        return DB::transaction(function () use ($request) {
            foreach ($request['data'] as $item) {
                $contractor = Contractor::find($item['id']);

                $updateData = [];

                if (!empty($item['title'])) {
                    $updateData['title'] = json_encode($item['title'], JSON_UNESCAPED_UNICODE);
                }

                if (!empty($item['description'])) {
                    $updateData['description'] = json_encode($item['description'], JSON_UNESCAPED_UNICODE);
                }

                if (!empty($item['image'])) {
                    $updateData['image'] = FileService::replace(
                        $item['image'],
                        $this->uploadFolder,
                        $contractor->getRawOriginal('image')
                    );
                }

                $contractor->update($updateData);
            }

            return true;
        });
    }

    public function deleteContractor(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $contractor = Contractor::where('id', $id)->first();
            if (!$contractor) {
                return false;
            }
            if ($contractor->image) {
                FileService::delete($contractor->getRawOriginal('image'));
            }

            $contractor->delete();
            return true;
        });
    }
}
