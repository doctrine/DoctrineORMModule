<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\DBAL\Logging\LoggerChain;
use DoctrineORMModule\Collector\SQLLoggerCollector;
use DoctrineORMModule\Options\SQLLoggerCollectorOptions;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use RuntimeException;

use function sprintf;

/**
 * DBAL Configuration ServiceManager factory
 */
final class SQLLoggerCollectorFactory implements FactoryInterface
{
    protected string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, ?array $options = null)
    {
        $options = $this->getOptions($serviceLocator);

        // @todo always ask the serviceLocator instead? (add a factory?)
        if ($options->getSqlLogger()) {
            $debugStackLogger = $serviceLocator->get($options->getSqlLogger());
        } else {
            $debugStackLogger = new DebugStack();
        }

        $configuration = $serviceLocator->get($options->getConfiguration());

        if ($configuration->getSQLLogger() !== null) {
            $logger = new LoggerChain([
                $debugStackLogger,
                $configuration->getSQLLogger(),
            ]);
            $configuration->setSQLLogger($logger);
        } else {
            $configuration->setSQLLogger($debugStackLogger);
        }

        return new SQLLoggerCollector($debugStackLogger, 'doctrine.sql_logger_collector.' . $options->getName());
    }

    /**
     * @throws RuntimeException
     */
    protected function getOptions(ContainerInterface $serviceLocator): mixed
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
     * @psalm-return class-string
     */
    protected function getOptionsClass(): string
    {
        return SQLLoggerCollectorOptions::class;
    }
}
