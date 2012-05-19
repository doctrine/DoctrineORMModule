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
use DoctrineModule\Service as CommonService;
use DoctrineORMModule\Service as ORMService;

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
class Module
{
    public function init($mm)
    {
        $mm->events()->attach('loadModules.post', function($e) {
            $config   = $e->getConfigListener()->getMergedConfig();
            $autoload = isset($config['doctrine']['orm_autoload_annotations']) ?
                $config['doctrine']['orm_autoload_annotations'] :
                false;

            if ($autoload) {
                $refl = new \ReflectionClass('Doctrine\ORM\Mapping\Driver\AnnotationDriver');
                $path = realpath(dirname($refl->getFileName())) . '/DoctrineAnnotations.php';

                AnnotationRegistry::registerFile($path);
            }
        });
    }

    public function onBootstrap($e)
    {
        $app    = $e->getTarget();
        $events = $app->events()->getSharedManager();

        // Attach to helper set event and load the entity manager helper.
        $events->attach('doctrine', 'loadCli.post', function($e) {
            $cli = $e->getTarget();

            $cli->addCommands(array(
                // DBAL Commands
                new \Doctrine\DBAL\Tools\Console\Command\RunSqlCommand(),
                new \Doctrine\DBAL\Tools\Console\Command\ImportCommand(),

                // ORM Commands
                new \Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand(),
                new \Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand(),
                new \Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand(),
                new \Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand(),
                new \Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand(),
                new \Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand(),
                new \Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand(),
                new \Doctrine\ORM\Tools\Console\Command\ConvertDoctrine1SchemaCommand(),
                new \Doctrine\ORM\Tools\Console\Command\GenerateRepositoriesCommand(),
                new \Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand(),
                new \Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand(),
                new \Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand(),
                new \Doctrine\ORM\Tools\Console\Command\RunDqlCommand(),
                new \Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand(),
                new \Doctrine\ORM\Tools\Console\Command\InfoCommand()
            ));

            $em = $e->getParam('ServiceManager')->get('doctrine.entitymanager.orm_default');
            $eh = new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em);
            $cli->getHelperSet()->set($eh, 'em');
        });
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * Expected to return \Zend\ServiceManager\Configuration object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Configuration
     */
    public function getServiceConfiguration()
    {
        return array(
            'factories' => array(
                'doctrine.connection.orm_default'    => new CommonService\ConnectionFactory('orm_default'),
                'doctrine.configuration.orm_default' => new ORMService\ConfigurationFactory('orm_default'),
                'doctrine.driver.orm_default'        => new CommonService\DriverFactory('orm_default'),
                'doctrine.entitymanager.orm_default' => new ORMService\EntityManagerFactory('orm_default'),
                'doctrine.eventmanager.orm_default'  => new CommonService\EventManagerFactory('orm_default'),
            )
        );
    }
}