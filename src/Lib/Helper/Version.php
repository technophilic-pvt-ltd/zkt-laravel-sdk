<?php

namespace Technophilic\Zkteco\Lib\Helper;;

use Technophilic\Zkteco\Lib\Helper\Util;
use Technophilic\Zkteco\Lib\ZKTeco;

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
