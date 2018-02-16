<?php

namespace DoctrineORMModule\Service;

use DoctrineModule\Service\AbstractFactory;
use DoctrineORMModule\Collector\MappingCollector;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Service factory responsible for instantiating {@see \DoctrineORMModule\Collector\MappingCollector}
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class MappingCollectorFactory extends AbstractFactory
{
    /**
     * {@inheritDoc}
     *
     * @return \DoctrineORMModule\Collector\MappingCollector
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $name          = $this->getName();
        /* @var $objectManager \Doctrine\Common\Persistence\ObjectManager */
        $objectManager = $container->get('doctrine.entitymanager.' . $name);

        return new MappingCollector($objectManager->getMetadataFactory(), 'doctrine.mapping_collector.' . $name);
    }

    /**
     * {@inheritDoc}
     *
     * @return \DoctrineORMModule\Collector\MappingCollector
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, \DoctrineORMModule\Collector\MappingCollector::class);
    }

    /**
     * {@inheritDoc}
     */
    public function getOptionsClass()
    {
    }
}
