<?php

namespace Devprom\CommonBundle\Doctrine\DBAL\Driver\PDOMySql;

class Driver extends \Doctrine\DBAL\Driver\PDOMySql\Driver
{

    public function getReconnectExceptions()
    {
        return array(
            'MySQL server has gone away',
        );
    }

}