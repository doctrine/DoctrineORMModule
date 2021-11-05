<?php

namespace DoctrineORMModuleTest\Options;

use DoctrineORMModule\Options\SQLLoggerCollectorOptions;
use PHPUnit\Framework\TestCase;

class SQLLoggerCollectorOptionsTest extends TestCase
{
    public function testSetGetSQLLogger(): void
    {
        $options = new SQLLoggerCollectorOptions();
        $options->setSqlLogger('sql-logger-name');
        $this->assertSame('sql-logger-name', $options->getSqlLogger());
        $options->setSqlLogger(null);
        $this->assertNull($options->getSqlLogger());
    }

    public function testSetGetConfiguration(): void
    {
        $options = new SQLLoggerCollectorOptions();
        $options->setConfiguration('configuration-name');
        $this->assertSame('configuration-name', $options->getConfiguration());
        $options->setConfiguration(null);
        $this->assertSame('doctrine.configuration.orm_default', $options->getConfiguration());
    }

    public function testSetGetName(): void
    {
        $options = new SQLLoggerCollectorOptions();
        $this->assertSame('orm_default', $options->getName()); // testing defaults too!
        $options->setName('collector-name');
        $this->assertSame('collector-name', $options->getName());
        $options->setName(null);
        $this->assertSame('', $options->getName());
    }
}
