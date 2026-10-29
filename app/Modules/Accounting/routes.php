<?php

declare(strict_types=1);

use App\Modules\Accounting\Controllers\AccountingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AccountingController::class, 'index'])->name('index');
Route::get('/health', [AccountingController::class, 'health'])->name('health');
