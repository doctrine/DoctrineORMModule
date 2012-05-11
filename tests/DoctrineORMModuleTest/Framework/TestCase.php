<?php

namespace DoctrineORMModuleTest\Framework;

use PHPUnit_Framework_TestCase;
use DoctrineModule\Service\Doctrine;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Zend\Di\Locator;

class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var Locator
     */
    protected static $locator;

    /**
     * @var boolean
     */
    protected static $hasDb = false;

    /**
     * @var \DoctrineModule\Service\Doctrine
     */
    protected $_service;

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
     * @param Locator $locator
     */
    public static function setLocator(Locator $locator)
    {
        self::$locator = $locator;
    }

    /**
     * @return Locator
     */
    public function getLocator()
    {
    	return self::$locator;
    }

    /**
     * Get EntityManager.
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->getLocator()->get('Doctrine\ORM\EntityManager');
    }
}
