#### Caching queries, results and metadata

If you want to set a cache for query, result and metadata, you can specify this inside your `module.config.php`

```php
'doctrine' => array(
    'configuration' => array(
        'orm_default' => array(
            'query_cache'       => 'apc',
            'result_cache'      => 'apc',
            'metadata_cache'    => 'apc'
        )
    )
),
```

The previous configuration take in consideration an Apc adapter. You can specify any other adapter that implements
the `Doctrine\Common\Cache\Cache` interface.

##### Example with Memcached

```php
'doctrine' => array(
    'configuration' => array(
        'orm_default' => array(
            'query_cache'       => 'memcached',
            'result_cache'      => 'memcached',
            'metadata_cache'    => 'memcached'
        )
    )
),
```

In this case you have to specify a custom factory in your `service_manager` configuration to create a
`Memcached` object:

```php
// module.config.php
'service_manager' => array(
    'factories' => array(
        'my_memcached_alias' => function() {
            $memcached = new \Memcached();
            $memcached->addServer('localhost', 11211);
            return $memcached;
        }
    )
),
```

Please be aware that you can't use *Closures* inside yout module configuration if you want to cache it. To avoid this problem, you have to use a Factory:
```php
// module.config.php
'service_manager' => array(
    'factories' => array(
        'my_memcached_alias' => __NAMESPACE__ . '\Cache\MemcachedFactory'
    )
),
```

```php
// MemcachedFactory.php
namespace YourModule\Cache;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MemcachedFactory implements FactoryInterface {

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return \Memcached
     */
    public function createService(ServiceLocatorInterface $serviceLocator) {

        $memcached = new \Memcached();
        $memcached->addServer('localhost', 11211);
        return $memcached;
    }

}
```


Other supported that need a custom factory are:

* `memcache`: require you to return a `Memcache` instance (use the `my_memcache_alias` as the key in the
service manager).
* `redis`: require you to return a `Redis` instance (use the `my_redis_alias` as the key in the service manager).
