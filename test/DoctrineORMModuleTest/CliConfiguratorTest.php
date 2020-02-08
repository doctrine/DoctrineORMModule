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
     * @var \Laminas\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $objectManager;

    /**
     * {@inheritDoc}
     */
    public function setUp() : void
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

    public function testValidHelpers()
    {
        $application = new Application();

        $cliConfigurator = new CliConfigurator($this->serviceManager);
        $cliConfigurator->configure($application);

        $helperSet = $application->getHelperSet();

        /* @var $emHelper \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper */
        $emHelper = $helperSet->get('em');
        $this->assertInstanceOf(\Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper::class, $emHelper);
        $this->assertSame($this->objectManager, $emHelper->getEntityManager());

        /* @var $dbHelper \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper */
        $dbHelper = $helperSet->get('db');
        $this->assertInstanceOf(\Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper::class, $dbHelper);
        $this->assertSame($this->objectManager->getConnection(), $dbHelper->getConnection());
    }

    /**
     * @param string $commandName
     * @param string $className
     *
     * @dataProvider dataProviderForTestValidCommands
     */
    public function testValidCommands($commandName, $className)
    {
        $application = new Application();

        $cliConfigurator = new CliConfigurator($this->serviceManager);
        $cliConfigurator->configure($application);

        /* @var $command \Symfony\Component\Console\Command\Command */
        $command = $application->get($commandName);
        $this->assertInstanceOf($className, $command);

        // check for the entity-manager option
        $this->assertTrue($command->getDefinition()->hasOption('object-manager'));

        $entityManagerOption = $command->getDefinition()->getOption('object-manager');

        $this->assertTrue($entityManagerOption->isValueOptional());
        $this->assertFalse($entityManagerOption->isValueRequired());
        $this->assertFalse($entityManagerOption->isArray());
        $this->assertNull($entityManagerOption->getShortcut());
        $this->assertSame('doctrine.entitymanager.orm_default', $entityManagerOption->getDefault());
        $this->assertSame('The name of the object manager to use.', $entityManagerOption->getDescription());
    }

    /**
     * @return array
     */
    public function dataProviderForTestValidCommands()
    {
        return [
            [
                'dbal:import',
                \Doctrine\DBAL\Tools\Console\Command\ImportCommand::class,
            ],
            [
                'dbal:run-sql',
                \Doctrine\DBAL\Tools\Console\Command\RunSqlCommand::class,
            ],
            [
                'orm:clear-cache:query',
                \Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand::class,
            ],
            [
                'orm:clear-cache:result',
                \Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand::class,
            ],
            [
                'orm:generate-proxies',
                \Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand::class,
            ],
            [
                'orm:ensure-production-settings',
                \Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand::class,
            ],
            [
                'orm:info',
                \Doctrine\ORM\Tools\Console\Command\InfoCommand::class,
            ],
            [
                'orm:schema-tool:create',
                \Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand::class,
            ],
            [
                'orm:schema-tool:update',
                \Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand::class,
            ],
            [
                'orm:schema-tool:drop',
                \Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand::class,
            ],
            [
                'orm:validate-schema',
                \Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand::class,
            ],
            [
                'orm:run-dql',
                \Doctrine\ORM\Tools\Console\Command\RunDqlCommand::class,
            ],
            [
                'migrations:generate',
                \Doctrine\Migrations\Tools\Console\Command\GenerateCommand::class,
            ],
            [
                'migrations:diff',
                \Doctrine\Migrations\Tools\Console\Command\DiffCommand::class,
            ],
            [
                'migrations:execute',
                \Doctrine\Migrations\Tools\Console\Command\ExecuteCommand::class,
            ],
        ];
    }
}
