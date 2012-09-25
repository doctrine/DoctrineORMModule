<?php

namespace DoctrineORMModule\Form\Element;

use RuntimeException;
use Traversable;
use Doctrine\Common\Collections\Collection;

class DoctrineEntityCollection extends DoctrineEntity
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type'     => 'select',
        'multiple' => 'multiple',
    );

    /**
     * {@inheritDoc}
     */
    public function getInputSpecification()
    {
        return array(
            'name'       => $this->getName(),
            'required'   => true,
            'validator'  => null
        );
    }

    /**
     * {@inheritDoc}
     */
    public function setValue($value)
    {
        if (!($om = $this->objectManager)) {
            throw new RuntimeException('No object manager was set');
        }

        if (!($targetClass = $this->targetClass)) {
            throw new RuntimeException('No target class was set');
        }

        $metadata = $om->getClassMetadata($targetClass);
        if (!$value instanceof Collection) {
            return parent::setValue($value);
        }

        $data = array();
        foreach($value as $object) {
            $data[] = array_shift($metadata->getIdentifierValues($object));
        }

        return parent::setValue($data);
    }
}

