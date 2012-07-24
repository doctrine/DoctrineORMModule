<?php

namespace DoctrineORMModule\Hydrator;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class DoctrineEntity implements HydratorInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var ClassMetadata
     */
    protected $metadata;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @param ObjectManager     $objectManager
     * @param HydratorInterface $hydrator
     */
    public function __construct(ObjectManager $objectManager, HydratorInterface $hydrator = null)
    {
        $this->objectManager = $objectManager;

        if ($hydrator === null) {
            $hydrator = new ClassMethodsHydrator(false);
        }

        $this->setHydrator($hydrator);
    }

    /**
     * @param HydratorInterface $hydrator
     * @return DoctrineEntity
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
        return $this;
    }

    /**
     * @return HydratorInterface
     */
    public function getHydrator()
    {
        return $this->hydrator;
    }

    /**
     * Extract values from an object
     *
     * @param  object $object
     * @return array
     */
    public function extract($object)
    {
        return $this->hydrator->extract($object);
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
        $this->metadata = $this->objectManager->getClassMetadata(get_class($object));

        foreach($data as $field => &$value) {
            // Handle DateTime objects properly
            if (in_array($this->metadata->getTypeOfField($field), array('datetime', 'time', 'date'))) {
                if (is_int($value)) {
                    $dt = new DateTime();
                    $dt->setTimestamp($value);

                    $value = $dt;
                } else if (is_string($value)) {
                    $value = new DateTime($value);
                }
            }

            if ($this->metadata->hasAssociation($field)) {
                $target = $this->metadata->getAssociationTargetClass($field);

                if ($this->metadata->isSingleValuedAssociation($field)) {
                    $value = $this->toOne($value, $target);
                } elseif ($this->metadata->isCollectionValuedAssociation($field)) {
                    $value = $this->toMany($value, $target);
                }
            }
        }

        return $this->hydrator->hydrate($data, $object);
    }

    /**
     * @param mixed  $valueOrObject
     * @param        $target
     * @return object
     */
    protected function toOne($valueOrObject, $target)
    {
        if (is_numeric($valueOrObject)) {
            return $this->find($target, $valueOrObject);
        }

        $identifiers = $this->metadata->getIdentifierValues($valueOrObject);
        return $this->find($target, $identifiers);
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
                $values[] = $this->find($target, $value);
                continue;
            }

            $identifiers = $this->metadata->getIdentifierValues($valueOrObject);
            $values[] = $this->find($target, $identifiers);
        }

        return $values;
    }

    /**
     * @param  string    $target
     * @param  int|array $identifiers
     * @return object
     */
    protected function find($target, $identifiers)
    {
        return $this->objectManager->find($target, $identifiers);
    }
}