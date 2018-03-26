<?php

namespace DoctrineORMModuleTest;

use Zend\Mvc\Application;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;

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
     * @return array
     */
    public static function getConfiguration()
    {
        $r = new \ReflectionClass(Application::class);
        $requiredParams = $r->getConstructor()->getNumberOfRequiredParameters();

        $configFile = $requiredParams == 1 ? 'TestConfigurationV3.php' : 'TestConfigurationV2.php';

        return include __DIR__ . '/../' . $configFile;
    }

    /**
     * Builds a new ServiceManager instance
     *
     * @param  array|null     $configuration
     * @return ServiceManager
     */
    public static function getServiceManager(array $configuration = null)
    {
        $configuration        = $configuration ?: static::getConfiguration();
        $serviceManager       = new ServiceManager();
        $serviceManagerConfig = new ServiceManagerConfig(
            $configuration['service_manager'] ?? []
        );
        $serviceManagerConfig->configureServiceManager($serviceManager);
        $serviceManager->setService('ApplicationConfig', $configuration);

        /** @var $moduleManager \Zend\ModuleManager\ModuleManager */
        $moduleManager = $serviceManager->get('ModuleManager');
        $moduleManager->loadModules();

        return $serviceManager;
    }
}
