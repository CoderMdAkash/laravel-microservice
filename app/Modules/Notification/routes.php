<?php

declare(strict_types=1);

use App\Modules\Notification\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [NotificationController::class, 'index'])->name('index');
Route::get('/health', [NotificationController::class, 'health'])->name('health');
