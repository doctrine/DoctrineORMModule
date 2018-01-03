<?php

use DoctrineORMModule\CliConfigurator;
use DoctrineORMModule\Listener\PostCliLoadListener;
use DoctrineORMModule\Service\CliConfiguratorFactory;
use DoctrineORMModule\Service\PostCliLoadListenerFactory;

return [
    'modules' => [
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
