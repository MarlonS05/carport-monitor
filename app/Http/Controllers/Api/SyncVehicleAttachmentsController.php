<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\SyncVehicleAttachments;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SyncVehicleAttachmentsRequest;
use App\Http\Resources\VehicleAttachmentsSyncResource;
use Illuminate\Http\JsonResponse;

final class SyncVehicleAttachmentsController extends Controller
{
    public function __invoke(SyncVehicleAttachmentsRequest $request, SyncVehicleAttachments $action): JsonResponse
    {
        $result = $action->handle(
            $request->mobile(),
            $request->attachmentIds(),
        );

        return (new VehicleAttachmentsSyncResource($result))
            ->response()
            ->setStatusCode(200);
    }
}
