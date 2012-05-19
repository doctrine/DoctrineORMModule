<?php
return array(
    'doctrine_orm_connection' => array(
        'driver'   => 'pdo_mysql',
        'host'     => 'localhost',
        'port'     => '3306',
        'user'     => 'root',
        'password' => '',
        'dbname'   => 'blitzaroo',
    ),

    'doctrine_orm_config' => array(
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
    ),
);
