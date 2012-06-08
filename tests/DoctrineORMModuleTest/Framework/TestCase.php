<?php

namespace DoctrineORMModuleTest\Framework;

use PHPUnit_Framework_TestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Zend\ServiceManager\ServiceManager;

class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceManager
     */
    protected static $sm;

    /**
     * @var boolean
     */
    protected static $hasDb = false;

    /**
     * Creates a database if not done already.
     */
    public function createDb()
    {
        if (self::$hasDb) {
            return;
        }

        $em = $this->getEntityManager();
        $tool = new SchemaTool($em);
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());
        self::$hasDb = true;
    }

    public function dropDb()
    {
        $em = $this->getEntityManager();
        $tool = new SchemaTool($em);
        $tool->dropSchema($em->getMetadataFactory()->getAllMetadata());
        $em->clear();
        self::$hasDb = false;
    }

    /**
     * @param ServiceManager $sm
     */
    public static function setServiceManager(ServiceManager $sm)
    {
        self::$sm = $sm;
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
    	return self::$sm;
    }

    /**
     * Get EntityManager.
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->getServiceManager()->get('doctrine_orm_default_entitymanager');
    }
}
