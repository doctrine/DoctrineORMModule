<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Yuml;

use DoctrineORMModule\Yuml\YumlController;
use Laminas\Http\Client;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\Plugin\Redirect;
use Laminas\Mvc\Controller\PluginManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

/**
 * Tests for Yuml redirector controller
 *
 * @link    http://www.doctrine-project.org/
 */
class YumlControllerTest extends TestCase
{
    protected YumlController $controller;

    /** @var Client&MockObject */
    protected $httpClient;

    /** @var PluginManager&MockObject */
    protected $pluginManager;

    /**
     * {@inheritDoc}
     *
     * @covers \DoctrineORMModule\Yuml\YumlController::__construct
     */
    public function setUp(): void
    {
        $this->httpClient    = $this->createMock(Client::class);
        $this->controller    = new YumlController($this->httpClient);
        $this->pluginManager = $this->getMockBuilder(PluginManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->controller->setPluginManager($this->pluginManager);
    }

    /**
     * @covers \DoctrineORMModule\Yuml\YumlController::indexAction
     */
    public function testIndexActionWillRedirectToYuml(): void
    {
        $response           = $this->createMock(Response::class);
        $controllerResponse = $this->createMock(Response::class);
        $redirect           = $this->createMock(Redirect::class);
        $this->httpClient->expects($this->any())->method('send')->will($this->returnValue($response));
        $response->expects($this->any())->method('isSuccess')->will($this->returnValue(true));
        $response->expects($this->any())->method('getBody')->will($this->returnValue('short-url'));
        $this
            ->pluginManager
            ->expects($this->any())
            ->method('get')->with('redirect')
            ->will($this->returnValue($redirect));
        $redirect
            ->expects($this->any())
            ->method('toUrl')
            ->with('https://yuml.me/short-url')
            ->will($this->returnValue($controllerResponse));

        $this->assertSame($controllerResponse, $this->controller->indexAction());
    }

    /**
     * @covers \DoctrineORMModule\Yuml\YumlController::indexAction
     */
    public function testIndexActionWillFailOnMalformedResponse(): void
    {
        $response = $this->createMock(Response::class);
        $this->httpClient->expects($this->any())->method('send')->will($this->returnValue($response));
        $response->expects($this->any())->method('isSuccess')->will($this->returnValue(false));

        $this->expectException(UnexpectedValueException::class);
        $this->controller->indexAction();
    }
}
