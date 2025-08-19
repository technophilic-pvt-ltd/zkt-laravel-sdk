# zkteco-laravel-sdk - A SDK to ZKTeco devices #

The `technophilic/zkteco-laravel-sdk` package provides easy to use functions to ZKTeco Device activities.

__Requires:__  **Laravel** >= **11.0**

__License:__ MIT or later

## Installation:
You can install the package via composer:

``` bash
composer require technophilic/zkteco-laravel-sdk
```

``` bash
php artisan zkteco:install
```
The package will automatically register itself.

You have to enable your php socket if it is not enabled. 


## Usage

1. Configuration

    Setup these two parameters in you .env file

```php
    ZKTECO_IP = Your Device IP

    ZKTECO_PORT = Device Port (default: 4370)
```
2. Create a object of ZKTeco class.

    
```php
    use Technophilic\ZKTecoLaravelSDK\ZKTeco;
  
    $zk = new ZKTeco();
```

3. Call ZKTeco methods

* __Connect__ 
```php
    $zk->connect();   // returns bool
```

* __Disconnect__ 
```php
    $zk->disconnect();   // returns bool
```

* __Device Version__ 
```php
    $zk->version(); 
```


* __Device Os Version__ 
```php
    $zk->osVersion(); 
```

* __Power Off__ 
```php
    $zk->shutdown(); 
```

* __Restart__ 
```php
    $zk->restart(); 
```

* __Sleep__ 
```php
    $zk->sleep(); 
```

* __Resume__ 
```php
    $zk->resume(); 
```

* __Voice Test__ 
```php
    $zk->testVoice(); 
```

* __Platform__ 
```php
    $zk->platform(); 
```

* __Serial Number__ 
```php
    $zk->serialNumber(); 
```

* __Device Name__ 
```php
    $zk->deviceName(); 
```

* __Get Device Time__ 
```php
    $zk->getTime(); 
    
    // returns bool/mixed bool|mixed Format: "Y-m-d H:i:s"
```

* __Set Device Time__ 
```php
    $zk->setTime(); 

    // parameter string $t Format: "Y-m-d H:i:s"
```

* __Get Users__ 
```php
    $zk->getUser(); 
```

* __Set Users__ 
```php
    $zk->setUser(); 

    //    set user

    //    1 s't parameter int $uid Unique ID (max 65535)
    //    2 nd parameter int|string $userid ID in DB (same like $uid, max length = 9, only numbers - depends device setting)
    //    3 rd parameter string $name (max length = 24)
    //    4 th parameter int|string $password (max length = 8, only numbers - depends device setting)
    //    5 th parameter int $role Default Util::LEVEL_USER
    //    6 th parameter int $cardno Default 0 (max length = 10, only numbers

    //    returns bool|mixed
```

* __Clear All Admin__ 
```php
    $zk->clearAdmin(); 
```

* __Clear All Users__ 
```php
    $zk->clearAdmin(); 
```

* __Remove A User__ 
```php
    $zk->removeUser($uid); 

    //  remove a user by $uid
    //  parameter integer $uid
    //  return bool|mixed

```

* __Get Attendance Log__ 
```php
    $zk->getAttendance();   // returns array[]
```

* __Clear Attendance Log__ 
```php
    $zk->clearAttendance(); 
```

# end
