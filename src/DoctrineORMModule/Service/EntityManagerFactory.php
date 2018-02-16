<?php

namespace DoctrineORMModule\Service;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Service\AbstractFactory;
use DoctrineORMModule\Options\EntityManager as DoctrineORMModuleEntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EntityManagerFactory extends AbstractFactory
{
    /**
     * {@inheritDoc}
     *
     * @return EntityManager
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $options DoctrineORMModuleEntityManager */
        $options    = $this->getOptions($container, 'entitymanager');
        $connection = $container->get($options->getConnection());
        $config     = $container->get($options->getConfiguration());

        // initializing the resolver
        // @todo should actually attach it to a fetched event manager here, and not
        //       rely on its factory code
        $container->get($options->getEntityResolver());

        return EntityManager::create($connection, $config);
    }

    /**
     * {@inheritDoc}
     * @return EntityManager
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, EntityManager::class);
    }

    /**
     * {@inheritDoc}
     */
    public function getOptionsClass()
    {
        return DoctrineORMModuleEntityManager::class;
    }
}
