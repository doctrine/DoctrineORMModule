<?php

namespace DoctrineORMModule\Service;

class DefaultEntityManagerFactory extends AbstractEntityManagerFactory
{
    public function getName()
    {
        return 'default';
    }
}