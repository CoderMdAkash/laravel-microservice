<?php

declare(strict_types=1);

use App\Modules\Attendance\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AttendanceController::class, 'index'])->name('index');
Route::get('/health', [AttendanceController::class, 'health'])->name('health');
