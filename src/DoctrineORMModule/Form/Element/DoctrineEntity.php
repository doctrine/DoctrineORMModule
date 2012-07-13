<?php

namespace DoctrineORMModule\Form\Element;

use RuntimeException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator\ObjectExists as ObjectExistsValidator;
use Zend\Form\Element;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\ValidatorInterface;

class DoctrineEntity extends Element implements InputProviderInterface
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
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $targetClass;

    /**
     * @var
     */
    protected $property;

    /**
     * @var \Closure|QueryBuilder|Query
     */
    protected $spec;

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
     * Accepted options for DoctrineEntity:
     * - entity_manager: a valid Doctrine 2 Entity Manager
     * - target_class: a FQCN of the target entity
     * - property: the property of the entity to use as the label in the options
     * - spec: a closure, QueryBuilder or Query
     *
     * @param  array|\Traversable $options
     * @return DoctrineEntity
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($options['entity_manager'])) {
            $this->setEntityManager($options['entity_manager']);
        }

        if (isset($options['target_class'])) {
            $this->setTargetClass($options['target_class']);
        }

        if (isset($options['property'])) {
            $this->setProperty($options['property']);
        }

        if (isset($options['spec'])) {
            $this->setSpec($options['spec']);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getEntities()
    {
        if (null === $this->entities) {
            $spec        = $this->spec;
            $em          = $this->entityManager;
            $targetClass = $this->targetClass;

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
     * Set the entity manager
     *
     * @param EntityManager $entityManager
     * @return DoctrineEntity
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * Get the entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Set the FQCN of the target entity
     *
     * @param  string $targetClass
     * @return DoctrineEntity
     */
    public function setTargetClass($targetClass)
    {
        $this->targetClass = $targetClass;
        return $this;
    }

    /**
     * Get the target class
     *
     * @return string
     */
    public function getTargetClass()
    {
        return $this->targetClass;
    }

    /**
     * Set the property to use as the label in the options
     *
     * @param  string $property
     * @return DoctrineEntity
     */
    public function setProperty($property)
    {
        $this->property = $property;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Set the spec
     *
     * @param $spec
     * @return DoctrineEntity
     */
    public function setSpec($spec)
    {
        $this->spec = $spec;
        return $this;
    }

    /**
     * Get the spec
     *
     * @return \Closure|Query|QueryBuilder
     */
    public function getSpec()
    {
        return $this->spec;
    }

    /**
     * @return array
     */
    public function getInputSpecification()
    {
        return array(
            'name'       => $this->getName(),
            'required'   => true,
            'validators' => array(
                $this->getValidator()
            )
        );
    }

    /**
     * Get the validator
     *
     * @return ValidatorInterface
     */
    protected function getValidator()
    {
        if (null === $this->validator) {
            $this->validator = new ObjectExistsValidator(array(
                'object_repository' => $this->entityManager->getRepository($this->targetClass),
                'fields'            => $this->entityManager->getClassMetadata($this->targetClass)
                                                           ->getIdentifierFieldNames()
            ));
        }

        return $this->validator;
    }

    /**
     * @throws RuntimeException
     */
    protected function loadOptions()
    {
        if (!empty($this->attributes['options'])) {
            return;
        }

        if (!($em = $this->entityManager)) {
            throw new RuntimeException('No entityManager was set');
        }

        if (!($targetClass = $this->targetClass)) {
            throw new RuntimeException('No targetClass was set');
        }

        $metadata   = $em->getClassMetadata($targetClass);
        $identifier = $metadata->getIdentifierFieldNames();
        $entities   = $this->getEntities();
        $options    = array();

        foreach($entities as $key => $entity) {
            if (($property = $this->property)) {
                if (!$metadata->hasField($property)) {
                    throw new RuntimeException(sprintf(
                        'Property "%s" could not be found in entity "%s"',
                        $property,
                        $targetClass
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