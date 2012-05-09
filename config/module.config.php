<?php
return array(

    // Module specific options
    'doctrine_orm_module' => array(
        // Use following setting if you know where your 'DoctrineAnnotations.php' is
        //'annotation_file' => __DIR__ . '/../vendor/doctrine-orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php',
        'use_annotations' => true,
    ),

    'di' => array(

        'instance' => array(

            'alias' => array(
                // EntityManager
                'doctrine_em' => 'Doctrine\ORM\EntityManager',
                'orm_em'      => 'Doctrine\ORM\EntityManager',

                // configuration
                'orm_config'       => 'DoctrineORMModule\Doctrine\ORM\Configuration',
                'orm_connection'   => 'DoctrineORMModule\Doctrine\ORM\Connection',
                'orm_driver_chain' => 'DoctrineORMModule\Doctrine\ORM\DriverChain',
                'orm_evm'          => 'DoctrineModule\Doctrine\Common\EventManager',
            ),

            // ORM main configuration
            'orm_config' => array(
                'parameters' => array(
                    'opts' => array(
                        'auto_generate_proxies'     => true,
                        'proxy_dir'                 => 'data/DoctrineORMModule/Proxy',
                        'proxy_namespace'           => 'DoctrineORMModule\Proxy',
                        'entity_namespaces'         => array(),
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

            // Connection parameters
            'DoctrineORMModule\Doctrine\ORM\Connection' => array(
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
                ),
            ),

            // Driver chain where various mapping drivers have to be defined
            'orm_driver_chain' => array(
                'parameters' => array(
                    'drivers' => array(),
                    'cache' => 'doctrine_cache_array'
                )
            ),

            // Event manager attached to DBAL Connection and EntityManager
            'orm_evm' => array(
                'parameters' => array(
                    'opts' => array(
                        'subscribers' => array()
                    )
                )
            ),

            // Commands to be attached to CLI tools
            'doctrine_cli' => array(
                'injections' => array(
                    // Migrations
                    'Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand',
                    'Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand',
                    'Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand',
                    'Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand',
                    'Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand',
                    'Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand',

                    // DBAL
                    'Doctrine\DBAL\Tools\Console\Command\RunSqlCommand',
                    'Doctrine\DBAL\Tools\Console\Command\ImportCommand',

                    // ORM
                    'Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand',
                    'Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand',
                    'Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand',
                    'Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand',
                    'Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand',
                    'Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand',
                    'Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand',
                    'Doctrine\ORM\Tools\Console\Command\ConvertDoctrine1SchemaCommand',
                    'Doctrine\ORM\Tools\Console\Command\GenerateRepositoriesCommand',
                    'Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand',
                    'Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand',
                    'Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand',
                    'Doctrine\ORM\Tools\Console\Command\RunDqlCommand',
                    'Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand',
                    'Doctrine\ORM\Tools\Console\Command\InfoCommand'
                ),
            ),

            // CLI helpers
            'doctrine_cli_helperset' => array(
                'injections' => array(
                    'set' => array(
                        array(
                            'helper' => 'Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper',
                            'alias' => 'em'
                        ),
                        array(
                            'helper' => 'Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper',
                            'alias' => 'db'
                        ),
                    ),
                ),
            ),
        ),

        // Definitions (enforcing DIC behavior)
        'definition' => array(
            'class' => array(

                // EntityManager factory setup
                'Doctrine\ORM\EntityManager' => array(
                    'instantiator' => array(
                        'DoctrineORMModule\Factory\EntityManager',
                        'get'
                    ),
                ),
                'DoctrineORMModule\Factory\EntityManager' => array(
                    'methods' => array(
                        'get' => array(
                            'conn' => array(
                                'type' => 'DoctrineORMModule\Doctrine\ORM\Connection',
                                'required' => true,
                            ),
                        ),
                    ),
                ),

                // Connection factory setup
                'Doctrine\DBAL\Connection' => array(
                    'instantiator' => array(
                        'DoctrineORMModule\Factory\Connection',
                        'get'
                    ),
                ),
                'DoctrineORMModule\Factory\Connection' => array(
                    'methods' => array(
                        'get' => array(
                            'connection' => array(
                                'type' => 'DoctrineORMModule\Doctrine\ORM\Connection',
                                'required' => true,
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
