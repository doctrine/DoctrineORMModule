<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Types\Type;
use DoctrineORMModule\Options\Configuration as DoctrineORMModuleConfiguration;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use RuntimeException;
use function is_string;
use function sprintf;

/**
 * DBAL Configuration ServiceManager factory
 */
class DBALConfigurationFactory implements FactoryInterface
{
    /** @var string */
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     *
     * @return Configuration
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $config = new Configuration();
        $this->setupDBALConfiguration($container, $config);

        return $config;
    }

    /**
     * {@inheritDoc}
     *
     * @return Configuration
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, Configuration::class);
    }

    public function setupDBALConfiguration(ContainerInterface $container, Configuration $config) : void
    {
        $options = $this->getOptions($container);
        $config->setResultCacheImpl($container->get($options->resultCache));

        $sqlLogger = $options->sqlLogger;
        if (is_string($sqlLogger) && $container->has($sqlLogger)) {
            $sqlLogger = $container->get($sqlLogger);
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

    protected function getOptionsClass() : string
    {
        return DoctrineORMModuleConfiguration::class;
    }
}
