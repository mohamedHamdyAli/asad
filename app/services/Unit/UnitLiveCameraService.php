<?php

namespace App\Services\Unit;

use App\Models\UnitLiveCamera;

class UnitLiveCameraService
{
    // public function getLiveStreamLink($unitId)
    // {
    //     $camera = UnitLiveCamera::where('unit_id', $unitId)->first();

    //     if (!$camera) {
    //         return [
    //             'status' => false,
    //             'message' => 'Camera not found',
    //             'data' => []
    //         ];
    //     }

    //     $user = config('services.hikvision.username');
    //     $pass = urlencode(config('services.hikvision.password'));
    //     $port = config('services.hikvision.port');
    //     $ip   = $camera->ip_address;

    //     $rtspUrl = "rtsp://{$user}:{$pass}@{$ip}:{$port}/Streaming/Channels/102/";

    //     $outputDir = public_path("streams/unit_{$unitId}");
    //     if (!file_exists($outputDir)) {
    //         mkdir($outputDir, 0777, true);
    //     }

    //     $hlsFile = "{$outputDir}/index.m3u8";
    //     $ffmpegPath = "C:\\ffmpeg\\bin\\ffmpeg.exe";

    //     if (!file_exists($hlsFile) || time() - filemtime($hlsFile) > 10) {
    //         $cmd = "\"{$ffmpegPath}\" -rtsp_transport tcp -i \"{$rtspUrl}\" -vcodec libx264 -preset ultrafast -tune zerolatency -vf scale=1280:720 -f hls -hls_time 2 -hls_list_size 5 -hls_flags delete_segments \"{$hlsFile}\" -loglevel error > NUL 2>&1 &";
    //         pclose(popen($cmd, "r"));
    //     }

    //     $streamUrl  = url("streams/unit_{$unitId}/index.m3u8");
    //     $webViewUrl = url("unit-live-camera/{$unitId}/view");

    //     return [
    //         'status'  => true,
    //         'message' => 'Live stream ready',
    //         'data'    => [
    //             'stream_url'  => $streamUrl,
    //             'webview_url' => $webViewUrl
    //         ]
    //     ];
    // }
}
