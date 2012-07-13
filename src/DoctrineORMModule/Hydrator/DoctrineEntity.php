<?php

namespace DoctrineORMModule\Hydrator;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Zend\Stdlib\Hydrator\HydratorInterface;

class DoctrineEntity implements HydratorInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var ClassMetadataInfo
     */
    protected $metadata;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Extract values from an object
     *
     * @param  object $object
     * @return array
     */
    public function extract($object)
    {
        $result = array();

        $names = $this->metadata->getFieldNames();
        foreach ($names as $name) {
            $result[$name] = $this->metadata->getFieldValue($object, $name);
        }

        return $result;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array  $data
     * @param  object $object
     * @throws \Exception
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        $this->metadata = $this->em->getClassMetadata(get_class($object));

        foreach($data as $field => $value)
        {
            if ($this->metadata->hasAssociation($field)) {
                $target = $this->metadata->getAssociationTargetClass($field);

                if ($this->metadata->isSingleValuedAssociation($field)) {
                    $value = $this->toOne($value, $target);
                } elseif ($this->metadata->isCollectionValuedAssociation($field)) {
                    $value = $this->toMany($value, $target);
                }
            }

            $this->metadata->setFieldValue($object, $field, $value);
        }

        return $object;
    }

    /**
     * @param mixed  $valueOrObject
     * @param        $target
     * @return object
     */
    protected function toOne($valueOrObject, $target)
    {
        if (is_numeric($valueOrObject)) {
            return $this->em->getReference($target, $valueOrObject);
        }

        $identifiers = $this->metadata->getIdentifierValues($valueOrObject);
        return $this->em->getReference($target, $identifiers);
    }

    /**
     * @param mixed $valueOrObject
     * @param       $target
     * @return array
     */
    protected function toMany($valueOrObject, $target)
    {
        if (!is_array($valueOrObject)) {
            $valueOrObject = (array) $valueOrObject;
        }

        $values = array();
        foreach($valueOrObject as $value) {
            if (is_numeric($value)) {
                $values[] = $this->em->getReference($target, $value);
                continue;
            }

            $identifiers = $this->metadata->getIdentifierValues($valueOrObject);
            $values[] = $this->em->getReference($target, $identifiers);
        }

        return $values;
    }
}