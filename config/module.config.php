<?php
return array(
    'doctrine' => array(
        'orm_autoload_annotations' => true,

        'connection' => array(
            'orm_default' => array(
                'configuration' => 'orm_default',
                'eventmanager'  => 'orm_default',

                'params' => array(
                    'host'     => 'localhost',
                    'port'     => '3306',
                    'user'     => 'username',
                    'password' => 'password',
                    'dbname'   => 'database',
                )
            ),
        ),

        'configuration' => array(
            'orm_default' => array(
                'metadata_cache'    => 'array',
                'query_cache'       => 'array',
                'result_cache'      => 'array',

                'driver'            => 'orm_default',

                'generate_proxies'  => true,
                'proxy_dir'         => 'data/DoctrineORMModule/Proxy',
                'proxy_namespace'   => 'DoctrineORMModule\Proxy'
            )
        ),

        'driver' => array(
            'orm_default' => array(
                'class'   => 'Doctrine\ORM\Mapping\Driver\DriverChain',
                'drivers' => array()
            )
        ),

        'entitymanager' => array(
            'orm_default' => array(
                'connection'    => 'orm_default',
                'configuration' => 'orm_default'
            )
        ),

        'eventmanager' => array(
            'orm_default' => array()
        ),

        'sql_logger_collector' => array(
            'orm_default' => array(),
        ),
    ),

    // zendframework/zend-developer-tools specific settings

    'view_manager' => array(
        'template_map' => array(
            'zend-developer-tools/toolbar/doctrine-orm' => __DIR__ . '/../view/zend-developer-tools/toolbar/doctrine-orm.phtml',
        ),
    ),

    'zenddevelopertools' => array(
        'profiler' => array(
            'collectors' => array(
                'orm_default' => 'doctrine.sql_logger_collector.orm_default',
            ),
        ),
        'toolbar' => array(
            'entries' => array(
                'orm_default' => 'zend-developer-tools/toolbar/doctrine-orm',
            ),
        ),
    ),
);
