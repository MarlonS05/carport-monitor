<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MileageUnit;
use Database\Factories\VehicleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Vehicle extends Model
{
    /** @use HasFactory<VehicleFactory> */
    use HasFactory;

    protected $fillable = [
        'external_id',
        'mobile_id',
        'name',
        'mileage',
        'mileage_unit',
        'description',
        'maintenance_schedule_image',
        'user_manual_url',
        'service_manual_url',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'mileage' => 'integer',
            'mileage_unit' => MileageUnit::class,
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    /**
     * @return BelongsTo<Mobile, $this>
     */
    public function mobile(): BelongsTo
    {
        return $this->belongsTo(Mobile::class);
    }

    /**
     * @param  Builder<Vehicle>  $query
     */
    public function scopeVisibleTo(Builder $query, User $user): void
    {
        $query->where(function (Builder $query) use ($user): void {
            $query->whereNull('mobile_id')
                ->orWhereHas('mobile.users', fn (Builder $query) => $query->where('users.id', $user->id));
        });
    }

    public function formattedMileage(): string
    {
        return number_format($this->mileage).' '.$this->mileage_unit->value;
    }

    public function maintenanceScheduleImageDataUri(): ?string
    {
        if ($this->maintenance_schedule_image === null) {
            return null;
        }

        return 'data:'.$this->maintenanceScheduleImageMimeType().';base64,'.$this->maintenance_schedule_image;
    }

    public function entryCount(): int
    {
        return $this->service_items_count ?? $this->serviceItems()->count();
    }

    public function userManualHref(): ?string
    {
        return $this->externalHref($this->user_manual_url);
    }

    public function serviceManualHref(): ?string
    {
        return $this->externalHref($this->service_manual_url);
    }

    /**
     * @return HasMany<ServiceItem, $this>
     */
    public function serviceItems(): HasMany
    {
        return $this->hasMany(ServiceItem::class);
    }

    /**
     * @return HasMany<VehicleAttachment, $this>
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(VehicleAttachment::class);
    }

    private function externalHref(?string $url): ?string
    {
        if ($url === null || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        if (preg_match('/^[a-z][a-z0-9+.-]*:/i', $url)) {
            return $url;
        }

        if (str_starts_with($url, '//')) {
            return 'https:'.$url;
        }

        if (preg_match('/[\s\\\\]/', $url) || str_starts_with($url, '/')) {
            return null;
        }

        return 'https://'.$url;
    }

    private function maintenanceScheduleImageMimeType(): string
    {
        $decoded = base64_decode($this->maintenance_schedule_image, true);

        if ($decoded === false) {
            return 'image/jpeg';
        }

        if (str_starts_with($decoded, "\xFF\xD8\xFF")) {
            return 'image/jpeg';
        }

        if (str_starts_with($decoded, "\x89PNG")) {
            return 'image/png';
        }

        if (str_starts_with($decoded, 'GIF8')) {
            return 'image/gif';
        }

        if (str_starts_with($decoded, 'RIFF') && str_contains(substr($decoded, 0, 16), 'WEBP')) {
            return 'image/webp';
        }

        return 'image/jpeg';
    }
}
