<?php

namespace DoctrineORMModuleTest\Options;

use DoctrineORMModule\Options\DBALConnection;
use PHPUnit\Framework\TestCase;

/**
 * @covers \DoctrineORMModule\Options\DBALConnection
 */
class DBALConnectionTest extends TestCase
{
    public function testSetNullCommentedTypes(): void
    {
        $options = new DBALConnection();
        $options->setDoctrineCommentedTypes([]);
        $this->assertSame([], $options->getDoctrineCommentedTypes());
    }

    public function testSetGetCommentedTypes(): void
    {
        $options = new DBALConnection();
        $options->setDoctrineCommentedTypes(['mytype']);
        $this->assertSame(['mytype'], $options->getDoctrineCommentedTypes());
    }
}
