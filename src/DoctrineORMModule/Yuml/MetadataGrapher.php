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

namespace DoctrineORMModule\Yuml;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;

/**
 * Utility to generate Yuml compatible strings from metadata graphs
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class MetadataGrapher
{
    /**
     * Temporary array where already visited collections are stored
     *
     * @var array
     */
    protected $visitedAssociations = array();

    /**
     * @var \Doctrine\Common\Persistence\Mapping\ClassMetadata[]
     */
    private $metadata;

    /**
     * Generate a YUML compatible `dsl_text` to describe a given array
     * of entities
     *
     * @param  $metadata \Doctrine\Common\Persistence\Mapping\ClassMetadata[]
     *
     * @return string
     */
    public function generateFromMetadata(array $metadata)
    {
        $this->metadata            = $metadata;
        $this->visitedAssociations = array();
        $str                       = array();

        foreach ($metadata as $class) {
            $parent = $this->getParent($class);

            if ($parent) {
                $str[] = $this->getClassString($parent) . '^' . $this->getClassString($class);
            }

            $associations = $class->getAssociationNames();

            if (empty($associations) && !isset($this->visitedAssociations[$class->getName()])) {
                $str[] = $this->getClassString($class);

                continue;
            }

            foreach ($associations as $associationName) {
                if ($parent && in_array($associationName, $parent->getAssociationNames())) {
                    continue;
                }

                if ($this->visitAssociation($class->getName(), $associationName)) {
                    $str[] = $this->getAssociationString($class, $associationName);
                }
            }
        }

        return implode(',', $str);
    }

    private function getAssociationString(ClassMetadata $class1, $association)
    {
        $targetClassName = $class1->getAssociationTargetClass($association);
        $class2          = $this->getClassByName($targetClassName);
        $isInverse       = $class1->isAssociationInverseSide($association);
        $class1Count     = $class1->isCollectionValuedAssociation($association) ? 2 : 1;

        if (null === $class2) {
            return $this->getClassString($class1)
                . ($isInverse ? '<' : '<>') . '-' . $association . ' '
                . ($class1Count > 1 ? '*' : ($class1Count ? '1' : ''))
                . ($isInverse ? '<>' : '>')
                . '[' . str_replace('\\', '.', $targetClassName) . ']';
        }

        $class1SideName = $association;
        $class2SideName = '';
        $class2Count    = 0;
        $bidirectional  = false;

        if ($isInverse) {
            $class2SideName = (string) $class1->getAssociationMappedByTargetField($association);

            if ($class2SideName) {
                $class2Count    = $class2->isCollectionValuedAssociation($class2SideName) ? 2 : 1;
                $bidirectional  = true;
            }
        } else {
            foreach ($class2->getAssociationNames() as $class2Side) {
                if ($class2->isAssociationInverseSide($class2Side)
                    && ($association === $class2->getAssociationMappedByTargetField($class2Side))
                ) {
                    $class2SideName = $class2Side;
                    $class2Count    = $class2->isCollectionValuedAssociation($class2SideName) ? 2 : 1;
                    $bidirectional  = true;
                    break;
                }
            }
        }

        $this->visitAssociation($targetClassName, $class2SideName);

        return $this->getClassString($class1)
            . ($bidirectional ? ($isInverse ? '<' : '<>') : '') // class2 side arrow
            . ($class2SideName ? $class2SideName . ' ' : '')
            . ($class2Count > 1 ? '*' : ($class2Count ? '1' : '')) // class2 side single/multi valued
            . '-'
            . $class1SideName . ' '
            . ($class1Count > 1 ? '*' : ($class1Count ? '1' : '')) // class1 side single/multi valued
            . (($bidirectional && $isInverse) ? '<>' : '>') // class1 side arrow
            . $this->getClassString($class2);
    }

    /**
     * Build the string representing the single graph item
     *
     * @param ClassMetadata   $class
     *
     * @return string
     */
    private function getClassString(ClassMetadata $class)
    {
        $this->visitAssociation($class->getName());

        $className    = $class->getName();
        $classText    = '[' . str_replace('\\', '.', $className);
        $fields       = array();
        $parent       = $this->getParent($class);
        $parentFields = $parent ? $parent->getFieldNames() : array();

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

        if (!empty($fields)) {
            $classText .= '|' . implode(';', $fields);
        }

        $classText .= ']';

        return $classText;
    }

    /**
     * Retrieve a class metadata instance by name from the given array
     *
     * @param string          $className
     *
     * @return ClassMetadata|null
     */
    private function getClassByName($className)
    {
        foreach ($this->metadata as $class) {
            if ($class->getName() === $className) {
                return $class;
            }
        }

        return null;
    }

    /**
     * Retrieve a class metadata's parent class metadata
     *
     * @param ClassMetadata   $class
     *
     * @return ClassMetadata|null
     */
    private function getParent($class)
    {
        $className = $class->getName();

        if (!class_exists($className) || (!$parent = get_parent_class($className))) {
            return null;
        }

        return $this->getClassByName($parent);
    }

    /**
     * Visit a given association and mark it as visited
     *
     * @param string      $className
     * @param string|null $association
     *
     * @return bool true if the association was visited before
     */
    private function visitAssociation($className, $association = null)
    {
        if (null === $association) {
            if (isset($this->visitedAssociations[$className])) {
                return false;
            }

            $this->visitedAssociations[$className] = array();

            return true;
        }

        if (isset($this->visitedAssociations[$className][$association])) {
            return false;
        }

        if (!isset($this->visitedAssociations[$className])) {
            $this->visitedAssociations[$className] = array();
        }

        $this->visitedAssociations[$className][$association] = true;

        return true;
    }
}
