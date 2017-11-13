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
