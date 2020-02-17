<?php

declare(strict_types=1);

namespace DoctrineORMModule\Service;

use Doctrine\Migrations\Tools\Console\Command\AbstractCommand;
use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use function class_exists;
use function strtolower;
use function ucfirst;

/**
 * Service factory for migrations command
 */
class MigrationsCommandFactory implements FactoryInterface
{
    /** @var string */
    private $name;

    public function __construct(string $name)
    {
        $this->name = ucfirst(strtolower($name));
    }

    /**
     * {@inheritDoc}
     *
     * @return AbstractCommand
     *
     * @throws InvalidArgumentException
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $className = 'Doctrine\Migrations\Tools\Console\Command\\' . $this->name . 'Command';

        if (! class_exists($className)) {
            throw new InvalidArgumentException();
        }

        $configuration = $container->get('doctrine.migrations_configuration.orm_default');
        $command       = new $className();

        $command->setMigrationConfiguration($configuration);

        return $command;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function createService(ServiceLocatorInterface $container) : AbstractCommand
    {
        return $this($container, 'Doctrine\Migrations\Tools\Console\Command\\' . $this->name . 'Command');
    }
}
