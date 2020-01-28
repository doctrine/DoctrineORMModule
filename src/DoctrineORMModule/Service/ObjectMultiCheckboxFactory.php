<?php

namespace DoctrineORMModule\Service;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Form\Element\ObjectMultiCheckbox;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\FactoryInterface;

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
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get(EntityManager::class);
        $element       = new ObjectMultiCheckbox;

        $element->getProxy()->setObjectManager($entityManager);

        return $element;
    }

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container->getServiceLocator(), ObjectMultiCheckbox::class);
    }
}
