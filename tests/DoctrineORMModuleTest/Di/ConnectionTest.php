<?php

namespace DoctrineORMModuleTest\Di;

use DoctrineORMModuleTest\Framework\TestCase;
use Doctrine\DBAL\Connection;

class ConnectionTest extends TestCase
{
    /**
     * Verifying that the connection that is injected into a custom object is actually the same that can be retrieved
     * directly or from the EntityManager in the locator
     */
    public function testCanInjectConnection()
    {
        $locator = $this->getLocator();
        /* @var $target ConnectionTestInjectTarget */
        $target = $locator->get('DoctrineORMModuleTest\\Di\\ConnectionTestInjectTarget');
        $this->assertInstanceOf('DoctrineORMModuleTest\\Di\\ConnectionTestInjectTarget', $target);
        $connection = $target->getConnection();
        $this->assertInstanceOf('Doctrine\\DBAL\\Connection', $connection);
        $this->assertSame($connection, $locator->get('Doctrine\\DBAL\\Connection'));
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $locator->get('Doctrine\\ORM\\EntityManager');
        $this->assertSame($connection, $em->getConnection());
    }
}

class ConnectionTestInjectTarget
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
