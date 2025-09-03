<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\Http\Requests\Api\ContactsRequest;
use App\services\Contacts\ContactsServiceApi;

class ContactsController extends Controller
{

    private ContactsServiceApi $contactsServiceApi;

    public function __construct(ContactsServiceApi $contactsServiceApi)
    {
        $this->contactsServiceApi = $contactsServiceApi;
    }
    public function getByCountry(ContactsRequest $request)
    {
        $contact = $this->contactsServiceApi->getByCountry($request->country)->first();
        return successReturnData(new ContactResource($contact), 'Data Fetched Successfully');
    }
}
