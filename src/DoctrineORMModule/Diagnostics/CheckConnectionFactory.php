<?php

namespace DoctrineORMModule\Diagnostics;

use Doctrine\DBAL\Connection;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CheckConnectionFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return CheckConnection
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var Connection $connection */
        $connection = $serviceLocator->get('doctrine.connection.orm_default');
        
        return new CheckConnection($connection);
    }
}
