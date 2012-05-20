<?php

namespace DoctrineORMModule\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EntityManagerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sl)
    {
        $connection = $sl->get('Configuration')->doctrine_orm_connection;
        $ormConfig  = $sl->get('Doctrine\ORM\Configuration');

        return \Doctrine\ORM\EntityManager::create($connection->toArray(), $ormConfig);
    }
}