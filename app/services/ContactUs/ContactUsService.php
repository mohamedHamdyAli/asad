<?php

namespace App\services\ContactUs;

use App\Models\ContactUs;
use Illuminate\Support\Facades\DB;

class ContactUsService
{
    public function getContactUs($id = null)
    {
        return $id ? ContactUs::find($id) : ContactUs::all();
    }

    public function createContactUs(array $request): bool
    {
        return DB::transaction(function () use ($request) {
            ContactUs::create($request);
            return true;
        });
    }

    public function updateContactUs(array $request, int $id): bool
    {
        return DB::transaction(function () use ($request, $id) {
            $contact = ContactUs::find($id);
            if (!$contact) {
                return false;
            }

            $contact->update($request);
            return true;
        });
    }


    public function deleteContactUs(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $contact = ContactUs::find($id);
            if (!$contact) {
                return false;
            }

            $contact->delete();
            return true;
        });
    }
}
