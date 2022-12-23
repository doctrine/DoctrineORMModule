<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use DoctrineModule\Service\AbstractFactory;
use DoctrineORMModule\Options\DBALConnection;
use PDO;
use Psr\Container\ContainerInterface;

use function array_key_exists;
use function array_merge;
use function assert;
use function is_string;

/**
 * DBAL Connection ServiceManager factory
 */
final class DBALConnectionFactory extends AbstractFactory
{
    /**
     * {@inheritDoc}
     *
     * @param string $requestedName
     *
     * @return Connection
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, ?array $options = null)
    {
        $options = $this->getOptions($serviceLocator, 'connection');
        assert($options instanceof DBALConnection);
        $pdo = $options->getPdo();

        if (is_string($pdo)) {
            $pdo = $serviceLocator->get($pdo);
            assert($pdo instanceof PDO);
        }

        $params = [
            'driverClass'  => $options->getDriverClass(),
            'wrapperClass' => $options->getWrapperClass(),
            'pdo'          => $pdo,
        ];
        $params = array_merge($params, $options->getParams());

        if (
            array_key_exists('platform', $params)
            && is_string($params['platform'])
            && $serviceLocator->has($params['platform'])
        ) {
            $params['platform'] = $serviceLocator->get($params['platform']);
        }

        $configuration = $serviceLocator->get($options->getConfiguration());
        $eventManager  = $serviceLocator->get($options->getEventManager());

        /** @psalm-suppress InvalidArgument */
        $connection = DriverManager::getConnection($params, $configuration, $eventManager);
        foreach ($options->getDoctrineTypeMappings() as $dbType => $doctrineType) {
            $connection->getDatabasePlatform()->registerDoctrineTypeMapping($dbType, $doctrineType);
        }

        foreach ($options->getDoctrineCommentedTypes() as $type) {
            $connection->getDatabasePlatform()->markDoctrineTypeCommented(Type::getType($type));
        }

        if ($options->useSavepoints()) {
            $connection->setNestTransactionsWithSavepoints(true);
        }

        return $connection;
    }

    /**
     * Get the class name of the options associated with this factory.
     */
    public function getOptionsClass(): string
    {
        return DBALConnection::class;
    }
}
