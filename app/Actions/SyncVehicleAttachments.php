<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\VehicleAttachmentsSyncResult;
use App\Models\Mobile;
use App\Models\Vehicle;
use App\Models\VehicleAttachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class SyncVehicleAttachments
{
    /**
     * @param  list<string>  $attachmentIds
     */
    public function handle(Mobile $mobile, array $attachmentIds): VehicleAttachmentsSyncResult
    {
        $query = VehicleAttachment::query()
            ->where('mobile_id', $mobile->id);

        if ($attachmentIds !== []) {
            $query->whereNotIn('external_id', $attachmentIds);
        }

        $toDelete = $query->get();

        /** @var list<string> $deletedIds */
        $deletedIds = [];

        DB::transaction(function () use ($toDelete, &$deletedIds): void {
            $vehicleIds = $toDelete->pluck('vehicle_id')->unique()->values();

            foreach ($toDelete as $attachment) {
                Storage::disk($attachment->disk)->delete($attachment->path);
                $attachment->delete();
                $deletedIds[] = $attachment->external_id;
            }

            if ($vehicleIds->isNotEmpty()) {
                Vehicle::query()
                    ->whereIn('id', $vehicleIds)
                    ->update(['updated_at' => now()]);
            }
        });

        return new VehicleAttachmentsSyncResult(
            deleted: count($deletedIds),
            attachmentIds: $deletedIds,
        );
    }
}
