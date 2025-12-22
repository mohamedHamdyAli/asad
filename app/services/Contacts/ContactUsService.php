<?php

namespace App\services\Contacts;

use App\Models\ContactUs;
use Illuminate\Support\Facades\DB;

class ContactUsService
{
     public function getContactUs(?string $country = null): array
    {
        if ($country) {
            $contact = ContactUs::where('country', $country)->first();

            return $contact ? [$contact] : [];
        }

        return ContactUs::all()->toArray();
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
