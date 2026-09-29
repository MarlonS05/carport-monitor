<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Data\VehicleSyncItemData;
use App\Enums\MileageUnit;
use App\Models\Mobile;
use App\Rules\SafeHref;
use App\Rules\ValidBase64;
use Illuminate\Validation\Rule;

final class SyncVehiclesRequest extends MobileApiRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...$this->mobileIdRules(),
            'vehicles' => ['required', 'array'],
            'vehicles.*.id' => ['required', 'uuid'],
            'vehicles.*.name' => ['required', 'string', 'max:255'],
            'vehicles.*.mileage' => ['required', 'integer', 'min:0'],
            'vehicles.*.mileage_unit' => ['required', Rule::enum(MileageUnit::class)],
            'vehicles.*.description' => ['nullable', 'string'],
            'vehicles.*.user_manual_url' => ['nullable', 'string', 'max:2048', new SafeHref],
            'vehicles.*.service_manual_url' => ['nullable', 'string', 'max:2048', new SafeHref],
            'vehicles.*.maintenance_schedule_image' => ['nullable', 'string', new ValidBase64],
            'vehicles.*.updated_at' => ['required', 'date'],
        ];
    }

    public function mobile(): Mobile
    {
        return Mobile::query()->findOrFail($this->validated('mobile_id'));
    }

    /**
     * @return list<VehicleSyncItemData>
     */
    public function vehicleItems(): array
    {
        return array_map(
            static fn (array $vehicle): VehicleSyncItemData => VehicleSyncItemData::fromArray($vehicle),
            $this->validated('vehicles'),
        );
    }
}
