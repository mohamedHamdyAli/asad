<?php

namespace App\services\Unit;

use App\Models\Folder;
use App\Models\Unit;
use App\services\FileService;
use Illuminate\Support\Facades\DB;

class FolderCrudService
{
    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = "units/folder";
    }

    public function getData($type)
    {
        return Folder::ofType($type)
            ->select('id', 'name', 'folder_image', 'unit_id')
            ->orderByDesc('id')
            ->get();
    }

    public function createFolder($request)
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

    public function updateFolderData($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $Folder = Folder::findOrFail($id);

            if (!empty($request['folder_image'])) {
                $request['folder_image'] = FileService::replace(
                    $request['folder_image'],
                    $this->uploadFolder,
                    $Folder->getRawOriginal('folder_image')
                );
            }
            if (!empty($request['name'])) {
                $request['name'] = json_encode($request['name'], JSON_UNESCAPED_UNICODE);
            }
            $Folder->update($request);
            return $Folder;
        });
    }

    public function deleteFolder($id)
    {
        return DB::transaction(function () use ($id) {
            $Folder = Folder::findOrFail($id);
            FileService::delete($Folder->getRawOriginal('folder_image'));
            $Folder->delete();
            return true;
        });
    }


}
