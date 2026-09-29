<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        Model::preventLazyLoading(! app()->isProduction());
        Model::preventSilentlyDiscardingAttributes(! app()->isProduction());

        Route::bind('vehicle', function (string $value): Vehicle {
            $query = Vehicle::query()->where('external_id', $value);

            if (request()->is('api/*')) {
                return $query->firstOrFail();
            }

            $user = auth()->user();

            abort_if($user === null, 404);

            return $query
                ->visibleTo($user)
                ->firstOrFail();
        });
    }
}
