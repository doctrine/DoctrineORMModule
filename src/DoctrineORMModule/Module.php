<?php

namespace DoctrineORMModule;

use DoctrineORMModule\Listener\PostCliLoadListener;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\DependencyIndicatorInterface;
use Zend\ModuleManager\ModuleManagerInterface;

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
    DependencyIndicatorInterface,
    BootstrapListenerInterface
{
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

    /**
     * @param EventInterface $event
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
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

        /* @var $postCliLoadListener PostCliLoadListener */
        $postCliLoadListener = $container->get(PostCliLoadListener::class);
        $postCliLoadListener->attach($events);

        /* @var $doctrineCli Application */
        $doctrineCli = $container->get('doctrine.cli');

        $eventDispatcher = $container->get('doctrine.cli.event_dispatcher');
        $doctrineCli->setDispatcher($eventDispatcher);
    }
}
