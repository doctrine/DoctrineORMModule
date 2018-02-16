<?php

namespace DoctrineORMModuleTest\Listener;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use DoctrineORMModule\CliConfigurator;
use DoctrineORMModuleTest\ServiceManagerFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;

/**
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Nicolas Eeckeloo <neeckeloo@gmail.com>
 */
class CliConfiguratorTest extends TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $objectManager;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = ServiceManagerFactory::getServiceManager();
        $this->objectManager  = $this->serviceManager->get('doctrine.entitymanager.orm_default');
    }

    public function testOrmDefaultIsUsedAsTheEntityManagerIfNoneIsProvided()
    {
        $application = new Application();

        $cliConfigurator = new CliConfigurator($this->serviceManager);
        $cliConfigurator->configure($application);

        /* @var $entityManagerHelper \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper */
        $entityManagerHelper = $application->getHelperSet()->get('entityManager');

        $this->assertInstanceOf(EntityManagerHelper::class, $entityManagerHelper);
        $this->assertSame($this->objectManager, $entityManagerHelper->getEntityManager());
    }

    /**
     * @backupGlobals enabled
     */
    public function testEntityManagerUsedCanBeSpecifiedInCommandLineArgument()
    {
        $objectManagerName = 'doctrine.entitymanager.some_other_name';

        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager = $this->getMockbuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager
            ->expects($this->atLeastOnce())
            ->method('getConnection')
            ->willReturn($connection);

        $this->serviceManager->setService($objectManagerName, $entityManager);

        $application = new Application();

        $_SERVER['argv'][] = '--object-manager=' . $objectManagerName;

        $cliConfigurator = new CliConfigurator($this->serviceManager);
        $cliConfigurator->configure($application);

        /* @var $entityManagerHelper \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper */
        $entityManagerHelper = $application->getHelperSet()->get('entityManager');

        $this->assertInstanceOf(EntityManagerHelper::class, $entityManagerHelper);
        $this->assertSame($entityManager, $entityManagerHelper->getEntityManager());
    }
}
