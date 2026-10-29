<?php

declare(strict_types=1);

use App\Gateway\Controllers\GatewayController;
use Illuminate\Support\Facades\Route;

Route::get('/overview', [GatewayController::class, 'overview'])->name('overview');
Route::get('/docs', [GatewayController::class, 'docs'])->name('docs');
Route::get('/dashboard/{employeeId}', [GatewayController::class, 'employeeDashboard'])->name('dashboard');
Route::match(['GET', 'POST'], '/dispatch/{service}', [GatewayController::class, 'dispatch'])->name('dispatch');
