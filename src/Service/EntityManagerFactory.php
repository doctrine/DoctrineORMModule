<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Service\AbstractFactory;
use DoctrineORMModule\Options\EntityManager as DoctrineORMModuleEntityManager;
use Interop\Container\ContainerInterface;

use function assert;

class EntityManagerFactory extends AbstractFactory
{
    /**
     * {@inheritDoc}
     *
     * @return EntityManager
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $options = $this->getOptions($container, 'entitymanager');
        assert($options instanceof DoctrineORMModuleEntityManager);
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
     *
     * @deprecated 4.1.0 With laminas-servicemanager v3 this method is obsolete and will be removed in 5.0.0.
     *
     * @return EntityManager
     */
    public function createService(ContainerInterface $container)
    {
        return $this($container, EntityManager::class);
    }

    public function getOptionsClass(): string
    {
        return DoctrineORMModuleEntityManager::class;
    }
}
