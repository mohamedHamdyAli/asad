<?php

namespace App\services\UnitIssues;

use App\Models\UnitIssue;
use Illuminate\Support\Facades\DB;

class UnitIssueService
{
    public function getAll()
    {
        return UnitIssue::select([
                'id', 'unit_id', 'user_id', 'title', 'description', 'status'
            ])
            ->with(['unit', 'user:id,name'])
            ->get();
    }

    public function getById(int $id)
    {
        return UnitIssue::select([
                'id', 'unit_id', 'user_id', 'title', 'description', 'status'
            ])
            ->with(['unit', 'user:id,name'])
            ->find($id);
    }

    public function create(array $validated)
    {
        return DB::transaction(function () use ($validated) {
            return UnitIssue::create($validated['data']);
        });
    }

    public function update(UnitIssue $issue, array $validated)
    {
        return DB::transaction(function () use ($issue, $validated) {
            $issue->update($validated['data']);
            return $issue;
        });
    }

    public function delete(UnitIssue $issue)
    {
        return DB::transaction(function () use ($issue) {
            return $issue->delete();
        });
    }
}
