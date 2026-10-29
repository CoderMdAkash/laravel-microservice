<?php

declare(strict_types=1);

use App\Modules\Inventory\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [InventoryController::class, 'index'])->name('index');
Route::get('/health', [InventoryController::class, 'health'])->name('health');
