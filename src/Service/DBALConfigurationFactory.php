<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Driver\Middleware;
use Doctrine\DBAL\Types\Type;
use DoctrineORMModule\Options\Configuration as DoctrineORMModuleConfiguration;
use InvalidArgumentException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use RuntimeException;
use UnexpectedValueException;

use function is_string;
use function method_exists;
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
     * @param string $requestedName
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

        if (method_exists($config, 'setMiddlewares')) {
            $middlewares = [];
            foreach ($options->middlewares as $middlewareName) {
                if (! is_string($middlewareName) || ! $serviceLocator->has($middlewareName)) {
                    throw new InvalidArgumentException('Middleware not exists');
                }

                $middleware = $serviceLocator->get($middlewareName);
                if (! $middleware instanceof Middleware) {
                    throw new UnexpectedValueException(sprintf(
                        'Invalid middleware with %s name. %s expected.',
                        $middlewareName,
                        Middleware::class,
                    ));
                }

                $middlewares[] = $middleware;
            }

            $config->setMiddlewares($middlewares);
        }

        foreach ($options->types as $name => $class) {
            if (Type::hasType($name)) {
                Type::overrideType($name, $class);
            } else {
                Type::addType($name, $class);
            }
        }
    }

    /**
     * @throws RuntimeException
     */
    public function getOptions(ContainerInterface $serviceLocator): mixed
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
