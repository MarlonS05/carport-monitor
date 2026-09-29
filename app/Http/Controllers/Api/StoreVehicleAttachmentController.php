<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\StoreVehicleAttachment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreVehicleAttachmentRequest;
use App\Http\Resources\VehicleAttachmentResource;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;

final class StoreVehicleAttachmentController extends Controller
{
    public function __invoke(
        StoreVehicleAttachmentRequest $request,
        Vehicle $vehicle,
        StoreVehicleAttachment $action,
    ): JsonResponse {
        $attachment = $action->handle(
            $request->mobile(),
            $vehicle,
            $request->uploadedFile(),
            $request->attachmentId(),
        );

        $attachment->load('vehicle');

        return (new VehicleAttachmentResource($attachment))
            ->response()
            ->setStatusCode(201);
    }
}
