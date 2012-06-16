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
$config['doctrine']['driver']['test'] = array(
    'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
    'cache' => 'array',
    'paths' => array(
        __DIR__ . '/DoctrineORMModuleTest/Assets/Entity'
    )
);
$config['doctrine']['driver']['orm_default']['drivers']['DoctrineORMModuleTest\Assets\Entity'] = 'test';
$config['doctrine']['connection']['orm_default'] = array(
    'configuration' => 'orm_default',
    'eventmanager'  => 'orm_default',
    'driverClass'   => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',

    'params' => array(
        'memory'        => true
    )
);

$serviceManager->setService('Configuration', $config);

/** @var $moduleManager \Zend\ModuleManager\ModuleManager */
$moduleManager = $serviceManager->get('ModuleManager');
$moduleManager->loadModules();

\DoctrineORMModuleTest\Framework\TestCase::setServiceManager($serviceManager);