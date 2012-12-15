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
     * Generate a YUML compatible `dsl_text` to describe a given array
     * of entities
     *
     * @param  $metadata \Doctrine\Common\Persistence\Mapping\ClassMetadata[]
     *
     * @return string
     */
    public function generateFromMetadata(array $metadata)
    {

        //[note: You can stick notes on diagrams too!{bg:cornsilk}],
        // [Customer]<>1-orders 0..*>[Order],
        // [Order]++*-*>[LineItem],
        // [Order]-1>[DeliveryMethod],
        // [Order]*-*>[Product],
        // [Category]<->[Product],
        // [DeliveryMethod]^[National],
        // [DeliveryMethod]^[International]"

        $str = array();

        foreach ($metadata as $class) {
            $classText = $this->getClassString($class);

            foreach ($class->getAssociationNames() as $associationName) {
                $classText .= $this->getAssociationString($metadata, $class, $associationName);
            }

            $str[] = $classText;
        }

        return implode(',', $str);
    }

    private function getAssociationString(array $metadata, ClassMetadata $class1, $association)
    {
        $targetClassName = $class1->getAssociationTargetClass($association);
        $class2          = $this->getClassByName($targetClassName, $metadata);

        if (!$class2) {
            // ...
        }

        $isInverse      = $class1->isAssociationInverseSide($association);
        $class1SideName = $association;
        $class1Count    = $class1->isCollectionValuedAssociation($association) ? 2 : 1;
        $class2SideName = '';
        $class2Count    = 0;
        $bidirectional  = false;

        if ($isInverse) {
            foreach ($class2->getAssociationNames() as $class2Side) {
                if (
                    !$class2->isAssociationInverseSide($class2Side)
                    && ($class2->getAssociationMappedByTargetField($class2Side) === $association)
                ) {
                    $class2SideName = $class2Side;
                    $class2Count    = $class2->isCollectionValuedAssociation($association) ? 2 : 1;
                    $bidirectional  = true;
                    break;
                }
            }
        } else {
            $class2SideName = (string) $class1->getAssociationMappedByTargetField($association);
            $class2Count    = $class2->isCollectionValuedAssociation($association) ? 2 : 1;

            if ($class2SideName) {
                $bidirectional  = true;
            }
        }

        // @todo mark owning side with <>
        return ($bidirectional ? ($isInverse ? '<' : '<>') : '') // class2 side arrow
            . ($class2SideName ? $class2SideName . ' ' : '')
            . ($class2Count > 1 ? '*' : ($class2Count ? '1' : '')) // class2 side single/multi valued
            . '-'
            . $class1SideName . ' '
            . ($class1Count > 1 ? '*' : ($class1Count ? '1' : '')) // class1 side single/multi valued
            . (($bidirectional && $isInverse) ? '<>' : '>') // class1 side arrow
            . $this->getClassString($class2);
    }

    private function getClassString(ClassMetadata $class)
    {
        $className = $class->getName();
        $classText = '[' . addslashes($className);

        /*
        $fields = array();

        foreach ($class->getFieldNames() as $fieldName) {
            if ($class->isIdentifier($fieldName)) {
                $fields[] = '+' . $fieldName;
            } else {
                $fields[] = $fieldName;
            }
        }

        if (!empty($fields)) {
            $classText .= '|' . implode(';', $fields);
        }
        */

        $classText .= ']';

        return $classText;
    }

    /**
     * @param string          $className
     * @param ClassMetadata[] $metadata
     *
     * @return ClassMetadata|null
     */
    private function getClassByName($className, $metadata)
    {
        foreach ($metadata as $class) {
            if ($class->getName() === $className) {
                return $class;
            }
        }

        return null;
    }

    private function drawFields(ClassMetadata $class)
    {

    }
}
