<?php

declare(strict_types=1);

namespace DoctrineORMModule\Collector;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\ClassMetadataFactory;
use Laminas\DeveloperTools\Collector\AutoHideInterface;
use Laminas\DeveloperTools\Collector\CollectorInterface;
use Laminas\Mvc\MvcEvent;

use function ksort;

/**
 * Collector to be used in DeveloperTools to record and display mapping information
 */
class MappingCollector implements CollectorInterface, AutoHideInterface
{
    /**
     * Collector priority
     */
    public const PRIORITY = 10;

    protected string $name;

    protected ?ClassMetadataFactory $classMetadataFactory = null;

    /** @var ClassMetadata[] indexed by class name */
    protected array $classes = [];

    public function __construct(ClassMetadataFactory $classMetadataFactory, string $name)
    {
        $this->classMetadataFactory = $classMetadataFactory;
        $this->name                 = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPriority(): int
    {
        return self::PRIORITY;
    }

    public function collect(MvcEvent $mvcEvent): void
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

    public function canHide(): bool
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
     * @param array{name: string, classes: ClassMetadata[]} $data
     */
    public function __unserialize(array $data): void
    {
        $this->name    = $data['name'];
        $this->classes = $data['classes'];
    }

    /**
     * @return ClassMetadata[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }
}
