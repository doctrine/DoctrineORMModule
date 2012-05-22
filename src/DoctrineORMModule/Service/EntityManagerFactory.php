<?php

namespace DoctrineORMModule\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EntityManagerFactory implements FactoryInterface
{
    /**
     * @var string
     */
    protected $connection;

    /**
     * @var string
     */
    protected $config;

    public function __construct($connection, $config)
    {
        $this->connection = $connection;
        $this->config     = $config;
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $connection = $serviceLocator->get($this->connection);
        $config     = $serviceLocator->get($this->config);

        return \Doctrine\ORM\EntityManager::create($connection, $config);
    }
}