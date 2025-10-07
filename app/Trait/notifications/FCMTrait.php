<?php

namespace App\Trait\Notifications;

use Google_Client;
use Illuminate\Support\Facades\Log;

trait FCMTrait
{
    public function sendFcm($tokens, $data, $types, $lang = 'ar')
    {
        $accessToken = $this->getAccessToken();
        $url = 'https://fcm.googleapis.com/v1/projects/' . env('PROJECT_ID') . '/messages:send';

        foreach ($tokens as $token) {
            $dataString = $this->checkType($token, $data, $types, $lang);

            $headers = [
                "Authorization: Bearer $accessToken",
                'Content-Type: application/json',
            ];

            // Initialize cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

            // Execute the request and capture response and status
            $result = curl_exec($ch);
            $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            // Check for errors in cURL execution or invalid status codes
            if ($result === FALSE) {
                Log::error('FCM Request Failed: ' . curl_error($ch));
            } elseif ($httpStatus !== 200) {
                Log::error("FCM Request Error: HTTP Status $httpStatus", [
                    'response' => $result,
                    'token' => $token,
                    'data' => $dataString,
                ]);
            } else {
                Log::info("FCM Notification Sent Successfully", [
                    'response' => $result,
                    'token' => $token,
                ]);
            }
            curl_close($ch);
        }

        return true;
    }

    private function checkType($token, $data, $types, $lang)
    {
        $fixedData = [
            "message" => [
                "token" => $token,
                "notification" => [
                    "title" => $data['title'],
                    "body"  => $data['body'],
                ],
            ]
        ];

        if (in_array('android', $types)) {
            $fixedData['message']['android'] = [
                "priority" => "high",
                "notification" => [
                    "sound" => "default",
                ],
            ];
        }

        if (in_array('ios', $types)) {
            $fixedData['message']['apns'] = [
                "headers" => [
                    "apns-priority" => "10",
                ],
                "payload" => [
                    "aps" => [
                        "sound" => "default",
                    ],
                ],
            ];
        }

        return json_encode($fixedData);
    }

    public function getAccessToken()
    {
        $serviceAccountFile = storage_path('iplace-firebase.json');
        $client = new Google_Client();
        $client->setAuthConfig($serviceAccountFile);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

        return $accessToken;
    }
}
