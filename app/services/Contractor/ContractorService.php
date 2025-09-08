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
                    'email' => $item['email'] ?? null
                ]);
            }

            return true;
        });
    }

    public function updateContractorData(array $request, int $id): bool
    {
        return DB::transaction(function () use ($request, $id) {
            if (isset($request['data']) && is_array($request['data'])) {
                $request = $request['data'];
            }

            $contractor = Contractor::find($id);
            if (!$contractor) {
                return false;
            }

            if (!empty($request['image'])) {
                $request['image'] = FileService::replace(
                    $request['image'],
                    $this->uploadFolder,
                    $contractor->getRawOriginal('image')
                );
            }

            if (!empty($request['title']) && is_array($request['title'])) {
                $request['title'] = json_encode($request['title'], JSON_UNESCAPED_UNICODE);
            }
            if (!empty($request['description']) && is_array($request['description'])) {
                $request['description'] = json_encode($request['description'], JSON_UNESCAPED_UNICODE);
            }

            if (isset($request['email'])) {
                $contractor->email = $request['email'];
            }
            $contractor->update($request);
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
