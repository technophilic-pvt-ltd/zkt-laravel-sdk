<?php

namespace Technophilic\ZKTecoLaravelSDK\Helper;;

use Technophilic\Zkteco\Helper\Util;
use Technophilic\ZKTecoLaravelSDK\ZKTeco;

class Version
{
    /**
     * @param ZKTeco $self
     * @return bool|mixed
     */
    static public function get(ZKTeco $self)
    {
        $self->_section = __METHOD__;

        $command = Util::CMD_VERSION;
        $command_string = '';

        return $self->_command($command, $command_string);
    }
}
