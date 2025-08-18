<?php

use App\Models\Folder;
use App\Models\Setting;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Storage;


if (!function_exists('getImageDashboardUrl')) {
    function getImageDashboardUrl(string $url): string
    {
        $filePath = "public/$url";

        $imageUrl = (Storage::exists($filePath) && !empty($url)) ? asset("storage/$url") : asset('storage/default.png');

        return "<a href='$imageUrl'><img class='rounded-circle' style='height: 80px; width: 80px; border-radius: 10%;' src='$imageUrl'></a>";
    }
}
if (!function_exists('getImageassetUrl')) {
    function getImageassetUrl($urls)
    {
        $buildUrl = function ($url) {
            if (empty($url)) {
                return asset('storage/default.png');
            }
            if (str_starts_with($url, 'staticImage/')) {
                return asset($url);
            }
            return asset("storage/$url");
        };

        return is_array($urls) ? array_map($buildUrl, (array) $urls) : $buildUrl($urls);
    }
}

if (!function_exists('resetCode')) {
    function resetCode()
    {
        return 1234;
    }
}

if (!function_exists('failedValidation')) {
    function failedValidation($validator)
    {
        $errors = collect($validator->errors()->toArray())->flatten()->first();
        $response = response()->json([
            'key' => 'Invalid data sent',
            'msg' => $errors,
            'code' => 422
        ]);

        throw new HttpResponseException($response);
    }
}

if (!function_exists('getLocalizedValue')) {
    function getLocalizedValue($model, $attribute)
    {
        if (!$model || !$model->{$attribute}) {
            return __('siteTrans.no_data_found');
        }

        $locale = request()->header('lang') ?? app()->getLocale();

        return $model->{$attribute}->{$locale} ?? $model->{$attribute}->ar;
    }
}

if (!function_exists('userAuth')) {
    function userAuth()
    {
        $user = auth('api')->user();
        if ($user != null && $user->role === 'user') {
            return $user;
        }
        return 'this user is not authenticated or not a user role';
    }
}

if (!function_exists('vendorAuth')) {
    function vendorAuth()
    {
        $user = auth('api')->user();
        if ($user != null && $user->role === 'vendor') {
            return $user;
        }
        return null;
    }
}

if (!function_exists('getSettingValue')) {

    function getSettingValue(string $key)
    {
        $setting = Setting::where('key', $key)->first()->value ?? null;
        return $setting;
    }
}
if (!function_exists('getSettingLangValue')) {

    function getSettingLangValue($key, $lang = 'ar')
    {
        $value = getSettingValue($key);
        $decoded = json_decode($value, true);
        $lang = request()->header('lang') ?? app()->getLocale();
        if (is_array($decoded) && isset($decoded[$lang])) {
            return $decoded[$lang];
        }
        return $value;
    }
}

if (!function_exists('calculateDistance')) {

    function calculateDistance($lat1, $lon1, $lat2, $lon2, $unit = 'K')
    {
        $apiKey = "AIzaSyCFrosvdjwurOh_cQzJvmjo6evqwislyic";
        // Build the URL for the Distance Matrix API request
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=$lat1,$lon1&destinations=$lat2,$lon2&key=$apiKey";

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the request
        $response = curl_exec($ch);
        curl_close($ch);

        // Decode the JSON response
        $data = json_decode($response, true);

        // // Check if the response contains distance and duration data
        // if (isset($data['rows'][0]['elements'][0]['distance']['value']) && isset($data['rows'][0]['elements'][0]['duration']['value'])) {
        //     $distanceInMeters = $data['rows'][0]['elements'][0]['distance']['value'];
        //     $durationInSeconds = $data['rows'][0]['elements'][0]['duration']['value'];

        //     // Convert meters to kilometers or miles
        //     $distance = $unit == 'M' ? $distanceInMeters * 0.000621371 : $distanceInMeters / 1000;

        //     // Convert duration from seconds to hours:minutes:seconds
        //     $duration = gmdate("H:i:s", $durationInSeconds);

        //     return [
        //         'distance' => round($distance, 2),
        //         'duration' => $duration,
        //     ];
        // } else {
        // Fallback to approximate distance using Haversine formula
        $haversine = haversineDistance($lat1, $lon1, $lat2, $lon2, $unit);
        return [
            'distance' => $haversine['distance'],
            'duration' => $haversine['duration']
        ];
        // }
    }

    function haversineDistance($lat1, $lon1, $lat2, $lon2, $unit = 'K', $speed = 50)
    {
        // Convert latitude and longitude from degrees to radians
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // Haversine formula
        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Radius of Earth in kilometers: 6371
        $distance = 6371 * $c;

        // Convert to miles if required
        if ($unit == 'M') {
            $distance *= 0.621371;
            $speed *= 0.621371; // Convert speed to miles/hour if the unit is miles
        }

        // Calculate duration (time = distance / speed)
        $durationInHours = $distance / $speed;

        // Convert duration from hours to hours:minutes:seconds format
        $durationInSeconds = $durationInHours * 3600;
        $duration = gmdate("H:i:s", $durationInSeconds);

        return [
            'distance' => round($distance, 2),
            'duration' => $duration,
        ];
    }
}

if (!function_exists('getFolderName')) {
    function getFolderName($id)
    {
        return Folder::where('id', $id)->first()->name->en ?? null;
    }
}
