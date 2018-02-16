<?php

namespace DoctrineORMModuleTest\Yuml;

use DoctrineORMModule\Yuml\YumlController;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Yuml redirector controller
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class YumlControllerTest extends TestCase
{
    /**
     * @var YumlController
     */
    protected $controller;

    /**
     * @var \Zend\Http\Client|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $httpClient;

    /**
     * @var \Zend\Mvc\Controller\PluginManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pluginManager;

    /**
     * {@inheritDoc}
     *
     * @covers \DoctrineORMModule\Yuml\YumlController::__construct
     */
    public function setUp()
    {
        $this->httpClient     = $this->createMock(\Zend\Http\Client::class);
        $this->controller     = new YumlController($this->httpClient);
        $this->pluginManager  = $this->getMockBuilder(\Zend\Mvc\Controller\PluginManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->controller->setPluginManager($this->pluginManager);
    }

    /**
     * @covers \DoctrineORMModule\Yuml\YumlController::indexAction
     */
    public function testIndexActionWillRedirectToYuml()
    {
        $response = $this->createMock(\Zend\Http\Response::class);
        $controllerResponse = $this->createMock(\Zend\Http\Response::class);
        $redirect = $this->createMock(\Zend\Mvc\Controller\Plugin\Redirect::class);
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
    public function testIndexActionWillFailOnMalformedResponse()
    {
        $response = $this->createMock(\Zend\Http\Response::class);
        $this->httpClient->expects($this->any())->method('send')->will($this->returnValue($response));
        $response->expects($this->any())->method('isSuccess')->will($this->returnValue(false));

        $this->expectException(\UnexpectedValueException::class);
        $this->controller->indexAction();
    }
}
