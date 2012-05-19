<?php

namespace DoctrineORMModule\Service;

use RuntimeException;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EntityManagerFactory implements FactoryInterface
{
    /**
     * @var string
     */
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $doctrine = $serviceLocator->get('Configuration');
        $doctrine = $doctrine['doctrine'];
        $config   = isset($doctrine['entitymanager'][$this->name]) ? $doctrine['entitymanager'][$this->name] : null;

        if (null === $config) {
            throw new RuntimeException(sprintf(
                'EntityManager with name "%s" could not be found in "doctrine.entitymanager".',
                $this->name
            ));
        }

        $connection = $serviceLocator->get("doctrine.connection.{$config['connection']}");
        $config     = $serviceLocator->get("doctrine.configuration.{$config['configuration']}");

        return EntityManager::create($connection, $config);
    }
}