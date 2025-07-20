<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {

        $loream = 'It is in fact part of the Latin gibberish that printers use to fill in space in a layout temporarily whilst awaiting the arrival of the
        final text, so that the client can have an idea in advance of what the finished page will look like!';

        $this->create_new_config('APP_NAME', 'ASAD');

        $this->create_new_config('APP_DESC', $loream);

        $this->create_new_config('ADDREESS', $loream);

        $this->create_new_config('PRIVACY_POLICY', $loream);

        $this->create_new_config('TERMS', $loream);

        $this->create_new_config('ABOUT', $loream);

        $this->create_new_config('MOBILE', Null);
        $this->create_new_config('WHATS_APP_NUMBER', Null);
        $this->create_new_config('APP_LOGO', 'logo1.png');


        $this->create_new_config('SMTP_MAILER', Null);
        $this->create_new_config('SMTP_HOST', Null);
        $this->create_new_config('SMTP_PORT', Null);
        $this->create_new_config('SMTP_EMAIL', Null);
        $this->create_new_config('SMTP_PASSWORD', Null);
        $this->create_new_config('SMTP_ENCRYPTION', Null);
        $this->create_new_config('FORMAL_EMAIL', Null);

        $this->create_new_config('RANDOM_ACTIVATION_CODE', 'true');
        $this->create_new_config('STATIC_ACTIVATION_CODE', '1234');

        $this->create_new_config('WHATS_APP', 'https://web.whatsapp.com/');
        $this->create_new_config('FACEBOOK_URL', 'https://www.facebook.com/');
        $this->create_new_config('TWITTER_URL', 'https://twitter.com/');
        $this->create_new_config('INSTAGRAM_URL', 'https://www.instagram.com');
        $this->create_new_config('LINKEDIN_URL', 'https://www.linkedin.com');
        $this->create_new_config('YOUTUBE_URL', 'https://www.youtube.com');
        $this->create_new_config('SNAPCHAT_URL', 'https://www.snapchat.com/en-GB');
        $this->create_new_config('GMAIL_URL', 'https://gmail.com');

        $this->create_new_config('SMS_PROVIDER_TYPE', Null);
        $this->create_new_config('SMS_PROVIDER_SENDER', Null);
        $this->create_new_config('SMS_PROVIDER_MOBILE', Null);
        $this->create_new_config('SMS_PROVIDER_PASSWORD', Null);

        // App config


    }

    public function create_new_config($key, $value)
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
