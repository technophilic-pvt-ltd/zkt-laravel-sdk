<?php

use App\Http\Controllers\ZkController;
use Illuminate\Support\Facades\Route;

Route::get('/test', [ZkController::class, 'test']);
