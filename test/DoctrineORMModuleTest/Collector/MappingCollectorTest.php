<?php

namespace DoctrineORMModuleTest\Collector;

use PHPUnit\Framework\TestCase;
use DoctrineORMModule\Collector\MappingCollector;

/**
 * Tests for the MappingCollector
 *
 * @author  Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class MappingCollectorTest extends TestCase
{
    /**
     * @var \Doctrine\Common\Persistence\Mapping\ClassMetadataFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadataFactory;

    /**
     * @var MappingCollector
     */
    protected $collector;

    /**
     * @covers \DoctrineORMModule\Collector\MappingCollector::__construct
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->metadataFactory = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadataFactory::class);
        $this->collector       = new MappingCollector($this->metadataFactory, 'test-collector');
    }

    /**
     * @covers \DoctrineORMModule\Collector\MappingCollector::getName
     */
    public function testGetName()
    {
        $this->assertSame('test-collector', $this->collector->getName());
    }

    /**
     * @covers \DoctrineORMModule\Collector\MappingCollector::getPriority
     */
    public function testGetPriority()
    {
        $this->assertIsInt($this->collector->getPriority());
    }

    /**
     * @covers \DoctrineORMModule\Collector\MappingCollector::collect
     * @covers \DoctrineORMModule\Collector\MappingCollector::getClasses
     */
    public function testCollect()
    {
        $m1 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $m1->expects($this->any())->method('getName')->will($this->returnValue('M1'));
        $m2 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $m2->expects($this->any())->method('getName')->will($this->returnValue('M2'));
        $this
            ->metadataFactory
            ->expects($this->any())
            ->method('getAllMetadata')
            ->will($this->returnValue([$m1, $m2]));

        $this->collector->collect($this->createMock(\Laminas\Mvc\MvcEvent::class));

        $classes = $this->collector->getClasses();

        $this->assertCount(2, $classes);
        $this->assertSame($classes['M1'], $m1);
        $this->assertSame($classes['M2'], $m2);
    }

    /**
     * @covers \DoctrineORMModule\Collector\MappingCollector::canHide
     */
    public function testCanHide()
    {
        $this->assertTrue($this->collector->canHide());

        $m1 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $m1->expects($this->any())->method('getName')->will($this->returnValue('M1'));
        $this->metadataFactory->expects($this->any())->method('getAllMetadata')->will($this->returnValue([$m1]));

        $this->collector->collect($this->createMock(\Laminas\Mvc\MvcEvent::class));

        $this->assertFalse($this->collector->canHide());
    }

    /**
     * @covers \DoctrineORMModule\Collector\MappingCollector::serialize
     * @covers \DoctrineORMModule\Collector\MappingCollector::unserialize
     * @covers \DoctrineORMModule\Collector\MappingCollector::collect
     */
    public function testSerializeUnserializeAndCollectWithNoMetadataFactory()
    {
        $m1 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $m1->expects($this->any())->method('getName')->will($this->returnValue('M1'));
        $this->metadataFactory->expects($this->any())->method('getAllMetadata')->will($this->returnValue([$m1]));

        $this->collector->collect($this->createMock(\Laminas\Mvc\MvcEvent::class));

        /* @var $collector MappingCollector */
        $collector = unserialize(serialize($this->collector));

        $classes = $collector->getClasses();
        $this->assertCount(1, $classes);
        $this->assertEquals($m1, $classes['M1']);
        $this->assertSame('test-collector', $collector->getName());

        $collector->collect($this->createMock(\Laminas\Mvc\MvcEvent::class));

        $classes = $collector->getClasses();
        $this->assertCount(1, $classes);
        $this->assertEquals($m1, $classes['M1']);
    }
}
