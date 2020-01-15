<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Listener;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Tools\Console\Command\ImportCommand;
use Doctrine\DBAL\Tools\Console\Command\RunSqlCommand;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand;
use Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand;
use Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand;
use Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand;
use Doctrine\ORM\Tools\Console\Command\InfoCommand;
use Doctrine\ORM\Tools\Console\Command\RunDqlCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand;
use Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use DoctrineORMModule\CliConfigurator;
use DoctrineORMModuleTest\ServiceManagerFactory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use function assert;

/**
 * @link    http://www.doctrine-project.org/
 */
class CliConfiguratorTest extends TestCase
{
    protected ServiceManager $serviceManager;

    protected EntityManager $objectManager;

    protected function setUp() : void
    {
        $this->serviceManager = ServiceManagerFactory::getServiceManager();
        $this->objectManager  = $this->serviceManager->get('doctrine.entitymanager.orm_default');
    }

    public function testOrmDefaultIsUsedAsTheEntityManagerIfNoneIsProvided() : void
    {
        $application = new Application();

        $cliConfigurator = new CliConfigurator($this->serviceManager);
        $cliConfigurator->configure($application);

        $entityManagerHelper = $application->getHelperSet()->get('entityManager');
        assert($entityManagerHelper instanceof EntityManagerHelper);

        $this->assertInstanceOf(EntityManagerHelper::class, $entityManagerHelper);
        $this->assertSame($this->objectManager, $entityManagerHelper->getEntityManager());
    }

    /**
     * @backupGlobals enabled
     */
    public function testEntityManagerUsedCanBeSpecifiedInCommandLineArgument() : void
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

        $entityManagerHelper = $application->getHelperSet()->get('entityManager');
        assert($entityManagerHelper instanceof EntityManagerHelper);

        $this->assertInstanceOf(EntityManagerHelper::class, $entityManagerHelper);
        $this->assertSame($entityManager, $entityManagerHelper->getEntityManager());
    }

    public function testValidHelpers() : void
    {
        $application = new Application();

        $cliConfigurator = new CliConfigurator($this->serviceManager);
        $cliConfigurator->configure($application);

        $helperSet = $application->getHelperSet();

        $emHelper = $helperSet->get('em');
        assert($emHelper instanceof EntityManagerHelper);
        $this->assertInstanceOf(EntityManagerHelper::class, $emHelper);
        $this->assertSame($this->objectManager, $emHelper->getEntityManager());

        $dbHelper = $helperSet->get('db');
        assert($dbHelper instanceof ConnectionHelper);
        $this->assertInstanceOf(ConnectionHelper::class, $dbHelper);
        $this->assertSame($this->objectManager->getConnection(), $dbHelper->getConnection());
    }

    /**
     * @dataProvider dataProviderForTestValidCommands
     */
    public function testValidCommands(string $commandName, string $className) : void
    {
        $application = new Application();

        $cliConfigurator = new CliConfigurator($this->serviceManager);
        $cliConfigurator->configure($application);

        $command = $application->get($commandName);
        assert($command instanceof Command);
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
     * @return mixed[]
     */
    public function dataProviderForTestValidCommands() : array
    {
        return [
            [
                'dbal:import',
                ImportCommand::class,
            ],
            [
                'dbal:run-sql',
                RunSqlCommand::class,
            ],
            [
                'orm:clear-cache:query',
                QueryCommand::class,
            ],
            [
                'orm:clear-cache:result',
                ResultCommand::class,
            ],
            [
                'orm:generate-proxies',
                GenerateProxiesCommand::class,
            ],
            [
                'orm:ensure-production-settings',
                EnsureProductionSettingsCommand::class,
            ],
            [
                'orm:info',
                InfoCommand::class,
            ],
            [
                'orm:schema-tool:create',
                CreateCommand::class,
            ],
            [
                'orm:schema-tool:update',
                UpdateCommand::class,
            ],
            [
                'orm:schema-tool:drop',
                DropCommand::class,
            ],
            [
                'orm:validate-schema',
                ValidateSchemaCommand::class,
            ],
            [
                'orm:run-dql',
                RunDqlCommand::class,
            ],
            [
                'migrations:generate',
                GenerateCommand::class,
            ],
            [
                'migrations:diff',
                DiffCommand::class,
            ],
            [
                'migrations:execute',
                ExecuteCommand::class,
            ],
        ];
    }
}
