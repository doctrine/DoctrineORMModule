<?php
namespace DoctrineORMModuleTest\Authentication\Adapter;
use DoctrineORMModuleTest\Framework\TestCase;

class DoctrineEntityTest extends TestCase
{
    public function setUp()
    {
        $this->createDb();
    }

    public function testInvalidLogin()
    {
        $em = $this->getEntityManager();

        $adapter = new \DoctrineModule\Authentication\Adapter\DoctrineEntity(
            $em,
            'DoctrineORMModuleTest\Assets\Entity\Test'
        );
        $adapter->setIdentity('username');
        $adapter->setCredential('password');

        $result = $adapter->authenticate();

        $this->assertFalse($result->isValid());
    }

    public function testValidLogin()
    {
        $em = $this->getEntityManager();

        $entity = new \DoctrineORMModuleTest\Assets\Entity\Test;
        $entity->setUsername('username');
        $entity->password('password');
        $em->persist($entity);
        $em->flush();

        $adapter = new \DoctrineModule\Authentication\Adapter\DoctrineEntity(
            $em,
            'DoctrineORMModuleTest\Assets\Entity\Test'
        );
        $adapter->setIdentity('username');
        $adapter->setCredential('password');

        $result = $adapter->authenticate();

        $this->assertTrue($result->isValid());
    }
}
