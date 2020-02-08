<?php

namespace DoctrineORMModule\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Service factory for migrations command
 */
class MigrationsCommandFactory implements FactoryInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = ucfirst(strtolower($name));
    }

    /**
     * {@inheritDoc}
     *
     * @return \Doctrine\Migrations\Tools\Console\Command\AbstractCommand
     * @throws \InvalidArgumentException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $className = 'Doctrine\Migrations\Tools\Console\Command\\' . $this->name . 'Command';

        if (! class_exists($className)) {
            throw new \InvalidArgumentException();
        }

        // @TODO currently hardcoded: `orm_default` should be injected
        /* @var $configuration \Doctrine\Migrations\Configuration\Configuration */
        $configuration = $container->get('doctrine.migrations_configuration.orm_default');
        /* @var $command \Doctrine\Migrations\Tools\Console\Command\AbstractCommand */
        $command       = new $className;

        $command->setMigrationConfiguration($configuration);

        return $command;
    }

    /**
     * @param \Laminas\ServiceManager\ServiceLocatorInterface $container
     * @return \Doctrine\Migrations\Tools\Console\Command\AbstractCommand
     * @throws \InvalidArgumentException
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, 'Doctrine\Migrations\Tools\Console\Command\\' . $this->name . 'Command');
    }
}
