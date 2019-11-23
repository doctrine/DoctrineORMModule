<?php

namespace DoctrineORMModule\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use DoctrineModule\Service\AbstractFactory;
use DoctrineORMModule\Options\DBALConnection;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * DBAL Connection ServiceManager factory
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Kyle Spraggs <theman@spiffyjr.me>
 */
class DBALConnectionFactory extends AbstractFactory
{
    /**
     * {@inheritDoc}
     *
     * @return Connection
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var $options DBALConnection */
        $options = $this->getOptions($container, 'connection');
        $pdo     = $options->getPdo();

        if (is_string($pdo)) {
            $pdo = $container->get($pdo);
        }

        $params = [
            'driverClass'  => $options->getDriverClass(),
            'wrapperClass' => $options->getWrapperClass(),
            'pdo'          => $pdo,
        ];
        $params = array_merge($params, $options->getParams());

        if (array_key_exists('platform', $params)
            && is_string($params['platform'])
            && $container->has($params['platform'])
        ) {
            $params['platform'] = $container->get($params['platform']);
        }

        $configuration = $container->get($options->getConfiguration());
        $eventManager  = $container->get($options->getEventManager());

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
     * {@inheritDoc}
     * @return Connection
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, Connection::class);
    }

    /**
     * Get the class name of the options associated with this factory.
     *
     * @return string
     */
    public function getOptionsClass()
    {
        return DBALConnection::class;
    }
}
