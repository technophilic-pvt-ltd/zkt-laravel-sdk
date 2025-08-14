<?php

return [
    'default_device' => [
        'ip' => env('ZKTECO_DEVICE_IP', '192.168.1.163'),
        'port' => env('ZKTECO_DEVICE_PORT', 4370),
    ],

    'attendance_table' => env('ZKTECO_ATTENDANCE_TABLE', 'attendances'),

    'employees_table' => env('ZKTECO_EMPLOYEES_TABLE', 'employees'),

    'routes' => [
        'iclock_prefix' => 'iclock',  // Device communication routes
        'api_prefix' => 'api/zkteco', // Your API routes
    ],

    'auto_sync' => env('ZKTECO_AUTO_SYNC', false),

];
