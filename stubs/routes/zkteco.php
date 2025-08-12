<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZKTeco\ZKTecoController;

Route::get('/test', [ZKTecoController::class, 'index'])->name('index');
Route::get('/attd', [ZKTecoController::class, 'getAttendance'])->name('attd');
