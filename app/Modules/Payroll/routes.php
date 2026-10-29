<?php

declare(strict_types=1);

use App\Modules\Payroll\Controllers\PayrollController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PayrollController::class, 'index'])->name('index');
Route::get('/health', [PayrollController::class, 'health'])->name('health');
