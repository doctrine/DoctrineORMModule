<?php

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
