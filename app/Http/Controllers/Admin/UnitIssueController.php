<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UnitIssueRequest;
use App\services\UnitIssues\UnitIssueService;

class UnitIssueController extends Controller
{
    protected $service;

    public function __construct(UnitIssueService $service)
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
        $issue = $this->service->getById($id);

        if (!$issue) {
            return response()->json(['status' => 'error', 'message' => 'Issue not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $issue
        ]);
    }

    public function store(UnitIssueRequest $request)
    {
        $created = $this->service->create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Issue created successfully',
            'data' => $created
        ]);
    }

    public function update(UnitIssueRequest $request, $id)
    {
        $issue = $this->service->getById($id);

        if (!$issue) {
            return response()->json(['status' => 'error', 'message' => 'Issue not found'], 404);
        }

        $updated = $this->service->update($issue, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Issue updated successfully',
            'data' => $updated
        ]);
    }

    public function destroy($id)
    {
        $issue = $this->service->getById($id);

        if (!$issue) {
            return response()->json(['status' => 'error', 'message' => 'Issue not found'], 404);
        }

        $this->service->delete($issue);

        return response()->json(['status' => 'success', 'message' => 'Issue deleted']);
    }
}
