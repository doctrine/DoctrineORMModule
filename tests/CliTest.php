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

use PHPUnit_Framework_TestCase;
use DoctrineORMModuleTest\Util\ServiceManagerFactory;

/**
 * Tests used to verify that command line functionality is active
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class CliTest extends PHPUnit_Framework_TestCase
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
        $this->entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');
        $this->cli           = $serviceManager->get('doctrine.cli');
        $this->assertSame(1, $invocations);
    }

    public function testValidHelpers()
    {
        $helperSet = $this->cli->getHelperSet();

        /* @var $emHelper \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper */
        $emHelper = $helperSet->get('em');
        $this->assertInstanceOf('Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper', $emHelper);
        $this->assertSame($this->entityManager, $emHelper->getEntityManager());

        /* @var $dbHelper \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper */
        $dbHelper = $helperSet->get('db');
        $this->assertInstanceOf('Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper', $dbHelper);
        $this->assertSame($this->entityManager->getConnection(), $dbHelper->getConnection());
    }

    public function testValidCommands()
    {
        $this->assertInstanceOf('Doctrine\DBAL\Tools\Console\Command\ImportCommand', $this->cli->get('dbal:import'));
        $this->assertInstanceOf('Doctrine\DBAL\Tools\Console\Command\RunSqlCommand', $this->cli->get('dbal:run-sql'));
        $this->assertInstanceOf(
            'Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand',
            $this->cli->get('orm:clear-cache:metadata')
        );
        $this->assertInstanceOf(
            'Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand',
            $this->cli->get('orm:clear-cache:query')
        );
        $this->assertInstanceOf(
            'Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand',
            $this->cli->get('orm:clear-cache:result')
        );
        $this->assertInstanceOf(
            'Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand',
            $this->cli->get('orm:generate-proxies')
        );
        $this->assertInstanceOf(
            'Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand',
            $this->cli->get('orm:ensure-production-settings')
        );
        $this->assertInstanceOf(
            'Doctrine\ORM\Tools\Console\Command\InfoCommand',
            $this->cli->get('orm:info')
        );
        $this->assertInstanceOf(
            'Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand',
            $this->cli->get('orm:schema-tool:create')
        );
        $this->assertInstanceOf(
            'Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand',
            $this->cli->get('orm:schema-tool:update')
        );
        $this->assertInstanceOf(
            'Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand',
            $this->cli->get('orm:schema-tool:drop')
        );
        $this->assertInstanceOf(
            'Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand',
            $this->cli->get('orm:validate-schema')
        );
        $this->assertInstanceOf(
            'Doctrine\ORM\Tools\Console\Command\RunDqlCommand',
            $this->cli->get('orm:run-dql')
        );
        $this->assertInstanceOf(
            'Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand',
            $this->cli->get('migrations:generate')
        );
        $this->assertInstanceOf(
            'Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand',
            $this->cli->get('migrations:diff')
        );
        $this->assertInstanceOf(
            'Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand',
            $this->cli->get('migrations:execute')
        );
    }
}
