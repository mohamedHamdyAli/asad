<?php

namespace App\Services\Unit;

use App\Models\Folder;
use App\Models\Unit;
use App\Services\FileService;
use Illuminate\Support\Facades\DB;

class FolderCrudService
{
    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = "unit/folder";
    }

    public function getData($type){
        return Folder::getFolderByType($type);
    }

    public function createFolder($request)
    {
        return DB::transaction(function () use ($request) {
            if (!empty($request['folder_image'])) {
                $request['folder_image'] = FileService::upload($request['folder_image'], $this->uploadFolder);
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
