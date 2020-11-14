<?php

use Doctrine\DBAL\Driver\PDOSqlite\Driver;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use DoctrineModule\Service\EventManagerFactory;
use DoctrineORMModule\Service\ConfigurationFactory;
use DoctrineORMModule\Service\DBALConnectionFactory;
use DoctrineORMModule\Service\EntityManagerFactory;
use DoctrineORMModuleTest\Assets\Entity\TargetEntity;
use DoctrineORMModuleTest\Assets\Entity\TargetInterface;
use DoctrineORMModuleTest\Assets\RepositoryClass;

return [
    'doctrine' => [
        'configuration' => [
            'orm_default' => [
                'default_repository_class_name' => RepositoryClass::class,
            ],
            'orm_other' => [
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
            'orm_other' => [
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
            'orm_other' => [
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
            'orm_other' => [
                'configuration' => 'orm_other',
                'eventmanager'  => 'orm_other',
                'driverClass'   => Driver::class,
                'params' => ['memory' => true],
            ],
        ],
        'eventmanager' => [
            'orm_other' => [],
        ],
        'migrations_configuration' => [
            'orm_default' => ['directory' => 'build/'],
            'orm_other' => [
                'directory' => 'build/orm_other',
                'name'      => 'Doctrine Database Migrations 2',
                'namespace' => 'Application\MigrationsOther',
                'table'     => 'MigrationsOther',
                'column'    => 'versionOther',
            ],
        ],
    ],

    'service_manager' => [
        'factories' => [
            'doctrine.entitymanager.orm_other' => new EntityManagerFactory('orm_other'),
            'doctrine.connection.orm_other'    => new DBALConnectionFactory('orm_other'),
            'doctrine.configuration.orm_other' => new ConfigurationFactory('orm_other'),
            'doctrine.eventmanager.orm_other'  => new EventManagerFactory('orm_other'),
        ],
    ],
];
