<?php
return array(
    'di' => array(
        'instance' => array(
            'orm_config' => array(
                'parameters' => array(
                    'opts' => array(
                        'auto_generate_proxies'     => false,
                        'proxy_dir'                 => 'data/DoctrineORMModule/Proxy',
                        'proxy_namespace'           => 'DoctrineORMModule\Proxy',
                    ),
                    'metadataCache'  => 'doctrine_cache_array',
                    'queryCache'     => 'doctrine_cache_array',
                    'resultCache'    => 'doctrine_cache_array',
                )
            ),
            'DoctrineORMModule\Doctrine\ORM\Connection' => array(
                'parameters' => array(
                    'params' => array(
                        'driver'   => 'pdo_mysql',
                        'host'     => 'localhost',
                        'port'     => '3306',
                        'user'     => 'root',
                        'password' => '',
                        'dbname'   => 'travis_test',
                    ),
                ),
            ),
            'orm_driver_chain' => array(
                'parameters' => array(
                    'drivers' => array(
                        'application_annotation_driver' => array(
                            'class'     => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                            'namespace' => 'DoctrineORMModuleTest\Assets\Entity',
                            'paths'     => array(
                                'vendor/doctrine/DoctrineORMModule/tests/DoctrineORMModuleTest/Assets/Entity'
                            ),
                        ),
                    ),
                    'cache' => 'doctrine_cache_array',
                )
            ),
        ),
    ),
);