<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Mobile;

final class SyncVehicleAttachmentsRequest extends MobileApiRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...$this->mobileIdRules(),
            'attachment_ids' => ['present', 'array'],
            'attachment_ids.*' => ['uuid'],
        ];
    }

    public function mobile(): Mobile
    {
        return Mobile::query()->findOrFail($this->validated('mobile_id'));
    }

    /**
     * @return list<string>
     */
    public function attachmentIds(): array
    {
        return array_map(
            static fn (string $id): string => $id,
            $this->validated('attachment_ids'),
        );
    }
}
