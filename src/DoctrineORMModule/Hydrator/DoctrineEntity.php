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
        $this->metadata = $this->objectManager->getClassMetadata(get_class($object));

        foreach($data as $field => &$value)
        {
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
            return $this->objectManager->find($target, $valueOrObject);
        }

        $identifiers = $this->metadata->getIdentifierValues($valueOrObject);
        return $this->objectManager->find($target, $identifiers);
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
                $values[] = $this->objectManager->find($target, $value);
                continue;
            }

            $identifiers = $this->metadata->getIdentifierValues($valueOrObject);
            $values[] = $this->objectManager->find($target, $identifiers);
        }

        return $values;
    }
}