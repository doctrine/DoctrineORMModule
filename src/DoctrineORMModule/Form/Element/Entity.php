<?php

namespace DoctrineORMModule\Form\Element;

use Closure, InvalidArgumentException, RuntimeException, Doctrine\ORM\EntityManager, Doctrine\ORM\EntityRepository, Doctrine\ORM\Query, Zend\Form\Element\Multi;

class Entity extends Multi
{

    const NULL_VALUE = '__NULL_VALUE__';

    protected $_expanded = false;

    protected $_multiple = false;

    protected $_entityClass;

    protected $_em;

    protected $_qb;

    protected $_property;

    protected $_method;

    protected $_emptyValue;

    protected $_entities;

    protected $helpers = array('default' => 'formSelect',
        'multiple' => 'formSelect', 'expanded' => 'formRadio',
        'multipleExpanded' => 'formMultiCheckbox');

    public function setMultiple ($multiple)
    {
        $this->_multiple = $multiple;
        return $this;
    }

    public function setExpanded ($expanded)
    {
        $this->_expanded = $expanded;
        return $this;
    }

    public function setEmptyValue ($emptyValue)
    {
        $this->_emptyValue = $emptyValue;
        return $this;
    }

    public function setProperty ($property)
    {
        $this->_property = $property;
        return $this;
    }

    public function setMethod ($method)
    {
        $this->_method = $method;
        return $this;
    }

    public function setEntityClass ($class)
    {
        $this->_entityClass = $class;
        return $this;
    }

    public function setEntityManager (EntityManager $entityManager)
    {
        $this->_em = $entityManager;
    }

    public function setQueryBuilder ($queryBuilder)
    {
        if (! is_callable($queryBuilder)) {
            throw new InvalidArgumentException('query builder must be callable');
        }

        $this->_qb = $queryBuilder;
    }

    public function init ()
    {
        if ($this->_multiple && $this->_expanded) {
            $this->helper = $this->helpers['multipleExpanded'];
        } else
            if ($this->_multiple) {
                $this->helper = $this->helpers['multiple'];
                $this->_isArray = true;
            } else
                if ($this->_expanded) {
                    $this->helper = $this->helpers['expanded'];
                } else {
                    $this->helper = $this->helpers['default'];
                }

        $this->validateParams();
        $this->load();
    }

    public function getEntities ()
    {
        if (null === $this->_entities) {
            if ($this->_qb instanceof Query) {
                $entities = $this->_qb->getQuery()->execute();
            } else
                if (is_callable($this->_qb)) {
                    $callable = $this->_qb;
                    $qb = $callable(
                        $this->_em->getRepository($this->_entityClass));
                    $entities = $qb->getQuery()->execute();
                } else {
                    $entities = $this->_em->getRepository($this->_entityClass)->findAll();
                }
            $this->_entities = $entities;
        }
        return $this->_entities;
    }

    protected function validateParams ()
    {
        if (null === $this->_em) {
            throw new InvalidArgumentException('No entityManager was set');
        }
        if (null === $this->_entityClass) {
            throw new InvalidArgumentException('No entityClass was set');
        }
    }

    protected function load ()
    {
        $mdata = $this->_em->getClassMetadata($this->_entityClass);
        $identifier = $mdata->getIdentifierFieldNames();

        if ($this->_emptyValue && ! $this->_multiple && ! $this->_expanded) {
            $this->options[self::NULL_VALUE] = $this->_emptyValue;
        }

        foreach ($this->getEntities() as $key => $entity) {
            if ($this->_property) {
                if (! isset($mdata->reflFields[$this->_property])) {
                    throw new RuntimeException(
                        sprintf(
                            'property "%s" could not be found in entity "%s"',
                            $this->_property, $this->_entityClass));
                }

                $value = $mdata->reflFields[$this->_property]->getValue($entity);
            } else
                if ($this->_method) {
                    if (! method_exists($entity, $this->_method)) {
                        throw new RuntimeException(
                            sprintf(
                                'method "%s" could not be found in class "%s"',
                                $this->_method, get_class($entity)));
                    }
                    $value = $entity->{$this->_method}();
                } else {
                    if (! method_exists($entity, '__toString')) {
                        throw new RuntimeException(
                            sprintf(
                                '%s must have a "__toString()" method defined if you have not set ' .
                                    'a "property" or "method" option.',
                                get_class($entity)));
                    }
                    $value = (string) $entity;
                }

            if (count($identifier) > 1) {
                $id = $key;
            } else {
                $id = current($mdata->getIdentifierValues($entity));
            }

            $this->options[$id] = $value;
        }
    }
}