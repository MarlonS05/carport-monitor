<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MileageUnit;
use Database\Factories\ServiceItemFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ServiceItem extends Model
{
    /** @use HasFactory<ServiceItemFactory> */
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'mobile_id',
        'external_id',
        'title',
        'description',
        'mileage',
        'mileage_unit',
        'occurred_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'mileage' => 'integer',
            'mileage_unit' => MileageUnit::class,
            'occurred_at' => 'date',
        ];
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

    public function formattedMileage(): string
    {
        return number_format($this->mileage).' '.$this->mileage_unit->value;
    }

    public function formattedDate(): string
    {
        return $this->occurred_at->format('M j, Y');
    }

    /**
     * @param  Builder<ServiceItem>  $query
     */
    public function scopeVisibleTo(Builder $query, User $user): void
    {
        $query->where(function (Builder $query) use ($user): void {
            $query->whereNull('mobile_id')
                ->orWhereHas('mobile.users', fn (Builder $query) => $query->where('users.id', $user->id));
        });
    }

    /**
     * @param  Builder<ServiceItem>  $query
     */
    public function scopeNewestFirst(Builder $query): void
    {
        $query->orderByDesc('occurred_at')->orderByDesc('id');
    }

    /**
     * @param  Builder<ServiceItem>  $query
     */
    public function scopeSearch(Builder $query, string $term): void
    {
        $needle = '%'.addcslashes($term, '%_\\').'%';

        $query->where(function (Builder $query) use ($needle): void {
            $query->where('title', 'like', $needle)
                ->orWhere('description', 'like', $needle);
        });
    }
}
