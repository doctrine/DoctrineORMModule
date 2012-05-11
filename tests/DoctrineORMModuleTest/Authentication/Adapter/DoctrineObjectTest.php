<?php

namespace DoctrineORMModuleTest\Authentication\Adapter;

use DoctrineORMModuleTest\Framework\TestCase;
use DoctrineModule\Authentication\Adapter\DoctrineObject;
use DoctrineORMModuleTest\Assets\Entity\Test as TestEntity;


class DoctrineObjectTest extends TestCase
{
    public function setUp()
    {
        $this->createDb();
    }

    public function testInvalidLogin()
    {
        $adapter = new DoctrineObject($this->getEntityManager(), 'DoctrineORMModuleTest\Assets\Entity\Test');
        $adapter->setIdentityValue('username');
        $adapter->setCredentialValue('password');

        $result = $adapter->authenticate();

        $this->assertFalse($result->isValid());
    }

    public function testValidLogin()
    {
        $em = $this->getEntityManager();
        $entity = new TestEntity();
        $entity->setUsername('username');
        $entity->setPassword('password');
        $em->persist($entity);
        $em->flush();

        $adapter = new DoctrineObject($em, 'DoctrineORMModuleTest\Assets\Entity\Test');
        $adapter->setIdentityValue('username');
        $adapter->setCredentialValue('password');

        $result = $adapter->authenticate();

        $this->assertTrue($result->isValid());
    }

    public function tearDown()
    {
        $this->dropDb();
    }
}
