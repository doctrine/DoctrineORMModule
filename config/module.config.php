<?php
return array(
    'di' => array(
        'definition' => array(
            'class' => array(
                'Memcache' => array(
                    'addServer' => array(
                        'host' => array('type' => false, 'required' => true),
                        'port' => array('type' => false, 'required' => true),
                    )
                ),
                'SpiffyDoctrineORM\Factory\EntityManager' => array(
                    'instantiator' => array('SpiffyDoctrineORM\Factory\EntityManager', 'get'),
                    'methods' => array(
                        'get' => array(
                            'conn' => array('type' => 'SpiffyDoctrineORM\Doctrine\ORM\Connection', 'required' => true)
                        )
                    )
                ),
            ),
        ),
        'instance' => array(
            'alias' => array(
                // entity manager
                'doctrine_em'    => 'SpiffyDoctrineORM\Factory\EntityManager',
                
                // configuration
                'orm_config'       => 'SpiffyDoctrineORM\Doctrine\ORM\Configuration',
                'orm_connection'   => 'SpiffyDoctrineORM\Doctrine\ORM\Connection',
                'orm_driver_chain' => 'SpiffyDoctrineORM\Doctrine\ORM\DriverChain',
                'orm_evm'          => 'SpiffyDoctrine\Doctrine\Common\EventManager',
            ),
            'orm_config' => array(
                'parameters' => array(
                    'opts' => array(
                        'auto_generate_proxies'     => true,
                        'proxy_dir'                 => __DIR__ . '/../../../data/SpiffyDoctrine/Proxy',
                        'proxy_namespace'           => 'SpiffyDoctrine\Proxy',
                        'custom_datetime_functions' => array(),
                        'custom_numeric_functions'  => array(),
                        'custom_string_functions'   => array(),
                        'custom_hydration_modes'    => array(),
                        'named_queries'             => array(),
                        'named_native_queries'      => array(),
                    ),
                    'metadataDriver' => 'orm_driver_chain',
                    'metadataCache'  => 'doctrine_cache_array',
                    'queryCache'     => 'doctrine_cache_array',
                    'resultCache'    => null,
                    'logger'         => null,
                )
            ),
            'orm_connection' => array(
                'parameters' => array(
                    'params' => array(
                        'driver'   => 'pdo_mysql',
                        'host'     => 'localhost',
                        'port'     => '3306', 
                        'user'     => 'testuser',
                        'password' => 'testpassword',
                        'dbname'   => 'testdbname',
                    ),
                    'config' => 'orm_config',
                    'evm'    => 'orm_evm',
                    'pdo'    => null
                )
            ),
            'orm_driver_chain' => array(
                'parameters' => array(
                    'drivers' => array(),
                    'cache' => 'doctrine_cache_array'
                )
            ),
            'orm_evm' => array(
                'parameters' => array(
                    'opts' => array(
                        'subscribers' => array()
                    )
                )
            ),
            'doctrine_em' => array(
                'parameters' => array(
                    'conn' => 'orm_connection',
                )
            ),
        )
    )
);