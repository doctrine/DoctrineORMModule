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
`here <https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/caching.html>`__.


Example with Redis
------------------

.. code:: php

    return [
        'doctrine' => [
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

In this case you have to specify a custom factory in your
``service_manager`` configuration to create a ``Redis`` object:

.. code:: php

    // module.config.php
    return [
        'service_manager' => [
            'factories' => [
                __NAMESPACE__ . '\Cache\Redis' => __NAMESPACE__ . '\Cache\RedisFactory',
            ],
        ],
        'doctrine' => [
            'cache' => [
                'redis' => [
                    'namespace' => __NAMESPACE__ . '_Doctrine',
                    'instance'  => __NAMESPACE__ . '\Cache\Redis',
                ],
            ],
        ],
    ];

.. code:: php

    // RedisFactory.php
    namespace YourModule\Cache;

    use Zend\ServiceManager\FactoryInterface;
    use Zend\ServiceManager\ServiceLocatorInterface;

    class RedisFactory implements FactoryInterface
    {
        public function createService(ServiceLocatorInterface $serviceLocator)
        {
            $redis = new Redis();
            $redis->connect('127.0.0.1', 6379);

            return $redis;
        }
    }

Read more about
`Caching <https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/caching.html>`__.


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
more <https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/second-level-cache.html#entity-cache-definition>`__).
Read more about `Second Level
Cache <http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/second-level-cache.html>`__.
