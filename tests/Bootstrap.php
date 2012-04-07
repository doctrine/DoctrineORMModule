<?php

$rootPath  = realpath(dirname(__DIR__));
$testsPath = "$rootPath/tests";

if (is_readable($testsPath . '/TestConfiguration.php')) {
    require_once $testsPath . '/TestConfiguration.php';
} else {
    require_once $testsPath . '/TestConfiguration.php.dist';
}

set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            $testsPath,
            get_include_path(),
        )
    )
);

if (getenv('ZF2_PATH')) {
    require_once getenv('ZF2_PATH') . '/Zend/Loader/AutoloaderFactory.php';
} else {
    // Assuming the module is in a subdir of a skeleton application
    $previousDir = $zf2Dir = $rootPath;
    while (!file_exists($zf2Dir . '/vendor/ZendFramework/library')) {
        $zf2Dir = dirname($zf2Dir);
        if($previousDir === $zf2Dir) {
            throw new RuntimeException(
                'Unable to locate "vendor/ZendFramework/library":'
                    . ' is DoctrineModule in a subdir of your application skeleton?'
            );
        }
        $previousDir = $zf2Dir;
    }
    require_once $zf2Dir . '/vendor/ZendFramework/library/Zend/Loader/AutoloaderFactory.php';
}

\Zend\Loader\AutoloaderFactory::factory();

$defaultListeners = new Zend\Module\Listener\DefaultListenerAggregate(
    new Zend\Module\Listener\ListenerOptions(
        array(
            'module_paths' => array(
                realpath(__DIR__ . '/../..')
            ),
        )
    )
);

$moduleManager = new \Zend\Module\Manager(array(
    'DoctrineModule',
    'DoctrineORMModule',
));
$moduleManager->events()->attachAggregate($defaultListeners);
$moduleManager->loadModules();

$config = $defaultListeners->getConfigListener()->getMergedConfig()->toArray();

// setup sqlite
$config['di']['instance']['orm_connection']['parameters']['params'] = array(
	'driver' => 'pdo_sqlite',
	'memory' => true,
);

// setup the driver
$config['di']['instance']['orm_driver_chain']['parameters']['drivers']['doctrine_test_driver'] = array(
	'class' 	=> 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
	'namespace' => 'DoctrineORMModuleTest\Assets\Entity',
	'paths'     => array(__DIR__ . '/DoctrineORMModuleTest/Assets/Entity'),
);

$di = new \Zend\Di\Di();
$di->instanceManager()->addTypePreference('Zend\Di\Locator', $di);

$config = new \Zend\Di\Configuration($config['di']);
$config->configure($di);

\DoctrineORMModuleTest\Framework\TestCase::$locator = $di;