<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'app_name'             => getSettingLangValue('APP_NAME'),
            'app_desc'             => getSettingLangValue('APP_DESC'),
            'addreess'             => getSettingLangValue('ADDREESS'),
            'privacy_policy'       => getSettingLangValue('PRIVACY_POLICY'),
            'terms'                => getSettingLangValue('TERMS'),
            'about'                => getSettingLangValue('ABOUT'),
            'mobile'               => getSettingValue('MOBILE'),
            'whats_app_number'     => getSettingValue('WHATS_APP_NUMBER'),
            'app_logo'             => getImageassetUrl(getSettingValue('APP_LOGO')),
            'whats_app'            => getSettingValue('WHATS_APP'),
            'facebook_url'         => getSettingValue('FACEBOOK_URL'),
            'twitter_url'          => getSettingValue('TWITTER_URL'),
            'instagram_url'        => getSettingValue('INSTAGRAM_URL'),
            'linkedin_url'         => getSettingValue('LINKEDIN_URL'),
            'youtube_url'          => getSettingValue('YOUTUBE_URL'),
            'snapchat_url'         => getSettingValue('SNAPCHAT_URL'),
            'gmail_url'            => getSettingValue('GMAIL_URL'),
        ];
    }
}
