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
                'generate_proxies' => false,
                'proxy_dir'        => 'ci/cache/DoctrineORMModule/Proxy',
            ],
        ],
        'migrations_configuration' => [
            'orm_default' => [
                'table_storage' => [
                    'table_name' => 'DoctrineMigrationVersions',
                    'version_column_name' => 'version',
                    'version_column_length' => 191,
                    'executed_at_column_name' => 'executedAt',
                    'execution_time_column_name' => 'executionTime',
                ],
                'migrations_paths' => [
                    'CiDoctrineMigrations' => 'ci',
                ],
                'migrations' => [],
                'all_or_nothing' => false,
                'transactional' => false,
                'check_database_platform' => true,
                'organize_migrations' => 'year', // year or year_and_month
                'custom_template' => null,
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
