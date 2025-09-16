<?php

namespace App\services\Contacts;

use App\Models\ContactUs;
use App\Http\Resources\ContactResource;

class ContactsServiceApi
{
    public function getByCountry(string $country)
    {
        $contact = ContactUs::where('country', $country)->first();
        if (!$contact) {
            return failReturnMsg('No contact found for this country', 404);
        }
        return successReturnData(new ContactResource($contact), 'Data Fetched Successfully');
    }
}
