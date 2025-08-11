<?php

namespace Technophilic\ZKTecoLaravelSdk;

use Rats\Zkteco\Lib\ZKTeco;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Throwable;

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

    public function handshake(Request $request)
    {
        $data = [
            'url' => json_encode($request->all()),
            'data' => $request->getContent(),
            'sn' => $request->input('SN'),
            'option' => $request->input('option'),
        ];

        $r = "GET OPTION FROM: {$request->input('SN')}\r\n" .
            "Stamp=9999\r\n" .
            "OpStamp=" . time() . "\r\n" .
            "ErrorDelay=60\r\n" .
            "Delay=30\r\n" .
            "ResLogDay=18250\r\n" .
            "ResLogDelCount=10000\r\n" .
            "ResLogCount=50000\r\n" .
            "TransTimes=00:00;14:05\r\n" .
            "TransInterval=1\r\n" .
            "TransFlag=1111000000\r\n" .
            //  "TimeZone=7\r\n" .
            "Realtime=1\r\n" .
            "Encrypt=0";

        return $r;
    }

    public function receiveRecords(Request $request)
    {
        $content['url'] = json_encode($request->all());
        $content['data'] = $request->getContent();;
        try {
            $arr = preg_split('/\\r\\n|\\r|,|\\n/', $request->getContent());
            $tot = 0;
            //operation log
            if ($request->input('table') == "OPERLOG") {
                foreach ($arr as $rey) {
                    if (isset($rey)) {
                        $tot++;
                    }
                }
                return "OK: " . $tot;
            }
            //attendance
            foreach ($arr as $rey) {
                if (empty($rey)) {
                    continue;
                }
                $data = explode("\t", $rey);
                $q['sn'] = $request->input('SN');
                $q['table'] = $request->input('table');
                $q['stamp'] = $request->input('Stamp');
                $q['employee_id'] = $data[0];
                $q['timestamp'] = $data[1];
                $q['status1'] = $this->validateAndFormatInteger($data[2] ?? null);
                $q['status2'] = $this->validateAndFormatInteger($data[3] ?? null);
                $q['status3'] = $this->validateAndFormatInteger($data[4] ?? null);
                $q['status4'] = $this->validateAndFormatInteger($data[5] ?? null);
                $q['status5'] = $this->validateAndFormatInteger($data[6] ?? null);
                $q['created_at'] = now();
                $q['updated_at'] = now();
                Log::info("records", $q);
                $tot++;
            }
            return "OK: " . $tot;
        } catch (Throwable $e) {
            $data['error'] = $e;
            report($e);
            return "ERROR: " . $tot . "\n";
        }
    }

    public function test(Request $request)
    {
        $log['data'] = $request->getContent();
    }

    public function getrequest(Request $request)
    {
        return "OK";
    }

    private function validateAndFormatInteger($value)
    {
        return isset($value) && $value !== '' ? (int)$value : null;
    }
}
