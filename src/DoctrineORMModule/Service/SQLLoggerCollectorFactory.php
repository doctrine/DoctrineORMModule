<?php

namespace DoctrineORMModule\Service;

use DoctrineORMModule\Options\SQLLoggerCollectorOptions;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use RuntimeException;
use DoctrineORMModule\Collector\SQLLoggerCollector;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\DBAL\Logging\LoggerChain;

/**
 * DBAL Configuration ServiceManager factory
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class SQLLoggerCollectorFactory implements FactoryInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var $options SQLLoggerCollectorOptions */
        $options = $this->getOptions($container);

        // @todo always ask the serviceLocator instead? (add a factory?)
        if ($options->getSqlLogger()) {
            $debugStackLogger = $container->get($options->getSqlLogger());
        } else {
            $debugStackLogger = new DebugStack();
        }

        /* @var $configuration \Doctrine\ORM\Configuration */
        $configuration = $container->get($options->getConfiguration());

        if (null !== $configuration->getSQLLogger()) {
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
     * @param  ContainerInterface $serviceLocator
     * @return mixed
     * @throws RuntimeException
     */
    protected function getOptions(ContainerInterface $serviceLocator)
    {
        $options = $serviceLocator->get('config');
        $options = $options['doctrine'];
        $options = $options['sql_logger_collector'][$this->name] ?? null;

        if (null === $options) {
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
