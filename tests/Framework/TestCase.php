<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Framework;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use DoctrineORMModuleTest\ServiceManagerFactory;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * Base test case for tests using the entity manager
 */
class TestCase extends PHPUnitTestCase
{
    /** @var bool */
    protected $hasDb = false;

    /** @var ?EntityManager */
    private $entityManager = null;

    /**
     * Creates a database if not done already.
     */
    public function createDb(): void
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
    public function dropDb(): void
    {
        $em   = $this->getEntityManager();
        $tool = new SchemaTool($em);
        $tool->dropSchema($em->getMetadataFactory()->getAllMetadata());
        $em->clear();

        $this->hasDb = false;
    }

    /**
     * Get EntityManager.
     */
    public function getEntityManager(): EntityManager
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
