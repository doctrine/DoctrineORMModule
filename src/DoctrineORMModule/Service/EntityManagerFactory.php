<?php

namespace DoctrineORMModule\Service;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Service\AbstractFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class EntityManagerFactory extends AbstractFactory
{
    public function createService(ServiceLocatorInterface $sl)
    {
        $options = $this->getOptions($sl, 'entitymanager');
        $connection = $sl->get($options->getConnection());
        $config     = $sl->get($options->getConfiguration());

        return EntityManager::create($connection, $config);
    }

    /**
     * Get the class name of the options associated with this factory.
     *
     * @return string
     */
    public function getOptionsClass()
    {
        return 'DoctrineORMModule\Options\EntityManager';
    }
}