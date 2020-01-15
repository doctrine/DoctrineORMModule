<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Options;

use DoctrineORMModule\Options\EntityManager;
use PHPUnit\Framework\TestCase;

/**
 * Tests for {@see \DoctrineORMModule\Options\EntityManager}
 *
 * @covers \DoctrineORMModule\Options\EntityManager
 */
class EntityManagerTest extends TestCase
{
    public function testSetGetResolver() : void
    {
        $options = new EntityManager();

        $this->assertSame('doctrine.entity_resolver.orm_default', $options->getEntityResolver());

        $options->setEntityResolver('foo');

        $this->assertSame('doctrine.entity_resolver.foo', $options->getEntityResolver());
    }
}
