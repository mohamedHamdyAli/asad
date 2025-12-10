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

        $loreamEn = 'It is in fact part of the Latin gibberish that printers use to fill in space in a layout temporarily whilst awaiting the arrival of the
        final text, so that the client can have an idea in advance of what the finished page will look like!';
        $loreamDe = 'Es handelt sich dabei um lateinische Wörter, die Drucker verwenden, um Lücken im Layout vorübergehend zu füllen, bis der endgültige Text vorliegt,
 damit der Kunde eine Vorstellung davon bekommt, wie die fertige Seite aussehen wird!';

        $locationEn = '6th of October City';
        $locationAr = 'مدينة 6 أكتوبر،.';

        $this->create_new_config('APP_NAME', 'ASAD');

        $this->create_new_config('About', json_encode(['en' => $loreamEn, 'ar' => $loreamDe], JSON_UNESCAPED_UNICODE));
        $this->create_new_config('Location', json_encode(['en' => $locationEn, 'ar' => $locationAr], JSON_UNESCAPED_UNICODE));

        $this->create_new_config('Privacy_Policy', json_encode(['en' => $loreamEn, 'ar' => $loreamDe], JSON_UNESCAPED_UNICODE));
        $this->create_new_config('Terms', json_encode(['en' => $loreamEn, 'ar' => $loreamDe], JSON_UNESCAPED_UNICODE));

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

        $this->create_new_config('Phone', '+201555880688');
        $this->create_new_config('Email', 'nahrPhpTeam@nahrPhpTeam.com');

        $this->create_new_config('SMS_PROVIDER_TYPE', Null);
        $this->create_new_config('SMS_PROVIDER_SENDER', Null);
        $this->create_new_config('SMS_PROVIDER_MOBILE', Null);
        $this->create_new_config('SMS_PROVIDER_PASSWORD', Null);

        // App config
        $this->create_new_config('currency', 'KWD');

        // website Images
        // About Page
        $this->create_new_config('about_inner_about01', Null);
        $this->create_new_config('about_inner_about02', Null);
        $this->create_new_config('about_inner_about03', Null);
        $this->create_new_config('about_inner_client01', Null);
        $this->create_new_config('about_more_about_us', Null);
        $this->create_new_config('about_banner', Null);

        // Contact Page
        $this->create_new_config('contact_service_station', Null);
        $this->create_new_config('contact_make_quote', Null);
        $this->create_new_config('contact_call_us', Null);

        // Home Page
        $this->create_new_config('home_Great_Company_Backup01', Null);
        $this->create_new_config('home_Great_Company_Backup02', Null);
        $this->create_new_config('home_Great_Company_Backup03', Null);
        $this->create_new_config('home_Services_Banner', Null);
        $this->create_new_config('home_cirtifaction01', Null);
        $this->create_new_config('home_cirtifaction02', Null);
        $this->create_new_config('home_cirtifaction03', Null);
    }

    public function create_new_config($key, $value)
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
