#### How to Register Custom DQL Functions

```php
return array(
    'doctrine' => array(
        'configuration' => array(
            'orm_default' => array(
                'numeric_functions' => array(
                    'ROUND' => 'path\to\my\query\round'
                )
            )
        ),
    ),
)
```

### How to register type mapping

```php
'doctrine' => array(
    'connection' => array(
        'orm_default' => array(
            'doctrine_type_mappings' => array(
                'enum' => 'string'
            ),
        )
    )
),
```

### How to add new type

```php
'doctrine' => array(
    'configuration' => array(
        'orm_default' => array(
            'types' => array(
                'mytype' => 'Application\Types\MyType'
            )
        )
    ),
),
```

```php
'connection' => array(
    'orm_default' => array(
        'doctrine_type_mappings' => array(
            'mytype' => 'mytype'
        ),
    )
),
```

### How to Define Relationships with Abstract Classes and Interfaces (ResolveTargetEntityListener)

```php
'doctrine' => array(
    'entity_resolver' => array(
        'orm_default' => array(
            'resolvers' => array(
                'Acme\\InvoiceModule\\Model\\InvoiceSubjectInterface', 'Acme\\CustomerModule\\Entity\\Customer'
            )
        )
    )
)
```

### How to enable and configure second-level cache

```php
'doctrine' => array(
   'configuration' => array(
       'orm_default' => array(
           'result_cache' => 'apc', // Second level cache reuse the cache defined in result cache

           'second_level_cache' => array(
               'enabled'               => true,
               'default_lifetime'      => 200,
               'default_lock_lifetime' => 500,

               'regions' => array(
                   'my_first_region' => array(
                       'lifetime'      => 800,
                       'lock_lifetime' => 1000
                   ),

                   'my_second_region' => array(
                       'lifetime'      => 10,
                       'lock_lifetime' => 20
                   )
               )
           )
       ),
   ),
)
```

You also need to add the `Cache` annotation to your model ([read more](http://doctrine-orm.readthedocs.org/en/latest/reference/second-level-cache.html#entity-cache-definition)).

### Set a custom default repository

```php
'doctrine' => array(
    'configuration' => array(
        'orm_default' => array(
            'default_repository_class_name' => 'MyCustomRepository'
        )
    )
)
```

### How to Use Two Connections

```php
'doctrine' => array(
        'connection' => array(
            'orm_crawler' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => array(
                    'host'     => 'localhost',
                    'port'     => '3306',
                    'user'     => 'root',
                    'password' => 'root',
                    'dbname'   => 'crawler',
                    'driverOptions' => array(
                        1002 => 'SET NAMES utf8'
                    ),
                )
            )
        ),

        'configuration' => array(
            'orm_crawler' => array(
                'metadata_cache'    => 'array',
                'query_cache'       => 'array',
                'result_cache'      => 'array',
                'hydration_cache'   => 'array',
                'driver'            => 'orm_crawler',
                'generate_proxies'  => true,
                'proxy_dir'         => 'data/DoctrineORMModule/Proxy',
                'proxy_namespace'   => 'DoctrineORMModule\Proxy',
                'filters'           => array()
            )
        ),

        'driver' => array(
            'Crawler_Driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/Crawler/Entity'
                )
            ),
            'orm_crawler' => array(
                'class'   => 'Doctrine\ORM\Mapping\Driver\DriverChain',
                'drivers' => array(
                    'Crawler\Entity' =>  'Crawler_Driver'
                )
            ),
        ),

        'entitymanager' => array(
            'orm_crawler' => array(
                'connection'    => 'orm_crawler',
                'configuration' => 'orm_crawler'
            )
        ),

        'eventmanager' => array(
            'orm_crawler' => array()
        ),

        'sql_logger_collector' => array(
            'orm_crawler' => array(),
        ),

        'entity_resolver' => array(
            'orm_crawler' => array()
        ),

    ),
```

Module.php
```php
public function getServiceConfig()
{
    return array(
        'factories' => array(
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
        ),
    );
}
```

## How to use second level cache

> Don't forget to also make your entities cacheable for this to work ([learn more](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/second-level-cache.html))

```php
'doctrine' => array(
    'configuration' => array(
        'orm_default' => array(
            'second_level_cache' => array(
                'enabled'                        => true,
                'default_lifetime'               => 3600,
                'default_lock_lifetime'          => 60,
                'file_lock_region_directory' => __DIR__ . '/../my_dir',
                'regions'                        => array(
                    'My\Region\Name' => array(
                        'lifetime'      => 20,
                        'lock_lifetime' => 200
                    )
                )
            )
        )
    ),
),
```
