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

use RuntimeException;
use ReflectionClass;
use Zend\EventManager\Event;
use Zend\ModuleManager\ModuleManager;
use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\Loader\StandardAutoloader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use DoctrineORMModule\ModuleManager\Feature\DoctrineDriverProviderInterface;

/**
 * Base module for Doctrine ORM.
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @since   0.1.0
 * @author  Kyle Spraggs <theman@spiffyjr.me>
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class Module implements BootstrapListenerInterface, ServiceProviderInterface
{
    /**
     * @param ModuleManager $moduleManager
     */
    public function init(ModuleManager $moduleManager)
    {
        $moduleManager->events()->attach('loadModules.post', array($this, 'registerAnnotations'));
    }

    /**
     * {@inheritDoc}
     */
    public function onBootstrap(Event $e)
    {
        $app = $e->getTarget();
        $sm  = $app->getServiceManager();
        $mm  = $sm->get('ModuleManager');

        $chain = $sm->get('Doctrine\ORM\Mapping\Driver\DriverChain');

        foreach($mm->getLoadedModules() as $module) {
            if (!$module instanceof DoctrineDriverProviderInterface
                && !method_exists($module, 'getDoctrineDrivers')
            ) {
                continue;
            }

            $drivers = $module->getDoctrineDrivers($sm->get('Doctrine\ORM\Configuration'));
            foreach($drivers as $namespace => $driver) {
                $chain->addDriver($driver, $namespace);
            }
        }
    }

    /**
     * @return array
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
                'doctrine_orm_metadata_cache'  => 'Doctrine\Common\Cache\ArrayCache',
                'doctrine_orm_query_cache'     => 'Doctrine\Common\Cache\ArrayCache',
                'doctrine_orm_result_cache'    => 'Doctrine\Common\Cache\ArrayCache',
            ),
            'factories' => array(
                'Doctrine\Common\Cache\ArrayCache' => function() {
                    return new \Doctrine\Common\Cache\ArrayCache;
                },

                'Doctrine\ORM\Configuration' => function($sm) {
                    $userConfig = $sm->get('Configuration')->doctrine_orm_config;
                    $config     = new \Doctrine\ORM\Configuration;

                    $config->setAutoGenerateProxyClasses($userConfig->proxy_auto_generate);
                    $config->setProxyDir($userConfig->proxy_dir);
                    $config->setProxyNamespace($userConfig->proxy_namespace);

                    $config->setEntityNamespaces($userConfig->entity_namespaces->toArray());

                    $config->setCustomDatetimeFunctions($userConfig->custom_datetime_functions->toArray());
                    $config->setCustomStringFunctions($userConfig->custom_string_functions->toArray());
                    $config->setCustomNumericFunctions($userConfig->custom_numeric_functions->toArray());

                    foreach($userConfig->named_queries as $query) {
                        $config->addNamedQuery($query->name, $query->dql);
                    }

                    foreach($userConfig->named_native_queries as $query) {
                        $config->addNamedNativeQuery($query->name, $query->sql, new $query->rsm);
                    }

                    $config->setMetadataCacheImpl($sm->get('doctrine_orm_metadata_cache'));
                    $config->setQueryCacheImpl($sm->get('doctrine_orm_query_cache'));
                    $config->setResultCacheImpl($sm->get('doctrine_orm_result_cache'));

                    $config->setSQLLogger($userConfig->sql_logger);

                    $config->setMetadataDriverImpl($sm->get('Doctrine\ORM\Mapping\Driver\DriverChain'));

                    return $config;
                },

                'Doctrine\ORM\EntityManager' => function($sm) {
                    $connection = $sm->get('Configuration')->doctrine_orm_connection;
                    $ormConfig  = $sm->get('Doctrine\ORM\Configuration');

                    return \Doctrine\ORM\EntityManager::create($connection->toArray(), $ormConfig);
                },

                'Doctrine\ORM\Mapping\Driver\DriverChain' => function($sm) {
                    return new \Doctrine\ORM\Mapping\Driver\DriverChain;
                },
            )
        );
    }
}
