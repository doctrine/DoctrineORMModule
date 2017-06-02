### How to Register Custom DQL Functions

```php
return [
    'doctrine' => [
        'configuration' => [
            'orm_default' => [
                'numeric_functions' => [
                    'ROUND' => 'path\to\my\query\round',
                ],
            ],
        ],
    ],
];
```

### How to register type mapping

```php
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
```

### How to add new type

```php
return [
    'doctrine' => [
        'configuration' => [
            'orm_default' => [
                'types' => [
                    'mytype' => 'Application\Types\MyType',
                ],
            ],
        ],
    ],
];
```

```php
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
```

### Option to set the doctrine type comment (DC2Type:myType) for custom types

```php
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
```

### How to Define Relationships with Abstract Classes and Interfaces (ResolveTargetEntityListener)

```php
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
```

### Set a custom default repository

```php
return [
    'doctrine' => [
        'configuration' => [
            'orm_default' => [
                'default_repository_class_name' => 'MyCustomRepository',
            ],
        ],
    ],
];
```

### How to Use Two Connections

```php
return [
    'doctrine' => [
        'connection' => [
            'orm_crawler' => [
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => [
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
                'driver'            => 'orm_crawler',
                'generate_proxies'  => true,
                'proxy_dir'         => 'data/DoctrineORMModule/Proxy',
                'proxy_namespace'   => 'DoctrineORMModule\Proxy',
                'filters'           => [],
            ],
        ],

        'driver' => [
            'Crawler_Driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Crawler/Entity',
                ],
            ],
            'orm_crawler' => [
                'class'   => 'Doctrine\ORM\Mapping\Driver\DriverChain',
                'drivers' => [
                    'Crawler\Entity' =>  'Crawler_Driver',
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
```

Module.php
```php
public function getServiceConfig()
{
    return [
        'factories' => [
            'doctrine.connection.orm_crawler'           => new \DoctrineORMModule\Service\DBALConnectionFactory('orm_crawler'),
            'doctrine.configuration.orm_crawler'        => new \DoctrineORMModule\Service\ConfigurationFactory('orm_crawler'),
            'doctrine.entitymanager.orm_crawler'        => new \DoctrineORMModule\Service\EntityManagerFactory('orm_crawler'),

            'doctrine.driver.orm_crawler'               => new \DoctrineModule\Service\DriverFactory('orm_crawler'),
            'doctrine.eventmanager.orm_crawler'         => new \DoctrineModule\Service\EventManagerFactory('orm_crawler'),
            'doctrine.entity_resolver.orm_crawler'      => new \DoctrineORMModule\Service\EntityResolverFactory('orm_crawler'),
            'doctrine.sql_logger_collector.orm_crawler' => new \DoctrineORMModule\Service\SQLLoggerCollectorFactory('orm_crawler'),

            'DoctrineORMModule\Form\Annotation\AnnotationBuilder' => function(\Zend\ServiceManager\ServiceLocatorInterface $sl) {
                return new \DoctrineORMModule\Form\Annotation\AnnotationBuilder($sl->get('doctrine.entitymanager.orm_crawler'));
            },
        ],
    ];
}
```

### How to Use Naming Strategy

[Official documentation](http://doctrine-orm.readthedocs.org/en/latest/reference/namingstrategy.html)

Zend Configuration

```php
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
```

### How to Use Quote Strategy

[Official documentation](http://doctrine-orm.readthedocs.org/projects/doctrine-orm/en/latest/reference/basic-mapping.html#quoting-reserved-words)

Zend Configuration

```php
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
```
