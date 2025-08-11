<?php

namespace Technophilic\ZktLaravelSdk\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ZkController extends Controller
{
    public function test()
    {
        return response()->json(['message' => 'ZK Controller loaded successfully']);
    }
}
