<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Form\Element\ObjectRadio;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

/**
 * Factory for {@see ObjectRadio}
 */
final class ObjectRadioFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @param string $requestedName
     *
     * @return ObjectRadio
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, ?array $options = null)
    {
        $entityManager = $serviceLocator->get(EntityManager::class);
        $element       = new ObjectRadio();

        $element->getProxy()->setObjectManager($entityManager);

        return $element;
    }
}
