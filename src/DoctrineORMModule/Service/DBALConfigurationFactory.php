<?php

namespace DoctrineORMModule\Service;

use DoctrineORMModule\Options\Configuration as DoctrineORMModuleConfiguration;
use Interop\Container\ContainerInterface;
use RuntimeException;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Types\Type;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * DBAL Configuration ServiceManager factory
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Kyle Spraggs <theman@spiffyjr.me>
 */
class DBALConfigurationFactory implements FactoryInterface
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
     *
     * @return \Doctrine\DBAL\Configuration
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config  = new Configuration();
        $this->setupDBALConfiguration($container, $config);

        return $config;
    }

    /**
     * {@inheritDoc}
     * @return \Doctrine\DBAL\Configuration
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, \Doctrine\DBAL\Configuration::class);
    }

    /**
     * @param ContainerInterface $container
     * @param Configuration      $config
     */
    public function setupDBALConfiguration(ContainerInterface $container, Configuration $config)
    {
        $options = $this->getOptions($container);
        $config->setResultCacheImpl($container->get($options->resultCache));

        $sqlLogger = $options->sqlLogger;
        if (is_string($sqlLogger) and $container->has($sqlLogger)) {
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
     * @param  ContainerInterface $serviceLocator
     * @return mixed
     * @throws RuntimeException
     */
    public function getOptions(ContainerInterface $serviceLocator)
    {
        $options = $serviceLocator->get('config');
        $options = $options['doctrine'];
        $options = $options['configuration'][$this->name] ?? null;

        if (null === $options) {
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

    /**
     * @return string
     */
    protected function getOptionsClass()
    {
        return DoctrineORMModuleConfiguration::class;
    }
}
