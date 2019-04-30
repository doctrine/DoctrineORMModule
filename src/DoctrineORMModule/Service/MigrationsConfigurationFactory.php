<?php

namespace DoctrineORMModule\Service;

use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\OutputWriter;
use DoctrineModule\Service\AbstractFactory;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

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
     * @return Configuration
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $name             = $this->getName();
        /* @var $connection \Doctrine\DBAL\Connection */
        $connection       = $container->get('doctrine.connection.' . $name);
        $appConfig        = $container->get('config');
        $migrationsConfig = $appConfig['doctrine']['migrations_configuration'][$name];

        $configuration = new Configuration($connection);

        $output = new ConsoleOutput();
        $writerCallback = static function ($message) use ($output) {
            $output->writeln($message);
        };

        $outputWriter = $configuration->getOutputWriter();
        if (method_exists($outputWriter, 'setCallback')) {
            $outputWriter->setCallback($writerCallback);
        } else {
            // Fallback for doctrine-migrations v1.x
            $configuration->setOutputWriter(new OutputWriter($writerCallback));
        }

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
     * @return Configuration
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, Configuration::class);
    }

    /**
     * {@inheritDoc}
     */
    public function getOptionsClass()
    {
    }
}
