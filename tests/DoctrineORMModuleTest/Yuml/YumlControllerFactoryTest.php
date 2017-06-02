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

namespace DoctrineORMModuleTest\Yuml;

use PHPUnit\Framework\TestCase;
use DoctrineORMModule\Yuml\YumlController;
use DoctrineORMModule\Yuml\YumlControllerFactory;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class YumlControllerFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $config = [
            'zenddevelopertools' => [
                'toolbar' => [
                    'enabled' => true,
                ],
            ],
        ];

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $pluginManager = $this->getMockBuilder(AbstractPluginManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serviceLocator->expects($this->once())->method('get')->with('config')->willReturn($config);
        $pluginManager->expects($this->once())->method('getServiceLocator')->willReturn($serviceLocator);

        $factory = new YumlControllerFactory();
        $controller = $factory->createService($pluginManager);

        $this->assertInstanceOf(YumlController::class, $controller);
    }

    public function testCreateServiceWithNotEnabledToolbar()
    {
        $config = [
            'zenddevelopertools' => [
                'toolbar' => [
                    'enabled' => false,
                ],
            ],
        ];

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $pluginManager = $this->getMockBuilder(AbstractPluginManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serviceLocator->expects($this->once())->method('get')->with('config')->willReturn($config);
        $pluginManager->expects($this->once())->method('getServiceLocator')->willReturn($serviceLocator);

        $factory = new YumlControllerFactory();

        $this->expectException(\Zend\ServiceManager\Exception\ServiceNotFoundException::class);
        $factory->createService($pluginManager);
    }

    public function testCreateServiceWithNoConfigKey()
    {
        $config = [
            'zenddevelopertools' => [],
        ];

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $pluginManager = $this->getMockBuilder(AbstractPluginManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serviceLocator->expects($this->once())->method('get')->with('config')->willReturn($config);
        $pluginManager->expects($this->once())->method('getServiceLocator')->willReturn($serviceLocator);

        $factory = new YumlControllerFactory();

        $this->expectException(\Zend\ServiceManager\Exception\ServiceNotFoundException::class);
        $factory->createService($pluginManager);
    }
}
