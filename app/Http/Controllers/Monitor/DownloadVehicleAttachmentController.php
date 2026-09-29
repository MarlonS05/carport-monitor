<?php

declare(strict_types=1);

namespace App\Http\Controllers\Monitor;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleAttachment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class DownloadVehicleAttachmentController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Vehicle $vehicle, VehicleAttachment $attachment): StreamedResponse
    {
        abort_unless($attachment->vehicle_id === $vehicle->id, 404);

        $this->authorize('view', $attachment);

        return Storage::disk($attachment->disk)->download(
            $attachment->path,
            $attachment->original_filename,
        );
    }
}
