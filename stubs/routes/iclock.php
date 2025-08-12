<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZKTeco\IClockController;

Route::get('/iclock', [IClockController::class, 'index'])->name('index');

Route::get('/iclock/cdata', [IClockController::class, 'handshake']);

Route::post('/iclock/cdata', [IClockController::class, 'receiveRecords']);

Route::get('/iclock/getrequest', [IClockController::class, 'getrequest']);
