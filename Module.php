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

use RuntimeException;
use ReflectionClass;
use Zend\EventManager\Event;
use Zend\Module\Consumer\AutoloaderProvider;
use Zend\Module\Manager;
use Zend\Module\ModuleEvent;
use Zend\Loader\StandardAutoloader;
use Doctrine\Common\Annotations\AnnotationRegistry;

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
class Module implements AutoloaderProvider
{
    /**
     * @param Manager $moduleManager
     */
    public function init(Manager $moduleManager)
    {
        $moduleManager->events()->attach('loadModules.post', array($this, 'registerAnnotations'));
    }

    /**
     * Registers annotations required for the Doctrine AnnotationReader
     *
     * @param  ModuleEvent $e
     * @throws RuntimeException
     */
    public function registerAnnotations(ModuleEvent $e)
    {
        $config = $e->getConfigListener()->getMergedConfig();
        $config = $config['doctrine_orm_module'];

        if ($config->use_annotations) {
            $annotationsFile = false;

            if (isset($config->annotation_file)) {
                $annotationsFile = realpath($config->annotation_file);
            }

            if (!$annotationsFile) {
                // Trying to load DoctrineAnnotations.php without knowing its location
                $annotationReflection = new ReflectionClass('Doctrine\ORM\Mapping\Driver\AnnotationDriver');
                $annotationsFile = realpath(dirname($annotationReflection->getFileName()) . '/DoctrineAnnotations.php');
            }

            if (!$annotationsFile) {
                throw new RuntimeException('Failed to load annotation mappings, check the "annotation_file" setting');
            }

            AnnotationRegistry::registerFile($annotationsFile);
        }

        if (!class_exists('Doctrine\ORM\Mapping\Entity', true)) {
            throw new RuntimeException('Doctrine could not be autoloaded: ensure it is in the correct path.');
        }
    }

    /**
     * Retrieves configuration that can be consumed by Zend\Loader\AutoloaderFactory
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        if (realpath(__DIR__ . '/vendor/doctrine-orm/lib')) {
            return array(
                'Zend\Loader\StandardAutoloader' => array(
                    StandardAutoloader::LOAD_NS => array(
                        __NAMESPACE__                   => __DIR__ . '/src/' . __NAMESPACE__,
                        __NAMESPACE__ . 'Test'          => __DIR__ . '/tests/' . __NAMESPACE__ . 'Test',
                        'Doctrine\ORM'                  => __DIR__ . '/vendor/doctrine-orm/lib/Doctrine/ORM',
                        'Doctrine\DBAL'                 => __DIR__ . '/vendor/doctrine-orm/lib/vendor/doctrine-dbal/lib/Doctrine/DBAL',
                    ),
                ),
            );
        }

        return array();
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}
