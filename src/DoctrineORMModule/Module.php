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

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\DependencyIndicatorInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\EventManager\EventInterface;
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
        $events->getSharedManager()->attach('doctrine', 'loadCli.post', array($this, 'initializeConsole'));
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

    /**
     * Initializes the console with additional commands from the ORM, DBAL and (optionally) DBAL\Migrations
     *
     * @param \Zend\EventManager\EventInterface $event
     *
     * @return void
     */
    public function initializeConsole(EventInterface $event)
    {
        /* @var $cli \Symfony\Component\Console\Application */
        $cli            = $event->getTarget();
        /* @var $serviceLocator \Zend\ServiceManager\ServiceLocatorInterface */
        $serviceLocator = $event->getParam('ServiceManager');

        $commands = array(
            'doctrine.dbal_cmd.runsql',
            'doctrine.dbal_cmd.import',
            'doctrine.orm_cmd.clear_cache_metadata',
            'doctrine.orm_cmd.clear_cache_result',
            'doctrine.orm_cmd.clear_cache_query',
            'doctrine.orm_cmd.schema_tool_create',
            'doctrine.orm_cmd.schema_tool_update',
            'doctrine.orm_cmd.schema_tool_drop',
            'doctrine.orm_cmd.ensure_production_settings',
            'doctrine.orm_cmd.convert_d1_schema',
            'doctrine.orm_cmd.generate_repositories',
            'doctrine.orm_cmd.generate_entities',
            'doctrine.orm_cmd.generate_proxies',
            'doctrine.orm_cmd.convert_mapping',
            'doctrine.orm_cmd.run_dql',
            'doctrine.orm_cmd.validate_schema',
            'doctrine.orm_cmd.info',
        );

        if (class_exists('Doctrine\\DBAL\\Migrations\\Version')) {
            $commands = ArrayUtils::merge(
                $commands,
                array(
                    'doctrine.migrations_cmd.execute',
                    'doctrine.migrations_cmd.generate',
                    'doctrine.migrations_cmd.migrate',
                    'doctrine.migrations_cmd.status',
                    'doctrine.migrations_cmd.version',
                    'doctrine.migrations_cmd.diff',
                    'doctrine.migrations_cmd.latest',
                )
            );
        }

        $cli->addCommands(array_map(array($serviceLocator, 'get'), $commands));

        /* @var $entityManager \Doctrine\ORM\EntityManager */
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $helperSet     = $cli->getHelperSet();

        if (class_exists('Symfony\Component\Console\Helper\QuestionHelper')) {
            $helperSet->set(new QuestionHelper(), 'dialog');
        } else {
            $helperSet->set(new DialogHelper(), 'dialog');
        }

        $helperSet->set(new ConnectionHelper($entityManager->getConnection()), 'db');
        $helperSet->set(new EntityManagerHelper($entityManager), 'em');
    }
}
