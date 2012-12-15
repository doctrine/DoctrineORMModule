<?php
return array(
    'doctrine' => array(
        'driver' => array(
            'test' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/DoctrineORMModuleTest/Assets/Entity'
                ),
            ),
        ),
    ),
);
$config['doctrine']['driver']['test'] = array(
    'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
    'cache' => 'array',
    'paths' => array(
        __DIR__ . '/DoctrineORMModuleTest/Assets/Entity'
    )
);
$config['doctrine']['entity_resolver']['orm_default'] = array(
    'resolvers' => array(
        'DoctrineORMModuleTest\Assets\Entity\TargetInterface' => 'DoctrineORMModuleTest\Assets\Entity\TargetEntity'
    )
);
$config['doctrine']['driver']['orm_default']['drivers']['DoctrineORMModuleTest\Assets\Entity'] = 'test';
$config['doctrine']['connection']['orm_default'] = array(
    'configuration' => 'orm_default',
    'eventmanager'  => 'orm_default',
    'driverClass'   => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
    'params' => array(
        'memory' => true,
    ),
);