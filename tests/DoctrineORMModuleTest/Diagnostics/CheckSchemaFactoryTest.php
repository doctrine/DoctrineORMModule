<?php

namespace DoctrineORMModuleTest\Diagnostics;

use Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand;
use DoctrineORMModule\Diagnostics\CheckCommand;
use DoctrineORMModule\Diagnostics\CheckSchemaFactory;
use Zend\Console\Request;
use Zend\ServiceManager\ServiceManager;

class CheckSchemaFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var Request */
    private $request;
    
    /** @var \PHPUnit_Framework_MockObject_MockObject|ValidateSchemaCommand */
    private $command;
    
    /** @var ServiceManager */
    private $serviceLocator;
    
    /** @var CheckSchemaFactory */
    private $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->serviceLocator = new ServiceManager();
        
        $this->command = $this->getMockBuilder(ValidateSchemaCommand::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->request = new Request();
        
        $this->sut = new CheckSchemaFactory();
    }
    
    public function testCreateService()
    {
        $this->serviceLocator->setService('doctrine.orm_cmd.validate_schema', $this->command);
        $this->serviceLocator->setService('Request', $this->request);
        
        $actual = $this->sut->createService($this->serviceLocator);
        
        self::assertInstanceOf(CheckCommand::class, $actual);
    }
}
