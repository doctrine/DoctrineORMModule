<?php
return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'configuration' => 'doctrine_orm_default_configuration',
                'eventmanager'  => 'doctrine_orm_default_eventmanager',

                'driver'   => 'pdo_mysql',
                'host'     => 'localhost',
                'port'     => '3306',
                'user'     => 'username',
                'password' => 'password',
                'dbname'   => 'database',
            ),
        ),

        'eventmanager' => array(
            'default' => array(

            )
        ),

        'orm' => array(
            'entitymanager' => array(
                'default' => array(
                    'connection'    => 'doctrine_orm_default_connection',
                    'configuration' => 'doctrine_orm_default_configuration'
                )
            ),

            'configuration' => array(
                'default' => array(
                    'auto_generate_proxies'     => true,
                    'proxy_dir'                 => 'data/DoctrineORMModule/Proxy',
                    'proxy_namespace'           => 'DoctrineORMModule\Proxy',

                    'entity_namespaces'         => array(),

                    'custom_datetime_functions' => array(),
                    'custom_string_functions'   => array(),
                    'custom_numeric_functions'  => array(),

                    'named_queries'             => array(),
                    'named_native_queries'      => array(),

                    'sql_logger'                => null,
                )
            )
        )
    ),
);
