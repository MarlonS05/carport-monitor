<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Data\SyncSummary;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SyncSummary
 */
final class SyncSummaryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'created' => $this->created,
            'updated' => $this->updated,
            'ignored' => $this->ignored,
            'items' => array_map(
                static fn ($item): array => [
                    'id' => $item->id,
                    'status' => $item->status->value,
                ],
                $this->items,
            ),
        ];
    }
}
