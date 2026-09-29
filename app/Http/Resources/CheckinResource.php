<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Data\CheckinResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CheckinResult
 */
final class CheckinResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'users' => (object) $this->users,
            'isDatabaseSync' => $this->isDatabaseSync,
        ];
    }
}
