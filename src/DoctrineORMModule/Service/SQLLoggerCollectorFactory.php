<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\DBAL\Logging\LoggerChain;
use DoctrineORMModule\Collector\SQLLoggerCollector;
use DoctrineORMModule\Options\SQLLoggerCollectorOptions;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use RuntimeException;
use function sprintf;

/**
 * DBAL Configuration ServiceManager factory
 */
class SQLLoggerCollectorFactory implements FactoryInterface
{
    /** @var string */
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $options = $this->getOptions($container);

        // @todo always ask the serviceLocator instead? (add a factory?)
        if ($options->getSqlLogger()) {
            $debugStackLogger = $container->get($options->getSqlLogger());
        } else {
            $debugStackLogger = new DebugStack();
        }

        $configuration = $container->get($options->getConfiguration());

        if ($configuration->getSQLLogger() !== null) {
            $logger = new LoggerChain();
            $logger->addLogger($debugStackLogger);
            $logger->addLogger($configuration->getSQLLogger());
            $configuration->setSQLLogger($logger);
        } else {
            $configuration->setSQLLogger($debugStackLogger);
        }

        return new SQLLoggerCollector($debugStackLogger, 'doctrine.sql_logger_collector.' . $options->getName());
    }

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, SQLLoggerCollector::class);
    }

    /**
     * @return mixed
     *
     * @throws RuntimeException
     */
    protected function getOptions(ContainerInterface $serviceLocator)
    {
        $options = $serviceLocator->get('config');
        $options = $options['doctrine'];
        $options = $options['sql_logger_collector'][$this->name] ?? null;

        if ($options === null) {
            throw new RuntimeException(
                sprintf(
                    'Configuration with name "%s" could not be found in "doctrine.sql_logger_collector".',
                    $this->name
                )
            );
        }

        $optionsClass = $this->getOptionsClass();

        return new $optionsClass($options);
    }

    /**
     * {@inheritDoc}
     */
    protected function getOptionsClass()
    {
        return SQLLoggerCollectorOptions::class;
    }
}
