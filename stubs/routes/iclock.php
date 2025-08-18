<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZKTeco\IClockController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::get('/iclock', [IClockController::class, 'index'])->name('index')->withoutMiddleware(VerifyCsrfToken::class);

Route::get('/iclock/cdata', [IClockController::class, 'handshake'])->withoutMiddleware(VerifyCsrfToken::class);

Route::post('/iclock/cdata', [IClockController::class, 'receiveRecords'])->withoutMiddleware(VerifyCsrfToken::class);

Route::get('/iclock/getrequest', [IClockController::class, 'getrequest'])->withoutMiddleware(VerifyCsrfToken::class);
