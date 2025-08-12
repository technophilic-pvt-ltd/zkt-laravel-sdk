<?php

namespace App\Http\Controllers\ZKTeco;  // ← Must be App\ namespace

use App\Http\Controllers\Controller;
use Technophilic\ZKTecoLaravelSDK\ZKTeco;

class ZKTecoController extends Controller
{
    protected $zkTeco;

    public function __construct(ZKTeco $zkTeco)
    {
        $this->zkTeco = $zkTeco;
    }

    public function index()
    {
        return response()->json(['message' => 'ZKTecoController function ran']);
    }


    public function getAttendance()
    {
        $attd = $this->zkTeco->getAttendance();
        dd($attd);
    }
}
