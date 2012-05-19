<?php

namespace DoctrineORMModule\ModuleManager\Feature;

use Doctrine\DBAL\Configuration;

interface DoctrineDriverProviderInterface
{
    /**
     * Expected to return an array of drivers to be added to the Doctrine DriverChain.
     *
     * @return array
     */
    public function getDoctrineDrivers(Configuration $config);
}
