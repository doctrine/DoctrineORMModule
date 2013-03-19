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

namespace DoctrineORMModuleTest\Controller;

use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;

class IndexControllerTest extends AbstractConsoleControllerTestCase {

	/**
	 * @var string
	 */
	private static $MIGRATIONS_DIRECTORY = 'DoctrineTestMigrations';

	/**
	 * @var string
	 */
	private static $MIGRATIONS_NAMESPACE = 'DoctrineTestMigrations';

	/**
	 * @var string
	 */
	private static $MIGRATIONS_TABLE = 'test_migrations';

	/**
	 * {@inheritDoc}
	 */
	public function setUp() {
		$this->setApplicationConfig(
			include __DIR__ . '/../../TestConfiguration.php.dist'
		);

		if (!file_exists(self::$MIGRATIONS_DIRECTORY)) {
			mkdir(self::$MIGRATIONS_DIRECTORY);
		}

		parent::setUp();

		$testConfig = $this->getApplicationServiceLocator()->get('Config');
		$testConfig['doctrine']['migrations']['directory'] = self::$MIGRATIONS_DIRECTORY;
		$testConfig['doctrine']['migrations']['namespace'] = self::$MIGRATIONS_NAMESPACE;
		$testConfig['doctrine']['migrations']['table'] = self::$MIGRATIONS_TABLE;

		$this->getApplicationServiceLocator()->setAllowOverride(true);
		$this->getApplicationServiceLocator()->setService('Config', $testConfig);
	}

	/**
	 * {@inheritDoc}
	 */
	public function tearDown() {
		self::removeDir(self::$MIGRATIONS_DIRECTORY);

		parent::tearDown();
	}

	public function testIndexActionCanBeAccessed() {
		$request = new \Zend\Console\Request(array('scriptname.php', 'migrations:generate'));
		$this->dispatch($request);

		$this->assertResponseStatusCode(0);
		$this->assertModuleName('doctrineormmodule');
		$this->assertControllerName('doctrineormmodule\controller\index');
		$this->assertControllerClass('indexcontroller');
		$this->assertActionName('index');
		$this->assertMatchedRouteName('cliapp');
	}

	/**
	 * @param $dir
	 * @return bool
	 */
	private static function removeDir($dir) {
		$files = array_diff(scandir($dir), array('.', '..'));
		foreach ($files as $file) {
			(is_dir($dir . '/' . $file)) ? self::removeDir($dir . '/' . $file) : unlink($dir . '/' . $file);
		}
		return rmdir($dir);
	}
}