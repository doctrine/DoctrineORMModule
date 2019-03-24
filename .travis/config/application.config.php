<?php

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
        'module_paths' => [],
        'config_glob_paths' => [
            __DIR__ . '/module.config.php',
            __DIR__ . '/../.travis/config/travis.config.php',
        ],
    ],
];
