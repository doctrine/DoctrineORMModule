<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\OutputWriter;
use DoctrineModule\Service\AbstractFactory;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use function method_exists;

/**
 * DBAL Connection ServiceManager factory
 */
class MigrationsConfigurationFactory extends AbstractFactory
{
    /**
     * {@inheritDoc}
     *
     * @return Configuration
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $name             = $this->getName();
        $connection       = $container->get('doctrine.connection.' . $name);
        $appConfig        = $container->get('config');
        $migrationsConfig = $appConfig['doctrine']['migrations_configuration'][$name];

        $configuration = new Configuration($connection);

        $output         = new ConsoleOutput();
        $writerCallback = static function ($message) use ($output) : void {
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

    public function getOptionsClass() : string
    {
    }
}
