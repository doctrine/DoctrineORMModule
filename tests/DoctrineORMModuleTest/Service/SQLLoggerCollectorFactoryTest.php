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

namespace DoctrineORMModuleTest\Service;

use PHPUnit_Framework_TestCase as TestCase;
use DoctrineORMModule\Service\SQLLoggerCollectorFactory;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\Configuration as ORMConfiguration;
use Zend\ServiceManager\ServiceManager;

class SQLLoggerCollectorFactoryTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    protected $services;

    /**
     * @var SQLLoggerCollectorFactory
     */
    protected $factory;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->services = new ServiceManager();
        $this->factory = new SQLLoggerCollectorFactory('orm_default');
    }

    public function testCreateSQLLoggerCollector()
    {
        $configuration = new ORMConfiguration();
        $this->services->setService('doctrine.configuration.orm_default', $configuration);
        $this->services->setService(
            'Config',
            array(
                'doctrine' => array(
                    'sql_logger_collector' => array(
                        'orm_default' => array(),
                    ),
                ),
            )
        );
        $service = $this->factory->createService($this->services);
        $this->assertInstanceOf('DoctrineORMModule\Collector\SQLLoggerCollector', $service);
        $this->assertInstanceOf('Doctrine\DBAL\Logging\SQLLogger', $configuration->getSQLLogger());
    }

    public function testCreateSQLLoggerWithCustomConfiguration()
    {
        $configuration = new ORMConfiguration();
        $this->services->setService('configuration_service_id', $configuration);
        $this->services->setService(
            'Config',
            array(
                'doctrine' => array(
                    'sql_logger_collector' => array(
                        'orm_default' => array(
                            'configuration' => 'configuration_service_id',
                        ),
                    ),
                ),
            )
        );
        $this->factory->createService($this->services);
        $this->assertInstanceOf('Doctrine\DBAL\Logging\SQLLogger', $configuration->getSQLLogger());
    }

    public function testCreateSQLLoggerWithPreviousExistingLoggerChainsLoggers()
    {
        $originalLogger = $this->getMock('Doctrine\DBAL\Logging\SQLLogger');
        $originalLogger
            ->expects($this->once())
            ->method('startQuery')
            ->with($this->equalTo('test query'));
        $injectedLogger = $this->getMock('Doctrine\DBAL\Logging\DebugStack');
        $injectedLogger
            ->expects($this->once())
            ->method('startQuery')
            ->with($this->equalTo('test query'));

        $configuration = new ORMConfiguration();
        $configuration->setSQLLogger($originalLogger);
        $this->services->setService('doctrine.configuration.orm_default', $configuration);
        $this->services->setService('custom_logger', $injectedLogger);
        $this->services->setService(
            'Config',
            array(
                'doctrine' => array(
                    'sql_logger_collector' => array(
                        'orm_default' => array(
                            'sql_logger' => 'custom_logger',
                        ),
                    ),
                ),
            )
        );
        $this->factory->createService($this->services);
        /* @var $logger \Doctrine\DBAL\Logging\SQLLogger */
        $logger = $configuration->getSQLLogger();
        $logger->startQuery('test query');
    }

    public function testCreateSQLLoggerWithCustomLogger()
    {
        $configuration = new ORMConfiguration();
        $logger = new DebugStack();
        $this->services->setService('doctrine.configuration.orm_default', $configuration);
        $this->services->setService('logger_service_id', $logger);
        $this->services->setService(
            'Config',
            array(
                'doctrine' => array(
                    'sql_logger_collector' => array(
                        'orm_default' => array(
                            'sql_logger' => 'logger_service_id',
                        ),
                    ),
                ),
            )
        );
        $this->factory->createService($this->services);
        $this->assertSame($logger, $configuration->getSQLLogger());
    }

    public function testCreateSQLLoggerWithCustomName()
    {
        $this->services->setService('doctrine.configuration.orm_default', new ORMConfiguration());
        $this->services->setService(
            'Config',
            array(
                'doctrine' => array(
                    'sql_logger_collector' => array(
                        'orm_default' => array(
                            'name' => 'test_collector_name',
                        ),
                    ),
                ),
            )
        );
        /* @var $service \DoctrineORMModule\Collector\SQLLoggerCollector */
        $service = $this->factory->createService($this->services);
        $this->assertSame('test_collector_name', $service->getName());
    }
}
