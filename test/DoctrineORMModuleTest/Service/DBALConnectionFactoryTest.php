<?php

namespace DoctrineORMModuleTest\Service;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\DBAL\Driver\PDOSqlite\Driver as PDOSqliteDriver;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Configuration;
use DoctrineORMModuleTest\Assets\Types\MoneyType;
use PHPUnit\Framework\TestCase;
use DoctrineORMModule\Service\DBALConnectionFactory;
use Doctrine\DBAL\Types\Type;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\EventManager;
use Laminas\ServiceManager\ServiceManager;
use DoctrineORMModule\Service\ConfigurationFactory;

/**
 * @covers \DoctrineORMModule\Service\DBALConnectionFactory
 */
class DBALConnectionFactoryTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;
    /**
     * @var DBALConnectionFactory
     */
    protected $factory;

    /**
     * {@inheritDoc}
     */
    public function setUp() : void
    {
        $this->serviceManager = new ServiceManager();
        $this->factory = new DBALConnectionFactory('orm_default');
        $this->serviceManager->setService('doctrine.cache.array', new ArrayCache());
        $this->serviceManager->setService('doctrine.eventmanager.orm_default', new EventManager());
    }

    public function testNoConnectWithoutCustomMappingsAndCommentedTypes()
    {
        $config = [
            'doctrine' => [
                'connection' => [
                    'orm_default' => [
                        'driverClass'   => PDOSqliteDriver::class,
                        'params' => [
                            'memory' => true,
                        ],
                    ]
                ],
            ],
        ];
        $configurationMock = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serviceManager->setService('doctrine.configuration.orm_default', $configurationMock);
        $this->serviceManager->setService('config', $config);
        $this->serviceManager->setService('Configuration', $config);

        $dbal = $this->factory->createService($this->serviceManager);
        $this->assertFalse($dbal->isConnected());
    }

    public function testDoctrineMappingTypeReturnCorrectParent()
    {
        $config = [
            'doctrine' => [
                'connection' => [
                    'orm_default' => [
                        'driverClass'   => PDOSqliteDriver::class,
                        'params' => [
                            'memory' => true,
                        ],
                        'doctrineTypeMappings' => [
                            'money' => 'string',
                        ],
                    ],
                ],
            ],
        ];
        $configurationMock = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serviceManager->setService('doctrine.configuration.orm_default', $configurationMock);
        $this->serviceManager->setService('config', $config);
        $this->serviceManager->setService('Configuration', $config);

        $dbal = $this->factory->createService($this->serviceManager);
        $platform = $dbal->getDatabasePlatform();
        $this->assertSame('string', $platform->getDoctrineTypeMapping("money"));
    }

    public function testDoctrineAddCustomCommentedType()
    {
        $config = [
            'doctrine' => [
                'connection' => [
                    'orm_default' => [
                        'driverClass'   => PDOSqliteDriver::class,
                        'params' => [
                            'memory' => true,
                        ],
                        'doctrineTypeMappings' => [
                            'money' => 'money',
                        ],
                        'doctrineCommentedTypes' => [
                            'money',
                        ],
                    ],
                ],
                'configuration' => [
                    'orm_default' => [
                        'types' => [
                            'money' => MoneyType::class,
                        ],
                    ],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);
        $this->serviceManager->setService('Configuration', $config);
        $this->serviceManager->setService(
            'doctrine.driver.orm_default',
            $this->createMock(MappingDriver::class)
        );
        $configurationFactory = new ConfigurationFactory('orm_default');
        $this->serviceManager->setService(
            'doctrine.configuration.orm_default',
            $configurationFactory->createService($this->serviceManager)
        );
        $dbal = $this->factory->createService($this->serviceManager);
        $platform = $dbal->getDatabasePlatform();
        $type = Type::getType($platform->getDoctrineTypeMapping("money"));

        $this->assertInstanceOf(MoneyType::class, $type);
        $this->assertTrue($platform->isCommentedDoctrineType($type));
    }

    public function testGettingPlatformFromContainer()
    {
        $config = [
            'doctrine' => [
                'connection' => [
                    'orm_default' => [
                        'driverClass'   => PDOSqliteDriver::class,
                        'params' => [
                            'platform' => 'platform_service',
                        ],
                    ]
                ],
            ],
        ];
        $configurationMock = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $platformMock = $this->getMockBuilder(AbstractPlatform::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serviceManager->setService('doctrine.configuration.orm_default', $configurationMock);
        $this->serviceManager->setService('config', $config);
        $this->serviceManager->setService('Configuration', $config);
        $this->serviceManager->setService('platform_service', $platformMock);

        $dbal = $this->factory->createService($this->serviceManager);
        $platform = $dbal->getDatabasePlatform();
        $this->assertSame($platformMock, $platform);
    }

    public function testWithoutUseSavepoints()
    {
        $config = [
            'doctrine' => [
                'connection' => [
                    'orm_default' => [
                        'driverClass'   => PDOSqliteDriver::class,
                        'params' => [
                            'memory' => true,
                        ],
                    ]
                ],
            ],
        ];
        $configurationMock = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serviceManager->setService('doctrine.configuration.orm_default', $configurationMock);
        $this->serviceManager->setService('config', $config);
        $this->serviceManager->setService('Configuration', $config);

        $dbal = $this->factory->createService($this->serviceManager);
        $this->assertFalse($dbal->getNestTransactionsWithSavepoints());
    }

    public function testWithUseSavepoints()
    {
        $config = [
            'doctrine' => [
                'connection' => [
                    'orm_default' => [
                        'driverClass'   => PDOSqliteDriver::class,
                        'use_savepoints' => true,
                        'params' => [
                            'memory' => true,
                        ],
                    ]
                ],
            ],
        ];
        $configurationMock = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serviceManager->setService('doctrine.configuration.orm_default', $configurationMock);
        $this->serviceManager->setService('config', $config);
        $this->serviceManager->setService('Configuration', $config);

        $dbal = $this->factory->createService($this->serviceManager);
        $this->assertTrue($dbal->getNestTransactionsWithSavepoints());
    }
}
