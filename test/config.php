<?php

return [
    'modules' => [
        'Laminas\Cache',
        'Laminas\Form',
        'Laminas\Hydrator',
        'Laminas\Mvc\Console',
        'Laminas\Paginator',
        'Laminas\Router',
        'Laminas\Validator',
        'DoctrineModule',
        'DoctrineORMModule',
    ],
    'module_listener_options' => [
        'config_glob_paths' => [
            __DIR__ . '/testing.config.php',
        ],
        'module_paths' => [],
    ],
];
