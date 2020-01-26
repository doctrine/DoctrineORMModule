<?php

namespace DoctrineORMModuleTest\Options;

use PHPUnit\Framework\TestCase;
use DoctrineORMModule\Options\EntityManager;

/**
 * Tests for {@see \DoctrineORMModule\Options\EntityManager}
 *
 * @covers \DoctrineORMModule\Options\EntityManager
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class EntityManagerTest extends TestCase
{
    public function testSetGetResolver()
    {
        $options = new EntityManager();

        $this->assertSame('doctrine.entity_resolver.orm_default', $options->getEntityResolver());

        $options->setEntityResolver('foo');

        $this->assertSame('doctrine.entity_resolver.foo', $options->getEntityResolver());
    }
}
