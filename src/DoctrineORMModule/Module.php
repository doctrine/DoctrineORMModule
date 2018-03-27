<?php

namespace DoctrineORMModule;

use Interop\Container\ContainerInterface;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\DependencyIndicatorInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use DoctrineORMModule\CliConfigurator;

/**
 * Base module for Doctrine ORM.
 *
 * @license MIT
 * @link    www.doctrine-project.org
 * @author  Kyle Spraggs <theman@spiffyjr.me>
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class Module implements
    BootstrapListenerInterface,
    ControllerProviderInterface,
    ConfigProviderInterface,
    DependencyIndicatorInterface
{
    /**
     * {@inheritDoc}
     */
    public function init(ModuleManagerInterface $manager)
    {
        $events = $manager->getEventManager();
        $serviceManager = $manager->getEvent()->getParam('ServiceManager');

        $events->getSharedManager()->attach('doctrine', 'loadCli.post', [$this, 'initializeConsole'], 1);
    }

    /**
     * Initialize the Doctrine console
     *
     * @param EventInterface
     */
    public function initializeConsole(EventInterface $event)
    {
        $container = $event->getParam('ServiceManager');
        $cliConfigurator = $container->get(CliConfigurator::class);
        $cliConfigurator->configure($event->getTarget());
    }

    /**
     * {@inheritDoc}
     */
    public function onBootstrap(EventInterface $event)
    {
        /* @var $application \Zend\Mvc\Application */
        $application = $event->getTarget();

        /* @var $container ContainerInterface */
        $container = $application->getServiceManager();

        $events = $application->getEventManager();

        // Initialize logger collector once the profiler is initialized itself
        $events->attach(
            'profiler_init',
            function () use ($container) {
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
