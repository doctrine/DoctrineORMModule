<?php

namespace DoctrineORMModuleTest\Yuml;

use DoctrineORMModule\Yuml\MetadataGrapher;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the metadata to string converter
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class MetadataGrapherTest extends TestCase
{
    /**
     * @var MetadataGrapher
     */
    protected $grapher;

    /**
     * {@inheritDoc}
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->grapher = new MetadataGrapher();
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawSimpleEntity()
    {
        $class = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class->expects($this->any())->method('getName')->will($this->returnValue('Simple\\Entity'));
        $class->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));
        $class->expects($this->any())->method('getAssociationNames')->will($this->returnValue([]));

        $this->assertSame('[Simple.Entity]', $this->grapher->generateFromMetadata([$class]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawSimpleEntityWithFields()
    {
        $class = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class->expects($this->any())->method('getName')->will($this->returnValue('Simple\\Entity'));
        $class->expects($this->any())->method('getFieldNames')->will($this->returnValue(['a', 'b', 'c']));
        $class->expects($this->any())->method('getAssociationNames')->will($this->returnValue([]));
        $class->expects($this->any())->method('isIdentifier')->will(
            $this->returnCallback(
                function ($field) {
                    return $field === 'a';
                }
            )
        );

        $this->assertSame('[Simple.Entity|+a;b;c]', $this->grapher->generateFromMetadata([$class]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawOneToOneUniDirectionalAssociation()
    {
        $class1 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['b']));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class2 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue([]));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $this->assertSame('[A]-b 1>[B]', $this->grapher->generateFromMetadata([$class1, $class2]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawOneToOneBiDirectionalAssociation()
    {
        $class1 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['b']));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class2 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['a']));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class2->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('b'));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $this->assertSame('[A]<>a 1-b 1>[B]', $this->grapher->generateFromMetadata([$class1, $class2]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawOneToOneBiDirectionalInverseAssociation()
    {
        $class1 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['b']));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class1->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('a'));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class2 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['a']));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $this->assertSame('[A]<a 1-b 1<>[B]', $this->grapher->generateFromMetadata([$class1, $class2]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawOneToManyBiDirectionalAssociation()
    {
        $class1 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['b']));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class2 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['a']));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class2->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('b'));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $this->assertSame('[A]<>a 1-b *>[B]', $this->grapher->generateFromMetadata([$class1, $class2]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawOneToManyBiDirectionalInverseAssociation()
    {
        $class1 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['b']));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class2 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['a']));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class2->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('b'));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $this->assertSame('[A]<>a *-b 1>[B]', $this->grapher->generateFromMetadata([$class1, $class2]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawManyToManyUniDirectionalAssociation()
    {
        $class1 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['b']));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class2 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue([]));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $this->assertSame('[A]-b *>[B]', $this->grapher->generateFromMetadata([$class1, $class2]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawManyToManyUniDirectionalInverseAssociation()
    {
        $class1 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue([]));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class2 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['a']));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $this->assertSame('[A],[B]-a *>[A]', $this->grapher->generateFromMetadata([$class1, $class2]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawManyToManyBiDirectionalAssociation()
    {
        $class1 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['b']));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class2 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['a']));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class2->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('b'));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $this->assertSame('[A]<>a *-b *>[B]', $this->grapher->generateFromMetadata([$class1, $class2]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawManyToManyBiDirectionalInverseAssociation()
    {
        $class1 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['b']));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('a'));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class2 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['a']));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $this->assertSame('[A]<a *-b *<>[B]', $this->grapher->generateFromMetadata([$class1, $class2]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawManyToManyAssociationWithoutKnownInverseSide()
    {
        $class1 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['b']));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $this->assertSame('[A]<>-b *>[B]', $this->grapher->generateFromMetadata([$class1]));
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawInheritance()
    {
        $class1 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class2 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $child  = get_class($this->createMock('stdClass'));
        $class1->expects($this->any())->method('getName')->will($this->returnValue('stdClass'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue([]));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));
        $class2->expects($this->any())->method('getName')->will($this->returnValue($child));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue([]));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $this->assertSame(
            '[stdClass]^[' . str_replace('\\', '.', $child) . ']',
            $this->grapher->generateFromMetadata([$class2, $class1])
        );
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawInheritedFields()
    {
        $class1 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class2 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $child  = get_class($this->createMock('stdClass'));

        $class1->expects($this->any())->method('getName')->will($this->returnValue('stdClass'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue([]));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(['inherited']));

        $class2->expects($this->any())->method('getName')->will($this->returnValue($child));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue([]));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(['inherited', 'field2']));

        $this->assertSame(
            '[stdClass|inherited]^[' . str_replace('\\', '.', $child) . '|field2]',
            $this->grapher->generateFromMetadata([$class2, $class1])
        );
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     */
    public function testDrawInheritedAssociations()
    {
        $class1 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class2 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class3 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class4 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $child  = get_class($this->createMock('stdClass'));

        $class1->expects($this->any())->method('getName')->will($this->returnValue('stdClass'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['a']));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class2->expects($this->any())->method('getName')->will($this->returnValue($child));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['a', 'b']));
        $class2
            ->expects($this->any())
            ->method('getAssociationTargetClass')
            ->will(
                $this->returnCallback(
                    function ($assoc) {
                        return strtoupper($assoc);
                    }
                )
            );
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class3->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class3->expects($this->any())->method('getAssociationNames')->will($this->returnValue([]));
        $class3->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class4->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class4->expects($this->any())->method('getAssociationNames')->will($this->returnValue([]));
        $class4->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $childName = str_replace('\\', '.', $child);
        $this->assertSame(
            '[stdClass]<>-a *>[A],[stdClass]^[' . $childName . '],[' . $childName . ']<>-b *>[B]',
            $this->grapher->generateFromMetadata([$class1, $class2])
        );
    }

    /**
     * @covers \DoctrineORMModule\Yuml\MetadataGrapher
     * @dataProvider injectMultipleRelationsWithBothBiAndMonoDirectional
     */
    public function testDrawMultipleClassRelatedBothBiAndMonoDirectional($class1, $class2, $class3, $expected)
    {
        $this->assertSame(
            $expected,
            $this->grapher->generateFromMetadata([$class1, $class2,$class3])
        );
    }

    /**
     * dataProvider to inject classes in every order possible into the test
     *     testDrawMultipleClassRelatedBothBiAndMonoDirectional
     *
     * @return array
     */
    public function injectMultipleRelationsWithBothBiAndMonoDirectional()
    {
        $class1 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['c']));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('C'));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class2 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['c']));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('C'));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        $class3 = $this->createMock(\Doctrine\Common\Persistence\Mapping\ClassMetadata::class);
        $class3->expects($this->any())->method('getName')->will($this->returnValue('C'));
        $class3->expects($this->any())->method('getAssociationNames')->will($this->returnValue(['b']));
        $class3
            ->expects($this->any())
            ->method('getAssociationTargetClass')
            ->with($this->logicalOr($this->equalTo('b'), $this->equalTo('c')))
            ->will($this->returnCallback([$this,'getAssociationTargetClassMock']));
        $class3->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class3->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class3->expects($this->any())->method('getFieldNames')->will($this->returnValue([]));

        return [
            [$class1, $class2, $class3, '[A]-c 1>[C],[B]<>b *-c 1>[C]'],
            [clone $class1, clone $class3, clone $class2, '[A]-c 1>[C],[C]<c 1-b *<>[B]'],
            [clone $class2, clone $class1, clone $class3, '[B]<>b *-c 1>[C],[A]-c 1>[C]'],
            [clone $class2, clone $class3, clone $class1, '[B]<>b *-c 1>[C],[A]-c 1>[C]'],
            [clone $class3, clone $class1, clone $class2, '[C]<c 1-b *<>[B],[A]-c 1>[C]'],
            [clone $class3, clone $class2, clone $class1, '[C]<c 1-b *<>[B],[A]-c 1>[C]'],
        ];
    }

    /**
     * To mock getAssociationTargetClass method with args
     *
     * @param  string $a
     * @return string
     */
    public function getAssociationTargetClassMock($a)
    {
        return strtoupper($a);
    }
}
