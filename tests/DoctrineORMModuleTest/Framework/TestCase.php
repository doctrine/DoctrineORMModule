<?php

namespace DoctrineORMModuleTest\Framework;

use DoctrineORMModuleTest\ServiceManagerFactory;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Base test case for tests using the entity manager
 */
class TestCase extends PHPUnitTestCase
{
    /**
     * @var boolean
     */
    protected $hasDb = false;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Creates a database if not done already.
     */
    public function createDb()
    {
        if ($this->hasDb) {
            return;
        }

        $em   = $this->getEntityManager();
        $tool = new SchemaTool($em);
        $tool->updateSchema($em->getMetadataFactory()->getAllMetadata());
        $this->hasDb = true;
    }

    /**
     * Drops existing database
     */
    public function dropDb()
    {
        $em   = $this->getEntityManager();
        $tool = new SchemaTool($em);
        $tool->dropSchema($em->getMetadataFactory()->getAllMetadata());
        $em->clear();

        $this->hasDb = false;
    }

    /**
     * Get EntityManager.
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        if ($this->entityManager) {
            return $this->entityManager;
        }

        $serviceManager = ServiceManagerFactory::getServiceManager();
        $serviceManager->get('doctrine.entity_resolver.orm_default');
        $this->entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');

        return $this->entityManager;
    }
}
