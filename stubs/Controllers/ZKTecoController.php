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

    public function getUsers()
    {
        $users = $this->zkTeco->getUser();
        dd($users);
    }

    public function unlockDoor()
    {
        return $this->zkTeco->unlockDoor();
    }

    public function testVoice()
    {
        return $this->zkTeco->testVoice();
    }

    public function restart()
    {
        return $this->zkTeco->restart();
    }

    public function removeUser($uid)
    {
        return $this->zkTeco->removeUser($uid);
    }

    public function message()
    {
        return $this->zkTeco->writeLCD();
    }
}
