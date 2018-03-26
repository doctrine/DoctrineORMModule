<?php

namespace DoctrineORMModule\Service;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use DoctrineModule\Service\AbstractFactory;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Doctrine\DBAL\Migrations\OutputWriter;

/**
 * DBAL Connection ServiceManager factory
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class MigrationsConfigurationFactory extends AbstractFactory
{
    /**
     * {@inheritDoc}
     *
     * @return \Doctrine\DBAL\Migrations\Configuration\Configuration
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $name             = $this->getName();
        /* @var $connection \Doctrine\DBAL\Connection */
        $connection       = $container->get('doctrine.connection.' . $name);
        $appConfig        = $container->get('config');
        $migrationsConfig = $appConfig['doctrine']['migrations_configuration'][$name];

        $output = new ConsoleOutput();
        $writer = new OutputWriter(function ($message) use ($output) {
            return $output->writeln($message);
        });

        $configuration = new Configuration($connection, $writer);

        $configuration->setName($migrationsConfig['name']);
        $configuration->setMigrationsDirectory($migrationsConfig['directory']);
        $configuration->setMigrationsNamespace($migrationsConfig['namespace']);
        $configuration->setMigrationsTableName($migrationsConfig['table']);
        $configuration->registerMigrationsFromDirectory($migrationsConfig['directory']);

        if (method_exists($configuration, 'setMigrationsColumnName')) {
            $configuration->setMigrationsColumnName($migrationsConfig['column']);
        }

        if (isset($migrationsConfig['custom_template']) && method_exists($configuration, 'setCustomTemplate')) {
            $configuration->setCustomTemplate($migrationsConfig['custom_template']);
        }

        return $configuration;
    }

    /**
     * {@inheritDoc}
     *
     * @return \Doctrine\DBAL\Migrations\Configuration\Configuration
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, \Doctrine\DBAL\Migrations\Configuration\Configuration::class);
    }

    /**
     * {@inheritDoc}
     */
    public function getOptionsClass()
    {
    }
}
