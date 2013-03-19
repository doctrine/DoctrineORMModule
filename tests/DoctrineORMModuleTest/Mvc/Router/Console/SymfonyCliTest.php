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
 * and is licensed under the MIT license.
 */

namespace DoctrineORMModuleTest\Mvc\Router\Console;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Console\Request;
use DoctrineORMModule\Mvc\Router\Console\SymfonyCli;
use DoctrineORMModuleTest\Util\ServiceManagerFactory;
use Zend\Mvc\Router\RoutePluginManager;

class SymfonyCliTest extends TestCase {

	/**
	 * @var
	 */
	private $serviceLocator;

	/**
	 * @var
	 */
	private $routePluginManager;

	/**
	 * {@inheritDoc}
	 */
	public function setUp() {
		$this->serviceLocator = ServiceManagerFactory::getServiceManager();
		$this->routePluginManager = new RoutePluginManager();
		$this->routePluginManager->setServiceLocator($this->serviceLocator);
		parent::setUp();
	}

	/**
	 * {@inheritDoc}
	 */
	public function tearDown() {
		$this->serviceLocator = null;
		parent::tearDown();
	}


	public function testMatching() {
		$request = new Request(array('scriptname.php', 'migrations:diff'));
		$route = new SymfonyCli();
		$route->setServiceLocator($this->routePluginManager);
		$match = $route->match($request);

		$this->assertInstanceOf('Zend\Mvc\Router\Console\RouteMatch', $match, "The route matches");
	}

	public function testMatchingWithParams() {
		$request = new Request(array('scriptname.php', 'migrations:diff', '--help'));
		$route = new SymfonyCli();
		$route->setServiceLocator($this->routePluginManager);
		$match = $route->match($request);

		$this->assertInstanceOf('Zend\Mvc\Router\Console\RouteMatch', $match, "The route matches");
	}

	public function testListMatching() {
		$request = new Request(array('scriptname.php', 'list', 'migrations'));
		$route = new SymfonyCli();
		$route->setServiceLocator($this->routePluginManager);
		$match = $route->match($request);

		$this->assertInstanceOf('Zend\Mvc\Router\Console\RouteMatch', $match, "The route matches");
	}

	public function testNotMatching() {
		$request = new Request(array('scriptname.php', 'orm:diff'));
		$route = new SymfonyCli();
		$route->setServiceLocator($this->routePluginManager);
		$match = $route->match($request);

		$this->assertNull($match, "The route must not match");
	}
}