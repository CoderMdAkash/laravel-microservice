<?php

declare(strict_types=1);

use App\Modules\Identity\Controllers\IdentityController;
use Illuminate\Support\Facades\Route;

Route::get('/', [IdentityController::class, 'index'])->name('index');
Route::get('/health', [IdentityController::class, 'health'])->name('health');
