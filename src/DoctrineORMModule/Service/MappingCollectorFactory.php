<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use DoctrineModule\Service\AbstractFactory;
use DoctrineORMModule\Collector\MappingCollector;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Service factory responsible for instantiating {@see \DoctrineORMModule\Collector\MappingCollector}
 */
class MappingCollectorFactory extends AbstractFactory
{
    /**
     * {@inheritDoc}
     *
     * @return MappingCollector
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $name          = $this->getName();
        $objectManager = $container->get('doctrine.entitymanager.' . $name);

        return new MappingCollector($objectManager->getMetadataFactory(), 'doctrine.mapping_collector.' . $name);
    }

    /**
     * {@inheritDoc}
     *
     * @return MappingCollector
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, MappingCollector::class);
    }

    public function getOptionsClass() : string
    {
    }
}
