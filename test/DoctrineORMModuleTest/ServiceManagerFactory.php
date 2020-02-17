<?php

namespace DoctrineORMModuleTest;

use Laminas\Mvc\Service\ServiceManagerConfig;
use Laminas\ServiceManager\ServiceManager;

/**
 * Utility used to retrieve a freshly bootstrapped application's service manager
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class ServiceManagerFactory
{
    /**
     * Builds a new ServiceManager instance
     *
     * @param  array|null     $configuration
     * @return ServiceManager
     */
    public static function getServiceManager(array $configuration = null)
    {
        $configuration        = $configuration ?: include __DIR__ . '/../config.php';
        $serviceManager       = new ServiceManager();
        $serviceManagerConfig = new ServiceManagerConfig(
            $configuration['service_manager'] ?? []
        );
        $serviceManagerConfig->configureServiceManager($serviceManager);
        $serviceManager->setService('ApplicationConfig', $configuration);

        /** @var $moduleManager \Laminas\ModuleManager\ModuleManager */
        $moduleManager = $serviceManager->get('ModuleManager');
        $moduleManager->loadModules();

        return $serviceManager;
    }
}
