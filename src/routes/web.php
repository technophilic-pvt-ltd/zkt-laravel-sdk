<?php

use Illuminate\Support\Facades\Route;
use Technophilic\ZktLaravelSdk\Http\Controllers\ZkController;

Route::get('/test', [ZkController::class, 'test']);
