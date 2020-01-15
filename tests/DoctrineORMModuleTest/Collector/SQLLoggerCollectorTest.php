<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Collector;

use Doctrine\DBAL\Logging\DebugStack;
use DoctrineORMModule\Collector\SQLLoggerCollector;
use Laminas\Mvc\MvcEvent;
use PHPUnit\Framework\TestCase;
use function microtime;
use function sleep;

class SQLLoggerCollectorTest extends TestCase
{
    protected DebugStack $logger;

    protected string $name = 'test-collector-name';

    protected SQLLoggerCollector $collector;

    protected function setUp() : void
    {
        parent::setUp();
        $this->logger    = new DebugStack();
        $this->collector = new SQLLoggerCollector($this->logger, $this->name);
    }

    public function testHasCorrectName() : void
    {
        $this->assertSame($this->name, $this->collector->getName());
    }

    public function testGetPriority() : void
    {
        $this->assertIsInt($this->collector->getPriority());
    }

    public function testCollect() : void
    {
        $this->collector->collect(new MvcEvent());
        $this->addToAssertionCount(1);
    }

    public function testCanHide() : void
    {
        $this->assertTrue($this->collector->canHide());
        $this->logger->startQuery('some sql');
        $this->assertFalse($this->collector->canHide());
    }

    public function testGetQueryCount() : void
    {
        $this->assertSame(0, $this->collector->getQueryCount());
        $this->logger->startQuery('some sql');
        $this->assertSame(1, $this->collector->getQueryCount());
        $this->logger->startQuery('some more sql');
        $this->assertSame(2, $this->collector->getQueryCount());
    }

    public function testGetQueryTime() : void
    {
        $start = microtime(true);
        $this->assertEquals(0, $this->collector->getQueryTime());

        $this->logger->startQuery('some sql');
        sleep(1);
        $this->logger->stopQuery();
        $time  = microtime(true) - $start;
        $time1 = $this->collector->getQueryTime();
        $this->assertGreaterThan(0, $time1);
        $this->assertLessThan($time, $time1);

        $this->logger->startQuery('some more sql');
        $this->logger->stopQuery();
        $time  = microtime(true) - $start;
        $time2 = $this->collector->getQueryTime();
        $this->assertGreaterThan($time1, $time2);
        $this->assertLessThan($time, $time1);
    }
}
