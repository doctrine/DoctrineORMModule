<?php

declare(strict_types=1);

namespace DoctrineORMModule\Collector;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Laminas\DeveloperTools\Collector\AutoHideInterface;
use Laminas\DeveloperTools\Collector\CollectorInterface;
use Laminas\Mvc\MvcEvent;
use Serializable;
use function ksort;
use function serialize;
use function unserialize;

/**
 * Collector to be used in DeveloperTools to record and display mapping information
 */
class MappingCollector implements CollectorInterface, AutoHideInterface, Serializable
{
    /**
     * Collector priority
     */
    public const PRIORITY = 10;

    /** @var string */
    protected $name;

    /** @var ClassMetadataFactory|null */
    protected $classMetadataFactory = [];

    /** @var ClassMetadata[] indexed by class name */
    protected $classes = [];

    public function __construct(ClassMetadataFactory $classMetadataFactory, string $name)
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
        return self::PRIORITY;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(MvcEvent $mvcEvent)
    {
        if (! $this->classMetadataFactory) {
            return;
        }

        /** @var ClassMetadata[] $metadata */
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
     * @return ClassMetadata[]
     */
    public function getClasses() : array
    {
        return $this->classes;
    }
}
