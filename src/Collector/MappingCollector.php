<?php

declare(strict_types=1);

namespace DoctrineORMModule\Collector;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\ClassMetadataFactory;
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
    protected $classMetadataFactory = null;

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
     * @return array{name: string, classes: ClassMetadata[]}
     */
    public function __serialize(): array
    {
        return [
            'name'    => $this->name,
            'classes' => $this->classes,
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @deprecated 4.2.0 This function will be removed in 5.0.0. Use __serialize() instead.
     */
    public function serialize()
    {
        return serialize($this->__serialize());
    }

    /**
     * @param array{name: string, classes: ClassMetadata[]} $data
     */
    public function __unserialize(array $data): void
    {
        $this->name    = $data['name'];
        $this->classes = $data['classes'];
    }

    /**
     * {@inheritDoc}
     *
     * @deprecated 4.2.0 This function will be removed in 5.0.0. Use __unserialize() instead.
     */
    public function unserialize($serialized)
    {
        $this->__unserialize(unserialize($serialized));
    }

    /**
     * @return ClassMetadata[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }
}
