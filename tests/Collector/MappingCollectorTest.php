<?php

declare(strict_types=1);

namespace DoctrineORMModuleTest\Collector;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\ClassMetadataFactory;
use DoctrineORMModule\Collector\MappingCollector;
use Laminas\Mvc\MvcEvent;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

use function assert;
use function serialize;
use function unserialize;

/**
 * Tests for the MappingCollector
 */
class MappingCollectorTest extends TestCase
{
    /** @var ClassMetadataFactory|PHPUnit_Framework_MockObject_MockObject */
    protected $metadataFactory;

    /** @var MappingCollector */
    protected $collector;

    /**
     * @covers \DoctrineORMModule\Collector\MappingCollector::__construct
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->metadataFactory = $this->createMock(ClassMetadataFactory::class);
        $this->collector       = new MappingCollector($this->metadataFactory, 'test-collector');
    }

    /**
     * @covers \DoctrineORMModule\Collector\MappingCollector::getName
     */
    public function testGetName(): void
    {
        $this->assertSame('test-collector', $this->collector->getName());
    }

    /**
     * @covers \DoctrineORMModule\Collector\MappingCollector::getPriority
     */
    public function testGetPriority(): void
    {
        $this->assertIsInt($this->collector->getPriority());
    }

    /**
     * @covers \DoctrineORMModule\Collector\MappingCollector::collect
     * @covers \DoctrineORMModule\Collector\MappingCollector::getClasses
     */
    public function testCollect(): void
    {
        $m1 = $this->createMock(ClassMetadata::class);
        $m1->expects($this->any())->method('getName')->will($this->returnValue('M1'));
        $m2 = $this->createMock(ClassMetadata::class);
        $m2->expects($this->any())->method('getName')->will($this->returnValue('M2'));
        $this
            ->metadataFactory
            ->expects($this->any())
            ->method('getAllMetadata')
            ->will($this->returnValue([$m1, $m2]));

        $this->collector->collect($this->createMock(MvcEvent::class));

        $classes = $this->collector->getClasses();

        $this->assertCount(2, $classes);
        $this->assertSame($classes['M1'], $m1);
        $this->assertSame($classes['M2'], $m2);
    }

    /**
     * @covers \DoctrineORMModule\Collector\MappingCollector::canHide
     */
    public function testCanHide(): void
    {
        $this->assertTrue($this->collector->canHide());

        $m1 = $this->createMock(ClassMetadata::class);
        $m1->expects($this->any())->method('getName')->will($this->returnValue('M1'));
        $this->metadataFactory->expects($this->any())->method('getAllMetadata')->will($this->returnValue([$m1]));

        $this->collector->collect($this->createMock(MvcEvent::class));

        $this->assertFalse($this->collector->canHide());
    }

    /**
     * @covers \DoctrineORMModule\Collector\MappingCollector::serialize
     * @covers \DoctrineORMModule\Collector\MappingCollector::unserialize
     * @covers \DoctrineORMModule\Collector\MappingCollector::collect
     */
    public function testSerializeUnserializeAndCollectWithNoMetadataFactory(): void
    {
        $m1 = $this->createMock(ClassMetadata::class);
        $m1->expects($this->any())->method('getName')->will($this->returnValue('M1'));
        $this->metadataFactory->expects($this->any())->method('getAllMetadata')->will($this->returnValue([$m1]));

        $this->collector->collect($this->createMock(MvcEvent::class));

        $collector = unserialize(serialize($this->collector));
        assert($collector instanceof MappingCollector);

        $classes = $collector->getClasses();
        $this->assertCount(1, $classes);
        $this->assertEquals($m1, $classes['M1']);
        $this->assertSame('test-collector', $collector->getName());

        $collector->collect($this->createMock(MvcEvent::class));

        $classes = $collector->getClasses();
        $this->assertCount(1, $classes);
        $this->assertEquals($m1, $classes['M1']);
    }
}
