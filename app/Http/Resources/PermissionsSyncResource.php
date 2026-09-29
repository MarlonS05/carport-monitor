<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Data\PermissionsSyncResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PermissionsSyncResult
 */
final class PermissionsSyncResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'synced' => $this->synced,
            'user_ids' => $this->userIds,
        ];
    }
}
