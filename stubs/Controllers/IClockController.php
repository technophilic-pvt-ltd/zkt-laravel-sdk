<?php

namespace App\Http\Controllers\ZKTeco;  // ← Must be App\ namespace

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Technophilic\ZKTecoLaravelSDK\ZKTeco;
use Throwable;

class IClockController extends Controller
{
    protected $zkTeco;

    public function __construct(ZKTeco $zkTeco)
    {
        $this->zkTeco = $zkTeco;
    }

    public function index()
    {
        return response()->json(['message' => 'IClockController function ran']);
    }

    public function handshake()
    {
        $handshakeResponse =  $this->zkTeco->handleHandshake();
        return $handshakeResponse;
    }

    public function receiveRecords()
    {
        $recievedRecords = $this->zkTeco->handleRecievedRecords();
        return $recievedRecords;
    }

    public function getrequest()
    {
        return $this->zkTeco->handleGetRequest();
    }
}
