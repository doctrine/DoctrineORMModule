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
use DoctrineORMModule\Service\ConfigurationFactory;
use Doctrine\Common\Cache\ArrayCache;
use Zend\ServiceManager\ServiceManager;

class ConfigurationFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var ConfigurationFactory
     */
    protected $factory;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = new ServiceManager();
        $this->factory = new ConfigurationFactory('test_default');
        $this->serviceManager->setService('doctrine.cache.array', new ArrayCache());
        $this->serviceManager->setService(
            'doctrine.driver.orm_default',
            $this->getMock('Doctrine\Common\Persistence\Mapping\Driver\MappingDriver'
        ));
    }

    public function testWillInstantiateConfigWithoutNamingStrategySetting()
    {
        $config = array(
            'doctrine' => array(
                'configuration' => array(
                    'test_default' => array(),
                ),
            ),
        );
        $this->serviceManager->setService('Config', $config);
        $ormConfig = $this->factory->createService($this->serviceManager);
        $this->assertInstanceOf('Doctrine\ORM\Mapping\NamingStrategy', $ormConfig->getNamingStrategy());
    }

    public function testWillInstantiateConfigWithNamingStrategyObject()
    {
        $namingStrategy = $this->getMock('Doctrine\ORM\Mapping\NamingStrategy');

        $config = array(
            'doctrine' => array(
                'configuration' => array(
                    'test_default' => array(
                        'naming_strategy' => $namingStrategy,
                    ),
                ),
            ),
        );
        $this->serviceManager->setService('Config', $config);
        $factory = new ConfigurationFactory('test_default');
        $ormConfig = $factory->createService($this->serviceManager);
        $this->assertSame($namingStrategy, $ormConfig->getNamingStrategy());
    }

    public function testWillInstantiateConfigWithNamingStrategyReference()
    {
        $namingStrategy = $this->getMock('Doctrine\ORM\Mapping\NamingStrategy');
        $config = array(
            'doctrine' => array(
                'configuration' => array(
                    'test_default' => array(
                        'naming_strategy' => 'test_naming_strategy',
                    ),
                ),
            ),
        );
        $this->serviceManager->setService('Config', $config);
        $this->serviceManager->setService('test_naming_strategy', $namingStrategy);
        $ormConfig = $this->factory->createService($this->serviceManager);
        $this->assertSame($namingStrategy, $ormConfig->getNamingStrategy());
    }

    public function testWillNotInstantiateConfigWithInvalidNamingStrategyReference()
    {
        $config = array(
            'doctrine' => array(
                'configuration' => array(
                    'test_default' => array(
                        'naming_strategy' => 'test_naming_strategy',
                    ),
                ),
            ),
        );
        $this->serviceManager->setService('Config', $config);
        $this->setExpectedException('Zend\ServiceManager\Exception\InvalidArgumentException');
        $this->factory->createService($this->serviceManager);
    }
}
