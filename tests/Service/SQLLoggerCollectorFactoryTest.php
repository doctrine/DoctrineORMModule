<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Service;

use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\DBAL\Logging\SQLLogger;
use Doctrine\ORM\Configuration as ORMConfiguration;
use DoctrineORMModule\Collector\SQLLoggerCollector;
use DoctrineORMModule\Service\SQLLoggerCollectorFactory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

use function assert;

class SQLLoggerCollectorFactoryTest extends TestCase
{
    protected ServiceManager $services;

    protected SQLLoggerCollectorFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->services = new ServiceManager();
        $this->factory  = new SQLLoggerCollectorFactory('orm_default');
    }

    public function testCreateSQLLoggerCollector(): void
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
        $service = ($this->factory)($this->services, SQLLoggerCollector::class);
        $this->assertInstanceOf(SQLLoggerCollector::class, $service);
        $this->assertInstanceOf(SQLLogger::class, $configuration->getSQLLogger());
    }

    public function testCreateSQLLoggerWithCustomConfiguration(): void
    {
        $configuration = new ORMConfiguration();
        $this->services->setService('configuration_service_id', $configuration);
        $this->services->setService(
            'config',
            [
                'doctrine' => [
                    'sql_logger_collector' => [
                        'orm_default' => ['configuration' => 'configuration_service_id'],
                    ],
                ],
            ]
        );
        ($this->factory)($this->services, SQLLoggerCollector::class);
        $this->assertInstanceOf(SQLLogger::class, $configuration->getSQLLogger());
    }

    public function testCreateSQLLoggerWithPreviousExistingLoggerChainsLoggers(): void
    {
        $originalLogger = $this->createMock(SQLLogger::class);
        $originalLogger
            ->expects($this->once())
            ->method('startQuery')
            ->with($this->equalTo('test query'));
        $injectedLogger = $this->createMock(DebugStack::class);
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
                        'orm_default' => ['sql_logger' => 'custom_logger'],
                    ],
                ],
            ]
        );
        ($this->factory)($this->services, SQLLoggerCollector::class);
        $logger = $configuration->getSQLLogger();
        assert($logger instanceof SQLLogger);
        $logger->startQuery('test query');
    }

    public function testCreateSQLLoggerWithCustomLogger(): void
    {
        $configuration = new ORMConfiguration();
        $logger        = new DebugStack();
        $this->services->setService('doctrine.configuration.orm_default', $configuration);
        $this->services->setService('logger_service_id', $logger);
        $this->services->setService(
            'config',
            [
                'doctrine' => [
                    'sql_logger_collector' => [
                        'orm_default' => ['sql_logger' => 'logger_service_id'],
                    ],
                ],
            ]
        );
        ($this->factory)($this->services, SQLLoggerCollector::class);
        $this->assertSame($logger, $configuration->getSQLLogger());
    }

    public function testCreateSQLLoggerWithCustomName(): void
    {
        $this->services->setService('doctrine.configuration.orm_default', new ORMConfiguration());
        $this->services->setService(
            'config',
            [
                'doctrine' => [
                    'sql_logger_collector' => [
                        'orm_default' => ['name' => 'test_collector_name'],
                    ],
                ],
            ]
        );
        $service = ($this->factory)($this->services, SQLLoggerCollector::class);
        assert($service instanceof SQLLoggerCollector);
        $this->assertSame('doctrine.sql_logger_collector.test_collector_name', $service->getName());
    }
}
