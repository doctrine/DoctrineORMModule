<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\Command\DoctrineCommand;
use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use RuntimeException;
use Symfony\Component\Console\Input\ArgvInput;

use function array_key_exists;
use function class_exists;
use function preg_match;
use function ucfirst;

/**
 * Service factory for migrations command
 */
class MigrationsCommandFactory implements FactoryInterface
{
    /** @var string */
    private $commandClassName;

    /** @var string */
    private $defaultObjectManagerName = 'doctrine.entitymanager.orm_default';

    public function __construct(string $name)
    {
        // The configuration of migrations looses the case of the command name so mixed
        // case names must be resolved here.
        switch ($name) {
            case 'dumpschema':
                $name = 'DumpSchema';
                break;
            case 'syncmetadatastorage':
                $name = 'SyncMetadata';
                break;
            case 'uptodate':
                $name = 'UpToDate';
                break;
            default:
                $name = ucfirst($name);
                break;
        }

        $this->commandClassName = 'Doctrine\Migrations\Tools\Console\Command\\' . $name . 'Command';
    }

    /**
     * {@inheritDoc}
     *
     * @return DoctrineCommand
     *
     * @throws InvalidArgumentException
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, ?array $options = null)
    {
        $commandClassName = $this->commandClassName;

        if (! class_exists($commandClassName)) {
            throw new InvalidArgumentException('The class ' . $commandClassName . ' does not exist');
        }

        $config            = $serviceLocator->get('config');
        $objectManagerName = $this->getObjectManagerName();

        // Copied from DoctrineModule/ServiceFactory/AbstractDoctrineServiceFactory
        if (
            ! preg_match(
                '/^doctrine\.((?<mappingType>orm|odm)\.|)(?<serviceType>[a-z0-9_]+)\.(?<serviceName>[a-z0-9_]+)$/',
                $objectManagerName,
                $matches
            )
        ) {
            throw new RuntimeException('The object manager name is invalid: ' . $objectManagerName);
        }

        $migrationConfig           = $config['doctrine']['migrations_configuration'][$matches['serviceName']] ?? [];
        $dependencyFactoryServices = [];

        if (array_key_exists('dependency_factory_services', $migrationConfig)) {
            $dependencyFactoryServices = $migrationConfig['dependency_factory_services'];
            unset($migrationConfig['dependency_factory_services']);
        }

        $dependencyFactory = DependencyFactory::fromEntityManager(
            new ConfigurationArray($migrationConfig),
            new ExistingEntityManager($serviceLocator->get($objectManagerName))
        );

        foreach ($dependencyFactoryServices as $id => $service) {
            $dependencyFactory->setService($id, $serviceLocator->get($service));
        }

        // An object manager may not have a migrations configuration and that's OK.
        // Use default values in that case.
        return new $commandClassName($dependencyFactory);
    }

    /**
     * @deprecated 4.1.0 With laminas-servicemanager v3 this method is obsolete and will be removed in 5.0.0.
     *
     * @throws InvalidArgumentException
     */
    public function createService(ServiceLocatorInterface $serviceLocator): DoctrineCommand
    {
        return $this($serviceLocator, $this->commandClassName);
    }

    private function getObjectManagerName(): string
    {
        $arguments = new ArgvInput();

        if (! $arguments->hasParameterOption('--object-manager')) {
            return $this->defaultObjectManagerName;
        }

        return $arguments->getParameterOption('--object-manager');
    }
}
