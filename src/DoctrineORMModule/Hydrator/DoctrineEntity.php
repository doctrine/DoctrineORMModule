<?php

namespace DoctrineORMModule\Hydrator;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
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
     * @var ClassMethodsHydrator
     */
    protected $hydrator;

    /**
     * @param EntityManager     $em
     * @param HydratorInterface $hydrator
     */
    public function __construct(EntityManager $em, HydratorInterface $hydrator = null)
    {
        $this->em = $em;

        if ($hydrator) {
            $this->setHydrator($hydrator);
        }
    }

    /**
     * @param  HydratorInterface $hydrator
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
        if (null === $this->hydrator) {
            $this->hydrator = new ClassMethodsHydrator(false);
        }

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
        $result = $this->getHydrator()->extract($object);

        foreach($result as &$value) {
            if ($value instanceof \DateTime) {
                $value = $value->format('Y-m-d H:i:s');
            }
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

        foreach($data as $field => &$value)
        {
            if ($this->metadata->hasAssociation($field)) {
                $association = $this->metadata->getAssociationMapping($field);
                $target = $this->metadata->getAssociationTargetClass($field);

                switch($association['type'])
                {
                    case ClassMetadataInfo::ONE_TO_MANY:
                    case ClassMetadataInfo::MANY_TO_MANY:
                        $value = $this->toMany($value, $target, $association);
                        break;
                    case ClassMetadataInfo::ONE_TO_ONE:
                    case ClassMetadataInfo::MANY_TO_ONE:
                        $value = $this->toOne($value, $target, $association);
                        break;
                    default:
                        throw new \Exception('Unimplemented type');
                        break;
                }
            }
        }

        return $this->getHydrator()->hydrate($data, $object);
    }

    /**
     * @param mixed  $valueOrObject
     * @param        $target
     * @param array  $association
     * @return object
     */
    protected function toOne($valueOrObject, $target, array $association)
    {
        if (is_numeric($valueOrObject)) {
            return $this->em->getReference($target, $valueOrObject);
        }

        $objectValues = $this->extract($valueOrObject);
        $identifiers = array();

        foreach($association['sourceToTargetKeyColumns'] as $column) {
            $identifiers[$column] = $objectValues[$column];
        }

        return $this->em->getReference($target, $identifiers);
    }

    /**
     * @param mixed $valueOrObject
     * @param       $target
     * @param array $association
     * @return array
     */
    protected function toMany($valueOrObject, $target, array $association)
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

            $objectValues = $this->extract($value);
            $identifiers = array();

            foreach($association['relationToTargetKeyColumns'] as $column) {
                $identifiers[$column] = $objectValues[$column];
            }

            $values[] = $this->em->getReference($target, $identifiers);
        }

        return $values;
    }
}