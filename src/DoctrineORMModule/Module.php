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
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace DoctrineORMModule;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\Tools\Console\ConsoleRunner as ORMConsoleRunner;
use DoctrineModule\Service as CommonService;
use DoctrineORMModule\Service as ORMService;

use Zend\ModuleManager\ModuleManagerInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\ModuleEvent;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\EventManager\EventInterface;

use Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand;

use ReflectionClass;

/**
 * Base module for Doctrine ORM.
 *
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link    www.doctrine-project.org
 * @since   1.0
 * @version $Revision$
 * @author  Kyle Spraggs <theman@spiffyjr.me>
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class Module implements ServiceProviderInterface, ConfigProviderInterface
{
    public function init(ModuleManagerInterface $moduleManager)
    {
        $moduleManager->getEventManager()->attach('loadModules.post', function(ModuleEvent $e) {
            $config   = $e->getConfigListener()->getMergedConfig();
            $autoload = isset($config['doctrine']['orm_autoload_annotations']) ?
                $config['doctrine']['orm_autoload_annotations'] :
                false;

            if ($autoload) {
                $refl = new ReflectionClass('Doctrine\ORM\Mapping\Driver\AnnotationDriver');
                $path = dirname($refl->getFileName()) . '/DoctrineAnnotations.php';
                AnnotationRegistry::registerFile($path);

                $refl = new ReflectionClass('Zend\Form\Annotation\AnnotationBuilder');
                $path = dirname($refl->getFileName()) . '/ZendAnnotations.php';
                AnnotationRegistry::registerFile($path);
            }
        });
    }

    public function onBootstrap(EventInterface $e)
    {
        /* @var $app \Zend\Mvc\ApplicationInterface */
        $app    = $e->getTarget();
        $events = $app->getEventManager()->getSharedManager();

        // Attach to helper set event and load the entity manager helper.
        $events->attach('doctrine', 'loadCli.post', function(EventInterface $e) {
            /* @var $cli \Symfony\Component\Console\Application */
            $cli = $e->getTarget();

            ORMConsoleRunner::addCommands($cli);
            $cli->addCommands(array(
                new DiffCommand(),
                new ExecuteCommand(),
                new GenerateCommand(),
                new MigrateCommand(),
                new StatusCommand(),
                new VersionCommand(),
            ));

            /* @var $sm ServiceLocatorInterface */
            $sm = $e->getParam('ServiceManager');
            /* @var $em \Doctrine\ORM\EntityManager */
            $em = $sm->get('doctrine.entitymanager.orm_default');
            $db = new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection());
            $eh = new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em);
            $cli->getHelperSet()->set($db, 'db');
            $cli->getHelperSet()->set($eh, 'em');
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * {@inheritDoc}
     */
    public function getServiceConfiguration()
    {
        return array(
            'aliases' => array(
                'Doctrine\ORM\EntityManager' => 'doctrine.entitymanager.orm_default',
            ),
            'factories' => array(
                'DoctrineORMModule\Form\Annotation\AnnotationBuilder' => function(ServiceLocatorInterface $sl) {
                    return new \DoctrineORMModule\Form\Annotation\AnnotationBuilder(
                        $sl->get('doctrine.entitymanager.orm_default')
                    );
                },
                'doctrine.connection.orm_default'    => new CommonService\ConnectionFactory('orm_default'),
                'doctrine.configuration.orm_default' => new ORMService\ConfigurationFactory('orm_default'),
                'doctrine.driver.orm_default'        => new CommonService\DriverFactory('orm_default'),
                'doctrine.entitymanager.orm_default' => new ORMService\EntityManagerFactory('orm_default'),
                'doctrine.eventmanager.orm_default'  => new CommonService\EventManagerFactory('orm_default'),
            )
        );
    }
}