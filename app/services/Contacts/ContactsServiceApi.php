<?php

namespace App\services\Contacts;

use App\Models\ContactUs;

class ContactsServiceApi
{
    public function getByCountry(string $country)
    {
        return ContactUs::where('country', $country)->get();
    }
}
