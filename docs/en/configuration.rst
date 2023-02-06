Configuration
=============

Register a Custom DQL Function
------------------------------

.. code:: php

    return [
        'doctrine' => [
            'configuration' => [
                'orm_default' => [
                    'numeric_functions' => [
                        'ROUND' => \My\DoctrineExtensions\Query\Mysql\Round::class,
                    ],
                ],
            ],
        ],
    ];

How to add a Custom Type
------------------------

First, implement a new type by extending `Doctrine\DBAL\Types\Type`. An example can be found in
the `ORM cookbook <https://www.doctrine-project.org/projects/doctrine-orm/en/current/cookbook/custom-mapping-types.html#custom-mapping-types>`__
Then, register your type implementation with DBAL as follows:

.. code:: php

    return [
        'doctrine' => [
            'configuration' => [
                'orm_default' => [
                    'types' => [
                        'newtype' => \My\Types\NewType::class,
                    ],
                ],
            ],
        ],
    ];

.. note::

    If your type uses a database type which is already `mapped by Doctrine <https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html#mapping-matrix>`__,
    Doctrine will need a comment hint to distinguish your type from other types. In your type class, override
    `requiresSQLCommentHint()` to return `true` to let Doctrine add a comment hint.

Next, you will need to register your custom type with the underlying database platform:

.. code:: php

    return [
        'doctrine' => [
            'connection' => [
                'orm_default' => [
                    'doctrine_type_mappings' => [
                        'mytype' => 'mytype',
                    ],
                ],
            ],
        ],
    ];

Using DBAL Middlewares
----------------------

.. note::

    This feature is only available when using DBAL 3.x and has no effect on DBAL 2.x!

`Official documentation <https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/architecture.html#middlewares>`__

Laminas configuration

.. code:: php

    return [
        'service_manager' => [
            'invokables' => [
                \My\Middlewares\CustomMiddleware::class => \My\Middlewares\CustomMiddleware::class,
                \My\Middlewares\AnotherCustomMiddleware::class => \My\Middlewares\AnotherCustomMiddleware::class,
            ],
        ],
        'doctrine' => [
            'configuration' => [
                'test_default' => [
                    'middlewares' => [
                        \My\Middlewares\CustomMiddleware::class,
                        \My\Middlewares\AnotherCustomMiddleware::class,
                    ],
                ],
            ],
        ],
    ];

Built-in Resolver
-----------------

How to Define Relationships with Abstract Classes and Interfaces (ResolveTargetEntityListener)

.. code:: php

    return [
        'doctrine' => [
            'entity_resolver' => [
                'orm_default' => [
                    'resolvers' => [
                        \Acme\InvoiceModule\Model\InvoiceSubjectInterface::class,
                        \Acme\CustomerModule\Entity\Customer::class,
                    ],
                ],
            ],
        ],
    ];


Set a Custom Default Repository
-------------------------------

.. code:: php

    return [
        'doctrine' => [
            'configuration' => [
                'orm_default' => [
                    'default_repository_class_name' => 'MyCustomRepository',
                ],
            ],
        ],
    ];


How to Use Two Connections
--------------------------

In this example we create an 'orm_crawler' ORM connection.
See also `this blog article <https://blog.tomhanderson.com/2016/03/zf2-doctrine-configure-second-object.html>`__.

.. code:: php

    return [
        'doctrine' => [
            'connection' => [
                'orm_crawler' => [
                    'driverClass'   => \Doctrine\DBAL\Driver\PDO\MySQL\Driver::class,
                    'eventmanager'  => 'orm_crawler',
                    'configuration' => 'orm_crawler',
                    'params'        => [
                        'host'     => 'localhost',
                        'port'     => '3306',
                        'user'     => 'root',
                        'password' => 'root',
                        'dbname'   => 'crawler',
                        'driverOptions' => [
                            1002 => 'SET NAMES utf8',
                        ],
                    ],
                ],
            ],

            'configuration' => [
                'orm_crawler' => [
                    'metadata_cache'    => 'array',
                    'query_cache'       => 'array',
                    'result_cache'      => 'array',
                    'hydration_cache'   => 'array',
                    'driver'            => 'orm_crawler_chain',
                    'generate_proxies'  => true,
                    'proxy_dir'         => 'data/DoctrineORMModule/Proxy',
                    'proxy_namespace'   => 'DoctrineORMModule\Proxy',
                    'filters'           => [],
                ],
            ],

            'driver' => [
                'orm_crawler_annotation' => [
                    'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                    'cache' => 'array',
                    'paths' => [
                        __DIR__ . '/../src/Crawler/Entity',
                    ],
                ],
                'orm_crawler_chain' => [
                    'class'   => \Doctrine\ORM\Mapping\Driver\DriverChain::class,
                    'drivers' => [
                        'Crawler\Entity' =>  'orm_crawler_annotation',
                    ],
                ],
            ],

            'entitymanager' => [
                'orm_crawler' => [
                    'connection'    => 'orm_crawler',
                    'configuration' => 'orm_crawler',
                ],
            ],

            'eventmanager' => [
                'orm_crawler' => [],
            ],

            'sql_logger_collector' => [
                'orm_crawler' => [],
            ],

            'entity_resolver' => [
                'orm_crawler' => [],
            ],
        ],
    ];

The ``DoctrineModule\ServiceFactory\AbstractDoctrineServiceFactory`` will create the following objects as needed:

    * doctrine.connection.orm_crawler
    * doctrine.configuration.orm_crawler
    * doctrine.entitymanager.orm_crawler
    * doctrine.driver.orm_crawler
    * doctrine.eventmanager.orm_crawler
    * doctrine.entity_resolver.orm_crawler
    * doctrine.sql_logger_collector.orm_crawler


You can retrieve them from the service manager via their keys.


How to Use a Naming Strategy
----------------------------

`Official documentation 
<https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/namingstrategy.html>`__

Laminas Configuration

.. code:: php

    return [
        'service_manager' => [
            'invokables' => [
                \Doctrine\ORM\Mapping\UnderscoreNamingStrategy::class => \Doctrine\ORM\Mapping\UnderscoreNamingStrategy::class,
            ],
        ],
        'doctrine' => [
            'configuration' => [
                'orm_default' => [
                    'naming_strategy' => \Doctrine\ORM\Mapping\UnderscoreNamingStrategy::class,
                ],
            ],
        ],
    ];

How to Use a Quote Strategy
---------------------------

`Official
documentation <https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/basic-mapping.html#quoting-reserved-words>`__

Laminas Configuration

.. code:: php

    return [
        'service_manager' => [
            'invokables' => [
                \Doctrine\ORM\Mapping\AnsiQuoteStrategy::class => \Doctrine\ORM\Mapping\AnsiQuoteStrategy::class,
            ],
        ],
        'doctrine' => [
            'configuration' => [
                'orm_default' => [
                    'quote_strategy' => \Doctrine\ORM\Mapping\AnsiQuoteStrategy::class,
                ],
            ],
        ],
    ];

How to Override RunSqlCommand Creation
--------------------------------------

The following Laminas configuration can be used to override the creation of the
``Doctrine\DBAL\Tools\Console\Command\RunSqlCommand`` instance used by this
module.

.. code:: php

    return [
        'service_manager' => [
            'factories' => [
                'doctrine.dbal_cmd.runsql' => MyCustomRunSqlCommandFactory::class,
            ],
        ],
    ];

How to Exclude Tables from a Schema Diff
----------------------------------------

The "schema_assets_filter" option can be used to exclude certain tables from being deleted in a schema update.
It should be set with a filter callback that will receive the table name and should return `false` for any tables that must be excluded and `true` for any other tables.

.. code:: php

    return [
        'doctrine' => [
            'configuration' => [
                'orm_default' => [
                    'schema_assets_filter' => fn (string $tableName): bool => (
                        ! in_array($tableName, ['doNotRemoveThisTable', 'alsoDoNotRemoveThisTable'])
                    ),
                ],
            ],
        ],
    ];

.. note::

    If you want your application config to be cached, you should use a callable in terms of a static
    function (like `MyFilterClass::filter`) instead of a closure.
