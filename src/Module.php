<?php

declare(strict_types=1);

namespace DoctrineORMModule;

use Laminas\DeveloperTools\ProfilerEvent;
use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\ControllerProviderInterface;
use Laminas\ModuleManager\Feature\DependencyIndicatorInterface;
use Laminas\ModuleManager\Feature\InitProviderInterface;
use Laminas\ModuleManager\ModuleManagerInterface;

use function class_exists;

/**
 * Base module for Doctrine ORM.
 */
final class Module implements
    ControllerProviderInterface,
    ConfigProviderInterface,
    DependencyIndicatorInterface,
    InitProviderInterface
{
    public function init(ModuleManagerInterface $manager): void
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
                /** @param EventInterface $event */
                static function ($event): void {
                    $container = $event->getTarget()->getParam('ServiceManager');
                    $container->get('doctrine.sql_logger_collector.orm_default');
                }
            );
    }

    /**
     * {@inheritDoc}
     *
     * @return array<array-key,mixed>
     */
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * {@inheritDoc}
     *
     * @return array<array-key,mixed>
     */
    public function getControllerConfig(): array
    {
        return include __DIR__ . '/../config/controllers.config.php';
    }

    /**
     * {@inheritDoc}
     *
     * @return array<string>
     */
    public function getModuleDependencies(): array
    {
        return ['DoctrineModule'];
    }
}
