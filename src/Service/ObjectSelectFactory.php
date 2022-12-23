<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Form\Element\ObjectSelect;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

/**
 * Factory for {@see ObjectSelect}
 */
final class ObjectSelectFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @param string $requestedName
     *
     * @return ObjectSelect
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, ?array $options = null)
    {
        $entityManager = $serviceLocator->get(EntityManager::class);
        $element       = new ObjectSelect();

        $element->getProxy()->setObjectManager($entityManager);

        return $element;
    }
}
