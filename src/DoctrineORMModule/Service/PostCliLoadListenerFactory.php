<?php

namespace DoctrineORMModule\Service;

use DoctrineORMModule\CliConfigurator;
use DoctrineORMModule\Listener\PostCliLoadListener;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PostCliLoadListenerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $cliConfigurator CliConfigurator */
        $cliConfigurator = $container->get(CliConfigurator::class);

        return new PostCliLoadListener($cliConfigurator);
    }

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, PostCliLoadListener::class);
    }
}
