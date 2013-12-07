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

#### How to use Memcache

```php
'doctrine' => array(
    'configuration' => array(
        'orm_default' => array(
            'metadata_cache'    => 'my_memcache',
            'query_cache'       => 'my_memcache',
            'result_cache'      => 'my_memcache',
            'hydration_cache'   => 'my_memcache',
        )
    ),
);
```

```php
'service_manager' => array(
    'factories' => array(
        'doctrine.cache.my_memcache' => function ($sm) {
            $cache = new \Doctrine\Common\Cache\MemcacheCache();
            $memcache = new \Memcache();
            $memcache->connect('localhost', 11211);
            $cache->setMemcache($memcache);
            return $cache;
        },
    ),
),
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
            'doctrine.sql_logger_collector.orm_crawler' => new \DoctrineORMModule\Service\EntityResolverFactory('orm_crawler'),

            'DoctrineORMModule\Form\Annotation\AnnotationBuilder' => function(\Zend\ServiceManager\ServiceLocatorInterface $sl) {
                return new \DoctrineORMModule\Form\Annotation\AnnotationBuilder($sl->get('doctrine.entitymanager.orm_crawler'));
            },
        ),
    );
}
```
