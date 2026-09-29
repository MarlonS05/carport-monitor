<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\Checkin;
use App\Http\Controllers\Controller;
use App\Http\Resources\CheckinResource;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CheckinController extends Controller
{
    public function __invoke(string $updated_at, Checkin $action): JsonResponse
    {
        try {
            $mobileUpdatedAt = Carbon::parse(urldecode($updated_at));
        } catch (InvalidFormatException) {
            throw new NotFoundHttpException;
        }

        $result = $action->handle($mobileUpdatedAt);

        return (new CheckinResource($result))
            ->response()
            ->setStatusCode(200);
    }
}
