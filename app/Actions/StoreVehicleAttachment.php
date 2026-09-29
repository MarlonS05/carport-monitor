<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Mobile;
use App\Models\Vehicle;
use App\Models\VehicleAttachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

final class StoreVehicleAttachment
{
    private const string DISK = 'local';

    public function handle(
        Mobile $mobile,
        Vehicle $vehicle,
        UploadedFile $file,
        string $externalId,
    ): VehicleAttachment {
        if ($vehicle->mobile_id !== $mobile->id) {
            throw new RuntimeException('This vehicle is not owned by the requesting mobile device.');
        }

        $extension = $this->extensionFromMimeType($file->getMimeType() ?? 'application/octet-stream');
        $path = 'vehicle-attachments/'.$vehicle->external_id.'/'.$externalId.'.'.$extension;
        $disk = Storage::disk(self::DISK);

        $disk->put($path, $file->get());

        try {
            $attachment = DB::transaction(function () use ($mobile, $vehicle, $file, $externalId, $path): VehicleAttachment {
                $attachment = VehicleAttachment::query()->create([
                    'external_id' => $externalId,
                    'vehicle_id' => $vehicle->id,
                    'mobile_id' => $mobile->id,
                    'original_filename' => $this->sanitizeFilename($file->getClientOriginalName()),
                    'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
                    'size' => $file->getSize(),
                    'disk' => self::DISK,
                    'path' => $path,
                ]);

                $vehicle->touch();

                return $attachment;
            });
        } catch (\Throwable $exception) {
            $disk->delete($path);

            throw $exception;
        }

        return $attachment;
    }

    private function sanitizeFilename(string $filename): string
    {
        $basename = basename(str_replace('\\', '/', $filename));

        return Str::limit($basename, 255, '');
    }

    private function extensionFromMimeType(string $mimeType): string
    {
        return match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
            default => 'bin',
        };
    }
}
