Caching
=======

Caching is very important in Doctrine.

In this example for Metadata, Queries, and Results we set an array 
cache for the result\_cache.  Please note the array cache is for 
development only and shown here along side other cache types.

If you want to set a cache for query, result and metadata, you can
specify this inside your ``config/autoload/local.php``

.. code:: php

    return [
        'doctrine' => [
            'configuration' => [
                'orm_default' => [
                    'query_cache'       => 'filesystem',
                    'result_cache'      => 'array',
                    'metadata_cache'    => 'apc',
                    'hydration_cache'   => 'memcached',
                ],
            ],
        ],
    ];

The previous configuration takes into consideration different cache
adapters. You can specify any other adapter that implements the
``Doctrine\Common\Cache\Cache`` interface. Find more
`here <https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/caching.html>`__.


Redis Example
-------------

This example uses a factory to create the Redis cache object.  The Redis install used here
can be found at `https://github.com/phpredis/phpredis#class-redis <https://github.com/phpredis/phpredis#class-redis>`__
See also `https://redislabs.com/lp/php-redis/ <https://redislabs.com/lp/php-redis/>`__

module.config.php

.. code:: php

    namespace Db;

    return [
        'service_manager' => [
            'factories' => [
                 'Db\Cache\Redis' => Db\Cache\RedisFactory::class,
            ],
        ],
        'doctrine' => [
            'cache' => [
                'redis' => [
                    'namespace' => 'Db_Doctrine',
                    'instance'  => 'Db\Cache\Redis',
                ],
            ],
            'configuration' => [
                'orm_default' => [
                    'query_cache'       => 'redis',
                    'result_cache'      => 'redis',
                    'metadata_cache'    => 'redis',
                    'hydration_cache'   => 'redis',
                ],
            ],
        ],
    ];


Db\\Cache\\RedisFactory

.. code:: php

    namespace Db\Cache;

    use Interop\Container\ContainerInterface;
    use Redis;

    class RedisFactory
    {
        public function __invoke(
            ContainerInterface $container,
            $requestedName,
            array $options = null
        ) {
            $redis = new Redis(); 
            $redis->connect('127.0.0.1', 6379);

            return $redis;
        }
    }


Read more about
`Caching <https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/caching.html>`__.


How to enable and configure Second Level Cache
----------------------------------------------

.. code:: php

    return [
        'doctrine' => [
            'configuration' => [
                'orm_default' => [
                    'result_cache' => 'redis', // Second level cache reuse the cache defined in result cache
                    'second_level_cache' => [
                        'enabled'               => true,
                        'default_lifetime'      => 200,
                        'default_lock_lifetime' => 500,
                        'file_lock_region_directory' => __DIR__ . '/../my_dir',
                        'regions' => [
                            'My\FirstRegion\Name' => [
                                'lifetime'      => 800,
                                'lock_lifetime' => 1000,
                            ],
                            'My\SecondRegion\Name' => [
                                'lifetime'      => 10,
                                'lock_lifetime' => 20,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

You also need to add the ``Cache`` annotation to your model (`read
more <https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/second-level-cache.html#entity-cache-definition>`__).
Read more about `Second Level
Cache <https://docs.doctrine-project.org/projects/doctrine-orm/en/current/reference/second-level-cache.html>`__.
