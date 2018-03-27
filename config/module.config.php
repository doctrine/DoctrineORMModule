<?php

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\DBAL\Tools\Console;
use Doctrine\ORM\Tools\Console\Command;
use DoctrineModule\Form\Element;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use DoctrineORMModule\CliConfigurator;
use DoctrineORMModule\Service;
use DoctrineORMModule\Yuml;

return [
    'doctrine' => [
        'connection' => [
            // Configuration for service `doctrine.connection.orm_default` service
            'orm_default' => [
                // configuration instance to use. The retrieved service name will
                // be `doctrine.configuration.$thisSetting`
                'configuration' => 'orm_default',

                // event manager instance to use. The retrieved service name will
                // be `doctrine.eventmanager.$thisSetting`
                'eventmanager'  => 'orm_default',

                // connection parameters, see
                // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html
                'params' => [
                    'host'     => 'localhost',
                    'port'     => '3306',
                    'user'     => 'username',
                    'password' => 'password',
                    'dbname'   => 'database',
                ],
            ],
        ],

        // Configuration details for the ORM.
        // See http://docs.doctrine-project.org/en/latest/reference/configuration.html
        'configuration' => [
            // Configuration for service `doctrine.configuration.orm_default` service
            'orm_default' => [
                // metadata cache instance to use. The retrieved service name will
                // be `doctrine.cache.$thisSetting`
                'metadata_cache'    => 'array',

                // DQL queries parsing cache instance to use. The retrieved service
                // name will be `doctrine.cache.$thisSetting`
                'query_cache'       => 'array',

                // ResultSet cache to use.  The retrieved service name will be
                // `doctrine.cache.$thisSetting`
                'result_cache'      => 'array',

                // Hydration cache to use.  The retrieved service name will be
                // `doctrine.cache.$thisSetting`
                'hydration_cache'   => 'array',

                // Mapping driver instance to use. Change this only if you don't want
                // to use the default chained driver. The retrieved service name will
                // be `doctrine.driver.$thisSetting`
                'driver'            => 'orm_default',

                // Generate proxies automatically (turn off for production)
                'generate_proxies'  => true,

                // directory where proxies will be stored. By default, this is in
                // the `data` directory of your application
                'proxy_dir'         => 'data/DoctrineORMModule/Proxy',

                // namespace for generated proxy classes
                'proxy_namespace'   => 'DoctrineORMModule\Proxy',

                // SQL filters. See http://docs.doctrine-project.org/en/latest/reference/filters.html
                'filters'           => [],

                // Custom DQL functions.
                // You can grab common MySQL ones at https://github.com/beberlei/DoctrineExtensions
                // Further docs at http://docs.doctrine-project.org/en/latest/cookbook/dql-user-defined-functions.html
                'datetime_functions' => [],
                'string_functions' => [],
                'numeric_functions' => [],

                // Second level cache configuration (see doc to learn about configuration)
                'second_level_cache' => [],
            ],
        ],

        // Metadata Mapping driver configuration
        'driver' => [
            // Configuration for service `doctrine.driver.orm_default` service
            'orm_default' => [
                // By default, the ORM module uses a driver chain. This allows multiple
                // modules to define their own entities
                'class'   => MappingDriverChain::class,

                // Map of driver names to be used within this driver chain, indexed by
                // entity namespace
                'drivers' => [],
            ],
        ],

        // Entity Manager instantiation settings
        'entitymanager' => [
            // configuration for the `doctrine.entitymanager.orm_default` service
            'orm_default' => [
                // connection instance to use. The retrieved service name will
                // be `doctrine.connection.$thisSetting`
                'connection'    => 'orm_default',

                // configuration instance to use. The retrieved service name will
                // be `doctrine.configuration.$thisSetting`
                'configuration' => 'orm_default',
            ],
        ],

        'eventmanager' => [
            // configuration for the `doctrine.eventmanager.orm_default` service
            'orm_default' => [],
        ],

        // SQL logger collector, used when ZendDeveloperTools and its toolbar are active
        'sql_logger_collector' => [
            // configuration for the `doctrine.sql_logger_collector.orm_default` service
            'orm_default' => [],
        ],

        // mappings collector, used when ZendDeveloperTools and its toolbar are active
        'mapping_collector' => [
            // configuration for the `doctrine.sql_logger_collector.orm_default` service
            'orm_default' => [],
        ],

        // form annotation builder configuration
        'formannotationbuilder' => [
            // Configuration for service `doctrine.formannotationbuilder.orm_default` service
            'orm_default' => [],
        ],

        // entity resolver configuration, allows mapping associations to interfaces
        'entity_resolver' => [
            // configuration for the `doctrine.entity_resolver.orm_default` service
            'orm_default' => [],
        ],

        // authentication service configuration
        'authentication' => [
            // configuration for the `doctrine.authentication.orm_default` authentication service
            'orm_default' => [
                // name of the object manager to use. By default, the EntityManager is used
                'objectManager' => 'doctrine.entitymanager.orm_default',
                //'identityClass' => 'Application\Model\User',
                //'identityProperty' => 'username',
                //'credentialProperty' => 'password',
            ],
        ],

        // migrations configuration
        'migrations_configuration' => [
            'orm_default' => [
                'directory'       => 'data/DoctrineORMModule/Migrations',
                'name'            => 'Doctrine Database Migrations',
                'namespace'       => 'DoctrineORMModule\Migrations',
                'table'           => 'migrations',
                'column'          => 'version',
                'custom_template' => null,
            ],
        ],

        // migrations commands base config
        'migrations_cmd' => [
            'generate' => [],
            'execute'  => [],
            'migrate'  => [],
            'status'   => [],
            'version'  => [],
            'diff'     => [],
            'latest'   => [],
        ],
    ],

    'service_manager' => [
        'factories' => [
            CliConfigurator::class => Service\CliConfiguratorFactory::class,
            'Doctrine\ORM\EntityManager' => Service\EntityManagerAliasCompatFactory::class,
        ],
        'invokables' => [
            // DBAL commands
            'doctrine.dbal_cmd.runsql' => Console\Command\RunSqlCommand::class,
            'doctrine.dbal_cmd.import' => Console\Command\ImportCommand::class,
            // ORM Commands
            'doctrine.orm_cmd.clear_cache_metadata' => Command\ClearCache\MetadataCommand::class,
            'doctrine.orm_cmd.clear_cache_result' => Command\ClearCache\ResultCommand::class,
            'doctrine.orm_cmd.clear_cache_query' => Command\ClearCache\QueryCommand::class,
            'doctrine.orm_cmd.schema_tool_create' => Command\SchemaTool\CreateCommand::class,
            'doctrine.orm_cmd.schema_tool_update' => Command\SchemaTool\UpdateCommand::class,
            'doctrine.orm_cmd.schema_tool_drop' => Command\SchemaTool\DropCommand::class,
            'doctrine.orm_cmd.convert_d1_schema' => Command\ConvertDoctrine1SchemaCommand::class,
            'doctrine.orm_cmd.generate_entities' => Command\GenerateEntitiesCommand::class,
            'doctrine.orm_cmd.generate_proxies' => Command\GenerateProxiesCommand::class,
            'doctrine.orm_cmd.convert_mapping' => Command\ConvertMappingCommand::class,
            'doctrine.orm_cmd.run_dql' => Command\RunDqlCommand::class,
            'doctrine.orm_cmd.validate_schema' => Command\ValidateSchemaCommand::class,
            'doctrine.orm_cmd.info' => Command\InfoCommand::class,
            'doctrine.orm_cmd.ensure_production_settings' => Command\EnsureProductionSettingsCommand::class,
            'doctrine.orm_cmd.generate_repositories' => Command\GenerateRepositoriesCommand::class,
        ],
    ],

    // Factory mappings - used to define which factory to use to instantiate a particular doctrine
    // service type
    'doctrine_factories' => [
        'connection'               => Service\DBALConnectionFactory::class,
        'configuration'            => Service\ConfigurationFactory::class,
        'entitymanager'            => Service\EntityManagerFactory::class,
        'entity_resolver'          => Service\EntityResolverFactory::class,
        'sql_logger_collector'     => Service\SQLLoggerCollectorFactory::class,
        'mapping_collector'        => Service\MappingCollectorFactory::class,
        'formannotationbuilder'    => Service\FormAnnotationBuilderFactory::class,
        'migrations_configuration' => Service\MigrationsConfigurationFactory::class,
        'migrations_cmd'           => Service\MigrationsCommandFactory::class,
    ],

    // Zend\Form\FormElementManager configuration
    'form_elements' => [
        'aliases' => [
            'objectselect'        => Element\ObjectSelect::class,
            'objectradio'         => Element\ObjectRadio::class,
            'objectmulticheckbox' => Element\ObjectMultiCheckbox::class,
        ],
        'factories' => [
            Element\ObjectSelect::class        => Service\ObjectSelectFactory::class,
            Element\ObjectRadio::class         => Service\ObjectRadioFactory::class,
            Element\ObjectMultiCheckbox::class => Service\ObjectMultiCheckboxFactory::class,
        ],
    ],

    'hydrators' => [
        'factories' => [
            DoctrineObject::class => Service\DoctrineObjectHydratorFactory::class
        ],
    ],

    ////////////////////////////////////////////////////////////////////
    // `zendframework/zend-developer-tools` specific settings         //
    // ignore these if you're not developing additional features for  //
    // zend developer tools                                           //
    ////////////////////////////////////////////////////////////////////

    'router' => [
        'routes' => [
            'doctrine_orm_module_yuml' => [
                'type' => 'literal',
                'options' => [
                    'route' => '/ocra_service_manager_yuml',
                    'defaults' => [
                        'controller' => Yuml\YumlController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],

    'view_manager' => [
        'template_map' => [
            'zend-developer-tools/toolbar/doctrine-orm-queries'
                => __DIR__ . '/../view/zend-developer-tools/toolbar/doctrine-orm-queries.phtml',
            'zend-developer-tools/toolbar/doctrine-orm-mappings'
                => __DIR__ . '/../view/zend-developer-tools/toolbar/doctrine-orm-mappings.phtml',
        ],
    ],

    'zenddevelopertools' => [
        'profiler' => [
            'collectors' => [
                'doctrine.sql_logger_collector.orm_default' => 'doctrine.sql_logger_collector.orm_default',
                'doctrine.mapping_collector.orm_default'    => 'doctrine.mapping_collector.orm_default',
            ],
        ],
        'toolbar' => [
            'entries' => [
                'doctrine.sql_logger_collector.orm_default' => 'zend-developer-tools/toolbar/doctrine-orm-queries',
                'doctrine.mapping_collector.orm_default'    => 'zend-developer-tools/toolbar/doctrine-orm-mappings',
            ],
        ],
    ],
];
