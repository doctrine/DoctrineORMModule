<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace DoctrineORMModule;

use Doctrine\DBAL\Tools\Console\Command as DBALCommand;
use Doctrine\ORM\Tools\Console\Command as ORMCommand;
use DoctrineModule\Form\Element;
use Zend\Router\Http\Literal;
use Zend\ServiceManager\Factory\InvokableFactory;

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
                'string_functions'   => [],
                'numeric_functions'  => [],

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
                'class'   => 'Doctrine\ORM\Mapping\Driver\DriverChain',

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
                'directory' => 'data/DoctrineORMModule/Migrations',
                'name'      => 'Doctrine Database Migrations',
                'namespace' => 'DoctrineORMModule\Migrations',
                'table'     => 'migrations',
                'column'    => 'version',
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
        'aliases' => [
            // DBAL commands
            'doctrine.dbal_cmd.runsql' => DBALCommand\RunSqlCommand::class,
            'doctrine.dbal_cmd.import' => DBALCommand\ImportCommand::class,
            // ORM Commands
            'doctrine.orm_cmd.clear_cache_metadata'       => ORMCommand\ClearCache\MetadataCommand::class,
            'doctrine.orm_cmd.clear_cache_result'         => ORMCommand\ClearCache\ResultCommand::class,
            'doctrine.orm_cmd.clear_cache_query'          => ORMCommand\ClearCache\QueryCommand::class,
            'doctrine.orm_cmd.schema_tool_create'         => ORMCommand\SchemaTool\CreateCommand::class,
            'doctrine.orm_cmd.schema_tool_update'         => ORMCommand\SchemaTool\UpdateCommand::class,
            'doctrine.orm_cmd.schema_tool_drop'           => ORMCommand\SchemaTool\DropCommand::class,
            'doctrine.orm_cmd.convert_d1_schema'          => ORMCommand\ConvertDoctrine1SchemaCommand::class,
            'doctrine.orm_cmd.generate_entities'          => ORMCommand\GenerateEntitiesCommand::class,
            'doctrine.orm_cmd.generate_proxies'           => ORMCommand\GenerateProxiesCommand::class,
            'doctrine.orm_cmd.convert_mapping'            => ORMCommand\ConvertMappingCommand::class,
            'doctrine.orm_cmd.run_dql'                    => ORMCommand\RunDqlCommand::class,
            'doctrine.orm_cmd.validate_schema'            => ORMCommand\ValidateSchemaCommand::class,
            'doctrine.orm_cmd.info'                       => ORMCommand\InfoCommand::class,
            'doctrine.orm_cmd.ensure_production_settings' => ORMCommand\EnsureProductionSettingsCommand::class,
            'doctrine.orm_cmd.generate_repositories'      => ORMCommand\GenerateRepositoriesCommand::class,
        ],
        'factories' => [
            'Doctrine\ORM\EntityManager' => Service\EntityManagerAliasCompatFactory::class,
            DBALCommand\RunSqlCommand::class                  => InvokableFactory::class,
            DBALCommand\ImportCommand::class                  => InvokableFactory::class,
            ORMCommand\ClearCache\MetadataCommand::class      => InvokableFactory::class,
            ORMCommand\ClearCache\ResultCommand::class        => InvokableFactory::class,
            ORMCommand\ClearCache\QueryCommand::class         => InvokableFactory::class,
            ORMCommand\SchemaTool\CreateCommand::class        => InvokableFactory::class,
            ORMCommand\SchemaTool\UpdateCommand::class        => InvokableFactory::class,
            ORMCommand\SchemaTool\DropCommand::class          => InvokableFactory::class,
            ORMCommand\ConvertDoctrine1SchemaCommand::class   => InvokableFactory::class,
            ORMCommand\GenerateEntitiesCommand::class         => InvokableFactory::class,
            ORMCommand\GenerateProxiesCommand::class          => InvokableFactory::class,
            ORMCommand\ConvertMappingCommand::class           => InvokableFactory::class,
            ORMCommand\RunDqlCommand::class                   => InvokableFactory::class,
            ORMCommand\ValidateSchemaCommand::class           => InvokableFactory::class,
            ORMCommand\InfoCommand::class                     => InvokableFactory::class,
            ORMCommand\EnsureProductionSettingsCommand::class => InvokableFactory::class,
            ORMCommand\GenerateRepositoriesCommand::class     => InvokableFactory::class,
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
            \DoctrineModule\Stdlib\Hydrator\DoctrineObject::class => Service\DoctrineObjectHydratorFactory::class,
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
                'type' => Literal::class,
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
