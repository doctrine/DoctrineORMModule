<?php

namespace DoctrineORMModuleTest\Service;

use PHPUnit\Framework\TestCase;
use DoctrineORMModule\Service\SQLLoggerCollectorFactory;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\Configuration as ORMConfiguration;
use Laminas\ServiceManager\ServiceManager;

class SQLLoggerCollectorFactoryTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    protected $services;

    /**
     * @var SQLLoggerCollectorFactory
     */
    protected $factory;

    /**
     * {@inheritDoc}
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->services = new ServiceManager();
        $this->factory = new SQLLoggerCollectorFactory('orm_default');
    }

    public function testCreateSQLLoggerCollector()
    {
        $configuration = new ORMConfiguration();
        $this->services->setService('doctrine.configuration.orm_default', $configuration);
        $this->services->setService(
            'config',
            [
                'doctrine' => [
                    'sql_logger_collector' => [
                        'orm_default' => [],
                    ],
                ],
            ]
        );
        $service = $this->factory->createService($this->services);
        $this->assertInstanceOf(\DoctrineORMModule\Collector\SQLLoggerCollector::class, $service);
        $this->assertInstanceOf(\Doctrine\DBAL\Logging\SQLLogger::class, $configuration->getSQLLogger());
    }

    public function testCreateSQLLoggerWithCustomConfiguration()
    {
        $configuration = new ORMConfiguration();
        $this->services->setService('configuration_service_id', $configuration);
        $this->services->setService(
            'config',
            [
                'doctrine' => [
                    'sql_logger_collector' => [
                        'orm_default' => [
                            'configuration' => 'configuration_service_id',
                        ],
                    ],
                ],
            ]
        );
        $this->factory->createService($this->services);
        $this->assertInstanceOf(\Doctrine\DBAL\Logging\SQLLogger::class, $configuration->getSQLLogger());
    }

    public function testCreateSQLLoggerWithPreviousExistingLoggerChainsLoggers()
    {
        $originalLogger = $this->createMock(\Doctrine\DBAL\Logging\SQLLogger::class);
        $originalLogger
            ->expects($this->once())
            ->method('startQuery')
            ->with($this->equalTo('test query'));
        $injectedLogger = $this->createMock(\Doctrine\DBAL\Logging\DebugStack::class);
        $injectedLogger
            ->expects($this->once())
            ->method('startQuery')
            ->with($this->equalTo('test query'));

        $configuration = new ORMConfiguration();
        $configuration->setSQLLogger($originalLogger);
        $this->services->setService('doctrine.configuration.orm_default', $configuration);
        $this->services->setService('custom_logger', $injectedLogger);
        $this->services->setService(
            'config',
            [
                'doctrine' => [
                    'sql_logger_collector' => [
                        'orm_default' => [
                            'sql_logger' => 'custom_logger',
                        ],
                    ],
                ],
            ]
        );
        $this->factory->createService($this->services);
        /* @var $logger \Doctrine\DBAL\Logging\SQLLogger */
        $logger = $configuration->getSQLLogger();
        $logger->startQuery('test query');
    }

    public function testCreateSQLLoggerWithCustomLogger()
    {
        $configuration = new ORMConfiguration();
        $logger = new DebugStack();
        $this->services->setService('doctrine.configuration.orm_default', $configuration);
        $this->services->setService('logger_service_id', $logger);
        $this->services->setService(
            'config',
            [
                'doctrine' => [
                    'sql_logger_collector' => [
                        'orm_default' => [
                            'sql_logger' => 'logger_service_id',
                        ],
                    ],
                ],
            ]
        );
        $this->factory->createService($this->services);
        $this->assertSame($logger, $configuration->getSQLLogger());
    }

    public function testCreateSQLLoggerWithCustomName()
    {
        $this->services->setService('doctrine.configuration.orm_default', new ORMConfiguration());
        $this->services->setService(
            'config',
            [
                'doctrine' => [
                    'sql_logger_collector' => [
                        'orm_default' => [
                            'name' => 'test_collector_name',
                        ],
                    ],
                ],
            ]
        );
        /* @var $service \DoctrineORMModule\Collector\SQLLoggerCollector */
        $service = $this->factory->createService($this->services);
        $this->assertSame('doctrine.sql_logger_collector.test_collector_name', $service->getName());
    }
}
