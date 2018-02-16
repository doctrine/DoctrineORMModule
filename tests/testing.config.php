<?php
return [
    'doctrine' => [
        'configuration' => [
            'orm_default' => [
                'default_repository_class_name' => \DoctrineORMModuleTest\Assets\RepositoryClass::class,
            ],
        ],
        'driver' => [
            'DoctrineORMModuleTest\Assets\Entity' => [
                'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/DoctrineORMModuleTest/Assets/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'DoctrineORMModuleTest\Assets\Entity' => 'DoctrineORMModuleTest\Assets\Entity',
                ],
            ],
        ],
        'entity_resolver' => [
            'orm_default' => [
                'resolvers' => [
                    \DoctrineORMModuleTest\Assets\Entity\TargetInterface::class
                        => \DoctrineORMModuleTest\Assets\Entity\TargetEntity::class,
                ],
            ],
        ],
        'connection' => [
            'orm_default' => [
                'configuration' => 'orm_default',
                'eventmanager'  => 'orm_default',
                'driverClass'   => \Doctrine\DBAL\Driver\PDOSqlite\Driver::class,
                'params' => [
                    'memory' => true,
                ],
            ],
        ],
        'migrations_configuration' => [
            'orm_default' => [
                'directory' => 'build/',
            ],
        ],
    ],
];
