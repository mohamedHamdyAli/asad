<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UnitPhaseNoteRequest;
use App\services\UnitPhaseNotes\UnitPhaseNoteService;

class UnitPhaseNoteController extends Controller
{
    protected $service;

    public function __construct(UnitPhaseNoteService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->service->getAll()
        ]);
    }

    public function show($id)
    {
        $note = $this->service->getById($id);

        if (!$note) {
            return response()->json(['status' => 'error', 'message' => 'Note not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $note
        ]);
    }

    public function store(UnitPhaseNoteRequest $request)
    {
        $created = $this->service->create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Note created successfully',
            'data' => $created
        ]);
    }

    public function update(UnitPhaseNoteRequest $request, $id)
    {
        $note = $this->service->getById($id);

        if (!$note) {
            return response()->json(['status' => 'error', 'message' => 'Note not found'], 404);
        }

        $updated = $this->service->update($note, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Note updated successfully',
            'data' => $updated
        ]);
    }

    public function destroy($id)
    {
        $note = $this->service->getById($id);

        if (!$note) {
            return response()->json(['status' => 'error', 'message' => 'Note not found'], 404);
        }

        $this->service->delete($note);

        return response()->json(['status' => 'success', 'message' => 'Note deleted']);
    }
}
