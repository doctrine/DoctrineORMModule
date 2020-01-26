<?php

namespace DoctrineORMModuleTest\Options;

use PHPUnit\Framework\TestCase;
use DoctrineORMModule\Options\DBALConnection;
use Doctrine\ORM\Repository\DefaultRepositoryFactory;

/**
 * @covers \DoctrineORMModule\Options\DBALConnection
 */
class DBALConnectionTest extends TestCase
{
    public function testSetNullCommentedTypes()
    {
        $options = new DBALConnection();
        $options->setDoctrineCommentedTypes([]);
        $this->assertSame([], $options->getDoctrineCommentedTypes());
    }

    public function testSetGetCommentedTypes()
    {
        $options = new DBALConnection();
        $options->setDoctrineCommentedTypes(['mytype']);
        $this->assertSame(['mytype'], $options->getDoctrineCommentedTypes());
    }
}
