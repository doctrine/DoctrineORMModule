<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Types\Type;
use DoctrineORMModule\Options\Configuration as DoctrineORMModuleConfiguration;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use RuntimeException;

use function is_string;
use function sprintf;

/**
 * DBAL Configuration ServiceManager factory
 */
class DBALConfigurationFactory implements FactoryInterface
{
    protected string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     *
     * @return Configuration
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, ?array $options = null)
    {
        $config = new Configuration();
        $this->setupDBALConfiguration($serviceLocator, $config);

        return $config;
    }

    public function setupDBALConfiguration(ContainerInterface $serviceLocator, Configuration $config): void
    {
        $options = $this->getOptions($serviceLocator);
        $config->setResultCacheImpl($serviceLocator->get($options->resultCache));

        $sqlLogger = $options->sqlLogger;
        if (is_string($sqlLogger) && $serviceLocator->has($sqlLogger)) {
            $sqlLogger = $serviceLocator->get($sqlLogger);
        }

        $config->setSQLLogger($sqlLogger);

        foreach ($options->types as $name => $class) {
            if (Type::hasType($name)) {
                Type::overrideType($name, $class);
            } else {
                Type::addType($name, $class);
            }
        }
    }

    /**
     * @return mixed
     *
     * @throws RuntimeException
     */
    public function getOptions(ContainerInterface $serviceLocator)
    {
        $options = $serviceLocator->get('config');
        $options = $options['doctrine'];
        $options = $options['configuration'][$this->name] ?? null;

        if ($options === null) {
            throw new RuntimeException(
                sprintf(
                    'Configuration with name "%s" could not be found in "doctrine.configuration".',
                    $this->name
                )
            );
        }

        $optionsClass = $this->getOptionsClass();

        return new $optionsClass($options);
    }

    protected function getOptionsClass(): string
    {
        return DoctrineORMModuleConfiguration::class;
    }
}
