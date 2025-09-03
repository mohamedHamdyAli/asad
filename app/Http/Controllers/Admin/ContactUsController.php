<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\services\Contacts\ContactUsService;
use App\Http\Requests\Admin\ContactUsRequest;

class ContactUsController extends Controller
{
    private ContactUsService $contactUsService;

    public function __construct(ContactUsService $contactUsService)
    {
        $this->contactUsService = $contactUsService;
    }

    public function index()
    {
        $data = $this->contactUsService->getContactUs();
        return response()->json([
            'status' => 'success',
            'message' => 'Contacts fetched successfully',
            'data' => $data
        ]);
    }

    public function store(ContactUsRequest $request)
    {
        $created = $this->contactUsService->createContactUs($request->validated());
        return response()->json([
            'status' => $created ? 'success' : 'error',
            'message' => $created ? 'Contact created successfully' : 'Failed to create contact'
        ]);
    }

    public function show($id)
    {
        $data = $this->contactUsService->getContactUs($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contact not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Contact fetched successfully',
            'data' => $data
        ]);
    }

    public function edit($id)
    {
        $data = $this->contactUsService->getContactUs($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contact not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Contact fetched successfully',
            'data' => $data
        ]);
    }

    public function update(ContactUsRequest $request, $id)
    {
        $updated = $this->contactUsService->updateContactUs($request->validated(), $id);
        if (!$updated) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contact not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Contact updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $deleted = $this->contactUsService->deleteContactUs($id);
        if (!$deleted) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contact not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Contact deleted successfully'
        ]);
    }
}
