<?php
namespace App\services\UnitPhaseNotes;

use App\Models\UnitPhaseNote;
use Illuminate\Support\Facades\DB;

class UnitPhaseNoteService
{
    public function getAll()
    {
        return UnitPhaseNote::select([
                'id', 'unit_id', 'user_id', 'note',
            ])
            ->with(['unit', 'user:id,name'])
            ->get();
    }

    public function getById(int $id)
    {
        return UnitPhaseNote::select([
                'id', 'unit_id', 'user_id', 'note',
            ])
            ->with(['unit', 'user:id,name'])
            ->find($id);
    }

    public function create(array $validated)
    {
        return DB::transaction(function () use ($validated) {
            return UnitPhaseNote::create($validated['data']);
        });
    }

    public function update(UnitPhaseNote $note, array $validated)
    {
        return DB::transaction(function () use ($note, $validated) {
            $note->update($validated['data']);
            return $note;
        });
    }

    public function delete(UnitPhaseNote $note)
    {
        return DB::transaction(function () use ($note) {
            return $note->delete();
        });
    }
}
