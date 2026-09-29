<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Mobile;

final class SyncPermissionsRequest extends MobileApiRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...$this->mobileIdRules(),
            'user_ids' => ['present', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ];
    }

    public function mobile(): Mobile
    {
        return Mobile::query()->findOrFail($this->validated('mobile_id'));
    }

    /**
     * @return list<int>
     */
    public function userIds(): array
    {
        return array_map(
            static fn (int|string $id): int => (int) $id,
            $this->validated('user_ids'),
        );
    }
}
