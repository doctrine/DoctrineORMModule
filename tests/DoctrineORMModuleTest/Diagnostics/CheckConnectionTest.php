<?php

namespace DoctrineORMModuleTest\Diagnostics;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Connections\MasterSlaveConnection;
use Doctrine\DBAL\Sharding\PoolingShardConnection;
use Doctrine\ORM\EntityManagerInterface;
use DoctrineORMModule\Diagnostics\CheckConnection;
use Zend\ServiceManager\ServiceManager;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;

/**
 * @covers \DoctrineORMModule\Diagnostics\CheckConnection
 */
class CheckConnectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \PHPUnit_Framework_MockObject_MockObject|PoolingShardConnection */
    private $poolingShardConnection;
    
    /** @var  \PHPUnit_Framework_MockObject_MockObject|Connection */
    private $connection;
    
    /** @var  \PHPUnit_Framework_MockObject_MockObject|MasterSlaveConnection */
    private $masterSlaveConnection;
    
    /** @var  \PHPUnit_Framework_MockObject_MockObject|EntityManagerInterface */
    private $entityManager;
    
    /** @var  ServiceManager */
    private $serviceLocator;

    protected function setUp()
    {
        parent::setUp();

        $this->serviceLocator = new ServiceManager();
        
        $this->entityManager = $this->getMock(EntityManagerInterface::class);

        $this->poolingShardConnection = $this->getMockBuilder(PoolingShardConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->masterSlaveConnection = $this->getMockBuilder(MasterSlaveConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
    
    public function testCheckMasterSlaveConnectionSuccessful()
    {
        $this->masterSlaveConnection->method('ping')->willReturnOnConsecutiveCalls(true, true);
        
        $actual = (new CheckConnection($this->masterSlaveConnection))->check();
        
        self::assertInstanceOf(Success::class, $actual);
    }
    
    public function testCheckMasterSlaveConnectionFailedFirstPing()
    {
        $this->masterSlaveConnection->method('ping')->willReturnOnConsecutiveCalls(false, true);

        $actual = (new CheckConnection($this->masterSlaveConnection))->check();

        self::assertInstanceOf(Failure::class, $actual);
    }
    
    public function testCheckMasterSlaveConnectionFailedSecondPing()
    {
        $this->masterSlaveConnection->method('ping')->willReturnOnConsecutiveCalls(true, false);

        $actual = (new CheckConnection($this->masterSlaveConnection))->check();

        self::assertInstanceOf(Failure::class, $actual);
    }

    public function testCheckConnectionSuccessful()
    {
        $this->connection->method('ping')->willReturn(true);

        $actual = (new CheckConnection($this->connection))->check();

        self::assertInstanceOf(Success::class, $actual);
    }
    
    public function testCheckConnectionFailed()
    {
        $this->connection->method('ping')->willReturn(false);

        $actual = (new CheckConnection($this->connection))->check();

        self::assertInstanceOf(Failure::class, $actual);
    }

    public function testCheckPoolingShardConnectionSuccessful()
    {
        $this->poolingShardConnection->method('ping')->willReturn(true);

        $actual = (new CheckConnection($this->poolingShardConnection))->check();

        self::assertInstanceOf(Success::class, $actual);
    }
    
    public function testCheckPoolingShardConnectionFailed()
    {
        $this->poolingShardConnection->method('ping')->willReturn(false);

        $actual = (new CheckConnection($this->poolingShardConnection))->check();

        self::assertInstanceOf(Failure::class, $actual);
    }
}
