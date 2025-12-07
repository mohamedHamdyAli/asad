<?php

namespace App\services;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use RuntimeException;

class FileService
{
    /**
     * @param $requestFile
     * @param $folder
     * @return string
     */
    public static function compressAndUpload($requestFile, $folder)
    {
        $file_name = uniqid('', true) . time() . '.' . $requestFile->getClientOriginalExtension();
        if (in_array($requestFile->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
            // Check the Extension should be jpg or png and do compression
            $image = Image::make($requestFile)->encode(null, 60);
            Storage::disk(config('filesystems.default'))->put($folder . '/' . $file_name, $image);
        } else {
            // Else assign file as it is
            $file = $requestFile;
            $file->storeAs($folder, $file_name, 'public');
        }
        return $folder . '/' . $file_name;
    }


    /**
     * @param $requestFile
     * @param $folder
     * @return string
     */
    public static function upload($requestFile, $folder)
    {
        $file_name = uniqid('', true) . time() . '.' . $requestFile->getClientOriginalExtension();
        $path = $folder . '/' . $file_name;

        $requestFile->storeAs($folder, $file_name, 'public');

        $fullPath = storage_path('app/public/' . $path);
        $ext = strtolower($requestFile->getClientOriginalExtension());

        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            return $path;
        }

        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                $img = imagecreatefromjpeg($fullPath);
                break;
            case 'png':
                $img = imagecreatefrompng($fullPath);
                imagesavealpha($img, true);
                break;
        }

        if (!$img) return $path;

        $width  = imagesx($img);
        $height = imagesy($img);

        $watermarkPath = public_path('staticImage/watermark/watermark.png');
        if (!file_exists($watermarkPath)) {
            return $path;
        }

        $watermark = imagecreatefrompng($watermarkPath);
        imagesavealpha($watermark, true);

        $wmW = imagesx($watermark);
        $wmH = imagesy($watermark);

        $scale = max($width, $height) / $wmW;

        $newW = $wmW * $scale * 0.7;
        $newH = $wmH * $scale * 0.7;

        $resized = imagecreatetruecolor($newW, $newH);
        imagesavealpha($resized, true);
        imagealphablending($resized, false);

        $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
        imagefill($resized, 0, 0, $transparent);

        imagecopyresampled(
            $resized,
            $watermark,
            0,
            0,
            0,
            0,
            $newW,
            $newH,
            $wmW,
            $wmH
        );

        imagedestroy($watermark);


        $rotated = imagerotate($resized, 45, $transparent);
        imagedestroy($resized);


        $rw = imagesx($rotated);
        $rh = imagesy($rotated);

        imagecopymerge(
            $img,
            $rotated,
            ($width - $rw) / 2,
            ($height - $rh) / 2,
            0,
            0,
            $rw,
            $rh,
            35
        );

        imagedestroy($rotated);


        if ($ext === 'png') {
            imagepng($img, $fullPath);
        } else {
            imagejpeg($img, $fullPath, 90);
        }

        imagedestroy($img);

        return $path;
    }

    /**
     * @param $requestFile
     * @param $folder
     * @param $deleteRawOriginalImage
     * @return string
     */
    public static function replace($requestFile, $folder, $deleteRawOriginalImage)
    {
        self::delete($deleteRawOriginalImage);
        return self::upload($requestFile, $folder);
    }

    /**
     * @param $requestFile
     * @param $folder
     * @param $deleteRawOriginalImage
     * @return string
     */
    public static function compressAndReplace($requestFile, $folder, $deleteRawOriginalImage)
    {
        if (!empty($deleteRawOriginalImage)) {
            self::delete($deleteRawOriginalImage);
        }
        return self::compressAndUpload($requestFile, $folder);
    }


    /**
     * @param $requestFile
     * @param $code
     * @return string
     */
    public static function uploadLanguageFile($requestFile, $code)
    {
        $filename = $code . '.' . $requestFile->getClientOriginalExtension();
        if (file_exists(base_path('resources/lang/') . $filename)) {
            File::delete(base_path('resources/lang/') . $filename);
        }
        $requestFile->move(base_path('resources/lang/'), $filename);
        return $filename;
    }

    public static function generateJsonLanguageFile($code, $file)
    {
        $sourcePath = base_path("storage/app/$file.php");
        $langDir = base_path("resources/lang");
        $filename = "$code.json";
        $path = "$langDir/$filename";

        if (!File::exists($sourcePath)) {
            throw new Exception("Source translation file not found: $sourcePath");
        }

        $translations = include $sourcePath;
        if (!is_array($translations)) {
            throw new Exception("Invalid translation file format: $sourcePath must return an array.");
        }

        if (!File::exists($langDir)) {
            File::makeDirectory($langDir, 0777, true, true);
        } else {
            @chmod($langDir, 0777);
        }

        $content = json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if (File::exists($path)) {
            File::delete($path);
        }

        File::put($path, $content);

        @chmod($path, 0777);

        return $filename;
    }


    /**
     * @param $file
     * @return bool
     */
    public static function deleteLanguageFile($file)
    {
        if (file_exists(base_path('resources/lang/') . $file)) {
            return File::delete(base_path('resources/lang/') . $file);
        }
        return true;
    }


    /**
     * @param $image = rawOriginalPath
     * @return bool
     */
    public static function delete($image)
    {
        if (!empty($image) && Storage::disk(config('filesystems.default'))->exists($image)) {
            return Storage::disk(config('filesystems.default'))->delete($image);
        }

        //Image does not exist in server so feel free to upload new image
        return true;
    }

    /**
     * @throws Exception
     */
    // public static function compressAndUploadWithWatermark($requestFile, $folder)
    // {
    //     $file_name = uniqid('', true) . time() . '.' . $requestFile->getClientOriginalExtension();

    //     try {
    //         if (in_array($requestFile->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
    //             $watermarkPath = Setting::where('name', 'watermark_image')->value('value');

    //             $fullWatermarkPath = storage_path('app/public/' . $watermarkPath);
    //             $watermark = null;

    //             $imagePath = $requestFile->getPathname();
    //             if (!file_exists($imagePath) || !is_readable($imagePath)) {
    //                 throw new RuntimeException("Uploaded image file is not readable at path: " . $imagePath);
    //             }
    //             $image = Image::make($imagePath)->encode(null, 60);
    //             $imageWidth = $image->width();
    //             $imageHeight = $image->height();

    //             if (!empty($watermarkPath) && file_exists($fullWatermarkPath)) {
    //                 $watermark = Image::make($fullWatermarkPath)
    //                     ->resize($imageWidth, $imageHeight, function ($constraint) {
    //                         $constraint->aspectRatio(); // Preserve aspect ratio
    //                     })
    //                     ->opacity(10);
    //             }

    //             if ($watermark) {
    //                 $image->insert($watermark, 'center');
    //             }

    //             Storage::disk(config('filesystems.default'))->put($folder . '/' . $file_name, (string)$image->encode());
    //         } else {
    //             // Else assign file as it is
    //             $file = $requestFile;
    //             $file->storeAs($folder, $file_name, 'public');
    //         }
    //         return $folder . '/' . $file_name;
    //     } catch (Exception $e) {
    //         throw new RuntimeException($e);
    //         //            $file = $requestFile;
    //         //            return  $file->storeAs($folder, $file_name, 'public');
    //     }
    // }
    // public static function compressAndReplaceWithWatermark($requestFile, $folder, $deleteRawOriginalImage = null)
    // {

    //     if (!empty($deleteRawOriginalImage)) {
    //         self::delete($deleteRawOriginalImage);
    //     }

    //     $file_name = uniqid('', true) . time() . '.' . $requestFile->getClientOriginalExtension();

    //     try {
    //         if (in_array($requestFile->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
    //             $watermarkPath = Setting::where('name', 'watermark_image')->value('value');
    //             $fullWatermarkPath = storage_path('app/public/' . $watermarkPath);
    //             $watermark = null;
    //             $imagePath = $requestFile->getPathname();
    //             if (!file_exists($imagePath) || !is_readable($imagePath)) {
    //                 throw new RuntimeException("Uploaded image file is not readable at path: " . $imagePath);
    //             }
    //             $image = Image::make($imagePath)->encode(null, 60);
    //             $imageWidth = $image->width();
    //             $imageHeight = $image->height();


    //             if (!empty($watermarkPath) && file_exists($fullWatermarkPath)) {
    //                 $watermark = Image::make($fullWatermarkPath)
    //                     ->resize($imageWidth, $imageHeight, function ($constraint) {
    //                         $constraint->aspectRatio(); // Preserve aspect ratio
    //                     })
    //                     ->opacity(10);
    //             }

    //             if ($watermark) {
    //                 $image->insert($watermark, 'center');
    //             }


    //             Storage::disk(config('filesystems.default'))->put($folder . '/' . $file_name, (string)$image->encode());
    //         } else {

    //             $file = $requestFile;
    //             $file->storeAs($folder, $file_name, 'public');
    //         }

    //         return $folder . '/' . $file_name;
    //     } catch (Exception $e) {
    //         throw new RuntimeException($e);
    //     }
    // }
}
