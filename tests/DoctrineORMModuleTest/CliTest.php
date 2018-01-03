<?php

namespace DoctrineORMModuleTest;

use DoctrineORMModule\Module;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Zend\EventManager\Event;

/**
 * Tests used to verify that command line functionality is active
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class CliTest extends TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * @var \Symfony\Component\Console\Application
     */
    protected $cli;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $objectManager;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $serviceManager     = ServiceManagerFactory::getServiceManager();
        /* @var $sharedEventManager \Zend\EventManager\SharedEventManagerInterface */
        $sharedEventManager = $serviceManager->get('SharedEventManager');
        /* @var $application \Zend\Mvc\Application */
        $application        = $serviceManager->get('Application');
        $invocations        = 0;

        $sharedEventManager->attach(
            'doctrine',
            'loadCli.post',
            function () use (&$invocations) {
                $invocations += 1;
            }
        );

        $application->bootstrap();
        $this->serviceManager = $serviceManager;
        $this->objectManager  = $serviceManager->get('doctrine.entitymanager.orm_default');
        $this->cli            = $serviceManager->get('doctrine.cli');
        $this->assertSame(1, $invocations);
    }

    public function testValidHelpers()
    {
        $helperSet = $this->cli->getHelperSet();

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
        /* @var $command \Symfony\Component\Console\Command\Command */
        $command = $this->cli->get($commandName);
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
                \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand::class,
            ],
            [
                'migrations:diff',
                \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand::class,
            ],
            [
                'migrations:execute',
                \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand::class,
            ],
        ];
    }
}
