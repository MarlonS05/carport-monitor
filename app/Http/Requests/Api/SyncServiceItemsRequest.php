<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Data\ServiceItemSyncItemData;
use App\Enums\MileageUnit;
use App\Models\Mobile;
use Illuminate\Validation\Rule;

final class SyncServiceItemsRequest extends MobileApiRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...$this->mobileIdRules(),
            'service_items' => ['required', 'array'],
            'service_items.*.id' => ['required', 'uuid'],
            'service_items.*.vehicle_id' => ['required', 'uuid'],
            'service_items.*.title' => ['required', 'string', 'max:255'],
            'service_items.*.description' => ['nullable', 'string'],
            'service_items.*.mileage' => ['required', 'integer', 'min:0'],
            'service_items.*.mileage_unit' => ['required', Rule::enum(MileageUnit::class)],
            'service_items.*.occurred_at' => ['required', 'date'],
            'service_items.*.updated_at' => ['required', 'date'],
        ];
    }

    public function mobile(): Mobile
    {
        return Mobile::query()->findOrFail($this->validated('mobile_id'));
    }

    /**
     * @return list<ServiceItemSyncItemData>
     */
    public function serviceItemItems(): array
    {
        return array_map(
            static fn (array $serviceItem): ServiceItemSyncItemData => ServiceItemSyncItemData::fromArray($serviceItem),
            $this->validated('service_items'),
        );
    }
}
