<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace DoctrineORMModuleTest\Collector;

use PHPUnit_Framework_TestCase;
use DoctrineORMModule\Collector\MappingCollector;

/**
 * Tests for the MappingCollector
 *
 * @author  Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class MappingCollectorTest extends PHPUnit_Framework_TestCase
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
    public function setUp()
    {
        parent::setUp();

        $this->metadataFactory = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadataFactory');
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
        $this->assertInternalType('int', $this->collector->getPriority());
    }

    /**
     * @covers \DoctrineORMModule\Collector\MappingCollector::collect
     * @covers \DoctrineORMModule\Collector\MappingCollector::getClasses
     */
    public function testCollect()
    {
        $m1 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $m1->expects($this->any())->method('getName')->will($this->returnValue('M1'));
        $m2 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $m2->expects($this->any())->method('getName')->will($this->returnValue('M2'));
        $this
            ->metadataFactory
            ->expects($this->any())
            ->method('getAllMetadata')
            ->will($this->returnValue(array($m1, $m2)));

        $this->collector->collect($this->getMock('Zend\\Mvc\\MvcEvent'));

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

        $m1 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $m1->expects($this->any())->method('getName')->will($this->returnValue('M1'));
        $this->metadataFactory->expects($this->any())->method('getAllMetadata')->will($this->returnValue(array($m1)));

        $this->collector->collect($this->getMock('Zend\\Mvc\\MvcEvent'));

        $this->assertFalse($this->collector->canHide());
    }

    /**
     * @covers \DoctrineORMModule\Collector\MappingCollector::serialize
     * @covers \DoctrineORMModule\Collector\MappingCollector::unserialize
     * @covers \DoctrineORMModule\Collector\MappingCollector::collect
     */
    public function testSerializeUnserializeAndCollectWithNoMetadataFactory()
    {
        $m1 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $m1->expects($this->any())->method('getName')->will($this->returnValue('M1'));
        $this->metadataFactory->expects($this->any())->method('getAllMetadata')->will($this->returnValue(array($m1)));

        $this->collector->collect($this->getMock('Zend\\Mvc\\MvcEvent'));

        /* @var $collector MappingCollector */
        $collector = unserialize(serialize($this->collector));

        $classes = $collector->getClasses();
        $this->assertCount(1, $classes);
        $this->assertEquals($m1, $classes['M1']);
        $this->assertSame('test-collector', $collector->getName());

        $collector->collect($this->getMock('Zend\\Mvc\\MvcEvent'));

        $classes = $collector->getClasses();
        $this->assertCount(1, $classes);
        $this->assertEquals($m1, $classes['M1']);
    }
}
