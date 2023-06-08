<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Service;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\Psr6\CacheAdapter;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Middleware;
use Doctrine\ORM\Cache\CacheConfiguration;
use Doctrine\ORM\Cache\DefaultCacheFactory;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\EntityListenerResolver;
use Doctrine\ORM\Mapping\NamingStrategy;
use Doctrine\ORM\Mapping\QuoteStrategy;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use DoctrineModule\Cache\LaminasStorageCache;
use DoctrineORMModule\Options\Configuration;
use DoctrineORMModule\Service\ConfigurationFactory;
use DoctrineORMModuleTest\Assets\RepositoryClass;
use Laminas\Cache\Storage\Adapter\Memory;
use Laminas\ServiceManager\Exception\InvalidArgumentException;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use stdClass;
use UnexpectedValueException;

use function assert;
use function class_exists;
use function get_class;

class ConfigurationFactoryTest extends TestCase
{
    protected ServiceManager $serviceManager;
    protected ConfigurationFactory $factory;

    public function setUp(): void
    {
        $this->serviceManager = new ServiceManager();
        $this->factory        = new ConfigurationFactory('test_default');
        $this->serviceManager->setService('doctrine.cache.array', $this->getArrayCacheInstance());
        $this->serviceManager->setService(
            'doctrine.driver.orm_default',
            $this->createMock(MappingDriver::class)
        );
    }

    protected function getArrayCacheInstance(): object
    {
        // Set up appropriate cache based on DoctrineModule version detection:
        return class_exists(ArrayCache::class)
            ? new ArrayCache()                          // DoctrineModule 5
            : new LaminasStorageCache(new Memory());    // DoctrineModule 6
    }

    public function testWillInstantiateConfigWithoutNamingStrategySetting(): void
    {
        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => [],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);
        $ormConfig = ($this->factory)($this->serviceManager, Configuration::class);
        $this->assertInstanceOf(NamingStrategy::class, $ormConfig->getNamingStrategy());
    }

    public function testWillInstantiateConfigWithNamingStrategyObject(): void
    {
        $namingStrategy = $this->createMock(NamingStrategy::class);

        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => ['naming_strategy' => $namingStrategy],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);
        $factory   = new ConfigurationFactory('test_default');
        $ormConfig = $factory($this->serviceManager, Configuration::class);
        $this->assertSame($namingStrategy, $ormConfig->getNamingStrategy());
    }

    public function testWillInstantiateConfigWithNamingStrategyReference(): void
    {
        $namingStrategy = $this->createMock(NamingStrategy::class);
        $config         = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => ['naming_strategy' => 'test_naming_strategy'],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);
        $this->serviceManager->setService('test_naming_strategy', $namingStrategy);
        $ormConfig = ($this->factory)($this->serviceManager, Configuration::class);
        $this->assertSame($namingStrategy, $ormConfig->getNamingStrategy());
    }

    public function testWillNotInstantiateConfigWithInvalidNamingStrategyReference(): void
    {
        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => ['naming_strategy' => 'test_naming_strategy'],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);
        $this->expectException(InvalidArgumentException::class);
        ($this->factory)($this->serviceManager, Configuration::class);
    }

    public function testWillInstantiateConfigWithQuoteStrategyObject(): void
    {
        $quoteStrategy = $this->createMock(QuoteStrategy::class);

        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => ['quote_strategy' => $quoteStrategy],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);
        $factory   = new ConfigurationFactory('test_default');
        $ormConfig = $factory($this->serviceManager, Configuration::class);
        $this->assertSame($quoteStrategy, $ormConfig->getQuoteStrategy());
    }

    public function testWillInstantiateConfigWithQuoteStrategyReference(): void
    {
        $quoteStrategy = $this->createMock(QuoteStrategy::class);
        $config        = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => ['quote_strategy' => 'test_quote_strategy'],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);
        $this->serviceManager->setService('test_quote_strategy', $quoteStrategy);
        $ormConfig = ($this->factory)($this->serviceManager, Configuration::class);
        $this->assertSame($quoteStrategy, $ormConfig->getQuoteStrategy());
    }

    public function testWillNotInstantiateConfigWithInvalidQuoteStrategyReference(): void
    {
        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => ['quote_strategy' => 'test_quote_strategy'],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);
        $this->expectException(InvalidArgumentException::class);
        ($this->factory)($this->serviceManager, Configuration::class);
    }

    public function testWillInstantiateConfigWithHydrationCacheSetting(): void
    {
        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => ['hydration_cache' => 'array'],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);
        $factory   = new ConfigurationFactory('test_default');
        $ormConfig = $factory($this->serviceManager, Configuration::class);
        $this->assertInstanceOf(get_class($this->getArrayCacheInstance()), $ormConfig->getHydrationCacheImpl());
    }

    public function testCanSetDefaultRepositoryClass(): void
    {
        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => [

                        'hydration_cache' => 'array',
                        'default_repository_class_name' => RepositoryClass::class,
                    ],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);

        $factory   = new ConfigurationFactory('test_default');
        $ormConfig = $factory($this->serviceManager, Configuration::class);
        $this->assertInstanceOf(get_class($this->getArrayCacheInstance()), $ormConfig->getHydrationCacheImpl());
    }

    public function testAcceptsMetadataFactory(): void
    {
        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => ['classMetadataFactoryName' => 'Factory'],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);
        $factory   = new ConfigurationFactory('test_default');
        $ormConfig = $factory($this->serviceManager, Configuration::class);
        $this->assertEquals('Factory', $ormConfig->getClassMetadataFactoryName());
    }

    public function testDefaultMetadatFactory(): void
    {
        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => [],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);
        $factory   = new ConfigurationFactory('test_default');
        $ormConfig = $factory($this->serviceManager, Configuration::class);
        $this->assertEquals(
            ClassMetadataFactory::class,
            $ormConfig->getClassMetadataFactoryName()
        );
    }

    public function testWillInstantiateConfigWithoutEntityListenerResolverSetting(): void
    {
        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => [],
                ],
            ],
        ];

        $this->serviceManager->setService('config', $config);

        $ormConfig = ($this->factory)($this->serviceManager, Configuration::class);

        $this->assertInstanceOf(
            EntityListenerResolver::class,
            $ormConfig->getEntityListenerResolver()
        );
    }

    public function testWillInstantiateConfigWithEntityListenerResolverObject(): void
    {
        $entityListenerResolver = $this->createMock(EntityListenerResolver::class);

        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => ['entity_listener_resolver' => $entityListenerResolver],
                ],
            ],
        ];

        $this->serviceManager->setService('config', $config);

        $ormConfig = ($this->factory)($this->serviceManager, Configuration::class);

        $this->assertSame($entityListenerResolver, $ormConfig->getEntityListenerResolver());
    }

    public function testWillInstantiateConfigWithSchemaAssetsFilterCallback(): void
    {
        $schemaAssetsFilter = static function ($tableName) {
            return $tableName === 'foobar';
        };

        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => ['schema_assets_filter' => $schemaAssetsFilter],
                ],
            ],
        ];

        $this->serviceManager->setService('config', $config);

        $ormConfig = ($this->factory)($this->serviceManager, Configuration::class);

        $this->assertSame($schemaAssetsFilter, $ormConfig->getSchemaAssetsFilter());
    }

    public function testWillInstantiateConfigWithEntityListenerResolverReference(): void
    {
        $entityListenerResolver = $this->createMock(EntityListenerResolver::class);

        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => ['entity_listener_resolver' => 'test_entity_listener_resolver'],
                ],
            ],
        ];

        $this->serviceManager->setService('config', $config);
        $this->serviceManager->setService('test_entity_listener_resolver', $entityListenerResolver);

        $ormConfig = ($this->factory)($this->serviceManager, Configuration::class);

        $this->assertSame($entityListenerResolver, $ormConfig->getEntityListenerResolver());
    }

    public function testDoNotCreateSecondLevelCacheByDefault(): void
    {
        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => [],
                ],
            ],
        ];

        $this->serviceManager->setService('config', $config);

        $ormConfig = ($this->factory)($this->serviceManager, Configuration::class);

        $this->assertNull($ormConfig->getSecondLevelCacheConfiguration());
    }

    public function testCanInstantiateWithSecondLevelCacheConfig(): void
    {
        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => [
                        'result_cache' => 'array',

                        'second_level_cache' => [
                            'enabled'                    => true,
                            'default_lifetime'           => 200,
                            'default_lock_lifetime'      => 500,
                            'file_lock_region_directory' => 'my_dir',

                            'regions' => [
                                'my_first_region' => [
                                    'lifetime'      => 800,
                                    'lock_lifetime' => 1000,
                                ],

                                'my_second_region' => [
                                    'lifetime'      => 10,
                                    'lock_lifetime' => 20,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->serviceManager->setService('config', $config);

        $ormConfig        = ($this->factory)($this->serviceManager, Configuration::class);
        $secondLevelCache = $ormConfig->getSecondLevelCacheConfiguration();

        $this->assertInstanceOf(CacheConfiguration::class, $secondLevelCache);

        $cacheFactory = $secondLevelCache->getCacheFactory();
        assert($cacheFactory instanceof DefaultCacheFactory);
        $this->assertInstanceOf(DefaultCacheFactory::class, $cacheFactory);
        $this->assertEquals('my_dir', $cacheFactory->getFileLockRegionDirectory());

        $regionsConfiguration = $secondLevelCache->getRegionsConfiguration();
        $this->assertEquals(200, $regionsConfiguration->getDefaultLifetime());
        $this->assertEquals(500, $regionsConfiguration->getDefaultLockLifetime());

        $this->assertEquals(800, $regionsConfiguration->getLifetime('my_first_region'));
        $this->assertEquals(10, $regionsConfiguration->getLifetime('my_second_region'));

        $this->assertEquals(1000, $regionsConfiguration->getLockLifetime('my_first_region'));
        $this->assertEquals(20, $regionsConfiguration->getLockLifetime('my_second_region'));

        // Doctrine does not allow to retrieve the cache adapter from cache factory, so we are forced to use
        // reflection here
        $reflProperty = new ReflectionProperty($cacheFactory, 'cacheItemPool');
        $reflProperty->setAccessible(true);
        $cacheDecorator = $reflProperty->getValue($cacheFactory);
        $this->assertInstanceOf(CacheAdapter::class, $cacheDecorator);
        $this->assertInstanceOf(get_class($this->getArrayCacheInstance()), $cacheDecorator->getCache());
    }

    public function testConfigureMiddlewares(): void
    {
        if (! class_exists(Middleware::class)) {
            $this->markTestSkipped('Middleware feature not exists in DBAL v2');
        }

        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => [
                        'middlewares' => ['middleware'],
                    ],
                ],
            ],
        ];

        $this->serviceManager->setService('config', $config);
        $this->serviceManager->setService('middleware', new class implements Middleware {
            public function wrap(Driver $driver): Driver
            {
                return $driver;
            }
        });

        $ormConfig = ($this->factory)($this->serviceManager, Configuration::class);

        $this->assertInstanceOf(Middleware::class, $ormConfig->getMiddlewares()[0] ?? null);
    }

    public function testConfigureMiddlewaresNotExisting(): void
    {
        if (! class_exists(Middleware::class)) {
            $this->markTestSkipped('Middleware feature not exists in DBAL v2');
        }

        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => [
                        'middlewares' => ['middleware'],
                    ],
                ],
            ],
        ];

        $this->serviceManager->setService('config', $config);

        $this->expectException(\InvalidArgumentException::class);

        ($this->factory)($this->serviceManager, Configuration::class);
    }

    public function testConfigureWrongMiddlewares(): void
    {
        if (! class_exists(Middleware::class)) {
            $this->markTestSkipped('Middleware feature not exists in DBAL v2');
        }

        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => [
                        'middlewares' => ['middleware'],
                    ],
                ],
            ],
        ];

        $this->serviceManager->setService('config', $config);
        $this->serviceManager->setService('middleware', new stdClass());

        $this->expectException(UnexpectedValueException::class);

        ($this->factory)($this->serviceManager, Configuration::class);
    }
}
