<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Service;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\Driver\PDO\SQLite\Driver;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use DoctrineModule\Cache\LaminasStorageCache;
use DoctrineORMModule\Service\ConfigurationFactory;
use DoctrineORMModule\Service\DBALConnectionFactory;
use DoctrineORMModuleTest\Assets\Types\MoneyType;
use Laminas\Cache\Storage\Adapter\Memory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

use function class_exists;

/**
 * @covers \DoctrineORMModule\Service\DBALConnectionFactory
 */
class DBALConnectionFactoryTest extends TestCase
{
    protected ServiceManager $serviceManager;
    protected DBALConnectionFactory $factory;

    public function setUp(): void
    {
        $this->serviceManager = new ServiceManager();
        $this->factory        = new DBALConnectionFactory('orm_default');
        // Set up appropriate cache based on DoctrineModule version detection:
        $arrayCache = class_exists(ArrayCache::class)
            ? new ArrayCache()                          // DoctrineModule 5
            : new LaminasStorageCache(new Memory());    // DoctrineModule 6
        $this->serviceManager->setService('doctrine.cache.array', $arrayCache);
        $this->serviceManager->setService('doctrine.eventmanager.orm_default', new EventManager());
    }

    public function testNoConnectWithoutCustomMappingsAndCommentedTypes(): void
    {
        $config            = [
            'doctrine' => [
                'connection' => [
                    'orm_default' => [
                        'driverClass'   => Driver::class,
                        'params' => ['memory' => true],
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

        $dbal = ($this->factory)($this->serviceManager, DBALConnection::class);
        $this->assertFalse($dbal->isConnected());
    }

    public function testDoctrineMappingTypeReturnCorrectParent(): void
    {
        $config            = [
            'doctrine' => [
                'connection' => [
                    'orm_default' => [
                        'driverClass'   => Driver::class,
                        'params' => ['memory' => true],
                        'doctrineTypeMappings' => ['money' => 'string'],
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

        $dbal     = ($this->factory)($this->serviceManager, DBALConnection::class);
        $platform = $dbal->getDatabasePlatform();
        $this->assertSame('string', $platform->getDoctrineTypeMapping('money'));
    }

    public function testDoctrineAddCustomCommentedType(): void
    {
        $config = [
            'doctrine' => [
                'connection' => [
                    'orm_default' => [
                        'driverClass'   => Driver::class,
                        'params' => ['memory' => true],
                        'doctrineTypeMappings' => ['money' => 'money'],
                        'doctrineCommentedTypes' => ['money'],
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
            $configurationFactory($this->serviceManager, Configuration::class)
        );
        $dbal     = ($this->factory)($this->serviceManager, DBALConnection::class);
        $platform = $dbal->getDatabasePlatform();
        $type     = Type::getType($platform->getDoctrineTypeMapping('money'));

        $this->assertInstanceOf(MoneyType::class, $type);
    }

    public function testGettingPlatformFromContainer(): void
    {
        $config            = [
            'doctrine' => [
                'connection' => [
                    'orm_default' => [
                        'driverClass'   => Driver::class,
                        'params' => ['platform' => 'platform_service'],
                    ],
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

        $dbal     = ($this->factory)($this->serviceManager, DBALConnection::class);
        $platform = $dbal->getDatabasePlatform();
        $this->assertSame($platformMock, $platform);
    }

    public function testWithoutUseSavepoints(): void
    {
        $config            = [
            'doctrine' => [
                'connection' => [
                    'orm_default' => [
                        'driverClass'   => Driver::class,
                        'params' => ['memory' => true],
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

        $dbal = ($this->factory)($this->serviceManager, DBALConnection::class);
        $this->assertFalse($dbal->getNestTransactionsWithSavepoints());
    }

    public function testWithUseSavepoints(): void
    {
        $config            = [
            'doctrine' => [
                'connection' => [
                    'orm_default' => [
                        'driverClass'   => Driver::class,
                        'use_savepoints' => true,
                        'params' => ['memory' => true],
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

        $dbal = ($this->factory)($this->serviceManager, DBALConnection::class);
        $this->assertTrue($dbal->getNestTransactionsWithSavepoints());
    }
}
