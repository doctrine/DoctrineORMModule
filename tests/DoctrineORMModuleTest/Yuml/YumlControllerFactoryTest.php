<?php

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
