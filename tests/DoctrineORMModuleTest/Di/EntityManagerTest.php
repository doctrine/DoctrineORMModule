<?php

namespace DoctrineORMModuleTest\Di;

use DoctrineORMModuleTest\Framework\TestCase;
use Doctrine\ORM\EntityManager;

class EntityManagerTest extends TestCase
{
    /**
     * Verifying that the EntityManager that is injected into a custom object is actually the same that can be
     * retrieved directly or from the locator
     */
    public function testCanInjectEntityManager()
    {
        $locator = $this->getLocator();
        $target = $locator->get('DoctrineORMModuleTest\Di\\EntityManagerTestInjectTarget');
        $this->assertInstanceOf('DoctrineORMModuleTest\Di\\EntityManagerTestInjectTarget', $target);
        $em = $target->getEntityManager();
        $this->assertInstanceOf('Doctrine\ORM\EntityManager', $em);
        $this->assertSame($em, $locator->get('Doctrine\ORM\EntityManager'));
    }
}

class EntityManagerTestInjectTarget
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }
}
