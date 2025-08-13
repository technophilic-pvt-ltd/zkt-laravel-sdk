<?php

namespace Technophilic\ZKTecoLaravelSDK;

use ErrorException;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Technophilic\ZKTecoLaravelSDK\Helper\Attendance;
use Technophilic\ZKTecoLaravelSDK\Helper\Device;
use Technophilic\ZKTecoLaravelSDK\Helper\Face;
use Technophilic\ZKTecoLaravelSDK\Helper\Fingerprint;
use Technophilic\ZKTecoLaravelSDK\Helper\Os;
use Technophilic\ZKTecoLaravelSDK\Helper\Pin;
use Technophilic\ZKTecoLaravelSDK\Helper\Platform;
use Technophilic\ZKTecoLaravelSDK\Helper\SerialNumber;
use Technophilic\ZKTecoLaravelSDK\Helper\Ssr;
use Technophilic\ZKTecoLaravelSDK\Helper\Time;
use Technophilic\ZKTecoLaravelSDK\Helper\User;
use Technophilic\ZKTecoLaravelSDK\Helper\Util;
use Technophilic\ZKTecoLaravelSDK\Helper\Connect;
use Technophilic\ZKTecoLaravelSDK\Helper\Door;
use Technophilic\ZKTecoLaravelSDK\Helper\Version;
use Technophilic\ZKTecoLaravelSDK\Helper\WorkCode;
use Throwable;

class ZKTeco
{
    public $_ip;
    public $_port;
    public $_zkclient;

    public $_data_recv = '';
    public $_session_id = 0;
    public $_section = '';

    /**
     * ZKLib constructor.
     * @param string $ip Device IP
     * @param integer $port Default: 4370
     */
    public function __construct()
    {

        $this->_ip = env('ZKTECO_IP', '192.168.1.163');
        $this->_port = env('ZKTECO_PORT', 4370);

        $this->_zkclient = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

        $timeout = array('sec' => 60, 'usec' => 500000);
        socket_set_option($this->_zkclient, SOL_SOCKET, SO_RCVTIMEO, $timeout);

        $this->connect();
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Create and send command to device
     *
     * @param string $command
     * @param string $command_string
     * @param string $type
     * @return bool|mixed
     */
    public function _command($command, $command_string, $type = Util::COMMAND_TYPE_GENERAL)
    {
        $chksum = 0;
        $session_id = $this->_session_id;

        $u = unpack('H2h1/H2h2/H2h3/H2h4/H2h5/H2h6/H2h7/H2h8', substr($this->_data_recv, 0, 8));
        $reply_id = hexdec($u['h8'] . $u['h7']);

        $buf = Util::createHeader($command, $chksum, $session_id, $reply_id, $command_string);

        socket_sendto($this->_zkclient, $buf, strlen($buf), 0, $this->_ip, $this->_port);

        try {
            @socket_recvfrom($this->_zkclient, $this->_data_recv, 1024, 0, $this->_ip, $this->_port);

            $u = unpack('H2h1/H2h2/H2h3/H2h4/H2h5/H2h6', substr($this->_data_recv, 0, 8));

            $ret = false;
            $session = hexdec($u['h6'] . $u['h5']);

            if ($type === Util::COMMAND_TYPE_GENERAL && $session_id === $session) {
                $ret = substr($this->_data_recv, 8);
            } else if ($type === Util::COMMAND_TYPE_DATA && !empty($session)) {
                $ret = $session;
            }

            return $ret;
        } catch (ErrorException $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Connect to device
     *
     * @return bool
     */
    public function connect()
    {
        return Connect::connect($this);
    }

    /**
     * Disconnect from device
     *
     * @return bool
     */
    public function disconnect()
    {
        return Connect::disconnect($this);
    }

    /**
     * Get device version
     *
     * @return bool|mixed
     */
    public function version()
    {
        return Version::get($this);
    }

    /**
     * Get OS version
     *
     * @return bool|mixed
     */
    public function osVersion()
    {
        return Os::get($this);
    }

    /**
     * Get platform
     *
     * @return bool|mixed
     */
    public function platform()
    {
        return Platform::get($this);
    }

    /**
     * Get firmware version
     *
     * @return bool|mixed
     */
    public function fmVersion()
    {
        return Platform::getVersion($this);
    }

    /**
     * Get work code
     *
     * @return bool|mixed
     */
    public function workCode()
    {
        return WorkCode::get($this);
    }

    /**
     * Get SSR
     *
     * @return bool|mixed
     */
    public function ssr()
    {
        return Ssr::get($this);
    }

    /**
     * Get pin width
     *
     * @return bool|mixed
     */
    public function pinWidth()
    {
        return Pin::width($this);
    }

    /**
     * @return bool|mixed
     */
    public function faceFunctionOn()
    {
        return Face::on($this);
    }

    /**
     * Get device serial number
     *
     * @return bool|mixed
     */
    public function serialNumber()
    {
        return SerialNumber::get($this);
    }

    /**
     * Get device name
     *
     * @return bool|mixed
     */
    public function deviceName()
    {
        return Device::name($this);
    }

    /**
     * Disable device
     *
     * @return bool|mixed
     */
    public function disableDevice()
    {
        return Device::disable($this);
    }

    /**
     * Enable device
     *
     * @return bool|mixed
     */
    public function enableDevice()
    {
        return Device::enable($this);
    }

    /**
     * Get users data
     *
     * @return array [userid, name, cardno, uid, role, password]
     */
    public function getUser()
    {
        return User::get($this);
    }

    /**
     * Set user data
     *
     * @param int $uid Unique ID (max 65535)
     * @param int|string $userid ID in DB (same like $uid, max length = 9, only numbers - depends device setting)
     * @param string $name (max length = 24)
     * @param int|string $password (max length = 8, only numbers - depends device setting)
     * @param int $role Default Util::LEVEL_USER
     * @param int $cardno Default 0 (max length = 10, only numbers)
     * @return bool|mixed
     */
    public function setUser($uid, $userid, $name, $password, $role = Util::LEVEL_USER, $cardno = 0)
    {
        return User::set($this, $uid, $userid, $name, $password, $role, $cardno);
    }



    /**
     * Remove All users
     *
     * @return bool|mixed
     */
    public function clearUsers()
    {
        return User::clear($this);
    }

    /**
     * Remove admin
     *
     * @return bool|mixed
     */
    public function clearAdmin()
    {
        return User::clearAdmin($this);
    }

    /**
     * Remove user by UID
     *
     * @param integer $uid
     * @return bool|mixed
     */
    public function removeUser($uid)
    {
        return User::remove($this, $uid);
    }



    /**
     * Get fingerprint data array by UID
     * TODO: Can get data, but don't know how to parse the data. Need more documentation about it...
     *
     * @param integer $uid Unique ID (max 65535)
     * @return array Binary fingerprint data array (where key is finger ID (0-9))
     */
    public function getFingerprint($uid)
    {
        return Fingerprint::get($this, $uid);
    }

    /**
     * Set fingerprint data array
     * TODO: Still can not set fingerprint. Need more documentation about it...
     *
     * @param integer $uid Unique ID (max 65535)
     * @param array $data Binary fingerprint data array (where key is finger ID (0-9) same like returned array from 'getFingerprint' method)
     * @return int Count of added fingerprints
     */
    public function setFingerprint($uid, array $data)
    {
        return Fingerprint::set($this, $uid, $data);
    }

    /**
     * Remove fingerprint by UID and fingers ID array
     *
     * @param integer $uid Unique ID (max 65535)
     * @param array $data Fingers ID array (0-9)
     * @return int Count of deleted fingerprints
     */
    public function removeFingerprint($uid, array $data)
    {
        return Fingerprint::remove($this, $uid, $data);
    }


    /**
     * Get attendance log
     *
     * @return array [uid, id, state, timestamp]
     */
    public function getAttendance()
    {
        return Attendance::get($this);
    }

    /**
     * Clear attendance log
     *
     * @return bool|mixed
     */
    public function clearAttendance()
    {
        return Attendance::clear($this);
    }

    /**
     * Set device time
     *
     * @param string $t Format: "Y-m-d H:i:s"
     * @return bool|mixed
     */
    public function setTime($t)
    {
        return Time::set($this, $t);
    }

    /**
     * Get device time
     *
     * @return bool|mixed Format: "Y-m-d H:i:s"
     */
    public function getTime()
    {
        return Time::get($this);
    }


    /**
     * turn off the device
     *
     * @return bool|mixed
     */
    public function shutdown()
    {
        return Device::powerOff($this);
    }

    /**
     * restart the device
     *
     * @return bool|mixed
     */
    public function restart()
    {
        return Device::restart($this);
    }


    /**
     * make sleep mood the device
     *
     * @return bool|mixed
     */
    public function sleep()
    {
        return Device::sleep($this);
    }


    /**
     * resume the device from sleep
     *
     * @return bool|mixed
     */
    public function resume()
    {
        return Device::resume($this);
    }


    /**
     * voice test Sound will "Thank you"
     *
     * @return bool|mixed
     */
    public function testVoice()
    {
        return Device::testVoice($this);
    }


    public function clearLCD()
    {
        return Device::clearLCD($this);
    }

    public function writeLCD()
    {
        return Device::writeLCD($this, 2, "Message for the Team");
    }

    public function unlockDoor()
    {
        return Door::unlock($this);
    }

    public function handleHandshake()
    {
        $request = Request::capture();
        $deviceSn = $request->input('SN');

        $handshakeResponse = "GET OPTION FROM: {$deviceSn}\r\n" .
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
            "Realtime=1\r\n" .
            "Encrypt=0";

        Log::info('Handshake response for device', ['device_sn' => $deviceSn]);

        return $handshakeResponse;
    }

    public function handleGetRequest()
    {
        Log::info('get request function ran');
        return "OK";
    }

    public function handleRecievedRecords()
    {
        try {
            $request = Request::capture();
            $recordsContent = preg_split('/\\r\\n|\\r|,|\\n/', $request->getContent());
            $attendanceData = [];

            // Handle operation logs.
            if ($request->input('table') == "OPERLOG") {
                foreach ($recordsContent as $record) {
                    if (isset($record)) {
                        //do somthing
                        continue;
                    }
                }
                return "OK: ";
            }

            // Process attendance logs.
            foreach ($recordsContent as $record) {
                if (empty($record)) {
                    continue;
                }
                Log::info('record', ['record' => $record]);
                $recordFields = explode("\t", $record);
                Log::info('record fields', ['record_fields' => $recordFields]);

                $attendanceData = [
                    'sn' => $request->input('SN'),
                    'table' => $request->input('table'),
                    'stamp' => $request->input('Stamp'),
                    'employee_id' => $recordFields[0] ?? null,
                    'time_stamp' => $recordFields[1] ?? null,
                    'status1' => $recordFields[2] ?? null,
                    'status2' => $recordFields[3] ?? null,
                    'status3' => $recordFields[4] ?? null,
                    'status4' => $recordFields[5] ?? null,
                    'status5' => $recordFields[6] ?? null,
                ];
            }
            Log::info('Final attendance data output', $attendanceData);
            return "OK: " . json_encode($attendanceData);
        } catch (Throwable $e) {
            report($e);
            return "ERROR: " . $e . "\n";
        }
    }

    public function index()
    {
        echo "service class ran";
    }
}
