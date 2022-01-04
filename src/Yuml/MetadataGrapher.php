<?php

declare(strict_types=1);

namespace DoctrineORMModule\Yuml;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Exception;

use function class_exists;
use function get_parent_class;
use function implode;
use function in_array;
use function str_replace;

/**
 * Utility to generate Yuml compatible strings from metadata graphs
 */
class MetadataGrapher
{
    /**
     * Temporary array where already visited collections are stored
     *
     * @var array<string,array<string,bool>>
     */
    protected array $visitedAssociations = [];

    /** @var ClassMetadata[] */
    private array $metadata;

    /**
     * Temporary array where reverse association name are stored
     *
     * @var ClassMetadata[]
     */
    private array $classByNames = [];

    /**
     * Generate a YUML compatible `dsl_text` to describe a given array
     * of entities
     *
     * @param ClassMetadata[] $metadata
     */
    public function generateFromMetadata(array $metadata): string
    {
        $this->metadata            = $metadata;
        $this->visitedAssociations = [];
        $str                       = [];

        foreach ($metadata as $class) {
            $parent = $this->getParent($class);

            if ($parent) {
                $str[] = $this->getClassString($parent) . '^' . $this->getClassString($class);
            }

            $associations = $class->getAssociationNames();

            if (empty($associations) && ! isset($this->visitedAssociations[$class->getName()])) {
                $str[] = $this->getClassString($class);

                continue;
            }

            foreach ($associations as $associationName) {
                if ($parent && in_array($associationName, $parent->getAssociationNames())) {
                    continue;
                }

                if (! $this->visitAssociation($class->getName(), $associationName)) {
                    continue;
                }

                $str[] = $this->getAssociationString($class, $associationName);
            }
        }

        return implode(',', $str);
    }

    private function getAssociationString(ClassMetadata $class1, string $association): string
    {
        $targetClassName = $class1->getAssociationTargetClass($association);
        $class2          = $this->getClassByName($targetClassName);
        $isInverse       = $class1->isAssociationInverseSide($association);
        $class1Count     = $class1->isCollectionValuedAssociation($association) ? 2 : 1;

        if ($class2 === null) {
            return $this->getClassString($class1)
                . ($isInverse ? '<' : '<>') . '-' . $association . ' '
                . ($class1Count > 1 ? '*' : '1')
                . ($isInverse ? '<>' : '>')
                . '[' . str_replace('\\', '.', $targetClassName) . ']';
        }

        $class1SideName = $association;
        $class2SideName = $this->getClassReverseAssociationName($class1, $class2);
        $class2Count    = 0;
        $bidirectional  = false;

        if ($class2SideName !== null) {
            if ($isInverse) {
                $class2Count   = $class2->isCollectionValuedAssociation($class2SideName) ? 2 : 1;
                $bidirectional = true;
            } elseif ($class2->isAssociationInverseSide($class2SideName)) {
                $class2Count   = $class2->isCollectionValuedAssociation($class2SideName) ? 2 : 1;
                $bidirectional = true;
            }
        }

        $this->visitAssociation($targetClassName, $class2SideName);

        return $this->getClassString($class1)
            . ($bidirectional ? ($isInverse ? '<' : '<>') : '') // class2 side arrow
            . ($class2SideName ? $class2SideName . ' ' : '')
            . ($class2Count > 1 ? '*' : ($class2Count ? '1' : '')) // class2 side single/multi valued
            . '-'
            . $class1SideName . ' '
            . ($class1Count > 1 ? '*' : '1') // class1 side single/multi valued
            . ($bidirectional && $isInverse ? '<>' : '>') // class1 side arrow
            . $this->getClassString($class2);
    }

    private function getClassReverseAssociationName(ClassMetadata $class1, ClassMetadata $class2): ?string
    {
        foreach ($class2->getAssociationNames() as $class2Side) {
            $targetClass = $this->getClassByName($class2->getAssociationTargetClass($class2Side));
            if (! $targetClass) {
                throw new Exception('Invalid class name for AssociationTargetClass ' . $class2Side);
            }

            if ($class1->getName() === $targetClass->getName()) {
                return $class2Side;
            }
        }

        return null;
    }

    /**
     * Build the string representing the single graph item
     */
    private function getClassString(ClassMetadata $class): string
    {
        $this->visitAssociation($class->getName());

        $className    = $class->getName();
        $classText    = '[' . str_replace('\\', '.', $className);
        $fields       = [];
        $parent       = $this->getParent($class);
        $parentFields = $parent ? $parent->getFieldNames() : [];

        foreach ($class->getFieldNames() as $fieldName) {
            if (in_array($fieldName, $parentFields)) {
                continue;
            }

            if ($class->isIdentifier($fieldName)) {
                $fields[] = '+' . $fieldName;
            } else {
                $fields[] = $fieldName;
            }
        }

        if (! empty($fields)) {
            $classText .= '|' . implode(';', $fields);
        }

        $classText .= ']';

        return $classText;
    }

    /**
     * Retrieve a class metadata instance by name from the given array
     */
    private function getClassByName(string $className): ?ClassMetadata
    {
        if (! isset($this->classByNames[$className])) {
            foreach ($this->metadata as $class) {
                if ($class->getName() === $className) {
                    $this->classByNames[$className] = $class;
                    break;
                }
            }
        }

        return $this->classByNames[$className] ?? null;
    }

    /**
     * Retrieve a class metadata's parent class metadata
     */
    private function getParent(ClassMetadata $class): ?ClassMetadata
    {
        $className = $class->getName();
        if (! class_exists($className)) {
            return null;
        }

        $parent = get_parent_class($className);
        if (! class_exists($className) || ! $parent) {
            return null;
        }

        return $this->getClassByName($parent);
    }

    /**
     * Visit a given association and mark it as visited
     *
     * @psalm-param class-string $className
     *
     * @return bool true if the association was visited before
     */
    private function visitAssociation(string $className, ?string $association = null): bool
    {
        if ($association === null) {
            if (isset($this->visitedAssociations[$className])) {
                return false;
            }

            $this->visitedAssociations[$className] = [];

            return true;
        }

        if (isset($this->visitedAssociations[$className][$association])) {
            return false;
        }

        if (! isset($this->visitedAssociations[$className])) {
            $this->visitedAssociations[$className] = [];
        }

        $this->visitedAssociations[$className][$association] = true;

        return true;
    }
}
