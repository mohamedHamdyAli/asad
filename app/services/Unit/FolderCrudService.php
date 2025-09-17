<?php

namespace App\services\Unit;

use App\Models\Folder;
use App\services\FileService;
use Illuminate\Support\Facades\DB;

class FolderCrudService
{
    private string $uploadFolder = "units/folder";

  public function getData($type, $unitId = null)
{
    $query = Folder::where('file_type', $type)
        ->select('id', 'name', 'folder_image', 'unit_id')
        ->orderByDesc('id');

    if ($unitId) {
        $query->where('unit_id', $unitId);
    }

    return $query->get();
}


    public function createFolder(array $request)
    {
        return DB::transaction(function () use ($request) {
            if (!empty($request['folder_image'])) {
                $request['folder_image'] = FileService::upload($request['folder_image'], $this->uploadFolder);
            }
            if (!empty($request['name'])) {
                $request['name'] = json_encode($request['name'], JSON_UNESCAPED_UNICODE);
            }
            return Folder::create($request);
        });
    }

    public function updateFolderData(array $request, int $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $folder = Folder::findOrFail($id);

            if (!empty($request['folder_image'])) {
                $request['folder_image'] = FileService::replace(
                    $request['folder_image'],
                    $this->uploadFolder,
                    $folder->getRawOriginal('folder_image')
                );
            }
            if (!empty($request['name'])) {
                $request['name'] = json_encode($request['name'], JSON_UNESCAPED_UNICODE);
            }
            $folder->update($request);
            return $folder;
        });
    }

    public function deleteFolder(int $id)
    {
        return DB::transaction(function () use ($id) {
            $folder = Folder::findOrFail($id);
            FileService::delete($folder->getRawOriginal('folder_image'));
            $folder->delete();
            return true;
        });
    }
}
