<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Factory that provides the `Doctrine\ORM\EntityManager` alias for `doctrine.entitymanager.orm_default`
 */
class EntityManagerAliasCompatFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @deprecated this method was introduced to allow aliasing of service `Doctrine\ORM\EntityManager`
     *             from `doctrine.entitymanager.orm_default`
     *
     * @return EntityManager
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return $container->get('doctrine.entitymanager.orm_default');
    }

    /**
     * {@inheritDoc}
     *
     * @deprecated this method was introduced to allow aliasing of service `Doctrine\ORM\EntityManager`
     *             from `doctrine.entitymanager.orm_default`
     *
     * @return EntityManager
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, EntityManager::class);
    }
}
