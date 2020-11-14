<?php

declare(strict_types=1);

namespace DoctrineORMModule\Console\Helper;

use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Tools\Console\Helper\ConfigurationHelperInterface;
use Interop\Container\ContainerInterface;
use RuntimeException;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;

use function preg_match;

class MigrationsConfigurationHelper implements
    HelperInterface,
    ConfigurationHelperInterface
{
    /** @var HelperSet */
    protected $helperSet;

    /** @var ContainerInterface */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setHelperSet(?HelperSet $helperSet = null): MigrationsConfigurationHelper
    {
        $this->helperSet = $helperSet;

        return $this;
    }

    public function getHelperSet(): ?HelperSet
    {
        return $this->helperSet;
    }

    public function getName(): string
    {
        return 'configuration';
    }

    public function getMigrationConfig(InputInterface $input): Configuration
    {
        $objectManagerAlias = $input->getOption('object-manager') ?: 'doctrine.entitymanager.orm_default';

        // Copied from DoctrineModule/ServiceFactory/AbstractDoctrineServiceFactory
        if (
            ! preg_match(
                '/^doctrine\.((?<mappingType>orm|odm)\.|)(?<serviceType>[a-z0-9_]+)\.(?<serviceName>[a-z0-9_]+)$/',
                $objectManagerAlias,
                $matches
            )
        ) {
            throw new RuntimeException('The object manager alias is invalid: ' . $objectManagerAlias);
        }

        return $this->container->get('doctrine.migrations_configuration.' . $matches['serviceName']);
    }
}
