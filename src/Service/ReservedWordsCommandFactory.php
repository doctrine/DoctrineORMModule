<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\DBAL\Tools\Console\Command\ReservedWordsCommand;
use Doctrine\DBAL\Tools\Console\ConnectionProvider\SingleConnectionProvider;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

use function class_exists;

class ReservedWordsCommandFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, ?array $options = null)
    {
        if (class_exists(SingleConnectionProvider::class)) {
            return new ReservedWordsCommand(
                new SingleConnectionProvider($serviceLocator->get('doctrine.connection.orm_default'))
            );
        }

        /** @psalm-suppress TooFewArguments */
        return new ReservedWordsCommand();
    }

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, ReservedWordsCommand::class);
    }
}
