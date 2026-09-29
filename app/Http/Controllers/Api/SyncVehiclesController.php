<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\SyncVehicles;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SyncVehiclesRequest;
use App\Http\Resources\SyncSummaryResource;
use Illuminate\Http\JsonResponse;

final class SyncVehiclesController extends Controller
{
    public function __invoke(SyncVehiclesRequest $request, SyncVehicles $action): JsonResponse
    {
        $summary = $action->handle(
            $request->mobile(),
            $request->vehicleItems(),
        );

        return (new SyncSummaryResource($summary))
            ->response()
            ->setStatusCode(200);
    }
}
