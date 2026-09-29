<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\RegisterMobile;
use App\Http\Controllers\Controller;
use App\Http\Resources\MobileResource;
use Illuminate\Http\JsonResponse;

final class RegisterMobileController extends Controller
{
    public function __invoke(RegisterMobile $action): JsonResponse
    {
        $mobile = $action->handle();

        return (new MobileResource($mobile))
            ->response()
            ->setStatusCode(200);
    }
}
