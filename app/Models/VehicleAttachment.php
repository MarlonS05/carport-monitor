<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\VehicleAttachmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class VehicleAttachment extends Model
{
    /** @use HasFactory<VehicleAttachmentFactory> */
    use HasFactory;

    protected $fillable = [
        'external_id',
        'vehicle_id',
        'mobile_id',
        'original_filename',
        'mime_type',
        'size',
        'disk',
        'path',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function formattedSize(): string
    {
        $bytes = $this->size;

        if ($bytes < 1024) {
            return $bytes.' B';
        }

        if ($bytes < 1_048_576) {
            return number_format($bytes / 1024, $bytes < 10_240 ? 1 : 0).' KB';
        }

        return number_format($bytes / 1_048_576, 1).' MB';
    }

    /**
     * @return BelongsTo<Vehicle, $this>
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return BelongsTo<Mobile, $this>
     */
    public function mobile(): BelongsTo
    {
        return $this->belongsTo(Mobile::class);
    }
}
