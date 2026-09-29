<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Monitor\DownloadVehicleAttachmentController;
use App\Livewire\Auth\SelectUser;
use App\Livewire\Monitor\Connect;
use App\Livewire\Monitor\Dashboard;
use App\Livewire\Monitor\ServiceLog;
use App\Livewire\Monitor\VehicleDetail;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', SelectUser::class)->name('login');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', LogoutController::class)->name('logout');

    Route::get('/dashboard', Dashboard::class)->name('monitor.dashboard');

    Route::get('/connect', Connect::class)->name('monitor.connect');

    Route::get('/vehicles/{vehicle}', VehicleDetail::class)->name('monitor.vehicles.show');

    Route::get('/vehicles/{vehicle}/attachments/{attachment}', DownloadVehicleAttachmentController::class)
        ->name('monitor.vehicles.attachments.download');

    Route::get('/vehicles/{vehicle}/service-log', ServiceLog::class)->name('monitor.vehicles.service-log');
});
