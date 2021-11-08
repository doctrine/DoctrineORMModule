<?php

namespace DoctrineORMModuleTest\Service;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Driver\PDO\SQLite\Driver;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use DoctrineORMModule\Service\ConfigurationFactory;
use DoctrineORMModule\Service\DBALConnectionFactory;
use DoctrineORMModuleTest\Assets\Types\MoneyType;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

/**
 * @covers \DoctrineORMModule\Service\DBALConnectionFactory
 */
class DBALConnectionFactoryTest extends TestCase
{
    /** @var ServiceManager */
    protected $serviceManager;
    /** @var DBALConnectionFactory */
    protected $factory;

    public function setUp(): void
    {
        $this->serviceManager = new ServiceManager();
        $this->factory        = new DBALConnectionFactory('orm_default');
        $this->serviceManager->setService('doctrine.cache.array', new ArrayCache());
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

        $dbal = $this->factory->createService($this->serviceManager);
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

        $dbal     = $this->factory->createService($this->serviceManager);
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
            $configurationFactory->createService($this->serviceManager)
        );
        $dbal     = $this->factory->createService($this->serviceManager);
        $platform = $dbal->getDatabasePlatform();
        $type     = Type::getType($platform->getDoctrineTypeMapping('money'));

        $this->assertInstanceOf(MoneyType::class, $type);
        $this->assertTrue($platform->isCommentedDoctrineType($type));
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

        $dbal     = $this->factory->createService($this->serviceManager);
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

        $dbal = $this->factory->createService($this->serviceManager);
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

        $dbal = $this->factory->createService($this->serviceManager);
        $this->assertTrue($dbal->getNestTransactionsWithSavepoints());
    }
}
