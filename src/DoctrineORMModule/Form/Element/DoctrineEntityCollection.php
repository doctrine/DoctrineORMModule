<?php

namespace DoctrineORMModule\Form\Element;

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
        );
    }

    /**
     * {@inheritDoc}
     */
    public function setValue($value)
    {
        if (!$value instanceof Collection) {
            return parent::setValue($value);
        }

        $data = array();
        foreach($value as $object) {
            $data[] = $this->getIdentifiers($object);
        }

        return parent::setValue($data);
    }
}

