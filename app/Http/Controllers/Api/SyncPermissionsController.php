<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\SyncMobilePermissions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SyncPermissionsRequest;
use App\Http\Resources\PermissionsSyncResource;
use Illuminate\Http\JsonResponse;

final class SyncPermissionsController extends Controller
{
    public function __invoke(SyncPermissionsRequest $request, SyncMobilePermissions $action): JsonResponse
    {
        $result = $action->handle(
            $request->mobile(),
            $request->userIds(),
        );

        return (new PermissionsSyncResource($result))
            ->response()
            ->setStatusCode(200);
    }
}
