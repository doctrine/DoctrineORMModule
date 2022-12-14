<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Service\AbstractFactory;
use DoctrineORMModule\Options\EntityManager as DoctrineORMModuleEntityManager;
use Psr\Container\ContainerInterface;

use function assert;

final class EntityManagerFactory extends AbstractFactory
{
    /**
     * {@inheritDoc}
     *
     * @param string $requestedName
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

    public function getOptionsClass(): string
    {
        return DoctrineORMModuleEntityManager::class;
    }
}
