<?php

require_once __DIR__ . '/../Entity/Entity.php';

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'params' => [
                    'host'     => '127.0.0.1',
                    'user'     => 'root',
                    'password' => '',
                    'dbname'   => 'database',
                ],
            ],
        ],
        'configuration' => [
            'orm_default' => [
                'metadata_cache'   => 'filesystem',
                'query_cache'      => 'filesystem',
                'generate_proxies' => Doctrine\ORM\Proxy\ProxyFactory::AUTOGENERATE_NEVER,
                'proxy_dir'        => 'ci/cache/DoctrineORMModule/Proxy',
            ],
        ],
        'migrations_configuration' => [
            'orm_default' => [
                'directory' => 'ci',
                'namespace' => 'CiDoctrineMigrations',
            ],
        ],
        'cache' => [
            'filesystem' => [
                'directory' => 'ci/cache/DoctrineModule',
            ],
        ],
        'driver' => [
            'ci_driver' => [
                'class' => Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                'cache' => 'array',
                'paths' => ['ci/Entity/'],
            ],
            'orm_default' => [
                'drivers' => [
                    'DoctrineORMModule\Ci\Entity' => 'ci_driver',
                ],
            ],
        ],
    ],
];

