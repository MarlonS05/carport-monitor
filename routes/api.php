<?php

declare(strict_types=1);

use App\Http\Controllers\Api\CheckinController;
use App\Http\Controllers\Api\RegisterMobileController;
use App\Http\Controllers\Api\StoreVehicleAttachmentController;
use App\Http\Controllers\Api\SyncPermissionsController;
use App\Http\Controllers\Api\SyncServiceItemsController;
use App\Http\Controllers\Api\SyncVehicleAttachmentsController;
use App\Http\Controllers\Api\SyncVehiclesController;
use Illuminate\Support\Facades\Route;

Route::get('/register', RegisterMobileController::class)->name('register');
Route::get('/checkin/{updated_at}', CheckinController::class)->where('updated_at', '.+')->name('checkin');

Route::post('/vehicles/sync', SyncVehiclesController::class)->name('vehicles.sync');
Route::post('/vehicles/{vehicle}/attachments', StoreVehicleAttachmentController::class)->name('vehicles.attachments.store');
Route::post('/attachments/sync', SyncVehicleAttachmentsController::class)->name('attachments.sync');
Route::post('/service-items/sync', SyncServiceItemsController::class)->name('service-items.sync');
Route::post('/permissions/sync', SyncPermissionsController::class)->name('permissions.sync');
