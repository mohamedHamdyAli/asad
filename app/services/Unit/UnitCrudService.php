<?php

namespace App\services\Unit;

use App\Models\Notification;
use App\Models\Unit;
use App\Models\User;
use App\services\FileService;
use App\Trait\notifications\NotifiesUnitOwnerTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UnitCrudService
{
    use NotifiesUnitOwnerTrait;

    private string $uploadFolder;

    public function __construct()
    {
        $this->uploadFolder = "units";
    }

    public function getUnitData($id = null)
    {
        $query = Unit::with('homeUnitGallery');

        return $id ? $query->find($id) : $query->get();
    }

    public function getUserUnits($userId, $status = null)
    {
        return Unit::allUserUnit($userId, $status);
    }

    public function getVendorUnits($vendorId, $status = null)
    {
        return Unit::allVendorUnit($vendorId, $status);
    }

    public function createUnit($request, ?User $actor = null)
    {
        $unit = DB::transaction(function () use ($request) {
            if (!empty($request['cover_image'])) {
                $request['cover_image'] = FileService::upload($request['cover_image'], $this->uploadFolder);
            }

            if (!empty($request['name'])) {
                $request['name'] = json_encode($request['name'], JSON_UNESCAPED_UNICODE);
            }
            if (!empty($request['description'])) {
                $request['description'] = json_encode($request['description'], JSON_UNESCAPED_UNICODE);
            }

            $unit = Unit::create($request);
            if (!empty($request['gallery'])) {
                foreach ($request['gallery'] as $image) {
                    $unit->homeUnitGallery()->create([
                        'image' => FileService::upload($image, "{$this->uploadFolder}/gallery"),
                    ]);
                }
            }

            return $unit;
        });

        if ($unit && !empty($unit->user_id)) {
            $this->notifyUnitOwner(
                $unit,
                'New unit assigned',
                'A new unit "{unit}" has been assigned to you.',
                $actor
            );
        }

        return $unit;
    }

    public function updateUnitData($request, $id, ?User $actor = null)
    {
        $unit = Unit::find($id);
        if (!$unit) return false;

        $originalValues = $this->captureUnitOriginals($unit);

        $result = DB::transaction(function () use (&$request, $unit) {
            if (!empty($request['cover_image'])) {
                $request['cover_image'] = FileService::replace(
                    $request['cover_image'],
                    $this->uploadFolder,
                    $unit->getRawOriginal('cover_image')
                );
            }
            if (!empty($request['name'])) {
                $request['name'] = json_encode($request['name'], JSON_UNESCAPED_UNICODE);
            }
            if (!empty($request['description'])) {
                $request['description'] = json_encode($request['description'], JSON_UNESCAPED_UNICODE);
            }

            $unit->update($request);
            if (!empty($request['extension_dates']) && is_array($request['extension_dates'])) {
                foreach ($request['extension_dates'] as $date) {
                    if (!$unit->extensionDates()->where('extended_date', $date)->exists()) {
                        $unit->extensionDates()->create([
                            'extended_date' => $date
                        ]);
                    }
                }
            }

            if (!empty($request['gallery']) && is_array($request['gallery'])) {
                foreach ($request['gallery'] as $image) {
                    $unit->homeUnitGallery()->create([
                        'image' => FileService::upload($image, "{$this->uploadFolder}/gallery"),
                    ]);
                }
            }

            return $unit->fresh();
        });

        if (!$result) {
            return false;
        }

        $this->dispatchUpdateNotifications($result, $originalValues, $request, $actor);

        return true;
    }

    private function captureUnitOriginals(Unit $unit): array
    {
        $fields = ['name', 'location', 'address', 'start_date', 'end_date', 'status', 'user_id', 'vendor_id', 'cover_image', 'description'];
        $values = [];
        foreach ($fields as $field) {
            $values[$field] = $unit->getRawOriginal($field);
        }
        return $values;
    }

    private function dispatchUpdateNotifications(Unit $unit, array $originalValues, array $request, ?User $actor): void
    {
        $newUserId = $unit->user_id;
        $previousUserId = $originalValues['user_id'] ?? null;
        $ownerChanged = !empty($newUserId) && (int) $newUserId !== (int) $previousUserId;

        if ($ownerChanged) {
            $this->notifyUnitOwner(
                $unit,
                'New unit assigned',
                'A new unit "{unit}" has been assigned to you.',
                $actor
            );
        }

        $changes = $this->detectChangedUnitFields($originalValues, $request);
        if (empty($changes)) {
            return;
        }

        $this->notifyAdminsOfUnitChange($unit, $changes, $actor);

        if (!$ownerChanged && !empty($newUserId)) {
            $lines = array_map(
                fn($c) => "{$c['label']}: {$c['old']} → {$c['new']}",
                $changes
            );
            $body = "Your project \"{unit}\" was updated:\n• " . implode("\n• ", $lines);
            $this->notifyUnitOwner($unit, 'Project updated', $body, $actor);
        }
    }

    private function detectChangedUnitFields(array $originalValues, array $request): array
    {
        $labels = [
            'name'        => 'Name',
            'location'    => 'Location',
            'address'     => 'Address',
            'start_date'  => 'Start date',
            'end_date'    => 'End date',
            'status'      => 'Status',
            'user_id'     => 'Owner',
            'vendor_id'   => 'Project manager',
            'cover_image' => 'Cover image',
            'description' => 'Description',
        ];

        $changes = [];
        foreach ($labels as $field => $label) {
            if (!array_key_exists($field, $request)) {
                continue;
            }
            $newRaw = $request[$field];
            $oldRaw = $originalValues[$field] ?? null;

            if ($newRaw === null || $newRaw === '') {
                continue;
            }

            $oldFormatted = $this->formatFieldValue($field, $oldRaw);
            $newFormatted = $this->formatFieldValue($field, $newRaw);

            if ($oldFormatted === $newFormatted) {
                continue;
            }

            $changes[] = [
                'label' => $label,
                'old'   => $oldFormatted,
                'new'   => $newFormatted,
            ];
        }

        return $changes;
    }

    private function formatFieldValue(string $field, $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        if ($field === 'name' || $field === 'description') {
            $decoded = is_string($value) ? json_decode($value, true) : $value;
            if (is_array($decoded)) {
                $en = trim((string) ($decoded['en'] ?? ''));
                $ar = trim((string) ($decoded['ar'] ?? ''));
                if ($en !== '' && $ar !== '') {
                    return "{$en} / {$ar}";
                }
                return $en !== '' ? $en : ($ar !== '' ? $ar : '—');
            }
            return (string) $value;
        }

        if ($field === 'user_id' || $field === 'vendor_id') {
            $user = User::find($value);
            return $user ? $user->name : ('#' . $value);
        }

        if ($field === 'cover_image') {
            return 'image updated';
        }

        if ($field === 'start_date' || $field === 'end_date') {
            try {
                return \Carbon\Carbon::parse($value)->format('Y-m-d');
            } catch (\Throwable $e) {
                return (string) $value;
            }
        }

        return (string) $value;
    }

    private function notifyAdminsOfUnitChange(Unit $unit, array $changes, ?User $actor): void
    {
        $admins = User::role('admin')->get();
        if ($admins->isEmpty()) {
            return;
        }

        $unitName = $this->extractUnitName($unit);
        $actorName = $actor?->name ?: 'A project manager';
        $title = 'Project updated';

        $lines = array_map(
            fn($c) => "{$c['label']}: {$c['old']} → {$c['new']}",
            $changes
        );
        $message = "{$actorName} updated project \"{$unitName}\":\n• " . implode("\n• ", $lines);

        foreach ($admins as $admin) {
            if ($actor && (int) $actor->id === (int) $admin->id) {
                continue;
            }

            try {
                Notification::create([
                    'user_id'         => $admin->id,
                    'type'            => 'all',
                    'objectable_id'   => $unit->id,
                    'objectable_type' => Unit::class,
                    'title'           => $title,
                    'body'            => $message,
                ]);
            } catch (\Throwable $e) {
                Log::error('Admin unit-update notification failed: ' . $e->getMessage());
            }
        }
    }

    public function deleteUnit($id)
    {
        return DB::transaction(function () use ($id) {
            $unit = Unit::find($id);
            if (!$unit)
                return false;

            FileService::delete($unit->getRawOriginal('cover_image'));
            $unit->delete();
            return true;
        });
    }

    public function deleteCoverImage($id)
    {
        return DB::transaction(function () use ($id) {
            $unit = Unit::find($id);
            if (!$unit) return false;

            FileService::delete($unit->getRawOriginal('cover_image'));

            $unit->update(['cover_image' => null]);

            return true;
        });
    }

    public function deleteGalleryImage($unitId, $imageId)
    {
        return DB::transaction(function () use ($unitId, $imageId) {
            $unit = Unit::find($unitId);
            if (!$unit) return false;

            $image = $unit->homeUnitGallery()->find($imageId);
            if (!$image) return false;

            FileService::delete($image->getRawOriginal('image'));
            $image->delete();

            return true;
        });
    }
}
