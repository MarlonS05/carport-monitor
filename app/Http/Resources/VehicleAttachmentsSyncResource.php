<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Data\VehicleAttachmentsSyncResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin VehicleAttachmentsSyncResult
 */
final class VehicleAttachmentsSyncResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'deleted' => $this->deleted,
            'attachment_ids' => $this->attachmentIds,
        ];
    }
}
