<?php

declare(strict_types=1);

namespace DoctrineORMModule\Yuml;

use Interop\Container\ContainerInterface;
use Laminas\Http\Client;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

use function sprintf;

class YumlControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @deprecated 4.1.0 With laminas-servicemanager v3 this method is obsolete and will be removed in 5.0.0.
     */
    public function createService(ServiceLocatorInterface $serviceLocator): YumlController
    {
        if ($serviceLocator instanceof AbstractPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        return $this($serviceLocator, YumlController::class);
    }

    /**
     * Create an object
     *
     * {@inheritDoc}
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): YumlController {
        $config = $container->get('config');

        if (
            ! isset($config['laminas-developer-tools']['toolbar']['enabled'])
            || ! $config['laminas-developer-tools']['toolbar']['enabled']
        ) {
            throw new ServiceNotFoundException(
                sprintf('Service %s could not be found', YumlController::class)
            );
        }

        return new YumlController(
            new Client('https://yuml.me/diagram/plain/class/', ['timeout' => 30])
        );
    }
}
