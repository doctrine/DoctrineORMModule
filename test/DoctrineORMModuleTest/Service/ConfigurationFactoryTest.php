<?php

namespace DoctrineORMModuleTest\Service;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Cache\CacheConfiguration;
use Doctrine\ORM\Cache\DefaultCacheFactory;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\EntityListenerResolver;
use Doctrine\ORM\Mapping\NamingStrategy;
use Doctrine\ORM\Mapping\QuoteStrategy;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use DoctrineORMModule\Service\ConfigurationFactory;
use DoctrineORMModuleTest\Assets\RepositoryClass;
use Laminas\ServiceManager\Exception\InvalidArgumentException;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class ConfigurationFactoryTest extends TestCase
{
    /** @var ServiceManager */
    protected $serviceManager;
    /** @var ConfigurationFactory */
    protected $factory;

    public function setUp(): void
    {
        $this->serviceManager = new ServiceManager();
        $this->factory        = new ConfigurationFactory('test_default');
        $this->serviceManager->setService('doctrine.cache.array', new ArrayCache());
        $this->serviceManager->setService(
            'doctrine.driver.orm_default',
            $this->createMock(MappingDriver::class)
        );
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
        $ormConfig = $this->factory->createService($this->serviceManager);
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
        $ormConfig = $factory->createService($this->serviceManager);
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
        $ormConfig = $this->factory->createService($this->serviceManager);
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
        $this->factory->createService($this->serviceManager);
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
        $ormConfig = $factory->createService($this->serviceManager);
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
        $ormConfig = $this->factory->createService($this->serviceManager);
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
        $this->factory->createService($this->serviceManager);
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
        $ormConfig = $factory->createService($this->serviceManager);
        $this->assertInstanceOf(ArrayCache::class, $ormConfig->getHydrationCacheImpl());
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
        $ormConfig = $factory->createService($this->serviceManager);
        $this->assertInstanceOf(ArrayCache::class, $ormConfig->getHydrationCacheImpl());
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
        $ormConfig = $factory->createService($this->serviceManager);
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
        $ormConfig = $factory->createService($this->serviceManager);
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

        $ormConfig = $this->factory->createService($this->serviceManager);

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

        $ormConfig = $this->factory->createService($this->serviceManager);

        $this->assertSame($entityListenerResolver, $ormConfig->getEntityListenerResolver());
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

        $ormConfig = $this->factory->createService($this->serviceManager);

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

        $ormConfig = $this->factory->createService($this->serviceManager);

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

        $ormConfig        = $this->factory->createService($this->serviceManager);
        $secondLevelCache = $ormConfig->getSecondLevelCacheConfiguration();

        $this->assertInstanceOf(CacheConfiguration::class, $secondLevelCache);

        $cacheFactory = $secondLevelCache->getCacheFactory();
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
        $reflProperty = new ReflectionProperty($cacheFactory, 'cache');
        $reflProperty->setAccessible(true);
        $this->assertInstanceOf(ArrayCache::class, $reflProperty->getValue($cacheFactory));
    }
}
