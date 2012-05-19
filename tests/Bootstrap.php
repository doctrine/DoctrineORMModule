<?php
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfiguration;

chdir(__DIR__);

$previousDir = '.';
while (!file_exists('config/application.config.php')) {
    $dir = dirname(getcwd());
    if($previousDir === $dir) {
        throw new RuntimeException(
            'Unable to locate "config/application.config.php": ' .
            'is DoctrineORMModule in a subdir of your application skeleton?'
        );
    }
    $previousDir = $dir;
    chdir($dir);
}

if (is_readable(__DIR__ . '/TestConfiguration.php')) {
    require_once __DIR__ . '/TestConfiguration.php';
} else {
    require_once __DIR__ . '/TestConfiguration.php.dist';
}

require_once('vendor/autoload.php');

// $configuration is loaded from TestConfiguration.php (or .dist)
$serviceManager = new ServiceManager(new ServiceManagerConfiguration($configuration['service_manager']));
$serviceManager->setService('ApplicationConfiguration', $configuration);
$serviceManager->setAllowOverride(true);

$config = $serviceManager->get('Configuration');
$config['doctrine']['connection']['orm_default'] = array(
    'configuration' => 'doctrine_orm_default_configuration',
    'eventmanager'  => 'doctrine_orm_default_eventmanager',
    'driver'        => 'pdo_sqlite',
    'memory'        => true
);

$serviceManager->setService('Configuration', $config);

/** @var $moduleManager \Zend\ModuleManager\ModuleManager */
$moduleManager = $serviceManager->get('ModuleManager');
$moduleManager->loadModules();

// register annotation driver
$sharedEvents = $moduleManager->events()->getSharedManager();
$sharedEvents->attach('DoctrineORMModule', 'loadDrivers', function($e) {
    $chain  = $e->getTarget();
    $driver = $e->getParam('config')->newDefaultAnnotationDriver(__DIR__ . '/DoctrineORMModuleTest/Assets/Entity');

    $chain->addDriver($driver, 'DoctrineORMModuleTest\Assets\Entity');
});

\DoctrineORMModuleTest\Framework\TestCase::setServiceManager($serviceManager);