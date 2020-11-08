<?php

require_once __DIR__ . '/../Entity/Entity.php';

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'params' => [
                    'user'     => 'travis',
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
                'proxy_dir'        => '.travis/cache/DoctrineORMModule/Proxy',
            ],
        ],
        'migrations_configuration' => [
            'orm_default' => [
                'table_storage' => [
                    'table_name' => 'DoctrineMigrationVersions',
                    'version_column_name' => 'version',
                    'version_column_length' => 1024,
                    'executed_at_column_name' => 'executedAt',
                    'execution_time_column_name' => 'executionTime',
                ],
                'migrations_paths' => [
                    'TravisDoctrineMigrations' => '.travis',
                ],
                'migrations' => [],
                'all_or_nothing' => false,
                'check_database_platform' => true,
                'organize_migrations' => 'year', // year or year_and_month
                'custom_template' => null,
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
