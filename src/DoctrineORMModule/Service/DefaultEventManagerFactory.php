<?php

namespace DoctrineORMModule\Service;

use \DoctrineModule\Service\AbstractEventManagerFactory;

class DefaultEventManagerFactory extends AbstractEventManagerFactory
{
    public function getName()
    {
        return 'default';
    }
}