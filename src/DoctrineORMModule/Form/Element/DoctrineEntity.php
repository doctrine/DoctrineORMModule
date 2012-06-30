<?php

namespace DoctrineORMModule\Form\Element;

use RuntimeException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Zend\Form\Element;

class DoctrineEntity extends Element
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'options' => array(),
        'type'    => 'select',
    );

    /**
     * @var array
     */
    protected $entities;

    /**
     * @return array|\Traversable
     */
    public function getAttributes()
    {
        $this->loadOptions();
        return parent::getAttributes();
    }

    /**
     * @return array
     */
    public function getEntities()
    {
        if (null === $this->entities) {
            $spec        = $this->getAttribute('spec');
            $em          = $this->getAttribute('entityManager');
            $targetClass = $this->getAttribute('targetClass');

            if (is_callable($spec)) {
                /** @var $spec \Closure  */
                $spec = $spec($em->getRepository($targetClass));
            }

            if ($spec instanceof QueryBuilder) {
                $entities = $spec->getQuery()->execute();
            } else if ($spec instanceof Query) {
                $entities = $spec->execute();
            } else {
                $entities = $em->getRepository($targetClass)->findAll();
            }
            $this->entities = $entities;
        }
        return $this->entities;
    }

    /**
     * @throws RuntimeException
     */
    protected function loadOptions()
    {
        if (!empty($this->attributes['options'])) {
            return;
        }

        if (!($em = $this->getAttribute('entityManager'))) {
            throw new RuntimeException('No entityManager was set');
        }

        if (!($targetClass = $this->getAttribute('targetClass'))) {
            throw new RuntimeException('No targetClass was set');
        }

        $metadata   = $em->getClassMetadata($targetClass);
        $identifier = $metadata->getIdentifierFieldNames();
        $entities   = $this->getEntities();
        $options    = array();

        foreach($entities as $key => $entity) {
            if (($property = $this->getAttribute('property'))) {
                if (!$metadata->hasField($property)) {
                    throw new RuntimeException(sprintf(
                        'Property "%s" could not be found in entity "%s"',
                        $property,
                        $this->targetClass
                    ));
                }
                $reflClass = $metadata->getReflectionProperty($property);
                $label     = $reflClass->getValue($entity);
            } else if (($method = $this->getAttribute('method'))) {
                if (!is_callable(array($entity, $method))) {
                    throw new RuntimeException(sprintf(
                        'Method "%s::%s" is not callable',
                        $this->targetClass,
                        $method
                    ));
                }
                $label = $entity->{$method}();
            } else {
                if (!is_callable(array($entity, '__toString'))) {
                    throw new RuntimeException(sprintf(
                        '%s must have a "__toString()" method defined if you have not set ' .
                        'a property or method to use.',
                        $targetClass
                    ));
                }
                $label = (string) $entity;
            }

            if (count($identifier) > 1) {
                $value = $key;
            } else {
                $value = current($metadata->getIdentifierValues($entity));
            }

            $options[] = array('label' => $label, 'value' => $value);
        }

        $this->attributes['options'] = $options;
    }
}