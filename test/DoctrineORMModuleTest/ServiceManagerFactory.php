<?php

namespace DoctrineORMModuleTest;

use Laminas\ModuleManager\ModuleManager;
use Laminas\Mvc\Service\ServiceManagerConfig;
use Laminas\ServiceManager\ServiceManager;

use function assert;

/**
 * Utility used to retrieve a freshly bootstrapped application's service manager
 *
 * @link    http://www.doctrine-project.org/
 */
class ServiceManagerFactory
{
    /**
     * Builds a new ServiceManager instance
     *
     * @param  mixed[]|null $configuration
     */
    public static function getServiceManager(?array $configuration = null): ServiceManager
    {
        $configuration        = $configuration ?: include __DIR__ . '/../config.php';
        $serviceManager       = new ServiceManager();
        $serviceManagerConfig = new ServiceManagerConfig(
            $configuration['service_manager'] ?? []
        );
        $serviceManagerConfig->configureServiceManager($serviceManager);
        $serviceManager->setService('ApplicationConfig', $configuration);

        $moduleManager = $serviceManager->get('ModuleManager');
        assert($moduleManager instanceof ModuleManager);
        $moduleManager->loadModules();

        return $serviceManager;
    }
}
