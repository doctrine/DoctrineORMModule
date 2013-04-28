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
use Zend\Stdlib\ArrayUtils;

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
        /* @var $application \Zend\Mvc\ApplicationInterface */
        $application    = $e->getTarget();
        $events         = $application->getEventManager()->getSharedManager();
        $serviceManager = $application->getServiceManager();

        $events->attach('doctrine', 'loadCli.post', array($this, 'initializeConsole'));
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

    public function initializeConsole(EventInterface $event)
    {
        /* @var $cli \Symfony\Component\Console\Application */
        $cli            = $event->getTarget();
        /* @var $serviceLocator \Zend\ServiceManager\ServiceLocatorInterface */
        $serviceLocator = $event->getParam('ServiceManager');

        $commands = array(
            'doctrine.cmd.dbal.runsql',
            'doctrine.cmd.dbal.import',
            'doctrine.cmd.orm.clear-cache.metadata',
            'doctrine.cmd.orm.clear-cache.result',
            'doctrine.cmd.orm.clear-cache.query',
            'doctrine.cmd.orm.schema-tool.create',
            'doctrine.cmd.orm.schema-tool.update',
            'doctrine.cmd.orm.schema-tool.drop',
            'doctrine.cmd.orm.ensure-production-settings',
            'doctrine.cmd.orm.convert-d1-schema',
            'doctrine.cmd.orm.generate-repositories',
            'doctrine.cmd.orm.generate-entities',
            'doctrine.cmd.orm.generate-proxies',
            'doctrine.cmd.orm.convert-mapping',
            'doctrine.cmd.orm.run-dql',
            'doctrine.cmd.orm.validate-schema',
            'doctrine.cmd.orm.info',
        );

        if (class_exists('Doctrine\\DBAL\\Migrations\\Version')) {
            $commands = ArrayUtils::merge(
                $commands,
                array(
                    'doctrine.cmd.migrations.execute',
                    'doctrine.cmd.migrations.generate',
                    'doctrine.cmd.migrations.migrate',
                    'doctrine.cmd.migrations.status',
                    'doctrine.cmd.migrations.version',
                    'doctrine.cmd.migrations.diff',
                )
            );
        }

        $cli->addCommands(array_map(array($serviceLocator, 'get'), $commands));

        /* @var $entityManager \Doctrine\ORM\EntityManager */
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $helperSet     = $cli->getHelperSet();

        $helperSet->set(new DialogHelper(), 'dialog');
        $helperSet->set(new ConnectionHelper($entityManager->getConnection()), 'db');
        $helperSet->set(new EntityManagerHelper($entityManager), 'em');
    }
}
