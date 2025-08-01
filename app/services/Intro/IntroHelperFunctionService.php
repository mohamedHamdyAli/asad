<?php

namespace App\services\Intro;

use App\Models\Intro;
use App\services\FileService;
use Illuminate\Support\Facades\DB;

class IntroHelperFunctionService
{
    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = "intro";
    }

    /**
     * Get intro data by ID or all intros.
     */
    public function getIntroData($id = null)
    {
        return $id ? Intro::find($id) : Intro::allActive();
    }

    public function getIntro()
    {
        return Intro::allActive(true);
    }

    /**
     * Create a new intro.
     */
    public function createIntro($request)
    {
        return DB::transaction(function () use ($request) {
            if (!empty($request['image'])) {
                $request['image'] = FileService::upload($request['image'], $this->uploadFolder);
            }

            $request['name'] = json_encode($request['name'], JSON_UNESCAPED_UNICODE);
            $request['description'] = json_encode($request['description'], JSON_UNESCAPED_UNICODE);

            Intro::create($request);
            return true;
        });
    }

    /**
     * Update an existing intro.
     */
    public function updateIntroData($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $intro = Intro::find($id);
            if (!$intro) {
                return false;
            }

            if (!empty($request['image'])) {
                $request['image'] = FileService::replace(
                    $request['image'],
                    $this->uploadFolder,
                    $intro->getRawOriginal('image')
                );
            }

            if (!empty($request['name'])) {
                $request['name'] = json_encode($request['name'], JSON_UNESCAPED_UNICODE);
            }
            if (!empty($request['description'])) {
                $request['description'] = json_encode($request['description'], JSON_UNESCAPED_UNICODE);
            }

            $intro->update($request);
            return true;
        });
    }

    /**
     * Delete an intro.
     */
    public function deleteIntro($id)
    {
        return DB::transaction(function () use ($id) {
            $intro = Intro::find($id);
            if (!$intro) {
                return false;
            }
            FileService::delete($intro->getRawOriginal('image'));
            $intro->delete();

            return true;
        });
    }
}
