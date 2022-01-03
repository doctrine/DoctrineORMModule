<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Yuml;

use DoctrineORMModule\Yuml\YumlController;
use DoctrineORMModule\Yuml\YumlControllerFactory;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;

class YumlControllerFactoryTest extends TestCase
{
    public function testCreateService(): void
    {
        $config = [
            'laminas-developer-tools' => [
                'toolbar' => ['enabled' => true],
            ],
        ];

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator->expects($this->once())->method('get')->with('config')->willReturn($config);

        $factory    = new YumlControllerFactory();
        $controller = $factory($serviceLocator, YumlController::class);

        $this->assertInstanceOf(YumlController::class, $controller);
    }

    public function testCreateServiceWithNotEnabledToolbar(): void
    {
        $config = [
            'laminas-developer-tools' => [
                'toolbar' => ['enabled' => false],
            ],
        ];

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator->expects($this->once())->method('get')->with('config')->willReturn($config);

        $factory = new YumlControllerFactory();

        $this->expectException(ServiceNotFoundException::class);
        $factory($serviceLocator, YumlController::class);
    }

    public function testCreateServiceWithNoConfigKey(): void
    {
        $config = [
            'laminas-developer-tools' => [],
        ];

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator->expects($this->once())->method('get')->with('config')->willReturn($config);

        $factory = new YumlControllerFactory();

        $this->expectException(ServiceNotFoundException::class);
        $factory($serviceLocator, YumlController::class);
    }
}
