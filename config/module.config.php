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

return array(
    'doctrine' => array(
        'connection' => array(
            // Configuration for service `doctrine.connection.orm_default` service
            'orm_default' => array(
                // configuration instance to use. The retrieved service name will
                // be `doctrine.configuration.$thisSetting`
                'configuration' => 'orm_default',

                // event manager instance to use. The retrieved service name will
                // be `doctrine.eventmanager.$thisSetting`
                'eventmanager'  => 'orm_default',

                // connection parameters, see
                // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html
                'params' => array(
                    'host'     => 'localhost',
                    'port'     => '3306',
                    'user'     => 'username',
                    'password' => 'password',
                    'dbname'   => 'database',
                )
            ),
        ),

        // Configuration details for the ORM.
        // See http://docs.doctrine-project.org/en/latest/reference/configuration.html
        'configuration' => array(
            // Configuration for service `doctrine.configuration.orm_default` service
            'orm_default' => array(
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
                'filters'           => array(),

                // Custom DQL functions.
                // You can grab common MySQL ones at https://github.com/beberlei/DoctrineExtensions
                // Further docs at http://docs.doctrine-project.org/en/latest/cookbook/dql-user-defined-functions.html
                'datetime_functions' => array(),
                'string_functions' => array(),
                'numeric_functions' => array(),
            )
        ),

        // Metadata Mapping driver configuration
        'driver' => array(
            // Configuration for service `doctrine.driver.orm_default` service
            'orm_default' => array(
                // By default, the ORM module uses a driver chain. This allows multiple
                // modules to define their own entities
                'class'   => 'Doctrine\ORM\Mapping\Driver\DriverChain',

                // Map of driver names to be used within this driver chain, indexed by
                // entity namespace
                'drivers' => array()
            )
        ),

        // Entity Manager instantiation settings
        'entitymanager' => array(
            // configuration for the `doctrine.entitymanager.orm_default` service
            'orm_default' => array(
                // connection instance to use. The retrieved service name will
                // be `doctrine.connection.$thisSetting`
                'connection'    => 'orm_default',

                // configuration instance to use. The retrieved service name will
                // be `doctrine.configuration.$thisSetting`
                'configuration' => 'orm_default'
            )
        ),

        'eventmanager' => array(
            // configuration for the `doctrine.eventmanager.orm_default` service
            'orm_default' => array()
        ),

        // SQL logger collector, used when ZendDeveloperTools and its toolbar are active
        'sql_logger_collector' => array(
            // configuration for the `doctrine.sql_logger_collector.orm_default` service
            'orm_default' => array(),
        ),

        // mappings collector, used when ZendDeveloperTools and its toolbar are active
        'mapping_collector' => array(
            // configuration for the `doctrine.sql_logger_collector.orm_default` service
            'orm_default' => array(),
        ),

        // form annotation builder configuration
        'formannotationbuilder' => array(
            // Configuration for service `doctrine.formannotation.orm_default` service
            'orm_default' => array(),
        ),

        // entity resolver configuration, allows mapping associations to interfaces
        'entity_resolver' => array(
            // configuration for the `doctrine.entity_resolver.orm_default` service
            'orm_default' => array()
        ),

        // authentication service configuration
        'authentication' => array(
            // configuration for the `doctrine.authentication.orm_default` authentication service
            'orm_default' => array(
                // name of the object manager to use. By default, the EntityManager is used
                'objectManager' => 'doctrine.entitymanager.orm_default',
                //'identityClass' => 'Application\Model\User',
                //'identityProperty' => 'username',
                //'credentialProperty' => 'password'
            ),
        ),

        // migrations configuration
        'migrations_configuration' => array(
            'orm_default' => array(
                'directory' => 'data/DoctrineORMModule/Migrations',
                'name'      => 'Doctrine Database Migrations',
                'namespace' => 'DoctrineORMModule\Migrations',
                'table'     => 'migrations',
            ),
        ),

        // migrations commands base config
        'migrations_cmd' => array(
            'generate' => array(),
            'execute'  => array(),
            'migrate'  => array(),
            'status'   => array(),
            'version'  => array(),
            'diff'     => array(),
        ),
    ),

    'service_manager' => array(
        'factories' => array(
            'Doctrine\ORM\EntityManager' => 'DoctrineORMModule\Service\EntityManagerAliasCompatFactory',
        ),
        'invokables' => array(
            // DBAL commands
            'doctrine.dbal_cmd.runsql' => '\Doctrine\DBAL\Tools\Console\Command\RunSqlCommand',
            'doctrine.dbal_cmd.import' => '\Doctrine\DBAL\Tools\Console\Command\ImportCommand',
            // ORM Commands
            'doctrine.orm_cmd.clear_cache_metadata' => '\Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand',
            'doctrine.orm_cmd.clear_cache_result' => '\Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand',
            'doctrine.orm_cmd.clear_cache_query' => '\Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand',
            'doctrine.orm_cmd.schema_tool_create' => '\Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand',
            'doctrine.orm_cmd.schema_tool_update' => '\Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand',
            'doctrine.orm_cmd.schema_tool_drop' => '\Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand',
            'doctrine.orm_cmd.convert_d1_schema' => '\Doctrine\ORM\Tools\Console\Command\ConvertDoctrine1SchemaCommand',
            'doctrine.orm_cmd.generate_entities' => '\Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand',
            'doctrine.orm_cmd.generate_proxies' => '\Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand',
            'doctrine.orm_cmd.convert_mapping' => '\Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand',
            'doctrine.orm_cmd.run_dql' => '\Doctrine\ORM\Tools\Console\Command\RunDqlCommand',
            'doctrine.orm_cmd.validate_schema' => '\Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand',
            'doctrine.orm_cmd.info' => '\Doctrine\ORM\Tools\Console\Command\InfoCommand',
            'doctrine.orm_cmd.ensure_production_settings'
                => '\Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand',
            'doctrine.orm_cmd.generate_repositories'
                => '\Doctrine\ORM\Tools\Console\Command\GenerateRepositoriesCommand',
        ),
    ),

    // Factory mappings - used to define which factory to use to instantiate a particular doctrine
    // service type
    'doctrine_factories' => array(
        'connection'               => 'DoctrineORMModule\Service\DBALConnectionFactory',
        'configuration'            => 'DoctrineORMModule\Service\ConfigurationFactory',
        'entitymanager'            => 'DoctrineORMModule\Service\EntityManagerFactory',
        'entity_resolver'          => 'DoctrineORMModule\Service\EntityResolverFactory',
        'sql_logger_collector'     => 'DoctrineORMModule\Service\SQLLoggerCollectorFactory',
        'mapping_collector'        => 'DoctrineORMModule\Service\MappingCollectorFactory',
        'formannotationbuilder'    => 'DoctrineORMModule\Service\FormAnnotationBuilderFactory',
        'migrations_configuration' => 'DoctrineORMModule\Service\MigrationsConfigurationFactory',
        'migrations_cmd'           => 'DoctrineORMModule\Service\MigrationsCommandFactory',
    ),

    // Zend\Form\FormElementManager configuration
    'form_elements' => array(
        'aliases' => array(
            'objectselect'        => 'DoctrineModule\Form\Element\ObjectSelect',
            'objectradio'         => 'DoctrineModule\Form\Element\ObjectRadio',
            'objectmulticheckbox' => 'DoctrineModule\Form\Element\ObjectMultiCheckbox',
        ),
        'factories' => array(
            'DoctrineModule\Form\Element\ObjectSelect'        => 'DoctrineORMModule\Service\ObjectSelectFactory',
            'DoctrineModule\Form\Element\ObjectRadio'         => 'DoctrineORMModule\Service\ObjectRadioFactory',
            'DoctrineModule\Form\Element\ObjectMultiCheckbox' => 'DoctrineORMModule\Service\ObjectMultiCheckboxFactory',
        ),
    ),

    'hydrators' => array(
        'factories' => array(
            'DoctrineModule\Stdlib\Hydrator\DoctrineObject' => 'DoctrineORMModule\Service\DoctrineObjectHydratorFactory'
        )
    ),

    ////////////////////////////////////////////////////////////////////
    // `zendframework/zend-developer-tools` specific settings         //
    // ignore these if you're not developing additional features for  //
    // zend developer tools                                           //
    ////////////////////////////////////////////////////////////////////

    'router' => array(
        'routes' => array(
            'doctrine_orm_module_yuml' => array(
                'type' => 'Zend\\Mvc\\Router\\Http\\Literal',
                'options' => array(
                    'route' => '/ocra_service_manager_yuml',
                    'defaults' => array(
                        'controller' => 'DoctrineORMModule\\Yuml\\YumlController',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'zend-developer-tools/toolbar/doctrine-orm-queries'
                => __DIR__ . '/../view/zend-developer-tools/toolbar/doctrine-orm-queries.phtml',
            'zend-developer-tools/toolbar/doctrine-orm-mappings'
                => __DIR__ . '/../view/zend-developer-tools/toolbar/doctrine-orm-mappings.phtml',
        ),
    ),

    'zenddevelopertools' => array(
        'profiler' => array(
            'collectors' => array(
                'doctrine.sql_logger_collector.orm_default' => 'doctrine.sql_logger_collector.orm_default',
                'doctrine.mapping_collector.orm_default'    => 'doctrine.mapping_collector.orm_default',
            ),
        ),
        'toolbar' => array(
            'entries' => array(
                'doctrine.sql_logger_collector.orm_default' => 'zend-developer-tools/toolbar/doctrine-orm-queries',
                'doctrine.mapping_collector.orm_default'    => 'zend-developer-tools/toolbar/doctrine-orm-mappings',
            ),
        ),
    ),
);
