<?php

namespace DoctrineORMModule\Service;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Service\AbstractFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class EntityManagerFactory extends AbstractFactory
{
    /**
     * {@inheritDoc}
     * @return EntityManager
     */
    public function createService(ServiceLocatorInterface $sl)
    {
        /* @var $options \DoctrineORMModule\Options\EntityManager */
        $options = $this->getOptions($sl, 'entitymanager');
        $connection = $sl->get($options->getConnection());
        $config     = $sl->get($options->getConfiguration());

        return EntityManager::create($connection, $config);
    }

    /**
     * {@inheritDoc}
     */
    public function getOptionsClass()
    {
        return 'DoctrineORMModule\Options\EntityManager';
    }
}