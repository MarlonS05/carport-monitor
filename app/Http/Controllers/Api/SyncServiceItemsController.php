<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\SyncServiceItems;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SyncServiceItemsRequest;
use App\Http\Resources\SyncSummaryResource;
use Illuminate\Http\JsonResponse;

final class SyncServiceItemsController extends Controller
{
    public function __invoke(SyncServiceItemsRequest $request, SyncServiceItems $action): JsonResponse
    {
        $summary = $action->handle(
            $request->mobile(),
            $request->serviceItemItems(),
        );

        return (new SyncSummaryResource($summary))
            ->response()
            ->setStatusCode(200);
    }
}
