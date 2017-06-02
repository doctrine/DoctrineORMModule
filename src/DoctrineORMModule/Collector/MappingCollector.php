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

namespace DoctrineORMModule\Collector;

use Serializable;

use ZendDeveloperTools\Collector\CollectorInterface;
use ZendDeveloperTools\Collector\AutoHideInterface;

use Zend\Mvc\MvcEvent;

use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;

/**
 * Collector to be used in ZendDeveloperTools to record and display mapping information
 *
 * @license MIT
 * @link    www.doctrine-project.org
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class MappingCollector implements CollectorInterface, AutoHideInterface, Serializable
{
    /**
     * Collector priority
     */
    const PRIORITY = 10;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var ClassMetadataFactory|null
     */
    protected $classMetadataFactory = [];

    /**
     * @var \Doctrine\Common\Persistence\Mapping\ClassMetadata[] indexed by class name
     */
    protected $classes = [];

    /**
     * @param ClassMetadataFactory $classMetadataFactory
     * @param string               $name
     */
    public function __construct(ClassMetadataFactory $classMetadataFactory, $name)
    {
        $this->classMetadataFactory = $classMetadataFactory;
        $this->name                 = (string) $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getPriority()
    {
        return static::PRIORITY;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(MvcEvent $mvcEvent)
    {
        if (! $this->classMetadataFactory) {
            return;
        }

        /* @var $metadata \Doctrine\Common\Persistence\Mapping\ClassMetadata[] */
        $metadata      = $this->classMetadataFactory->getAllMetadata();
        $this->classes = [];

        foreach ($metadata as $class) {
            $this->classes[$class->getName()] = $class;
        }
        ksort($this->classes);
    }

    /**
     * {@inheritDoc}
     */
    public function canHide()
    {
        return empty($this->classes);
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize(
            [
                'name'    => $this->name,
                'classes' => $this->classes,
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        $data          = unserialize($serialized);
        $this->name    = $data['name'];
        $this->classes = $data['classes'];
    }

    /**
     * @return \Doctrine\Common\Persistence\Mapping\ClassMetadata[]
     */
    public function getClasses()
    {
        return $this->classes;
    }
}
