<?php

require_once __DIR__ . '/../Entity/Entity.php';

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => 'Doctrine\\DBAL\\Driver\\PDOMySql\\Driver',
                'params' => [
                    'user'     => 'root',
                    'password' => '',
                    'dbname'   => 'database',
                    'host'     => 'mysql',
                ],
            ],
        ],
        'configuration' => [
            'orm_default' => [
                'metadata_cache'   => 'filesystem',
                'query_cache'      => 'filesystem',
                'generate_proxies' => Doctrine\ORM\Proxy\ProxyFactory::AUTOGENERATE_NEVER,
                'proxy_dir'        => '.travis/cache/DoctrineORMModule/Proxy',
            ],
        ],
        'migrations_configuration' => [
            'orm_default' => [
                'directory' => '.travis',
                'namespace' => 'TravisDoctrineMigrations',
            ],
        ],
        'cache' => [
            'filesystem' => [
                'directory' => '.travis/cache/DoctrineModule',
            ],
        ],
        'driver' => [
            'travis_driver' => [
                'class' => Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                'cache' => 'array',
                'paths' => ['.travis/Entity/'],
            ],
            'orm_default' => [
                'drivers' => [
                    'DoctrineORMModule\Travis\Entity' => 'travis_driver',
                ],
            ],
        ],
    ],
];
