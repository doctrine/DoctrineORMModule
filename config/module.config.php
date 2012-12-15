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
                'filters'           => array()
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

        // collector SQL logger, used when ZendDeveloperTools and its toolbar are active
        'sql_logger_collector' => array(
            // configuration for the `doctrine.sql_logger_collector.orm_default` service
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
            'zend-developer-tools/toolbar/doctrine-orm-queries'  => __DIR__ . '/../view/zend-developer-tools/toolbar/doctrine-orm-queries.phtml',
            'zend-developer-tools/toolbar/doctrine-orm-mappings' => __DIR__ . '/../view/zend-developer-tools/toolbar/doctrine-orm-mappings.phtml',
        ),
    ),

    'zenddevelopertools' => array(
        'profiler' => array(
            'collectors' => array(
                'orm_default'  => 'doctrine.sql_logger_collector.orm_default',
                'orm_default_mappings' => 'doctrine.mapping_collector.orm_default',
            ),
        ),
        'toolbar' => array(
            'entries' => array(
                'orm_default'  => 'zend-developer-tools/toolbar/doctrine-orm-queries',
                'orm_default_mappings' => 'zend-developer-tools/toolbar/doctrine-orm-mappings',
            ),
        ),
    ),
);
