<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Form\Element\ObjectMultiCheckbox;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for {@see ObjectMultiCheckbox}
 */
class ObjectMultiCheckboxFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return ObjectMultiCheckbox
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, ?array $options = null)
    {
        $entityManager = $serviceLocator->get(EntityManager::class);
        $element       = new ObjectMultiCheckbox();

        $element->getProxy()->setObjectManager($entityManager);

        return $element;
    }

    /**
     * {@inheritDoc}
     *
     * @deprecated 4.1.0 With laminas-servicemanager v3 this method is obsolete and will be removed in 5.0.0.
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, ObjectMultiCheckbox::class);
    }
}
