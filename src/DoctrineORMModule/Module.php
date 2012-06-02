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
use Zend\Loader\StandardAutoloader;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Base module for Doctrine ORM.
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @since   0.1.0
 * @author  Kyle Spraggs <theman@spiffyjr.me>
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class Module
{
    /**
     * @param ModuleManager $moduleManager
     */
    public function init(ModuleManager $moduleManager)
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

        if ($config['use_annotations']) {
            $annotationsFile = false;

            if (isset($config['annotation_file'])) {
                $annotationsFile = realpath($config['annotation_file']);
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
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }
}
