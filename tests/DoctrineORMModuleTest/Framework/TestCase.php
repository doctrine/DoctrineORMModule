<?php
namespace DoctrineORMModuleTest\Framework;
use PHPUnit_Framework_TestCase,
    DoctrineModule\Service\Doctrine;

class TestCase extends PHPUnit_Framework_TestCase
{
    public static $locator;

    /**
     * @var boolean
     */
    protected static $hasDb = false;

    /**
     * @var DoctrineModule\Service\Doctrine
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
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = array($em->getClassMetadata('DoctrineORMModuleTest\Assets\Entity\Test'));
        $tool->createSchema($classes);
        self::$hasDb = true;
    }

    public function getLocator()
    {
    	return self::$locator;
    }

    /**
     * Get EntityManager.
     *
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->getLocator()->get('doctrine_em');
    }
}
