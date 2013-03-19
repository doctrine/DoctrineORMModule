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

namespace DoctrineORMModule;

use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\DependencyIndicatorInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Loader\StandardAutoloader;
use Zend\EventManager\EventInterface;

use Symfony\Component\Console\Helper\DialogHelper;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;

/**
 * Base module for Doctrine ORM.
 *
 * @license MIT
 * @link    www.doctrine-project.org
 * @author  Kyle Spraggs <theman@spiffyjr.me>
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class Module implements
    ControllerProviderInterface,
    BootstrapListenerInterface,
    ServiceProviderInterface,
    ConfigProviderInterface,
    InitProviderInterface,
    DependencyIndicatorInterface
{
    /**
     * {@inheritDoc}
     */
    public function init(ModuleManagerInterface $manager)
    {
        $events = $manager->getEventManager();
        // Initialize logger collector once the profiler is initialized itself
        $events->attach(
            'profiler_init',
            function () use ($manager) {
                $manager->getEvent()->getParam('ServiceManager')->get('doctrine.sql_logger_collector.orm_default');
            }
        );
    }

    /**
     * {@inheritDoc}
     */
    public function onBootstrap(EventInterface $e)
    {
        /* @var $app \Zend\Mvc\ApplicationInterface */
        $app    = $e->getTarget();
        $events = $app->getEventManager()->getSharedManager();
        $serviceManager = $app->getServiceManager();

        // Attach to helper set event and load the entity manager helper.
        $events->attach('doctrine', 'loadCli.post', function (EventInterface $e) {
            /* @var $cli \Symfony\Component\Console\Application */
            $cli = $e->getTarget();

            /* @var $sm ServiceLocatorInterface */
            $sm = $e->getParam('ServiceManager');

            $ORMCommands = array(
                $sm->get('doctrine.cmd.dbal.runsql'),
                $sm->get('doctrine.cmd.dbal.import'),
                $sm->get('doctrine.cmd.orm.clear-cache.metadata'),
                $sm->get('doctrine.cmd.orm.clear-cache.result'),
                $sm->get('doctrine.cmd.orm.clear-cache.query'),
                $sm->get('doctrine.cmd.orm.schema-tool.create'),
                $sm->get('doctrine.cmd.orm.schema-tool.update'),
                $sm->get('doctrine.cmd.orm.schema-tool.drop'),
                $sm->get('doctrine.cmd.orm.ensure-production-settings'),
                $sm->get('doctrine.cmd.orm.convert-d1-schema'),
                $sm->get('doctrine.cmd.orm.generate-repositories'),
                $sm->get('doctrine.cmd.orm.generate-entities'),
                $sm->get('doctrine.cmd.orm.generate-proxies'),
                $sm->get('doctrine.cmd.orm.convert-mapping'),
                $sm->get('doctrine.cmd.orm.run-dql'),
                $sm->get('doctrine.cmd.orm.validate-schema'),
                $sm->get('doctrine.cmd.orm.info'),
            );

            if (class_exists('Doctrine\\DBAL\\Migrations\\Version')) {
                $ORMCommands[] = $sm->get('doctrine.cmd.migrations.execute');
                $ORMCommands[] = $sm->get('doctrine.cmd.migrations.generate');
                $ORMCommands[] = $sm->get('doctrine.cmd.migrations.migrate');
                $ORMCommands[] = $sm->get('doctrine.cmd.migrations.status');
                $ORMCommands[] = $sm->get('doctrine.cmd.migrations.version');
                $ORMCommands[] = $sm->get('doctrine.cmd.migrations.diff');
            }

            $cli->addCommands($ORMCommands);

            /* @var $em \Doctrine\ORM\EntityManager */
            $em = $sm->get('doctrine.entitymanager.orm_default');
            $helperSet = $cli->getHelperSet();
            $helperSet->set(new DialogHelper(), 'dialog');
            $helperSet->set(new ConnectionHelper($em->getConnection()), 'db');
            $helperSet->set(new EntityManagerHelper($em), 'em');
        });

        $serviceManager->get('doctrine.entity_resolver.orm_default');
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
    public function getServiceConfig()
    {
        return include __DIR__ . '/../../config/services.config.php';
    }

    /**
     * {@inheritDoc}
     */
    public function getControllerConfig()
    {
        return include __DIR__ . '/../../config/controllers.config.php';
    }

    /**
     * {@inheritDoc}
     */
    public function getModuleDependencies()
    {
        return array('DoctrineModule');
    }
}
