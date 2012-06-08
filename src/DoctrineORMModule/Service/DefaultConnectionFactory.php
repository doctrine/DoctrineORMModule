<?php

namespace DoctrineORMModule\Service;

use DoctrineModule\Service\AbstractConnectionFactory;

class DefaultConnectionFactory extends AbstractConnectionFactory
{
    /**
     * Get the name of the connection as defined in 'doctrine' => 'connections' config.
     *
     * @return mixed
     */
    public function getName()
    {
        return 'orm_default';
    }
}