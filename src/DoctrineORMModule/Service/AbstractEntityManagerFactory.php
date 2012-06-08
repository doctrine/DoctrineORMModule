<?php

namespace DoctrineORMModule\Service;

use RuntimeException;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractEntityManagerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $name = $this->getName();
        $cfg  = $serviceLocator->get('Configuration');
        $cfg  = isset($cfg['doctrine']['orm']['entitymanager'][$name]) ?
            $cfg['doctrine']['orm']['entitymanager'][$name] :
            null;

        if (null === $cfg) {
            throw new RuntimeException(sprintf(
                'EntityManager with name "%s" could not be found in configuration.',
                $name
            ));
        }

        $connection = $serviceLocator->get($cfg['connection']);
        $config     = $serviceLocator->get($cfg['configuration']);

        return EntityManager::create($connection, $config);
    }

    abstract public function getName();
}