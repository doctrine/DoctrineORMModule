<?php

namespace DoctrineORMModule;

use DoctrineORMModule\CliConfigurator;
use Interop\Container\ContainerInterface;
use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature\ControllerProviderInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\DependencyIndicatorInterface;
use Laminas\ModuleManager\ModuleManagerInterface;
use Laminas\DeveloperTools\ProfilerEvent;

/**
 * Base module for Doctrine ORM.
 *
 * @license MIT
 * @link    www.doctrine-project.org
 * @author  Kyle Spraggs <theman@spiffyjr.me>
 * @author  Marco Pivetta <ocramius@gmail.com>
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
                function (EventInterface $event) {
                    $event
                        ->getParam('ServiceManager')
                        ->get(CliConfigurator::class)
                        ->configure($event->getTarget())
                        ;
                },
                1
            );

        // Initialize logger collector in DeveloperTools
        if (class_exists(ProfilerEvent::class)) {
            $manager
                ->getEventManager()
                ->attach(
                    ProfilerEvent::EVENT_PROFILER_INIT,
                    function ($event) {
                        $container = $event->getTarget()->getParam('ServiceManager');
                        $container->get('doctrine.sql_logger_collector.orm_default');
                    }
                );
        }
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
