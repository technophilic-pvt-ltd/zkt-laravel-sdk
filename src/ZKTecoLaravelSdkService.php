<?php

namespace Technophilic\ZKTecoLaravelSdk;

use Rats\Zkteco\Lib\ZKTeco;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ZKTecoLaravelSdkService
{
    protected $zk;

    public function __construct($ip, $port)
    {
        $this->zk = new ZKTeco($ip, $port);
        $this->connect();
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    // Proxy existing ZKTeco methods
    public function connect()
    {
        return $this->zk->connect();
    }

    public function disconnect()
    {
        return $this->zk->disconnect();
    }

    public function addUser($uid, $userid, $name, $password, $role = 0, $cardno = 0)
    {
        return $this->zk->setUser($uid, $userid, $name, $password, $role, $cardno);
    }

    public function getUsers()
    {
        return $this->zk->getUser();
    }

    public function getAttendance()
    {
        return $this->zk->getAttendance();
    }

    // Custom functionality: Sync ZKTeco users to Laravel users table
    public function syncUsersToDatabase()
    {
        $this->connect();
        $zkUsers = $this->getUsers();
        $this->disconnect();

        foreach ($zkUsers as $zkUser) {
            User::updateOrCreate(
                ['zkteco_userid' => $zkUser['userid']],
                [
                    'name' => $zkUser['name'],
                    'email' => $zkUser['userid'] . '@zkteco.local', // Placeholder email
                    'password' => Hash::make($zkUser['password'] ?? 'default123'),
                    'zkteco_uid' => $zkUser['uid'],
                    'zkteco_role' => $zkUser['role'],
                    'zkteco_cardno' => $zkUser['cardno'] ?? 0,
                ]
            );
        }

        return count($zkUsers) . ' users synced to database.';
    }

    public function test()
    {
        return "test";
    }
}
