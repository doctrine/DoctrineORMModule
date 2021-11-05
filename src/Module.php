<?php

declare(strict_types=1);

namespace DoctrineORMModule;

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
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * {@inheritDoc}
     */
    public function getControllerConfig()
    {
        return include __DIR__ . '/../config/controllers.config.php';
    }

    /**
     * {@inheritDoc}
     */
    public function getModuleDependencies()
    {
        return ['DoctrineModule'];
    }
}
