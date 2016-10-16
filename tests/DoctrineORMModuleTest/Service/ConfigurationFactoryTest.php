<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace DoctrineORMModuleTest\Service;

use PHPUnit_Framework_TestCase;
use DoctrineORMModule\Service\ConfigurationFactory;
use Doctrine\Common\Cache\ArrayCache;
use Zend\ServiceManager\ServiceManager;

class ConfigurationFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;
    /**
     * @var ConfigurationFactory
     */
    protected $factory;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = new ServiceManager();
        $this->factory = new ConfigurationFactory('test_default');
        $this->serviceManager->setService('doctrine.cache.array', new ArrayCache());
        $this->serviceManager->setService(
            'doctrine.driver.orm_default',
            $this->getMockBuilder('Doctrine\Common\Persistence\Mapping\Driver\MappingDriver')->getMock()
        );
    }

    public function testWillInstantiateConfigWithoutNamingStrategySetting()
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
        $this->assertInstanceOf('Doctrine\ORM\Mapping\NamingStrategy', $ormConfig->getNamingStrategy());
    }

    public function testWillInstantiateConfigWithNamingStrategyObject()
    {
        $namingStrategy = $this->getMockBuilder('Doctrine\ORM\Mapping\NamingStrategy')
            ->getMock();

        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => [
                        'naming_strategy' => $namingStrategy,
                    ],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);
        $factory = new ConfigurationFactory('test_default');
        $ormConfig = $factory->createService($this->serviceManager);
        $this->assertSame($namingStrategy, $ormConfig->getNamingStrategy());
    }

    public function testWillInstantiateConfigWithNamingStrategyReference()
    {
        $namingStrategy = $this->getMockBuilder('Doctrine\ORM\Mapping\NamingStrategy')
            ->getMock();
        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => [
                        'naming_strategy' => 'test_naming_strategy',
                    ],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);
        $this->serviceManager->setService('test_naming_strategy', $namingStrategy);
        $ormConfig = $this->factory->createService($this->serviceManager);
        $this->assertSame($namingStrategy, $ormConfig->getNamingStrategy());
    }

    public function testWillNotInstantiateConfigWithInvalidNamingStrategyReference()
    {
        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => [
                        'naming_strategy' => 'test_naming_strategy',
                    ],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);
        $this->expectException('Zend\ServiceManager\Exception\InvalidArgumentException');
        $this->factory->createService($this->serviceManager);
    }

    public function testWillInstantiateConfigWithQuoteStrategyObject()
    {
        $quoteStrategy = $this->getMockBuilder('Doctrine\ORM\Mapping\QuoteStrategy')
            ->getMock();

        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => [
                        'quote_strategy' => $quoteStrategy,
                    ],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);
        $factory = new ConfigurationFactory('test_default');
        $ormConfig = $factory->createService($this->serviceManager);
        $this->assertSame($quoteStrategy, $ormConfig->getQuoteStrategy());
    }

    public function testWillInstantiateConfigWithQuoteStrategyReference()
    {
        $quoteStrategy = $this->getMockBuilder('Doctrine\ORM\Mapping\QuoteStrategy')
            ->getMock();
        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => [
                        'quote_strategy' => 'test_quote_strategy',
                    ],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);
        $this->serviceManager->setService('test_quote_strategy', $quoteStrategy);
        $ormConfig = $this->factory->createService($this->serviceManager);
        $this->assertSame($quoteStrategy, $ormConfig->getQuoteStrategy());
    }

    public function testWillNotInstantiateConfigWithInvalidQuoteStrategyReference()
    {
        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => [
                        'quote_strategy' => 'test_quote_strategy',
                    ],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);
        $this->expectException('Zend\ServiceManager\Exception\InvalidArgumentException');
        $this->factory->createService($this->serviceManager);
    }

    public function testWillInstantiateConfigWithHydrationCacheSetting()
    {
        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => [
                        'hydration_cache' => 'array',
                    ],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);
        $factory = new ConfigurationFactory('test_default');
        $ormConfig = $factory->createService($this->serviceManager);
        $this->assertInstanceOf('Doctrine\Common\Cache\ArrayCache', $ormConfig->getHydrationCacheImpl());
    }

    public function testCanSetDefaultRepositoryClass()
    {
        $repositoryClass = 'DoctrineORMModuleTest\Assets\RepositoryClass';

        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => [

                        'hydration_cache' => 'array',
                        'default_repository_class_name' => $repositoryClass,
                    ],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);

        $factory = new ConfigurationFactory('test_default');
        $ormConfig = $factory->createService($this->serviceManager);
        $this->assertInstanceOf('Doctrine\Common\Cache\ArrayCache', $ormConfig->getHydrationCacheImpl());
    }

    public function testAcceptsMetadataFactory()
    {
        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => [
                        'classMetadataFactoryName' => 'Factory',
                    ],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);
        $factory = new ConfigurationFactory('test_default');
        $ormConfig = $factory->createService($this->serviceManager);
        $this->assertEquals('Factory', $ormConfig->getClassMetadataFactoryName());
    }

    public function testDefaultMetadatFactory()
    {
        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => [
                    ],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);
        $factory = new ConfigurationFactory('test_default');
        $ormConfig = $factory->createService($this->serviceManager);
        $this->assertEquals('Doctrine\ORM\Mapping\ClassMetadataFactory', $ormConfig->getClassMetadataFactoryName());
    }

    public function testWillInstantiateConfigWithoutEntityListenerResolverSetting()
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

        $this->assertInstanceOf('Doctrine\ORM\Mapping\EntityListenerResolver', $ormConfig->getEntityListenerResolver());
    }

    public function testWillInstantiateConfigWithEntityListenerResolverObject()
    {
        $entityListenerResolver = $this->getMockBuilder('Doctrine\ORM\Mapping\EntityListenerResolver')
            ->getMock();

        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => [
                        'entity_listener_resolver' => $entityListenerResolver,
                    ],
                ],
            ],
        ];

        $this->serviceManager->setService('config', $config);

        $ormConfig = $this->factory->createService($this->serviceManager);

        $this->assertSame($entityListenerResolver, $ormConfig->getEntityListenerResolver());
    }

    public function testWillInstantiateConfigWithEntityListenerResolverReference()
    {
        $entityListenerResolver = $this->getMockBuilder('Doctrine\ORM\Mapping\EntityListenerResolver')
            ->getMock();

        $config = [
            'doctrine' => [
                'configuration' => [
                    'test_default' => [
                        'entity_listener_resolver' => 'test_entity_listener_resolver',
                    ],
                ],
            ],
        ];

        $this->serviceManager->setService('config', $config);
        $this->serviceManager->setService('test_entity_listener_resolver', $entityListenerResolver);

        $ormConfig = $this->factory->createService($this->serviceManager);

        $this->assertSame($entityListenerResolver, $ormConfig->getEntityListenerResolver());
    }

    public function testDoNotCreateSecondLevelCacheByDefault()
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

    public function testCanInstantiateWithSecondLevelCacheConfig()
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

        $this->assertInstanceOf('Doctrine\ORM\Cache\CacheConfiguration', $secondLevelCache);

        $cacheFactory = $secondLevelCache->getCacheFactory();
        $this->assertInstanceOf('Doctrine\ORM\Cache\DefaultCacheFactory', $cacheFactory);
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
        $reflProperty = new \ReflectionProperty($cacheFactory, 'cache');
        $reflProperty->setAccessible(true);
        $this->assertInstanceOf('Doctrine\Common\Cache\ArrayCache', $reflProperty->getValue($cacheFactory));
    }
}
