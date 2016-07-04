<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace DoctrineORMModuleTest;

use Doctrine\DBAL\Migrations\Tools\Console\Command as MigrationsCommand;
use Doctrine\DBAL\Tools\Console\Command as DBALCommand;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Command as ORMCommand;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use DoctrineORMModuleTest\Util\ServiceManagerFactory;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Tests used to verify that command line functionality is active
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class CliTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Console\Application
     */
    protected $cli;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $serviceManager     = ServiceManagerFactory::getServiceManager();
        /** @var $sharedEventManager SharedEventManagerInterface */
        $sharedEventManager = $serviceManager->get('SharedEventManager');
        /** @var $application \Zend\Mvc\Application */
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
        $this->entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');
        $this->cli           = $serviceManager->get('doctrine.cli');
        $this->assertSame(1, $invocations);
    }

    public function testValidHelpers()
    {
        $helperSet = $this->cli->getHelperSet();

        /** @var $emHelper EntityManagerHelper */
        $emHelper = $helperSet->get('em');
        $this->assertInstanceOf(EntityManagerHelper::class, $emHelper);
        $this->assertSame($this->entityManager, $emHelper->getEntityManager());

        /** @var $dbHelper ConnectionHelper */
        $dbHelper = $helperSet->get('db');
        $this->assertInstanceOf(ConnectionHelper::class, $dbHelper);
        $this->assertSame($this->entityManager->getConnection(), $dbHelper->getConnection());
    }

    public function testValidCommands()
    {
        $this->assertInstanceOf(DBALCommand\ImportCommand::class, $this->cli->get('dbal:import'));
        $this->assertInstanceOf(DBALCommand\RunSqlCommand::class, $this->cli->get('dbal:run-sql'));
        $this->assertInstanceOf(
            ORMCommand\ClearCache\MetadataCommand::class,
            $this->cli->get('orm:clear-cache:metadata')
        );
        $this->assertInstanceOf(
            ORMCommand\ClearCache\QueryCommand::class,
            $this->cli->get('orm:clear-cache:query')
        );
        $this->assertInstanceOf(
            ORMCommand\ClearCache\ResultCommand::class,
            $this->cli->get('orm:clear-cache:result')
        );
        $this->assertInstanceOf(
            ORMCommand\GenerateProxiesCommand::class,
            $this->cli->get('orm:generate-proxies')
        );
        $this->assertInstanceOf(
            ORMCommand\EnsureProductionSettingsCommand::class,
            $this->cli->get('orm:ensure-production-settings')
        );
        $this->assertInstanceOf(
            ORMCommand\InfoCommand::class,
            $this->cli->get('orm:info')
        );
        $this->assertInstanceOf(
            ORMCommand\SchemaTool\CreateCommand::class,
            $this->cli->get('orm:schema-tool:create')
        );
        $this->assertInstanceOf(
            ORMCommand\SchemaTool\UpdateCommand::class,
            $this->cli->get('orm:schema-tool:update')
        );
        $this->assertInstanceOf(
            ORMCommand\SchemaTool\DropCommand::class,
            $this->cli->get('orm:schema-tool:drop')
        );
        $this->assertInstanceOf(
            ORMCommand\ValidateSchemaCommand::class,
            $this->cli->get('orm:validate-schema')
        );
        $this->assertInstanceOf(
            ORMCommand\RunDqlCommand::class,
            $this->cli->get('orm:run-dql')
        );
        $this->assertInstanceOf(
            MigrationsCommand\GenerateCommand::class,
            $this->cli->get('migrations:generate')
        );
        $this->assertInstanceOf(
            MigrationsCommand\DiffCommand::class,
            $this->cli->get('migrations:diff')
        );
        $this->assertInstanceOf(
            MigrationsCommand\ExecuteCommand::class,
            $this->cli->get('migrations:execute')
        );
    }
}
