<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\Laminas\Hydrator\DoctrineObject;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

final class DoctrineObjectHydratorFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, ?array $options = null)
    {
        return new DoctrineObject($serviceLocator->get('doctrine.entitymanager.orm_default'));
    }
}
