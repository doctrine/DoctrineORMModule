<?php

declare(strict_types=1);

return [
    'modules' => [
        'Laminas\Cache',
        'Laminas\Cache\Storage\Adapter\Memory',
        'Laminas\Form',
        'Laminas\Hydrator',
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
