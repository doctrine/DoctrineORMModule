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

chdir(__DIR__);

$previousDir = '.';
while (!file_exists('config/application.config.php')) {
    $dir = dirname(getcwd());
    if($previousDir === $dir) {
        throw new RuntimeException(
            'Unable to locate "config/application.config.php":'
            . ' is DoctrineORMModule in a subdir of your application skeleton?'
        );
    }
    $previousDir = $dir;
    chdir($dir);
}

if (is_readable(__DIR__ . '/TestConfiguration.php')) {
    require_once __DIR__ . '/TestConfiguration.php';
} else {
    require_once __DIR__ . '/TestConfiguration.php.dist';
}

set_include_path(__DIR__ . PATH_SEPARATOR . get_include_path());
require_once (getenv('ZF2_PATH') ?: 'vendor/ZendFramework/library') . '/Zend/Loader/AutoloaderFactory.php';
\Zend\Loader\AutoloaderFactory::factory();

$defaultListeners = new Zend\Module\Listener\DefaultListenerAggregate(
    new Zend\Module\Listener\ListenerOptions(
        array(
            'module_paths' => array(
                './vendor',
            ),
        )
    )
);

$moduleManager = new \Zend\Module\Manager(array(
    'OcraComposer',
    'DoctrineModule',
    'DoctrineORMModule',
));
$moduleManager->events()->attachAggregate($defaultListeners);
$moduleManager->loadModules();

$config = $defaultListeners->getConfigListener()->getMergedConfig(false);

// setup sqlite
$config['di']['instance']['DoctrineORMModule\Doctrine\ORM\Connection']['parameters']['params'] = array(
	'driver' => 'pdo_sqlite',
	'memory' => true,
);

// setup the driver
$config['di']['instance']['orm_driver_chain']['parameters']['drivers']['doctrine_test_driver'] = array(
	'class' 	=> 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
	'namespace' => 'DoctrineORMModuleTest\Assets\Entity',
	'paths'     => array(__DIR__ . '/DoctrineORMModuleTest/Assets/Entity'),
);

$di = new \Zend\Di\Di();
$di->instanceManager()->addTypePreference('Zend\Di\Locator', $di);

$config = new \Zend\Di\Configuration($config['di']);
$config->configure($di);

\DoctrineORMModuleTest\Framework\TestCase::setLocator($di);