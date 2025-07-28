<?php

namespace Technophilic\ZKTecoLaravelSdk;

use Illuminate\Support\Facades\Facade;

class ZKTecoLaravelSdkFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'zkteco-laravel-sdk';
    }
}
