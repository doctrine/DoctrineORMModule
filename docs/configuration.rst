Configuration
=============

Register a Custom DQL Function
------------------------------

.. code:: php

    namespace Db;

    return [
        'doctrine' => [
            'configuration' => [
                'orm_default' => [
                    'numeric_functions' => [
                        'ROUND' => Db\DoctrineExtensions\Query\Mysql\Round::class,
                    ],
                ],
            ],
        ],
    ];


Register a Type mapping
-----------------------

.. code:: php

    return [
        'doctrine' => [
            'connection' => [
                'orm_default' => [
                    'doctrine_type_mappings' => [
                        'enum' => 'string',
                    ],
                ],
            ],
        ],
    ];


How to add a new type
---------------------

.. code:: php

    return [
        'doctrine' => [
            'configuration' => [
                'orm_default' => [
                    'types' => [
                        'newtype' => 'Db\DBAL\Types\NewType',
                    ],
                ],
            ],
        ],
    ];

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


Doctrine Type Comment
---------------------

Option to set the doctrine type comment (DC2Type:myType) for custom types

.. code:: php

    return [
        'doctrine' => [
            'connection' => [
                'orm_default' => [
                    'doctrineCommentedTypes' => [
                        'mytype',
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
                        'Acme\\InvoiceModule\\Model\\InvoiceSubjectInterface',
                        'Acme\\CustomerModule\\Entity\\Customer',
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
                    'driverClass'   => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
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
                    'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                    'cache' => 'array',
                    'paths' => [
                        __DIR__ . '/../src/Crawler/Entity',
                    ],
                ],
                'orm_crawler_chain' => [
                    'class'   => 'Doctrine\ORM\Mapping\Driver\DriverChain',
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


How to Use Naming Strategy
--------------------------

`Official documentation 
<https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/namingstrategy.html>`__

Zend Configuration

.. code:: php

    return [
        'service_manager' => [
            'invokables' => [
                'Doctrine\ORM\Mapping\UnderscoreNamingStrategy' => 'Doctrine\ORM\Mapping\UnderscoreNamingStrategy',
            ],
        ],
        'doctrine' => [
            'configuration' => [
                'orm_default' => [
                    'naming_strategy' => 'Doctrine\ORM\Mapping\UnderscoreNamingStrategy',
                ],
            ],
        ],
    ];

How to Use Quote Strategy
-------------------------

`Official
documentation <https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/basic-mapping.html#quoting-reserved-words>`__

Zend Configuration

.. code:: php

    return [
        'service_manager' => [
            'invokables' => [
                'Doctrine\ORM\Mapping\AnsiQuoteStrategy' => 'Doctrine\ORM\Mapping\AnsiQuoteStrategy',
            ],
        ],
        'doctrine' => [
            'configuration' => [
                'orm_default' => [
                    'quote_strategy' => 'Doctrine\ORM\Mapping\AnsiQuoteStrategy',
                ],
            ],
        ],
    ];

