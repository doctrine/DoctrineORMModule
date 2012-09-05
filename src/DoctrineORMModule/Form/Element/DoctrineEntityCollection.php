<?php

namespace DoctrineORMModule\Form\Element;

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
}

