<?php

namespace DoctrineORMModuleTest\ServiceManager;

use DoctrineORMModuleTest\Framework\TestCase;
use Doctrine\DBAL\Connection;

class EntityManagerTest extends TestCase
{
    /**
     * Verifying that the connection that is injected into a custom object is actually the same that can be retrieved
     * directly or from the EntityManager in the locator
     */
    public function testCanInjectConnection()
    {
        $sm = $this->getServiceManager();
        $em = $sm->get('Doctrine\ORM\EntityManager');

        $this->assertInstanceOf('Doctrine\ORM\EntityManager', $em);
    }
}
