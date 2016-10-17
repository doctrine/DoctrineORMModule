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

use PHPUnit_Framework_TestCase;
use DoctrineORMModuleTest\Assets\Types\MoneyType;
use DoctrineORMModule\Service\DBALConnectionFactory;
use Doctrine\DBAL\Types\Type;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\EventManager;
use Zend\ServiceManager\ServiceManager;
use DoctrineORMModule\Service\ConfigurationFactory;

/**
 * @covers \DoctrineORMModule\Service\DBALConnectionFactory
 */
class DBALConnectionFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;
    /**
     * @var DBALConnectionFactory
     */
    protected $factory;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = new ServiceManager();
        $this->factory = new DBALConnectionFactory('orm_default');
        $this->serviceManager->setService('doctrine.cache.array', new ArrayCache());
        $this->serviceManager->setService('doctrine.eventmanager.orm_default', new EventManager());
    }

    public function testDoctrineMappingTypeReturnCorrectParent()
    {
        $config = [
            'doctrine' => [
                'connection' => [
                    'orm_default' => [
                        'driverClass'   => \Doctrine\DBAL\Driver\PDOSqlite\Driver::class,
                        'params' => [
                            'memory' => true,
                        ],
                        'doctrineTypeMappings' => [
                            'money' => 'string'
                        ],
                    ]
                ],
            ],
        ];
        $configurationMock = $this->getMockBuilder(\Doctrine\ORM\Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serviceManager->setService('doctrine.configuration.orm_default', $configurationMock);
        $this->serviceManager->setService('config', $config);
        $this->serviceManager->setService('Configuration', $config);

        $dbal = $this->factory->createService($this->serviceManager);
        $platform = $dbal->getDatabasePlatform();
        $this->assertSame('string', $platform->getDoctrineTypeMapping("money"));
    }

    public function testDoctrineAddCustomCommentedType()
    {
        $config = [
            'doctrine' => [
                'connection' => [
                    'orm_default' => [
                        'driverClass'   => \Doctrine\DBAL\Driver\PDOSqlite\Driver::class,
                        'params' => [
                            'memory' => true,
                        ],
                        'doctrineTypeMappings' => [
                            'money' => 'money',
                        ],
                        'doctrineCommentedTypes' => [
                            'money'
                        ],
                    ]
                ],
                'configuration' => [
                    'orm_default' => [
                        'types' => [
                            'money' => \DoctrineORMModuleTest\Assets\Types\MoneyType::class,
                        ],
                    ],
                ],
            ],
        ];
        $this->serviceManager->setService('config', $config);
        $this->serviceManager->setService('Configuration', $config);
        $this->serviceManager->setService(
            'doctrine.driver.orm_default',
            $this->createMock(\Doctrine\Common\Persistence\Mapping\Driver\MappingDriver::class)
        );
        $configurationFactory = new ConfigurationFactory('orm_default');
        $this->serviceManager->setService(
            'doctrine.configuration.orm_default',
            $configurationFactory->createService($this->serviceManager)
        );
        $dbal = $this->factory->createService($this->serviceManager);
        $platform = $dbal->getDatabasePlatform();
        $type = Type::getType($platform->getDoctrineTypeMapping("money"));

        $this->assertInstanceOf(\DoctrineORMModuleTest\Assets\Types\MoneyType::class, $type);
        $this->assertTrue($platform->isCommentedDoctrineType($type));
    }
}
