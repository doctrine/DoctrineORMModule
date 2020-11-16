<?php

declare(strict_types=1);

namespace DoctrineORMModule;

use Doctrine\Migrations\Tools\Console\Command\VersionCommand;
use DoctrineORMModule\Console\Helper\MigrationsConfigurationHelper;
use Laminas\DeveloperTools\ProfilerEvent;
use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\ControllerProviderInterface;
use Laminas\ModuleManager\Feature\DependencyIndicatorInterface;
use Laminas\ModuleManager\ModuleManagerInterface;

use function class_exists;

/**
 * Base module for Doctrine ORM.
 */
class Module implements
    ControllerProviderInterface,
    ConfigProviderInterface,
    DependencyIndicatorInterface
{
    /**
     * {@inheritDoc}
     */
    public function init(ModuleManagerInterface $manager)
    {
        // Initialize the console
        $manager
            ->getEventManager()
            ->getSharedManager()
            ->attach(
                'doctrine',
                'loadCli.post',
                static function (EventInterface $event): void {
                    $event
                        ->getParam('ServiceManager')
                        ->get(CliConfigurator::class)
                        ->configure($event->getTarget());

                    if (class_exists(VersionCommand::class)) {
                        $event->getTarget()->getHelperSet()->set(
                            new MigrationsConfigurationHelper($event->getParam('ServiceManager'))
                        );
                    }
                },
                1
            );

        // Initialize logger collector in DeveloperTools
        if (! class_exists(ProfilerEvent::class)) {
            return;
        }

        $manager
            ->getEventManager()
            ->attach(
                ProfilerEvent::EVENT_PROFILER_INIT,
                static function ($event): void {
                    $container = $event->getTarget()->getParam('ServiceManager');
                    $container->get('doctrine.sql_logger_collector.orm_default');
                }
            );
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * {@inheritDoc}
     */
    public function getControllerConfig()
    {
        return include __DIR__ . '/../../config/controllers.config.php';
    }

    /**
     * {@inheritDoc}
     */
    public function getModuleDependencies()
    {
        return ['DoctrineModule'];
    }
}
