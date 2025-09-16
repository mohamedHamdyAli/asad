<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        return $this->contactsServiceApi->getByCountry($request['country']);
    }
}
