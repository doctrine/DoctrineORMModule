<?php

use DoctrineORMModule\CliConfigurator;
use DoctrineORMModule\Listener\PostCliLoadListener;
use DoctrineORMModule\Service\CliConfiguratorFactory;
use DoctrineORMModule\Service\PostCliLoadListenerFactory;

return [
    'modules' => [
        'Zend\Cache',
        'Zend\Form',
        'Zend\Hydrator',
        'Zend\Mvc\Console',
        'Zend\Paginator',
        'Zend\Router',
        'Zend\Validator',
        'DoctrineModule',
        'DoctrineORMModule',
    ],
    'module_listener_options' => [
        'config_glob_paths' => [
            __DIR__ . '/testing.config.php',
        ],
        'module_paths' => [],
    ],
    'service_manager' => [
        'factories' => [
            PostCliLoadListener::class => PostCliLoadListenerFactory::class,
            CliConfigurator::class => CliConfiguratorFactory::class,
        ],
    ],
];
