<?php

namespace Technophilic\ZKTecoLaravelSDK\Helper;

use Technophilic\ZKTecoLaravelSDK\ZKTeco;

class Door
{
    /**
     * @param ZKTeco $self
     * @return bool|mixed
     */
    static public function unlock(ZKTeco $self, int $time = 3)
    {
        $self->_section = __METHOD__;

        $command = Util::CMD_DOOR_UNLOCK;
        $command_string = pack('I', $time * 10); // Pack time as 32-bit unsigned integer

        return $self->_command($command, $command_string);
    }
}
