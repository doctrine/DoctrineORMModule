<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Form\Element\ObjectSelect;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for {@see ObjectSelect}
 */
class ObjectSelectFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return ObjectSelect
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $entityManager = $container->get(EntityManager::class);
        $element       = new ObjectSelect();

        $element->getProxy()->setObjectManager($entityManager);

        return $element;
    }

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container->getServiceLocator(), ObjectSelect::class);
    }
}
