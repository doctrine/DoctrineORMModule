<?php

namespace DoctrineORMModule\Hydrator;

use DateTime;
use RuntimeException;
use Traversable;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;
use Zend\Stdlib\Hydrator\ClassMethods as Hydrator;
use Zend\Stdlib\Hydrator\HydratorInterface;

class DoctrineEntity implements HydratorInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Zend\Stdlib\Hydrator\ClassMethods
     */
    protected $hydrator;

    /**
     * @param $em \Doctrine|ORM\EntityManager
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function em()
    {
        return $this->em;
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
            if ($value instanceof DateTime) {
                $value = $value->format('Y-m-d H:i:s');
            }
        }

        return $result;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  object $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        $objectClass = get_class($object);
        $metadata    = $this->em()->getClassMetadata($objectClass);

        foreach($data as $field => &$value) {
            if ($metadata->hasAssociation($field)) {
                $target = $metadata->getAssociationTargetClass($field);

                if (is_numeric($value)) {
                    $value = $this->em()->getReference($target, $value);
                } else if (is_array($value)) {
                    $assocData = $metadata->getAssociationMappedByTargetField($field);

                    if (false) {
                        // todo: implement to many mapping
                    } else {
                        $value = $this->em()->getReference($target, $value);
                    }
                }
            } else if ($metadata->hasField($field)) {
                $fdata = $metadata->getFieldMapping($field);

                $isDate = $fdata['type'] == 'datetime' || $fdata['type'] == 'date' || $fdata['type'] == 'time';
                if ($isDate && !$value instanceof DateTime) {
                    if ($value == 0) {
                        $value = 'now';
                    }

                    if (false === strtotime($value)) {
                        throw new RuntimeException(sprintf(
                            'Field "%s" is a date, time, or datetime but "%s" could not be turned into a valid date',
                            $field,
                            $value
                        ));
                    }

                    $value = new DateTime($value);
                }
            }
        }

        return $this->getHydrator()->hydrate($data, $object);
    }

    public function getHydrator()
    {
        if (null === $this->hydrator) {
            $this->hydrator = new Hydrator;
        }
        return $this->hydrator;
    }
}