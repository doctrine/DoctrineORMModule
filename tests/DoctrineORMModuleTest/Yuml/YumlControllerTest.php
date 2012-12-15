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

namespace OcraServiceManagerTest\ServiceManager;

use DoctrineORMModule\Yuml\YumlController;

/**
 * Tests for Yuml redirector controller
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class YumlControlleTest extends \PHPUnit_Framework_TestCase
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
        $this->httpClient     = $this->getMock('Zend\\Http\\Client');
        $this->controller     = new YumlController($this->httpClient);
        $this->pluginManager  = $this->getMock('Zend\\Mvc\\Controller\\PluginManager');
        $this->controller->setPluginManager($this->pluginManager);
    }

    /**
     * @covers \DoctrineORMModule\Yuml\YumlController::indexAction
     */
    public function testIndexActionWillRedirectToYuml()
    {
        $response = $this->getMock('Zend\\Http\\Response');
        $controllerResponse = $this->getMock('Zend\\Http\\Response');
        $redirect = $this->getMock('Zend\\Mvc\\Controller\\Plugin\\Redirect');
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
            ->with('http://yuml.me/short-url')
            ->will($this->returnValue($controllerResponse));

        $this->assertSame($controllerResponse, $this->controller->indexAction());
    }

    /**
     * @covers \DoctrineORMModule\Yuml\YumlController::indexAction
     */
    public function testIndexActionWillFailOnMalformedResponse()
    {
        $response = $this->getMock('Zend\\Http\\Response');
        $this->httpClient->expects($this->any())->method('send')->will($this->returnValue($response));
        $response->expects($this->any())->method('isSuccess')->will($this->returnValue(false));

        $this->setExpectedException('UnexpectedValueException');
        $this->controller->indexAction();
    }
}
