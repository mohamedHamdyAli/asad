<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FolderWithDocumentsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'folderName' => $this['foldername'],
            'folder_image' => getImageassetUrl($this['folder_image']),
            'created_at' => $this['created_at']?->format('d M Y'),
            'files' => UnitDocumentResource::collection($this['files']),
        ];
    }
}
