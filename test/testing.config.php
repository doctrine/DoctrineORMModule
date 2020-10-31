<?php

use Doctrine\DBAL\Driver\PDOSqlite\Driver;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use DoctrineORMModuleTest\Assets\Entity\TargetEntity;
use DoctrineORMModuleTest\Assets\Entity\TargetInterface;
use DoctrineORMModuleTest\Assets\RepositoryClass;

return [
    'doctrine' => [
        'configuration' => [
            'orm_default' => [
                'default_repository_class_name' => RepositoryClass::class,
            ],
        ],
        'driver' => [
            'DoctrineORMModuleTest\Assets\Entity' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/DoctrineORMModuleTest/Assets/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => ['DoctrineORMModuleTest\Assets\Entity' => 'DoctrineORMModuleTest\Assets\Entity'],
            ],
        ],
        'entity_resolver' => [
            'orm_default' => [
                'resolvers' => [
                    TargetInterface::class
                        => TargetEntity::class,
                ],
            ],
        ],
        'connection' => [
            'orm_default' => [
                'configuration' => 'orm_default',
                'eventmanager'  => 'orm_default',
                'driverClass'   => Driver::class,
                'params' => ['memory' => true],
            ],
        ],
        'migrations_configuration' => [
            'orm_default' => ['directory' => 'build/'],
        ],
    ],
];
