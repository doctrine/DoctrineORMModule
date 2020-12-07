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
        'module_paths' => [],
        'config_glob_paths' => [
            __DIR__ . '/module.config.php',
            __DIR__ . '/../ci/config/ci.config.php',
        ],
    ],
];
