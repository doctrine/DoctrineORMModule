<?php

namespace DoctrineORMModuleTest\Diagnostics;

use Doctrine\DBAL\Connection;
use DoctrineORMModule\Diagnostics\CheckConnection;
use DoctrineORMModule\Diagnostics\CheckConnectionFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * @covers \DoctrineORMModule\Diagnostics\CheckConnectionFactory
 */
class CheckConnectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|Connection */
    private $connection;
    
    /** @var ServiceManager */
    private $serviceLocator;
    
    /** @var CheckConnectionFactory */
    private $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->serviceLocator = new ServiceManager();
        
        $this->connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->sut = new CheckConnectionFactory();
    }
    
    public function testCreateService()
    {
        $this->serviceLocator->setService('doctrine.connection.orm_default', $this->connection);
        
        $actual = $this->sut->createService($this->serviceLocator);
        
        self::assertInstanceOf(CheckConnection::class, $actual);
    }
}
