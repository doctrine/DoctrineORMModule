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